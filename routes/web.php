<?php

use App\Ragnarok\Guild;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Guid\Guid;

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

Auth::routes();

// Route::view('/', 'index');

Route::get('/', function()
{
    $prontera_castles = App\Ragnarok\GuildCastle::prontera()->with('guild', 'guild.members')->get();

    return view('index', ['prontera_castles' => $prontera_castles]);
});

Route::view('/discord', 'discord');
Route::view('/forums', 'forums');

/**
 * Google analytics says these are hit many times, so we'll
 * send them to the homepage rather than getting a 404.
 */
Route::redirect('/login', '/#steps2play');
Route::redirect('/password/reset', '/#steps2play');
Route::redirect('/register', '/#steps2play');


Route::any('{query}', function() {
    return redirect('/')->with('message', 'Redirected 404.');
})->where('query', '.*');
