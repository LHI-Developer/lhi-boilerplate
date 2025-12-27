<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-auth', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return response()->json([
            'authenticated' => true,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_class' => get_class($user),
            'has_roles' => $user->roles->count(),
            'role_names' => $user->getRoleNames(),
        ]);
    }

    return response()->json([
        'authenticated' => false,
        'message' => 'Not logged in'
    ]);
});
