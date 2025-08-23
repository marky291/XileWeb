<?php

use App\Actions\ProcessWoeEventPoints;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Jobs\GuildMessagesToDiscord;
use App\Models\Patch;
use App\Ragnarok\ServerZeny;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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

Route::get('/', function () {

    $server_zeny = Cache::remember('server_zeny', now()->addMinutes(60), function() {
        return ServerZeny::first();
    });

    return view('index', [
        'server_zeny' => $server_zeny,
        'castles' => App\Ragnarok\GuildCastle::whereIn('castle_id', [28, 31, 15, 16])
            ->with('guild', 'guild.members')
            ->get()
            ->sortBy(function($castle, $key) {
                $order = ['28', '31', '15', '16'];
                return array_search((string)$castle->castle_id, $order);
            })
    ]);
})->name('home');

Route::get('/warofemperium', function()
{

    $server_zeny = Cache::remember('server_zeny', now()->addMinutes(60), function() {
        return ServerZeny::first();
    });

    return view('warofemperium', [
        'server_zeny' => $server_zeny,
        'castles' => App\Ragnarok\GuildCastle::whereIn('castle_id', [28, 31, 15, 16])
            ->with('guild', 'guild.members')
            ->get()
            ->sortBy(function($castle, $key) {
                $order = ['28', '31', '15', '16'];
                return array_search((string)$castle->castle_id, $order);
            })
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
        ->select('id', 'slug', 'title', 'patcher_notice', 'created_at', DB::raw('MONTH(created_at) as month, YEAR(created_at) as year'))
        ->where('client', 'x9') // All posts are for XileRO (x9) client
        ->orderBy('created_at', 'DESC')
        ->get();

    $groupedPosts = $rawPosts->groupBy(function ($date) {
        return Carbon::parse($date->created_at)->format('F Y'); // grouping by month and year
    });

    $response = response()->view('patcher', ['groupedPosts' => $groupedPosts]);
    $response->headers->set('Content-Security-Policy', "frame-ancestors https://xilero.net https://xileretro.net http://patch.xileretro.net");

    return $response;
});

// Alias route for clarity - both routes show the same XileRO posts
Route::get('x9/patch/notice', function () {
    $rawPosts = DB::table('posts')
        ->select('id', 'slug', 'title', 'patcher_notice', 'created_at', DB::raw('MONTH(created_at) as month, YEAR(created_at) as year'))
        ->where('client', 'x9') // All posts are for XileRO (x9) client
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

require __DIR__.'/auth.php';

Route::any('{query}', function() {
    return redirect('/')->with('message', 'Redirected 404.');
})->where('query', '.*');
