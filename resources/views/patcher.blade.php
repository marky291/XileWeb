@php use Carbon\Carbon; @endphp

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
    <title>XileRetro Patcher</title>
</head>

<body>

<style>
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    body {
        margin-top: 0 !important;
        padding-top: 0 !important;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-color: #000000;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #A68CD7;
        background-position: center top;
        background-image: url('{{ asset('assets/patcher/background2.png') }}');
    }

    h5 {
        padding: 15px 0px;
        border-bottom: 1px solid #280243;
    }

    ul {
        padding-left: 0px;
        margin-left: 7px;
        margin-top: 10px;
        list-style: none;
    }

    a {
        color: #476BB1;
        text-decoration: none;
    }

    span {
        color: #366EB5;
        text-decoration: underline;
        margin-left: 10px;
        font-weight: normal;
    }

    li {
        margin-top: 5px;
        margin-bottom: 5px;
        font-weight: bold;
    }

    h4 {
        font-size: 12px;
        margin-bottom: 1px;
    }

    .month {
        border-bottom: 1px solid #280243;
    }

    .description {
        margin-left: 25px;
        color: #FFFFFF;
        font-weight: normal;
    }
</style>

<h5>Always run your Patcher! <3</h5>
<div>
    <div class="month">
        @foreach($groupedPosts as $date => $posts)
            <h4>{{ $date }}</h4>
            <ul>
                @foreach($posts as $post)
                    <li>
                        <a target="_blank" href="{{ route('posts.show', $post->slug) }}">
                            {{ Carbon::parse($post->created_at)->format('d') }} <span>{{ $post->title }}</span>
                            <div class="description">
                                <p>{{ $post->blurb }}</p>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
        <h4>August 2023</h4>
        <ul>
            <li>
                <a target="_blank" href="https://discord.com/channels/702319926110584943/1139609025701490960">
                    27 <span>Client Text & Patcher</span>
                    <div class="description">
                        <p>We are temporary bring back the purple xilero patcher until we have our new patcher design
                            ready, on top of that the text inside for client selection has been updated!</p>
                    </div>
                </a>
            </li>
            <li>
                <a target="_blank" href="https://discord.com/channels/702319926110584943/1139609025701490960">
                    19 <span>Rookie Badge & Arena</span>
                    <div class="description">
                        <p>Rookie badge sprite refreshed! Plus, the legendary 5 MFFA and 3vs3 arena modes are back!</p>
                    </div>
                </a>
            </li>
            <li>
                <a target="_blank" href="https://discord.com/channels/702319926110584943/1139609025701490960">
                    11 <span>GM Gojo Joins</span>
                    <div class="description">
                        <p>Welcome aboard, GM Goju! As our new Game Master</p>
                    </div>
                </a>
            </li>
            <li>
                <a target="_blank" href="https://wiki.xilero.net/index.php?title=2023-08-06_Update">
                    06 <span>August Update</span>
                </a>

            </li>
            <li>
                <a target="_blank" href="https://discord.com/channels/702319926110584943/1136883969686769724">
                    04 <span>August Attendance Update</span>
                </a>
            </li>
            <li>
                <a target="_blank" href="https://discord.com/channels/702319926110584943/1136027424610599022">
                    01 <span>BG & Discord Update</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="month">
        <h4>July 2023</h4>
        <ul>
            <li>
                <a target="_blank" href="https://wiki.xilero.net/index.php?title=2023-07-02_Update">
                    02 <span>July Update & Patch</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="month">
        <h4>June 2023</h4>
        <ul>
            <li>
                <a target="_blank" href="https://wiki.xilero.net/index.php?title=2023-07-02_Update">
                    30 <span>June Patch</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="month">
        <h4>April 2023</h4>
        <ul>
            <li>
                <a target="_blank" href="https://wiki.xilero.net/index.php?title=2023-04-07_Update">
                    07 <span>April Update</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="month">
        <h4>October 2022</h4>
        <ul>
            <li>
                <a target="_blank" href="https://wiki.xilero.net/index.php?title=2022-10-08_Update">
                    08 <span>Halloween Mini-Update</span>
                </a>
            </li>
            <li>
                <a target="_blank" href="https://wiki.xilero.net/index.php?title=2022-10-02_Hot_Fix">
                    02 <span>Hot Fix for Client-Side</span>
            </li>
            <li>
                <a target="_blank" href="https://wiki.xilero.net/index.php?title=2022-10-01_Update">
                    01 <span>Halloween Update!</span>
            </li>
        </ul>
    </div>
    <div class="month">
        <h4>July 2022</h4>
        <ul>
            <li>
                01 <span style="text-decoration:underline;">Attendance & Login Updated</span>
            </li>
        </ul>
    </div>
    <div class="month">
        <h4>March 2022</h4>
        <ul>
            <li>
                <a target="_blank" href="https://wiki.xilero.net/index.php?title=2022-03-18_Update">
                    18 <span>BG Update & New Costumes</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="month"></div>
    <h4>February 2022</h4>
    <ul>
        <li>
            <a target="_blank" href="https://wiki.xilero.net/index.php?title=2022-02-27_Update">
                27 <span>BG Crash Fixes Implemented</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://www.facebook.com/groups/XileRetro/permalink/2235260533305437/">
                26 <span>Website Improvements</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://www.facebook.com/groups/XileRetro/permalink/2234950516669772/">
                24 <span>Battleground Fixes</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://www.facebook.com/groups/XileRetro/permalink/2232747056890118/">
                23 <span>PVP Event</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://www.facebook.com/groups/XileRetro/permalink/2230849123746578/">
                21 <span>Website Redesign</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://wiki.xilero.net/index.php?title=2022-02-11_Update">
                11 <span>Hot Fix!</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://wiki.xilero.net/index.php?title=2022-02-07_Update">
                07 <span>Mini-Release!</span>
            </a>
        </li>
    </ul>
</div>
<div class="month"></div>
<h4>January 2022</h4>
<ul>
    <li>
        30 <span>Small Clientside Hotfix</span>
    </li>
    <li>
        <a target="_blank" href="https://forums.xileretro.net/index.php?/topic/114-28th-january-2022/">
            28 <span>January Update!</span>
        </a>
    </li>
    <li>
        <a target="_blank" href="https://www.facebook.com/groups/XileRetro/permalink/2204590753039082/">
            16 <span>Rookie Bade Sprite Change</span>
        </a>
    </li>
    <li>
        <a target="_blank" href="https://forums.xileretro.net/index.php?/topic/112-6th-january-2022/">
            06 <span>New Years Patch</span>
        </a>
    </li>
</ul>
</div>
<div class="month">
    <h4>December 2021</h4>
    <ul>
        <li>
            <a target="_blank" href="https://www.facebook.com/groups/XileRetro/permalink/2188844757947015/">
                26 <span>Community Feedback Poll</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://forums.xileretro.net/index.php?/topic/110-13th-december-2021/">
                13 <span>X-Mas Patch</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://forums.xileretro.net/index.php?/topic/108-2nd-december-2021/">
                02 <span>Small Update (X-Mas Soon!)</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>October 2021</h4>
    <ul>
        <li>
            <a target="_blank" href="https://forums.xileretro.net/index.php?/topic/105-27th-october-2021/">
                27 <span>Halloween Addition</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://forums.xileretro.net/index.php?/topic/103-hot-fix-1st-october-2021/">
                01 <span>Hot Fix</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>September 2021</h4>
    <ul>
        <li>
            <a target="_blank" href="https://forums.xileretro.net/index.php?/topic/102-29th-september-2021/">
                29 <span>Patcher Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-1">
                27 <span>Halloween Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>August 2021</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-3">
                26 <span>Monster Costumes & Kiki Revamp</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-2">
                04 <span>Attendance & Auras/Colornames</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>July 2021</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-3">
                26 <span>Monster Costumes & Kiki Revamp</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-4">
                10 <span>Leveling Content & Ubers Seller</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>June 2021</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-5">
                24 <span>Various fixes and changes</span>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://forums.xileretro.net/index.php?/news/3rd-june-2021/">
                23 <span>Battlegrounds Released</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-6">
                03 <span>Anniversary and BG Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>May 2021</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-7">
                10 <span>Elemental Bullets & Player Ladder</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>April 2021</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-8">
                30 <span>Monthlies & Costume Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>March 2021</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-9">
                15 <span>New Costume Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-10">
                08 <span>Battle Royale Auto Event!</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-11">
                01 <span>PUBG Style GM Event</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>February 2021</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-12">
                22 <span>Various WoE Changes</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-13">
                15 <span>Unique Accessories & Content</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-14">
                08 <span>Holiday and Biolab Content!</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-15">
                01 <span>Snow Removal Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>January 2021</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-16">
                09 <span>After Christmas Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>December 2020</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-17">
                19 <span>Christmas Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-18">
                06 <span>Weekly Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>November 2020</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-19">
                23 <span>WoE 2.0 Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-20">
                10 <span>Bi-Weekly Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>October 2020</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-21">
                27 <span>Bi-Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-22">
                13 <span>Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-23">
                02 <span> Weekly Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>September 2020</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-24">
                22 <span>Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-25">
                11 <span>Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-26">
                04 <span> Weekly Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>August 2020</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-27">
                17 <span>Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-28">
                07 <span> 2-Step Weekly Update - Part 2</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>July 2020</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-29">
                24 <span>Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-30">
                13 <span>2-Step Weekly Update - Part 1</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-31">
                03 <span>Weekly Update</span>
            </a>
        </li>
    </ul>
</div>
<div class="month">
    <h4>June 2020</h4>
    <ul>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-32">
                26 <span>Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-33">
                20 <span>Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-34">
                13 <span>Weekly Update</span>
            </a>
        </li>
        <li>
            <a target="_blank"
               href="https://wiki.xilero.net/index.php?title=Updates_Changelog&fbclid=IwAR2F9bzjEltJ5rFj2brduv33ytC3FIsRuao9xTgoDw4dId0CmhaoVO6Gd9M#cite_note-35">
                05 <span>First update after launch!</span>
            </a>
        </li>
    </ul>
</div>
</div>
</body>
</html>
