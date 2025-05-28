<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\ValidateToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(UserController::class)->group(function () {
    Route::post("/users", "create");
    Route::get("/users", "login")->middleware(ValidateToken::class);
    Route::put("/users", "update")->middleware(ValidateToken::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
