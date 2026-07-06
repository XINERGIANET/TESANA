<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Client;
use App\Models\Attendance;

$client = Client::where('document', '08804479')->first();
if ($client) {
    echo "Client Found: " . $client->name . "\n";
    echo "DNI: " . $client->document . "\n";
    echo "Sessions Remaining: " . $client->sessions . "\n";
    echo "Service ID: " . $client->service_id . "\n";
    
    $attendances = Attendance::where('client_id', $client->id)->get();
    echo "Total Attendances: " . $attendances->count() . "\n";
    foreach ($attendances as $attendance) {
        echo "- " . $attendance->date . "\n";
    }
} else {
    echo "Client not found\n";
}
