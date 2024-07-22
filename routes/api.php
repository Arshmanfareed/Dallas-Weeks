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
Route::post('/create-link-account', [LinkedInController::class, 'createLinkAccount']);

Route::get('/accounts', [UnipileController::class, 'get_accounts'])->name('get_all_accounts');
Route::post('/retrieve_account', [UnipileController::class, 'retrieve_an_account'])->name('retrieve_an_account');
Route::post('/relations', [UnipileController::class, 'get_relations'])->name('get_relations');
Route::post('/view_profile', [UnipileController::class, 'view_profile'])->name('viewProfile');
Route::post('/invite_to_connect', [UnipileController::class, 'invite_to_connect'])->name('inviteToConnect');
Route::post('/message', [UnipileController::class, 'message'])->name('message');
Route::post('/inmail_message', [UnipileController::class, 'inmail_message'])->name('inmailMessage');
Route::post('/email', [UnipileController::class, 'email'])->name('email');
Route::post('/sendEmail', [UnipileController::class, 'email_message'])->name('email_message');
Route::post('/follow', [UnipileController::class, 'follow'])->name('follow');
Route::post('/search/sales_navigator', [UnipileController::class, 'sales_navigator_search'])->name('sales_navigator_search');
Route::post('/search/linkedin', [UnipileController::class, 'linkedin_search'])->name('linkedin_search');
Route::post('/search/post', [UnipileController::class, 'post_search'])->name('post_search');
Route::post('/search/post/reactions', [UnipileController::class, 'reactions_post_search'])->name('reactions_post_search');
