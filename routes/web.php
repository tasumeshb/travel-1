<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

Route::get('/run-commands', function (\Illuminate\Http\Request $request) {
    $token = config('app.cache_clear_token');
    if (empty($token) || $request->query('token') !== $token) {
        abort(403, 'Forbidden');
    }
    Artisan::call('optimize:clear');
    return response()->json(['status' => 'success', 'message' => 'Commands executed successfully.']);
})->name('cache.runCommands');
    
Route::get('/intro','LandingpageController@index');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/install/check-db', 'HomeController@checkConnectDatabase');

Route::get('/geocode', 'HomeController@getLocation');

// Social Login
Route::get('social-login/{provider}', 'Auth\LoginController@socialLogin');
Route::get('social-callback/{provider}', 'Auth\LoginController@socialCallBack');

// Logs
Route::get(config('admin.admin_route_prefix').'/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['auth', 'dashboard','system_log_view'])->name('admin.logs');

Route::get('/install','InstallerController@redirectToRequirement')->name('LaravelInstaller::welcome');
Route::get('/install/environment','InstallerController@redirectToWizard')->name('LaravelInstaller::environment');


Route::get('test',function(){ 

    DB::table('users')->insert([
        'first_name'        => 'System',
        'last_name'         => 'Admin',
        'email'             => 'admin@bookingcore.test',
        'password'          => bcrypt('admin123'),
        'phone'             => '112 666 888',
        'status'            => 'publish',
        'city'            => 'New York',
        'country'            => 'US',
        'created_at'        => date("Y-m-d H:i:s"),
        'email_verified_at' => date("Y-m-d H:i:s"),
        'bio'               => 'We\'re designers who have fallen in love with creating spaces for others to reflect, reset, and create. We split our time between two deserts (the Mojave, and the Sonoran). We love the way the heat sinks into our bones, the vibrant sunsets, and the wildlife we get to call our neighbors.'
    ]);
    $user = \App\User::where('email', 'admin@bookingcore.test')->first();
    if(!is_demo_mode()){
        $user->need_update_pw = 1;
        $user->save();
    }

    $user->assignRole('administrator');
    return redirect('/admin');
});

Route::get('/clear-cache', [\Modules\Core\Controllers\ToolsController::class, 'clearCache'])
    ->middleware('cache.clear.token')
    ->name('cache.clear');
