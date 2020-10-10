<?php

namespace App\Http\Controllers;

use App\Ragnarok\GuildCastle;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guild = GuildCastle::query()->where('id', GuildCastle::ID_Kriemhild);

        dd($guild);

        return view('index');
    }
}
