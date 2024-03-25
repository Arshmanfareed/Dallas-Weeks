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
use App\http\Controllers\CompaignController;
use App\Http\Controllers\CompaignElementController;
use App\http\Controllers\StripePaymentController;
use App\http\Controllers\LeadsController;
use App\http\Controllers\ReportController;
use App\http\Controllers\MessageController;
use App\http\Controllers\ContactController;
use App\http\Controllers\IntegrationController;
use App\http\Controllers\HomeController;
use App\http\Controllers\FeatureController;
use App\http\Controllers\SocialController;

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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/auth/linkedin/redirect', function () {
    return Socialite::driver('linkedin-openid')->redirect();
});

Route::get('/auth/linkedin/callback', function () {
    $user = Socialite::driver('linkedin-openid')->stateless()->user();
    // echo $user->token;
    // dd($user);

    $data = [
        'title' => 'Setting'
    ];

    return view('dashboard-account', compact('data', 'user'));

    // return redirect('/dashboard');



});

// Route::get('linkedin/login', [SocialController::class, 'provider'])->name('linked.login');
// Route::get('linkedin/callback', [SocialController::class, 'providerCallback'])->name('linked.user');


Route::get('/team-rolesandpermission', [RolespermissionController::class, 'rolespermission']);
Route::get('/roles-and-permission-setting', [SettingController::class, 'settingrolespermission']);
Route::get('/compaign/createcompaign', [CompaignController::class, 'compaigncreate']);
Route::get('/compaign/compaigninfo', [CompaignController::class, 'compaigninfo']);
Route::get('/compaign/createcompaignfromscratch', [CompaignController::class, 'fromscratch']);
Route::get('/compaign/getcompaignelementbyslug/{slug}', [CompaignElementController::class, 'compaignElement'])->name('getcompaignelementbyslug');
Route::get('/leads', [LeadsController::class, 'leads']);
Route::get('/report', [ReportController::class, 'report']);
Route::get('/message', [MessageController::class, 'message']);
Route::get('/contacts', [ContactController::class, 'contact']);
Route::get('/integration', [IntegrationController::class, 'integration']);
Route::get('/feature-suggestion', [FeatureController::class, 'featuresuggestions']);

Route::get('/', [HomeController::class, 'home']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/pricing', [HomeController::class, 'pricing']);
Route::get('/faq', [HomeController::class, 'faq']);
Route::get('/login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logoutUser'])->name('logout-user');
Route::get('/register', [RegisterController::class, 'register']);
Route::post('/register-user', [RegisterController::class, 'registerUser'])->name('register-user');
Route::get('/dashboard', [DasboardController::class, 'dashboard'])->name('dashobardz');
Route::get('/blacklist', [BlacklistController::class, 'blacklist']);
Route::get('/team', [TeamController::class, 'team']);
Route::get('/invoice', [InvoiceController::class, 'invoice']);
// Route::get('/rolesandpermission',[RolespermissionController::class,'rolespermission']);
Route::get('/setting', [SettingController::class, 'setting']);
Route::get('/accdashboard', [MaindashboardController::class, 'maindasboard']);
Route::get('/compaign', [CompaignController::class, 'compaign']);
Route::post('/check-credentials', [LoginController::class, 'checkCredentials'])->name('checkCredentials');

Route::controller(StripePaymentController::class)->group(function () {
    Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});
