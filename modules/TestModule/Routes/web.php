<?php

use Illuminate\Support\Facades\Route;
use Modules\TestModule\Http\Controllers\TestModuleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('testmodules', TestModuleController::class)->names('testmodule');
});
