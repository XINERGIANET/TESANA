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

        $assignments = self::buildAssignments($client->id, $excludeAttendanceId);
        $used = 0;
        foreach ($assignments as $attendanceId => $assignedService) {
            if ($assignedService && $assignedService->id == $latestService->id) {
                $used++;
            }
        }

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

        $usage = self::currentUsage($client);
        $client->update([
            'sessions' => $usage['remaining'],
        ]);

        $assignments = self::buildAssignments($client->id);
        foreach ($assignments as $attendanceId => $assignedService) {
            $isActive = ($assignedService && $assignedService->id == $latestService->id) ? 1 : 0;
            Attendance::where('id', $attendanceId)->update(['active' => $isActive]);
        }
    }

    public static function resolveService(Attendance $attendance)
    {
        if (!$attendance->client_id) {
            return null;
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
            $attendanceDate = optional($attendance->date)->format('Y-m-d');

            while ($serviceIndex < $clientServices->count()) {
                $currentService = $clientServices[$serviceIndex];
                $limit = intval(optional($currentService->service)->sessions ?: 0);

                if ($serviceIndex + 1 < $clientServices->count()) {
                    $nextService = $clientServices[$serviceIndex + 1];
                    $nextStartDate = optional($nextService->start_date)->format('Y-m-d');
                    if ($nextStartDate && $attendanceDate && $attendanceDate >= $nextStartDate) {
                        $serviceIndex++;
                        continue;
                    }
                }

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

