<?php

use App\Ragnarok\Char;
use App\Ragnarok\ServerZeny;
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
    return Cache::remember('discord', 0, function() {

        $uberCost = ServerZeny::first()->total_uber_cost ?? 0;
        $playerCount = Char::query()->online()->count() ?? 0;
        $latestCharacter = Char::latest('char_id')->first();

        return [
            'total_uber_cost' => $uberCost,
            'total_uber_cost_formatted' => number_format($uberCost),
            'player_count' => $playerCount,
            'player_count_formatted' => number_format($playerCount),
            'latest_character_name' => Str::ucfirst($latestCharacter->name)
        ];
    });
})->name('api.discord');
