<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LinkedInController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('messages', [ChatController::class, 'message']);
Route::post('/create-link-account', [LinkedInController::class, 'createLinkAccount']);
Route::post('/view_profile', [UnipileController::class, 'view_profile'])->name('viewProfile');
Route::post('/invite_to_connect', [UnipileController::class, 'invite_to_connect'])->name('inviteToConnect');
Route::post('/message', [UnipileController::class, 'message'])->name('message');
Route::post('/inmail_message', [UnipileController::class, 'inmail_message'])->name('inmailMessage');
Route::post('/email', [UnipileController::class, 'email'])->name('email');
