<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Counter;
use App\Ragnarok\Char;
use App\Ragnarok\ServerZeny;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index', [
        'server_zeny' => ServerZeny::first(),
        'prontera_castles' => App\Ragnarok\GuildCastle::prontera()->with('guild', 'guild.members')->get()
    ]);
})->name('home');

Route::get('/warofemperium', function()
{
    return view('warofemperium', [
        'server_zeny' => ServerZeny::first(),
        'prontera_castles' => App\Ragnarok\GuildCastle::prontera()->with('guild', 'guild.members')->get()
    ]);
})->name('woe');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'server.owner'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('posts', PostController::class)->only('show')->middleware('features:latest-posts');

require __DIR__.'/auth.php';
