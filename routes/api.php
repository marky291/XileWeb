<?php

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
