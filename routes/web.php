<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'SIT LHI Boilerplate is running',
        'version' => '1.0.0',
    ]);
});

Route::get('/test', function () {
    return view('welcome');
});
