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

Route::post('/accounts', [UnipileController::class, 'list_all_accounts'])->name('list_all_accounts');
Route::post('/retrieve/account', [UnipileController::class, 'retrieve_an_account'])->name('retrieve_an_account');
Route::post('/delete/account', [UnipileController::class, 'delete_account'])->name('delete_account');
Route::post('/restart/account', [UnipileController::class, 'restart_an_account'])->name('restart_an_account');
Route::post('/chats', [UnipileController::class, 'list_all_chats'])->name('list_all_chats');
Route::post('/retrieve/chat', [UnipileController::class, 'retrieve_a_chat'])->name('retrieve_a_chat');
Route::post('/chat/messages', [UnipileController::class, 'list_all_messages_from_chat'])->name('list_all_messages_from_chat');
Route::post('/chat/messages/attendees', [UnipileController::class, 'list_all_attendees_from_chat'])->name('list_all_attendees_from_chat');
Route::post('/retrieve/message', [UnipileController::class, 'retrieve_a_message'])->name('retrieve_a_message');
Route::post('/account/messages', [UnipileController::class, 'list_all_messages'])->name('list_all_messages');
Route::post('/account/attendees', [UnipileController::class, 'list_all_attendees'])->name('list_all_attendees');
Route::post('/retrieve/attendee', [UnipileController::class, 'retrieve_an_attendee'])->name('retrieve_an_attendee');
Route::post('/retrieve/attendee/1by1', [UnipileController::class, 'list_1_to_1_chats_from_attendee'])->name('list_1_to_1_chats_from_attendee');
Route::post('/attendee/messages', [UnipileController::class, 'list_all_messages_from_attendee'])->name('list_all_messages_from_attendee');
Route::post('/invitations', [UnipileController::class, 'list_all_invitaions'])->name('list_all_invitaions');
Route::post('/retrieve/account/me', [UnipileController::class, 'retrieve_own_profile'])->name('retrieve_own_profile');
Route::post('/relations', [UnipileController::class, 'list_all_relations'])->name('list_all_relations');
Route::post('/view_profile', [UnipileController::class, 'view_profile'])->name('viewProfile');
Route::post('/invite_to_connect', [UnipileController::class, 'invite_to_connect'])->name('inviteToConnect');

Route::post('/message', [UnipileController::class, 'message'])->name('message');
Route::post('/inmail_message', [UnipileController::class, 'inmail_message'])->name('inmailMessage');
Route::post('/sendEmail', [UnipileController::class, 'email_message'])->name('email_message');
Route::post('/follow', [UnipileController::class, 'follow'])->name('follow');
Route::post('/search/sales_navigator', [UnipileController::class, 'sales_navigator_search'])->name('sales_navigator_search');
Route::post('/search/linkedin', [UnipileController::class, 'linkedin_search'])->name('linkedin_search');
Route::post('/search/post', [UnipileController::class, 'post_search'])->name('post_search');
Route::post('/search/post/reactions', [UnipileController::class, 'reactions_post_search'])->name('reactions_post_search');
Route::post('/search/post/comments', [UnipileController::class, 'comments_post_search'])->name('comments_post_search');
Route::post('/search/messages', [UnipileController::class, 'messages_search'])->name('messages_search');
Route::post('/search/sales_navigator/lead_list', [UnipileController::class, 'lead_list_search'])->name('lead_list_search');
Route::post('/connection/count', [UnipileController::class, 'get_connection_count'])->name('get_connection_count');
