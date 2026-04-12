<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccessTokenController;
use App\Http\Controllers\PackageGroupController;

Route::options('/{path}', [DefaultController::class, 'cors'])->where('path', '.*');

Route::middleware(['cors'])->group(function () {
    // Login endpoints
    Route::middleware(['auth:login'])->group(function () {
        Route::get('/login/api', function (\Illuminate\Http\Request $request) {
            return response()->json($request->user('login'));
        });
    });

    Route::middleware(['auth:token'])->group(function () {
        Route::get('/login/token', function (\Illuminate\Http\Request $request) {
            return response()->json($request->user('token'));
        });
    });

    // All authenticated routes
    Route::middleware(['auth:token'])->group(function () {
        // User routes
        Route::get('/user/{name}', [UserController::class, 'findByUsername'])->where('name', '[a-z][a-z0-9\-\.]+');
        Route::get('/user/{userId}', [UserController::class, 'findById'])->where('userId', '[0-9]+');
        Route::get('/user', [UserController::class, 'getList']);
        Route::post('/user', [UserController::class, 'create']);
        Route::put('/user/{userId}', [UserController::class, 'update'])->where('userId', '[0-9]+');
        Route::delete('/user/{userId}', [UserController::class, 'deleteById'])->where('userId', '[0-9]+');
        Route::post('/user/{userId}/package-group', [UserController::class, 'createMapping'])->where('userId', '[0-9]+');
        Route::delete('/user/{userId}/package-group/{groupId}', [UserController::class, 'deleteMapping'])->where(['userId' => '[0-9]+', 'groupId' => '[0-9]+']);

        // Access token routes
        Route::get('/access-token/{userId}', [AccessTokenController::class, 'findByUserId'])->where('userId', '[0-9]+');
        Route::post('/access-token/{userId}', [AccessTokenController::class, 'add'])->where('userId', '[0-9]+');
        Route::delete('/access-token/{userId}/{tokenId}', [AccessTokenController::class, 'remove'])->where(['userId' => '[0-9]+', 'tokenId' => '[0-9]+']);

        // Permission routes
        Route::put('/permission/user/admin/{userId}', [UserController::class, 'giveAdmin'])->where('userId', '[0-9]+');
        Route::delete('/permission/user/admin/{userId}', [UserController::class, 'removeAdmin'])->where('userId', '[0-9]+');

        // Package group permission routes
        Route::post('/permission/package-group/join', [PackageGroupController::class, 'join']);
        Route::post('/permission/package-group/leave', [PackageGroupController::class, 'leave']);
        Route::post('/permission/package-group/protect', [PackageGroupController::class, 'protect']);
        Route::post('/permission/package-group/unprotect', [PackageGroupController::class, 'unprotect']);
        Route::get('/permission/package-group/approve', [PackageGroupController::class, 'getApprovals']);
        Route::post('/permission/package-group/approve', [PackageGroupController::class, 'approveRequest']);
        Route::delete('/permission/package-group/approve/{id}', [PackageGroupController::class, 'rejectRequest'])->where('id', '[0-9]+');
    });
});
