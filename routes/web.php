<?php

use App\Actions\ProcessWoeEventPoints;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Jobs\GuildMessagesToDiscord;
use App\Models\Patch;
use App\Ragnarok\ServerZeny;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

Route::get('/test', function() {
    ProcessWoeEventPoints::run('Kriemhild', today()->subDays(1), season: 1, sendDiscordNotification: true);
    ProcessWoeEventPoints::run('Swanhild', today()->subDays(2), season: 1, sendDiscordNotification: true);
    ProcessWoeEventPoints::run('Fadhringh', today()->subDays(3), season: 1, sendDiscordNotification: true);
    ProcessWoeEventPoints::run('Gondul', today()->subDays(3), season: 1, sendDiscordNotification: true);
    ProcessWoeEventPoints::run('Skoegul', today()->subDays(3), season: 1, sendDiscordNotification: true);
});

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

Route::view('/discord', 'discord');
Route::view('/forums', 'forums');

Route::resource('posts', PostController::class)->only('show');

Route::get('patch/notice', function () {
    $rawPosts = DB::table('posts')
        ->select('id', 'slug', 'title', 'blurb', 'created_at', DB::raw('MONTH(created_at) as month, YEAR(created_at) as year'))
        ->orderBy('created_at', 'DESC')
        ->get();

    $groupedPosts = $rawPosts->groupBy(function ($date) {
        return Carbon::parse($date->created_at)->format('F Y'); // grouping by month and year
    });

    $response = response()->view('patcher', ['groupedPosts' => $groupedPosts]);
    $response->headers->set('Content-Security-Policy', "frame-ancestors https://xilero.net https://xileretro.net http://patch.xileretro.net");

    return $response;
});

Route::get('patch/list', function() {
    $patches = Patch::all()->toArray();

    $formattedPatches = array_map(function($patch) {
        return sprintf(
            "%d %s %s // %s",
            $patch['number'],
            $patch['type'],
            $patch['patch_name'],
            $patch['comments'],
        );
    }, $patches);

    return response(implode("\n", $formattedPatches), 200)
        ->header('Content-Type', 'text/plain');
});

Route::any('{query}', function() {
    return redirect('/')->with('message', 'Redirected 404.');
})->where('query', '.*');


require __DIR__.'/auth.php';
