<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
{{-- rpatchur (web-view 0.7.3) renders with MSHTML on Windows; IE=edge forces IE11 mode.
     Everything below is intentionally IE11-safe: literal colours (no CSS vars / color-mix),
     margins instead of flex `gap`, no woff2 webfont, no CSS filter:blur. --}}
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- Sentinel "browser-logger-active": Laravel Boost's InjectBoost middleware skips
     pages already containing this string, preventing it from injecting its modern-JS
     console logger (const/WeakSet/arrow fns) that IE11 can't parse. Must be a real
     HTML comment (Blade {{-- --}} comments are stripped before output). -->
<title>XileRO Launcher</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body { width:100%; height:100%; overflow:hidden; background:#0c0b18; }
    body { font-family:'Segoe UI', Tahoma, Geneva, sans-serif; color:#eceaf7; user-select:none; -ms-user-select:none; }

    @keyframes xrShimmer { 0% { transform:translateX(-100%); } 100% { transform:translateX(220%); } }
    @keyframes xrPulse   { 0% { opacity:.5; } 50% { opacity:1; } 100% { opacity:.5; } }

    .xr-feed { overflow-y:auto; }
    .xr-feed::-webkit-scrollbar { width:7px; }
    .xr-feed::-webkit-scrollbar-track { background:transparent; }
    .xr-feed::-webkit-scrollbar-thumb { background:rgba(255,255,255,.12); border-radius:8px; }

    .shell { position:absolute; top:0; left:0; right:0; bottom:0; background:#16142b;
             border:1px solid rgba(255,255,255,.07); overflow:hidden; }

    /* Title bar */
    .titlebar { height:52px; background:#181631; border-bottom:1px solid rgba(255,255,255,.06);
                position:relative; z-index:6; }
    .tb-left { position:absolute; left:18px; top:0; height:52px; }
    .tb-right { position:absolute; right:14px; top:10px; }
    .logo { float:left; width:28px; height:28px; margin-top:12px; border-radius:8px; text-align:center;
            line-height:28px; font-weight:800; font-size:15px; color:#fff;
            background:linear-gradient(135deg,#8b7bf2,#d76ad0); box-shadow:0 4px 12px -2px rgba(139,123,242,.55); }
    .brand { float:left; margin:16px 0 0 11px; }
    .brand b { font-weight:800; letter-spacing:.5px; font-size:16px; }
    .brand span { font-weight:500; font-size:12px; color:#8e8cb0; margin-left:8px; }
    .ver { float:left; margin:17px 0 0 10px; font-size:11px; font-weight:600; color:#a8a4d8;
           background:rgba(255,255,255,.06); padding:2px 8px; border-radius:999px; }
    .winbtn { float:left; width:32px; height:32px; margin-left:6px; text-align:center; line-height:32px;
              border-radius:8px; color:#9a98bd; cursor:pointer; }
    .winbtn:hover { background:rgba(255,255,255,.08); color:#eceaf7; }
    .winbtn.close:hover { background:#cf4d6e; color:#fff; }

    /* Toolbar */
    .toolbar { height:58px; background:#161430; border-bottom:1px solid rgba(255,255,255,.05);
               position:relative; z-index:6; }
    .nav { position:absolute; left:14px; top:11px; }
    .nav a { float:left; height:36px; line-height:36px; padding:0 15px; border-radius:9px; color:#c4c2e0;
             font-weight:600; font-size:13.5px; cursor:pointer; text-decoration:none; }
    .nav a:hover { background:rgba(255,255,255,.06); color:#fff; }
    .status-pill { position:absolute; right:18px; top:20px; font-size:12.5px; font-weight:600; }
    .dot { display:inline-block; width:8px; height:8px; border-radius:50%; vertical-align:middle;
           margin-right:7px; }
    .srv-on { color:#46d18a; } .srv-on .dot { background:#46d18a; box-shadow:0 0 9px #46d18a; animation:xrPulse 2.4s infinite; }
    .sep { color:#3e3c5e; margin:0 11px; } .online { color:#9a98bd; }

    /* Stage */
    .stage { position:absolute; left:0; right:0; top:110px; bottom:120px; padding:22px; background:#14122a; z-index:5; }
    .feed-col { position:absolute; left:22px; top:22px; bottom:22px; right:370px; }
    .feed-head { font-size:18px; font-weight:800; color:#f3f1fb; }
    .feed-sub  { font-size:12.5px; color:#8e8cb0; margin-top:2px; }
    .viewall { float:right; font-size:12.5px; font-weight:700; color:#8b7bf2; cursor:pointer; }
    .xr-feed { position:absolute; left:0; right:-9px; top:48px; bottom:0; padding-right:9px; }
    .post { position:relative; padding:15px 17px; margin-bottom:11px; border-radius:14px;
            background:rgba(255,255,255,.025); border:1px solid rgba(255,255,255,.07); cursor:pointer; }
    .post:hover { border-color:rgba(255,255,255,.16); }
    .post.sel { background:rgba(255,255,255,.06); border-color:rgba(139,123,242,.45); }
    .tag { display:inline-block; font-size:10.5px; font-weight:700; letter-spacing:.4px; padding:3px 9px;
           border-radius:999px; }
    .tag.pinned { background:rgba(139,123,242,.18); color:#b3a6ff; }
    .tag.update { background:rgba(91,140,240,.18); color:#8fb2ff; }
    .tag.event  { background:rgba(215,106,208,.18); color:#f08ddf; }
    .post .date { font-size:11.5px; font-weight:500; color:#807ea4; margin-left:9px; }
    .post .title { font-weight:700; font-size:15px; color:#f1effb; margin:9px 0 5px; }
    .post .body  { font-size:13px; line-height:1.55; color:#a9a7cb; }

    /* Hero card */
    .hero { position:absolute; right:22px; top:22px; bottom:22px; width:330px; border-radius:16px;
            overflow:hidden; border:1px solid rgba(255,255,255,.08); background:#11102a; }
    .hero-bg { position:absolute; top:0; left:0; right:0; bottom:0;
        background:
          radial-gradient(120% 70% at 80% -12%, rgba(176,76,176,.42), transparent 54%),
          radial-gradient(120% 95% at 18% 120%, rgba(60,96,200,.42), transparent 55%),
          radial-gradient(165% 80% at 50% 150%, rgba(74,150,222,.5), transparent 52%),
          linear-gradient(180deg,#1a1842,#100e28); }
    .hero-dots { position:absolute; top:0; left:0; right:0; bottom:0; opacity:.6;
        background-image:radial-gradient(rgba(212,222,255,.16) 1px, transparent 1.5px); background-size:34px 34px; }
    .hero-planet { position:absolute; left:50%; bottom:-270px; margin-left:-270px; width:540px; height:540px;
        border-radius:50%; border-top:1.5px solid rgba(178,226,255,.42);
        background:radial-gradient(circle at 50% 26%, rgba(124,188,248,.32), rgba(24,42,96,.4) 52%, transparent 70%);
        box-shadow:inset 0 12px 50px rgba(150,210,255,.32), 0 -2px 46px rgba(120,200,255,.22); }
    /* <img> fills the hero. Modern engines crop via object-fit:cover; the rpatchur
       MSHTML/IE webview ignores object-fit and stretches to fill (width/height 100%),
       which still covers the container with no letterbox gap. */
    .hero-img { position:absolute; top:0; left:0; width:100%; height:100%;
        object-fit:cover; object-position:center; }
    .hero-cap { position:absolute; left:0; right:0; bottom:0; padding:18px 16px 16px;
        background:linear-gradient(180deg, transparent, rgba(9,8,22,.9) 58%); }
    .hero-badge { display:inline-block; font-size:10px; font-weight:800; letter-spacing:.6px; color:#fff;
        background:linear-gradient(135deg,#8b7bf2,#d76ad0); padding:3px 10px; border-radius:999px; margin-bottom:9px; }
    .hero-title { font-size:15.5px; font-weight:800; color:#f3f1fb; }
    .hero-sub { font-size:11.5px; font-weight:500; color:#9d9bc6; margin-top:3px; }

    /* Bottom bar */
    .bottom { position:absolute; left:0; right:0; bottom:0; height:120px; background:#181631;
              border-top:1px solid rgba(255,255,255,.06); padding:18px 20px; z-index:6; }
    .pwrap { position:absolute; left:20px; right:230px; top:24px; }
    .prow { position:relative; height:18px; margin-bottom:10px; }
    .sdot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:9px; vertical-align:middle;
            background:#8fb2ff; box-shadow:0 0 8px #8fb2ff; }
    .stext { font-weight:700; font-size:14px; color:#f1effb; }
    .itext { font-size:12.5px; color:#8e8cb0; margin-left:9px; }
    .pct { position:absolute; right:0; top:0; font-size:13px; font-weight:700; color:#8b7bf2; }
    .ptrack { height:9px; border-radius:999px; background:rgba(255,255,255,.07); overflow:hidden; position:relative; }
    .pfill { height:100%; width:0%; border-radius:999px; background:linear-gradient(90deg,#8b7bf2,#d76ad0);
             position:relative; overflow:hidden; }
    .pshine { position:absolute; top:0; bottom:0; width:40%; animation:xrShimmer 1.6s ease-in-out infinite;
              background:linear-gradient(90deg, transparent, rgba(255,255,255,.45), transparent); }

    .launch { position:absolute; right:20px; top:32px; height:56px; line-height:56px; padding:0 34px;
              border-radius:13px; font-weight:800; font-size:17px; letter-spacing:.4px;
              background:rgba(255,255,255,.06); color:#8a88b0; cursor:default; border:0; }
    .launch .ico { font-size:14px; margin-right:10px; }
    .launch.ready { color:#fff; cursor:pointer; background:linear-gradient(135deg,#8b7bf2,#d76ad0);
                    box-shadow:0 10px 26px -8px rgba(139,123,242,.6); }
    .launch.ready:hover { box-shadow:0 12px 30px -8px rgba(139,123,242,.8); }
</style>
</head>
<body>
<div class="shell">

    <!-- TITLE BAR -->
    <div class="titlebar">
        <div class="tb-left">
            <div class="logo">X</div>
            <div class="brand"><b>XILERO</b><span>Launcher</span></div>
            <div class="ver">{{ config('app.client_version', 'v5.1.0') }}</div>
        </div>
        <div class="tb-right">
            <div class="winbtn" title="Settings" onclick="rpc('setup')">&#9881;</div>
            <div class="winbtn" title="Minimize">&#8212;</div>
            <div class="winbtn close" title="Exit" onclick="rpc('exit')">&#10005;</div>
        </div>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
        <div class="nav">
            <a onclick="openUrl('https://xilero.net/#steps2play')">Register</a>
            <a onclick="openUrl('https://xilero.net')">Website</a>
            <a onclick="openUrl('https://discord.gg/xilero')">Discord</a>
            <a onclick="rpc('setup')">Settings</a>
        </div>
        <div class="status-pill">
            <span class="srv-on"><span class="dot"></span>Server online</span>
            <span class="sep">&middot;</span>
            <span class="online" id="playersOnline">&nbsp;</span>
        </div>
    </div>

    <!-- STAGE -->
    <div class="stage">
        <div class="feed-col">
            <div>
                <span class="viewall" onclick="openUrl('https://xilero.net/posts')">View all</span>
                <div class="feed-head">Latest Updates</div>
                <div class="feed-sub">News, patches &amp; announcements</div>
            </div>
            <div class="xr-feed">
                @forelse ($posts as $post)
                    <div class="post {{ $loop->first ? 'sel' : '' }}" onclick="selectPost(this)">
                        <span class="tag update">UPDATE</span>
                        <span class="date">{{ $post->created_at->format('M j, Y') }}</span>
                        <div class="title">{{ $post->title }}</div>
                        <div class="body">{{ Str::limit($post->patcher_notice ?: strip_tags($post->article_content), 120) }}</div>
                    </div>
                @empty
                    <div class="post sel">
                        <span class="tag update">UPDATE</span>
                        <span class="date">Always</span>
                        <div class="title">Always run your Patcher!</div>
                        <div class="body">Keep the launcher open until patching finishes — it pulls the latest client files so you never fall behind on events.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="hero">
            <div class="hero-bg"></div>
            <div class="hero-dots"></div>
            <div class="hero-planet"></div>
            @if ($posts->isNotEmpty() && $posts->first()->image)
                <img class="hero-img" src="{{ Storage::disk('public')->url($posts->first()->image) }}" alt="">
            @endif
            <div class="hero-cap">
                <div class="hero-badge">&#9733; LATEST UPDATE</div>
                @if ($posts->isNotEmpty())
                    <div class="hero-title">{{ $posts->first()->title }}</div>
                    <div class="hero-sub">{{ $posts->first()->created_at->format('F Y') }}</div>
                @else
                    <div class="hero-title">Welcome to XileRO</div>
                    <div class="hero-sub">Classic Ragnarok Online Private Server</div>
                @endif
            </div>
        </div>
    </div>

    <!-- BOTTOM: progress + launch -->
    <div class="bottom">
        <div class="pwrap">
            <div class="prow">
                <span class="sdot" id="statusDot"></span>
                <span class="stext" id="statusText">Checking for updates</span>
                <span class="itext" id="infoText">contacting patch server</span>
                <span class="pct" id="pct">0%</span>
            </div>
            <div class="ptrack"><div class="pfill" id="pfill"><div class="pshine"></div></div></div>
        </div>
        <button class="launch" id="launch" onclick="onLaunch()"><span class="ico" id="launchIco">&#8635;</span><span id="launchLabel">Checking…</span></button>
    </div>

</div>

<script>
    var ready = false;

    function el(id) { return document.getElementById(id); }

    /* rpatchur injects window.external.invoke into its webview; it is absent in a
       normal browser, so fall back to native behaviour for local testing. */
    function hasBridge() { return typeof external !== 'undefined' && external.invoke; }

    /* Plain-string commands. Only login/open_url use the JSON form in rpatchur. */
    function rpc(cmd) {
        if (hasBridge()) { external.invoke(cmd); }
        else { console.warn('rpatchur bridge unavailable for command: ' + cmd); }
    }
    function openUrl(u) {
        if (hasBridge()) { external.invoke(JSON.stringify({ function: 'open_url', parameters: { url: u } })); }
        else { window.open(u, '_blank'); }
    }

    function selectPost(node) {
        var posts = document.getElementsByClassName('post');
        for (var i = 0; i < posts.length; i++) { posts[i].className = posts[i].className.replace(' sel', ''); }
        node.className += ' sel';
    }

    function humanBytes(b) {
        b = Number(b) || 0;
        var u = ['B', 'KB', 'MB', 'GB'], i = 0;
        while (b >= 1024 && i < u.length - 1) { b = b / 1024; i++; }
        return b.toFixed(1) + ' ' + u[i];
    }

    function setProgress(pct) {
        pct = Math.max(0, Math.min(100, Math.round(pct)));
        el('pfill').style.width = pct + '%';
        el('pct').innerHTML = pct + '%';
    }
    function setDot(color) { el('statusDot').style.background = color; el('statusDot').style.boxShadow = '0 0 8px ' + color; }
    function setLaunch(label, icon, isReady) {
        el('launchLabel').innerHTML = label;
        el('launchIco').innerHTML = icon;
        el('launch').className = isReady ? 'launch ready' : 'launch';
        ready = isReady;
    }

    /* ---- rpatchur -> UI callbacks (signatures match rpatchur's web-view eval calls) ---- */
    function patchingStatusReady() {
        el('statusText').innerHTML = 'You’re up to date';
        el('infoText').innerHTML = 'all files verified';
        setDot('#46d18a'); setProgress(100);
        setLaunch('Start Game', '&#9654;', true);
    }
    function patchingStatusError(message) {
        el('statusText').innerHTML = 'Update error';
        el('infoText').innerHTML = message || 'unknown error';
        setDot('#e0688a');
        /* startup_option 3 equivalent: still let the player launch the client */
        setLaunch('Start Game', '&#9654;', true);
    }
    function patchingStatusDownloading(nbDownloaded, nbTotal, bytesPerSec) {
        el('statusText').innerHTML = 'Downloading updates';
        el('infoText').innerHTML = nbDownloaded + ' / ' + nbTotal + ' files · ' + humanBytes(bytesPerSec) + '/s';
        setDot('#8b7bf2');
        setProgress(nbTotal > 0 ? (nbDownloaded / nbTotal) * 100 : 0);
        setLaunch('Patching…', '&#8635;', false);
    }
    function patchingStatusInstalling(nbInstalled, nbTotal) {
        el('statusText').innerHTML = 'Installing updates';
        el('infoText').innerHTML = nbInstalled + ' / ' + nbTotal + ' applied';
        setDot('#8b7bf2');
        setProgress(nbTotal > 0 ? (nbInstalled / nbTotal) * 100 : 0);
        setLaunch('Patching…', '&#8635;', false);
    }
    function patchingStatusPatchApplied(fileName) {
        el('infoText').innerHTML = 'applied ' + fileName;
    }
    function notificationInProgress() {
        el('statusText').innerHTML = 'Patching already in progress…';
    }

    function onLaunch() { if (ready) { rpc('play'); } }

    window.onload = function () { external.invoke('start_update'); };
</script>
</body>
</html>
