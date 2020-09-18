@extends('layouts.app')

@section('content')

    <div class="container py-10 mx-auto">
        <h2>Your Ragnarok Accounts</h1>
        <h3>Manage your in game accounts and characters!</h2>

        <table class="table-auto">
            <thead>
                <tr>
                <th class="px-4 py-2">Account Name</th>
                <th class="px-4 py-2">Total Characters</th>
                <th class="px-4 py-2">Last Logged In</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td class="px-4 py-2 border">Intro to CSS</td>
                <td class="px-4 py-2 border">Adam</td>
                <td class="px-4 py-2 border">858</td>
                </tr>
                <tr class="bg-gray-100">
                <td class="px-4 py-2 border">A Long and Winding Tour</td>
                <td class="px-4 py-2 border">Adam</td>
                <td class="px-4 py-2 border">112</td>
                </tr>
                <tr>
                <td class="px-4 py-2 border">Intro to JavaScript</td>
                <td class="px-4 py-2 border">Chris</td>
                <td class="px-4 py-2 border">1,280</td>
                </tr>
            </tbody>
        </table>

        <button class="btn btn-primary">Create a new account</button>
    </div>

@endsection
