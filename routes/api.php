<?php

use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\NpcController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/discord', function () {
    return Cache::remember('discord', now()->addMinute(), function () {
        $playerCount = Char::query()->online()->count() ?? 0;
        $latestCharacter = Char::latest('char_id')->first();

        return [
            'player_count' => $playerCount,
            'player_count_formatted' => number_format($playerCount),
            'latest_character_name' => Str::ucfirst($latestCharacter->name),
        ];
    });
})->name('api.discord');

/*
|--------------------------------------------------------------------------
| API V1 Routes (Token Authenticated)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware(['auth:sanctum', 'ability:read'])->group(function () {
    Route::get('/items', [ItemController::class, 'index'])->name('api.v1.items.index');
    Route::post('/items/bulk', [ItemController::class, 'bulk'])->name('api.v1.items.bulk');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('api.v1.items.show');

    Route::get('/npcs', [NpcController::class, 'index'])->name('api.v1.npcs.index');
    Route::post('/npcs/bulk', [NpcController::class, 'bulk'])->name('api.v1.npcs.bulk');
    Route::get('/npcs/{npc}', [NpcController::class, 'show'])->name('api.v1.npcs.show');
});
