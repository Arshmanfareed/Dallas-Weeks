<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LinkedInController;
use App\Http\Controllers\UnipileController;

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


Route::post('/create-linkedin-account', [LinkedInController::class, 'createLinkedinAccount']);

Route::match(['get', 'post'], '/unipile-callback', [UnipileController::class, 'handleCallback']);
