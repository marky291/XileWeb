<?php

use App\Http\Controllers\Auth\DiscordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WikiController;
use App\Models\Patch;
use App\Models\Post;
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
    return view('index', [
        'castles' => Cache::remember('homepage:castles', now()->addMinutes(15), function () {
            return rescue(function () {
                return App\XileRetro\XileRetro_GuildCastle::whereIn('castle_id', [28, 31, 15, 16])
                    ->with('guild')
                    ->get()
                    ->sortBy(function ($castle, $key) {
                        $order = ['28', '31', '15', '16'];

                        return array_search((string) $castle->castle_id, $order);
                    });
            }, collect(), report: false);
        }),
        'popularUberItems' => Cache::remember('homepage:popular-uber-items', now()->addHour(), function () {
            $popularItemIds = DB::table(DB::raw(
                '(SELECT id, views, ROW_NUMBER() OVER (PARTITION BY item_id, refine_level ORDER BY views DESC) as rn FROM uber_shop_items WHERE enabled = 1 AND item_id IS NOT NULL) as ranked'
            ))
                ->where('rn', 1)
                ->orderByDesc('views')
                ->limit(24)
                ->pluck('id');

            return App\Models\UberShopItem::with('item')
                ->whereIn('id', $popularItemIds)
                ->orderByDesc('views')
                ->get()
                ->unique(fn ($item) => $item->display_name)
                ->values()
                ->take(18);
        }),
    ]);
})->name('home');

// User account management is handled by Filament /app panel

Route::view('/discord', 'discord');
Route::view('/forums', 'forums');

Route::resource('posts', PostController::class)->only(['index', 'show']);

// The rpatchur webview is Internet Explorer (MSHTML) on Windows, which cannot
// parse Debugbar's injected jQuery/PhpDebugBar scripts (causes IE "Syntax error"
// / "jQuery is undefined" dialogs). Disable Debugbar for the patcher page.
// XileRetro uses neoncube (legacy patcher) and has no rpatchur route.
Route::get('xilero/rpatchur', function () {
    if (app()->bound('debugbar')) {
        app('debugbar')->disable();
    }

    $posts = Post::query()
        ->where('client', Post::CLIENT_XILERO)
        ->latest()
        ->take(5)
        ->get();

    return view('rpatchur', compact('posts'));
});

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
    $patches = Patch::where('client', 'retro')->where('patcher', Patch::PATCHER_LEGACY)->orderBy('number')->get();

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
    $patches = Patch::where('client', 'xilero')->where('patcher', Patch::PATCHER_LEGACY)->orderBy('number')->get();

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

// rpatchur patch list (XileRO client). rpatchur parses each line as
// "<index> <filename>" (it reads words[0]=index, words[1]=file), so we emit
// exactly that — NO type token — and rely on the .thor file itself to carry the
// GRF-merge target. Trailing " // comment" is ignored by rpatchur. Only patches
// flagged patcher='rpatchur' are listed (legacy .gpf entries are excluded).
Route::get('xilero/rpatchur/list', function () {
    $patches = Patch::query()
        ->where('client', Patch::CLIENT_XILERO)
        ->where('patcher', Patch::PATCHER_RPATCHUR)
        ->orderBy('number')
        ->get();

    $lines = $patches->map(function (Patch $patch): string {
        $line = sprintf('%03d %s', $patch->number, $patch->patch_name);

        if (! empty($patch->comments)) {
            $line .= ' // '.$patch->comments;
        }

        return $line;
    });

    $content = $lines->implode("\r\n");
    $lastModified = $patches->max('updated_at') ?: now();

    return response($content, 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8')
        ->header('Content-Disposition', 'inline; filename="plist.txt"')
        ->header('Content-Length', (string) strlen($content))
        ->header('Cache-Control', 'no-cache, must-revalidate')
        ->header('Last-Modified', $lastModified->toRfc7231String())
        ->header('X-Content-Type-Options', 'nosniff');
});

// Wiki routes (file-based, dual-server — see docs/superpowers/specs/2026-06-27-xileweb-wiki-engine-design.md)
Route::get('/wiki', [WikiController::class, 'home'])->name('wiki.home');
Route::get('/wiki/search-index.json', [WikiController::class, 'searchIndex'])->name('wiki.search-index');
Route::get('/wiki/{server}/assets/{file}', [WikiController::class, 'asset'])
    ->where(['server' => '[a-z0-9_-]+', 'file' => '.*'])
    ->name('wiki.asset');
Route::get('/wiki/{server}/{path?}', [WikiController::class, 'show'])
    ->where(['server' => '[a-z0-9_-]+', 'path' => '.*'])
    ->name('wiki.show');

// Authentication routes (integrated with site design)
Route::middleware(['guest', \App\Http\Middleware\AuthMaintenanceMiddleware::class])->group(function () {
    Route::get('/login', \App\Livewire\Auth\GameAccountLogin::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');

    // Discord OAuth routes
    Route::get('/auth/discord/redirect', [DiscordController::class, 'redirect'])->name('auth.discord.redirect');
    Route::get('/auth/discord/callback', [DiscordController::class, 'callback'])->name('auth.discord.callback');
});

// Authenticated user routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Auth\Dashboard::class)->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

// Public shop (viewable by all, purchase requires auth)
Route::get('/donate-shop', \App\Livewire\DonateShop::class)->name('donate-shop');

// Item database viewer
Route::get('/item-database', \App\Livewire\ItemDatabase::class)->name('item-database');

require __DIR__.'/auth.php';

// SEO Routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

Route::fallback(function () {
    return redirect('/')->with('message', 'Redirected 404.');
});
