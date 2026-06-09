<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'PrintEasy API',
        'version' => '1.0.0',
        'status' => 'ok',
    ]);
});
