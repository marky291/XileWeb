<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\WikiController;
use App\Models\Patch;
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

Route::get('/', function () {
    return view('index', [
        'castles' => App\Ragnarok\GuildCastle::whereIn('castle_id', [28, 31, 15, 16])
            ->with('guild', 'guild.members')
            ->get()
            ->sortBy(function ($castle, $key) {
                $order = ['28', '31', '15', '16'];

                return array_search((string) $castle->castle_id, $order);
            }),
    ]);
})->name('home');

Route::get('/warofemperium', function () {
    return view('warofemperium', [
        'castles' => App\Ragnarok\GuildCastle::whereIn('castle_id', [28, 31, 15, 16])
            ->with('guild', 'guild.members')
            ->get()
            ->sortBy(function ($castle, $key) {
                $order = ['28', '31', '15', '16'];

                return array_search((string) $castle->castle_id, $order);
            }),
    ]);
})->name('woe');

// User account management is handled by Filament /app panel

Route::view('/discord', 'discord');
Route::view('/forums', 'forums');

Route::resource('posts', PostController::class)->only('show');

Route::get('retro/patch/notice', function () {
    $rawPosts = DB::table('posts')
        ->select('id', 'slug', 'title', 'patcher_notice', 'created_at', DB::raw('MONTH(created_at) as month, YEAR(created_at) as year'))
        ->where('client', 'retro') // Posts for Retro client
        ->orderBy('created_at', 'DESC')
        ->get();

    $groupedPosts = $rawPosts->groupBy(function ($date) {
        return Carbon::parse($date->created_at)->format('F Y'); // grouping by month and year
    });

    $response = response()->view('patcher', ['groupedPosts' => $groupedPosts]);
    $response->headers->set('Content-Security-Policy', 'frame-ancestors https://xilero.net https://xileretro.net http://patch.xileretro.net');

    return $response;
});

// XileRO specific patch notices
Route::get('xilero/patch/notice', function () {
    $rawPosts = DB::table('posts')
        ->select('id', 'slug', 'title', 'patcher_notice', 'created_at', DB::raw('MONTH(created_at) as month, YEAR(created_at) as year'))
        ->where('client', 'xilero') // Posts for XileRO client
        ->orderBy('created_at', 'DESC')
        ->get();

    $groupedPosts = $rawPosts->groupBy(function ($date) {
        return Carbon::parse($date->created_at)->format('F Y'); // grouping by month and year
    });

    $response = response()->view('patcher', ['groupedPosts' => $groupedPosts]);
    $response->headers->set('Content-Security-Policy', 'frame-ancestors https://xilero.net https://xileretro.net http://patch.xileretro.net');

    return $response;
});

Route::get('retro/patch/list', function () {
    $patches = Patch::where('client', 'retro')->orderBy('number')->get();

    $formattedPatches = array_map(function ($patch) {
        $base = sprintf(
            '%03d %s %s',
            $patch['number'],
            $patch['type'],
            $patch['patch_name']
        );

        if (! empty($patch['comments'])) {
            $base .= ' // '.$patch['comments'];
        }

        return $base;
    }, $patches->toArray());

    // Create the content with proper line endings
    $content = implode("\r\n", $formattedPatches);

    // Get last modified time from the most recent patch update
    $lastModified = $patches->max('updated_at') ?: now();

    return response($content, 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8')
        ->header('Content-Disposition', 'inline; filename="patchlist.txt"')
        ->header('Content-Length', strlen($content))
        ->header('Cache-Control', 'no-cache, must-revalidate')
        ->header('Last-Modified', $lastModified->toRfc7231String())
        ->header('X-Content-Type-Options', 'nosniff');
});

Route::get('xilero/patch/list', function () {
    $patches = Patch::where('client', 'xilero')->orderBy('number')->get();

    $formattedPatches = array_map(function ($patch) {
        $base = sprintf(
            '%03d %s %s',
            $patch['number'],
            $patch['type'],
            $patch['patch_name']
        );

        if (! empty($patch['comments'])) {
            $base .= ' // '.$patch['comments'];
        }

        return $base;
    }, $patches->toArray());

    // Create the content with proper line endings
    $content = implode("\r\n", $formattedPatches);

    // Get last modified time from the most recent patch update
    $lastModified = $patches->max('updated_at') ?: now();

    return response($content, 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8')
        ->header('Content-Disposition', 'inline; filename="patchlist.txt"')
        ->header('Content-Length', strlen($content))
        ->header('Cache-Control', 'no-cache, must-revalidate')
        ->header('Last-Modified', $lastModified->toRfc7231String())
        ->header('X-Content-Type-Options', 'nosniff');
});

// Wiki routes
// Route::get('/wiki', [WikiController::class, 'show'])->name('wiki.home');
// Route::get('/wiki/{path}', [WikiController::class, 'show'])->where('path', '.*')->name('wiki.show');

// Authentication routes (integrated with site design)
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\GameAccountLogin::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\GameAccountRegister::class)->name('register');
    Route::get('/password-reset', fn () => redirect('/app/password-reset'))->name('password.request');
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', \App\Livewire\Auth\GameAccountDashboard::class)->name('dashboard');
    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

Route::any('{query}', function () {
    return redirect('/')->with('message', 'Redirected 404.');
})->where('query', '.*');

require __DIR__.'/auth.php';
