<?php

namespace App\Support;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\ClientService;

class ClientAttendances
{
    public static function latestService(Client $client)
    {
        return ClientService::with('service')
            ->where('client_id', $client->id)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->first();
    }

    public static function currentUsage(Client $client, $excludeAttendanceId = null)
    {
        $latestService = self::latestService($client);

        if (!$latestService) {
            return [
                'service' => null,
                'limit' => 0,
                'used' => 0,
                'remaining' => 0,
            ];
        }

        $limit = intval(optional($latestService->service)->sessions ?: 0);
        $used = Attendance::where('client_id', $client->id)
            ->where('active', 1)
            ->when($excludeAttendanceId, function ($query) use ($excludeAttendanceId) {
                return $query->where('id', '<>', $excludeAttendanceId);
            })
            ->count();

        return [
            'service' => $latestService,
            'limit' => $limit,
            'used' => $used,
            'remaining' => max(0, $limit - $used),
        ];
    }

    public static function hasAvailableSession(Client $client, $excludeAttendanceId = null)
    {
        $usage = self::currentUsage($client, $excludeAttendanceId);

        return $usage['limit'] > 0 && $usage['remaining'] > 0;
    }

    public static function sync(Client $client)
    {
        $latestService = self::latestService($client);
        if (!$latestService) {
            $client->update(['sessions' => 0]);
            Attendance::where('client_id', $client->id)->update(['active' => 0]);
            return;
        }

        $serviceSessions = intval(optional($latestService->service)->sessions ?: optional($client->service)->sessions ?: 0);
        $activeAttendances = Attendance::where('client_id', $client->id)
            ->where('active', 1)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        foreach ($activeAttendances as $index => $attendance) {
            if ($serviceSessions < 1 || $index >= $serviceSessions) {
                $attendance->update(['active' => 0]);
            }
        }

        $activeCount = min($activeAttendances->count(), $serviceSessions);

        $client->update([
            'sessions' => max(0, $serviceSessions - $activeCount),
        ]);
    }

    public static function resolveService(Attendance $attendance)
    {
        if (!$attendance->client_id) {
            return null;
        }

        if ($attendance->active) {
            return optional($attendance->client) ? self::latestService($attendance->client) : null;
        }

        $assignments = self::buildAssignments($attendance->client_id);
        return $assignments[$attendance->id] ?? null;
    }

    public static function buildAssignments($clientId, $excludeAttendanceId = null)
    {
        $clientServices = ClientService::with('service')
            ->where('client_id', $clientId)
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        if ($clientServices->isEmpty()) {
            return [];
        }

        $attendances = Attendance::where('client_id', $clientId)
            ->when($excludeAttendanceId, function ($query) use ($excludeAttendanceId) {
                return $query->where('id', '<>', $excludeAttendanceId);
            })
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $assignments = [];
        $serviceIndex = 0;
        $usedSessions = [];

        foreach ($attendances as $attendance) {
            $assignedService = null;

            while ($serviceIndex < $clientServices->count()) {
                $currentService = $clientServices[$serviceIndex];
                $limit = intval(optional($currentService->service)->sessions ?: 0);

                if ($limit > 0 && ($usedSessions[$currentService->id] ?? 0) < $limit) {
                    $assignedService = $currentService;
                    break;
                }

                $serviceIndex++;
            }

            $assignments[$attendance->id] = $assignedService;

            if ($assignedService) {
                $usedSessions[$assignedService->id] = ($usedSessions[$assignedService->id] ?? 0) + 1;
            }
        }

        return $assignments;
    }
}
