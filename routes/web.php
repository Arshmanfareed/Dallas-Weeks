<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DasboardController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RolespermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\MaindashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignElementController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\ScheduleCampaign;
use App\Http\Controllers\UnipileController;
use App\Http\Controllers\LinkedInController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* This below is only for testing */

Route::get('/test_route', [TestController::class, 'base']); //Nothing to work

/* These are home pages url which does not require any authentication */
Route::get('/', [HomeController::class, 'home']); //Done
Route::get('/about', [HomeController::class, 'about']); //Done
Route::get('/pricing', [HomeController::class, 'pricing']); //Done
Route::get('/faq', [HomeController::class, 'faq']); //Done

/* These are login and signup url which does not require any authentication */
Route::get('/register', [RegisterController::class, 'register'])->name('register'); //Done
Route::post('/register-user', [RegisterController::class, 'registerUser'])->name('register-user'); //Done
Route::get('/verify_an_Email/{email}', [RegisterController::class, 'verifyAnEmail'])->name('verify_an_Email'); //Need to check
Route::get('/login', [LoginController::class, 'login'])->name('login'); //Done
Route::post('/check-credentials', [LoginController::class, 'checkCredentials'])->name('checkCredentials'); //Done
Route::post('/add_email_account', [LinkedInController::class, 'addEmailToAccount'])->name('addEmailAccount'); //Need to check
Route::post('/create-link-account', [LinkedInController::class, 'createLinkAccount'])->name('createLinkAccount'); //Need to check

/* These are for actions like campaign and leads */
Route::match(['get', 'post'], '/unipile-callback', [UnipileController::class, 'handleCallback']); //Need to check
Route::get('/delete_an_account', [LinkedInController::class, 'delete_an_account'])->name('delete_an_account'); //Need to check
Route::get('/delete_an_email_account/{seat_email}', [LinkedInController::class, 'delete_an_email_account'])->name('delete_an_email_account'); //Need to check

/* These are for dashboard which requires authentication */
Route::middleware(['userAuth'])->group(function () {
    /* These are for dashboard which does not require seat_id in session */
    Route::get('/dashboard', [DasboardController::class, 'dashboard'])->name('dashobardz'); //Done
    Route::get('/blacklist', [BlacklistController::class, 'blacklist']); //Need to check
    Route::get('/team', [TeamController::class, 'team']); //Need to check
    Route::get('/invoice', [InvoiceController::class, 'invoice']); //Need to check
    Route::get('/roles-and-permission-setting', [SettingController::class, 'settingrolespermission']); //Need to check
    Route::prefix('seat')->group(function () {
        Route::get('/getSeatById/{id}', [SeatController::class, 'get_seat_by_id'])->name('getSeatById'); //Need to check
        Route::get('/deleteSeat/{id}', [SeatController::class, 'delete_seat'])->name('deleteSeat'); //Need to check
        Route::get('/updateName/{id}/{seat_name}', [SeatController::class, 'update_name'])->name('updateName'); //Need to check
        Route::get('/filterSeat/{search}', [SeatController::class, 'filterSeat'])->name('filterSeat'); //Need to check
    });
    Route::controller(StripePaymentController::class)->group(function () {
        Route::get('stripe', 'stripe'); //Need to check
        Route::post('stripe', 'stripePost')->name('stripe.post'); //Need to check
    });
    Route::get('/team-rolesandpermission', [RolespermissionController::class, 'rolespermission']); //Need to check
    Route::post('/logout', [LoginController::class, 'logoutUser'])->name('logout-user'); //Need to check

    /* This dashboard uses to update seat_id in session */
    Route::match(['get', 'post'], '/accdashboard', [MaindashboardController::class, 'maindasboard'])->name('acc_dash'); //Need to check

    /* This setting might not requires account connectivity */
    Route::get('/setting', [SettingController::class, 'setting'])->name('dash-settings'); //Need to check

    /* These are for dashboard which requires account connectivity */
    Route::middleware(['linkedinAccount'])->group(function () {
        Route::prefix('campaign')->group(function () {
            Route::get('/', [CampaignController::class, 'campaign'])->name('campaigns'); //Need to check
            Route::get('/createcampaign', [CampaignController::class, 'campaigncreate'])->name('campaigncreate'); //Need to check
            Route::post('/campaigninfo', [CampaignController::class, 'campaigninfo'])->name('campaigninfo'); //Need to check
            Route::post('/createcampaignfromscratch', [CampaignController::class, 'fromscratch'])->name('createcampaignfromscratch'); //Need to check
            Route::get('/campaignDetails/{campaign_id}', [CampaignController::class, 'getCampaignDetails'])->name('campaignDetails'); //Need to check
            Route::get('/changeCampaignStatus/{campaign_id}', [CampaignController::class, 'changeCampaignStatus'])->name('changeCampaignStatus'); //Need to check
            Route::get('/{campaign_id}', [CampaignController::class, 'deleteCampaign'])->name('deleteCampaign'); //Need to check
            Route::get('/archive/{campaign_id}', [CampaignController::class, 'archiveCampaign'])->name('archiveCampaign'); //Need to check
            Route::get('/getcampaignelementbyslug/{slug}', [CampaignElementController::class, 'campaignElement'])->name('getcampaignelementbyslug'); //Need to check
            Route::post('/createCampaign', [CampaignElementController::class, 'createCampaign'])->name('createCampaign'); //Need to check
            Route::get('/getPropertyDatatype/{id}/{element_slug}', [PropertiesController::class, 'getPropertyDatatype'])->name('getPropertyDatatype'); //Need to check
            // Route::get('/scheduleDays/{schedule_id}', [ScheduleCampaign::class, 'scheduleDays'])->name('scheduleDays'); //Need to check
            Route::get('/editcampaign/{campaign_id}', [CampaignController::class, 'editCampaign'])->name('editCampaign'); //Need to check
            Route::post('/editCampaignInfo/{campaign_id}', [CampaignController::class, 'editCampaignInfo'])->name('editCampaignInfo'); //Need to check
            Route::post('/editCampaignSequence/{campaign_id}', [CampaignController::class, 'editCampaignSequence'])->name('editCampaignSequence'); //Need to check
            Route::get('/getcampaignelementbyid/{element_id}', [CampaignElementController::class, 'getcampaignelementbyid'])->name('getcampaignelementbyid'); //Need to check
            Route::post('/updateCampaign/{campaign_id}', [CampaignController::class, 'updateCampaign'])->name('updateCampaign'); //Need to check
            Route::get('/getPropertyRequired/{id}', [PropertiesController::class, 'getPropertyRequired'])->name('getPropertyRequired'); //Need to check
        });
        Route::prefix('leads')->group(function () {
            Route::get('/', [LeadsController::class, 'leads'])->name('dash-leads'); //Need to check
            Route::get('/getLeadsByCampaign/{id}/{search}', [LeadsController::class, 'getLeadsByCampaign'])->name('getLeadsByCampaign'); //Need to check
            Route::post('/sendLeadsToEmail', [LeadsController::class, 'sendLeadsToEmail'])->name('sendLeadsToEmail'); //Need to check
        });
        Route::prefix('message')->group(function () {
            Route::get('/', [MessageController::class, 'message'])->name('dash-messages');
            Route::get('/chat/profile/{profile_id}/{chat_id}', [MessageController::class, 'get_profile_and_latest_message'])->name('get_profile_and_latest_message'); //Need to check
            Route::get('/latest/{chat_id}', [MessageController::class, 'get_latest_Mesage_chat_id'])->name('get_latest_Mesage_chat_id'); //Need to check
            Route::get('/chat/latest/{chat_id}/{count}', [MessageController::class, 'get_latest_message_in_chat'])->name('get_latest_message_in_chat'); //Need to check
            Route::get('/chat/profile/{profile_id}', [MessageController::class, 'get_chat_Profile'])->name('get_chat_Profile'); //Need to check
            Route::get('/chat/receiver/{chat_id}', [MessageController::class, 'get_chat_receive'])->name('get_chat_receive'); //Need to check
            Route::get('/chat/sender', [MessageController::class, 'get_chat_sender'])->name('get_chat_sender'); //Need to check
            Route::get('/chat/{chat_id}', [MessageController::class, 'get_messages_chat_id'])->name('get_messages_chat_id'); //Need to check
            Route::get('/chat/{chat_id}/{cursor}', [MessageController::class, 'get_messages_chat_id_cursor'])->name('get_messages_chat_id_cursor'); //Need to check
            Route::get('/chats/{cursor}', [MessageController::class, 'get_remain_chats'])->name('get_remain_chats'); //Need to check
            Route::post('/send/chat', [MessageController::class, 'send_a_message'])->name('send_a_message'); //Need to check
            Route::post('/search', [MessageController::class, 'message_search'])->name('message_search'); //Need to check
            Route::get('/unread', [MessageController::class, 'unread_message'])->name('unread_message'); //Need to check
            Route::get('/profile/{profile_id}', [MessageController::class, 'profile_by_id'])->name('profile_by_id'); //Need to check
            Route::post('/retrieve/message/attachment', [UnipileController::class, 'retrieve_an_attachment_from_a_message'])->name('retrieve_an_attachment_from_a_message'); //Need to check
        });
        Route::get('/filterCampaign/{filter}/{search}', [CampaignController::class, 'filterCampaign'])->name('filterCampaign'); //Need to check
        Route::post('/createSchedule', [ScheduleCampaign::class, 'createSchedule'])->name('createSchedule'); //Need to check
        Route::get('/filterSchedule/{search}', [ScheduleCampaign::class, 'filterSchedule'])->name('filterSchedule'); //Need to check
        Route::get('/getElements/{campaign_id}', [CampaignElementController::class, 'getElements'])->name('getElements'); //Need to check
        Route::post('/import_csv', [CsvController::class, 'import_csv'])->name('import_csv'); //Need to check
        Route::get('/report', [ReportController::class, 'report'])->name('dash-reports'); //Need to check
        Route::get('/contacts', [ContactController::class, 'contact']); //Need to check
        Route::get('/integration', [IntegrationController::class, 'integration'])->name('dash-integrations'); //Need to check
        Route::get('/feature-suggestion', [FeatureController::class, 'featuresuggestions'])->name('dash-feature-suggestions'); //Need to check
    });
});
