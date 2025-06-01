<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    Log::info('Welcome page visited');

    return view('welcome');
});

Route::get('/info', function (): bool {
    Log::info('Phpinfo page visited');

    return phpinfo();
});

Route::get('/health', function () {
    $status = [];

    // Check Database Connection
    try {
        DB::connection()->getPdo();
        // Optionally, run a simple query
        DB::select('SELECT 1');
        $status['database'] = 'OK';
    } catch (Exception) {
        $status['database'] = 'Error';
    }

    // Check Redis Connection
    try {
        Cache::store('redis')->put('health_check', 'OK', 10);
        $value = Cache::store('redis')->get('health_check');
        $status['redis'] = $value === 'OK' ? 'OK' : 'Error';
    } catch (Exception) {
        $status['redis'] = 'Error';
    }

    // Check Storage Access
    try {
        $testFile = 'health_check.txt';
        Storage::put($testFile, 'OK');
        $content = Storage::get($testFile);
        Storage::delete($testFile);

        $status['storage'] = $content === 'OK' ? 'OK' : 'Error';
    } catch (Exception) {
        $status['storage'] = 'Error';
    }

    // Determine overall health status
    $isHealthy = collect($status)->every(fn ($value): bool => $value === 'OK');

    $httpStatus = $isHealthy ? 200 : 503;

    return response()->json($status, $httpStatus);
});
