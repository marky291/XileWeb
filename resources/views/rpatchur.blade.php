<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>XileRO Patcher</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            user-select: none;
            overflow: hidden;
        }
        .header { text-align: center; padding: 30px 20px 20px; }
        .header h1 {
            font-size: 32px;
            font-weight: 300;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #e94560;
            text-shadow: 0 0 20px rgba(233, 69, 96, 0.5);
        }
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .status-container { width: 100%; max-width: 500px; margin-bottom: 30px; }
        .status-text {
            font-size: 14px;
            color: #a0a0a0;
            margin-bottom: 10px;
            text-align: center;
            min-height: 20px;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #e94560, #ff6b6b);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        .buttons { display: flex; gap: 15px; }
        .btn {
            padding: 12px 40px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-play {
            background: linear-gradient(135deg, #e94560, #ff6b6b);
            color: #fff;
        }
        .btn-play:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(233, 69, 96, 0.4);
        }
        .btn-play:disabled { background: #555; cursor: not-allowed; }
        .btn-setup {
            background: transparent;
            color: #a0a0a0;
            border: 1px solid #a0a0a0;
        }
        .btn-setup:hover { color: #fff; border-color: #fff; }
        .footer { text-align: center; padding: 15px; font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <div class="header"><h1>XileRO</h1></div>
    <div class="content">
        <div class="status-container">
            <div class="status-text" id="statusText">Initializing...</div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>
        <div class="buttons">
            <button class="btn btn-play" id="btnPlay" disabled onclick="playGame()">Play</button>
            <button class="btn btn-setup" onclick="openSetup()">Setup</button>
        </div>
    </div>
    <div class="footer">XileRO Patcher</div>

    <script>
        // Patcher callbacks - called by rpatchur
        function patchingStatusReady() {
            document.getElementById('statusText').innerText = 'Ready to play!';
            document.getElementById('progressFill').style.width = '100%';
            document.getElementById('btnPlay').disabled = false;
        }

        function patchingStatusError(message) {
            document.getElementById('statusText').innerText = 'Error: ' + (message || 'Unknown error');
            document.getElementById('btnPlay').disabled = true;
        }

        function patchingStatusDownloading(filename, current, total) {
            var percent = total > 0 ? Math.round((current / total) * 100) : 0;
            document.getElementById('statusText').innerText = 'Downloading: ' + filename + ' (' + percent + '%)';
            document.getElementById('progressFill').style.width = percent + '%';
        }

        function patchingStatusInstalling(current, total) {
            var percent = total > 0 ? Math.round((current / total) * 100) : 0;
            document.getElementById('statusText').innerText = 'Installing... (' + current + '/' + total + ')';
            document.getElementById('progressFill').style.width = percent + '%';
        }

        function patchingStatusPatchApplied(index, total, name) {
            var percent = total > 0 ? Math.round(((index + 1) / total) * 100) : 0;
            document.getElementById('statusText').innerText = 'Applied: ' + name;
            document.getElementById('progressFill').style.width = percent + '%';
        }

        // Actions - call rpatchur functions
        function playGame() {
            external.invoke(JSON.stringify({ function: 'play' }));
        }

        function openSetup() {
            external.invoke(JSON.stringify({ function: 'setup' }));
        }

        // Start patching when page loads
        window.onload = function() {
            document.getElementById('statusText').innerText = 'Checking for updates...';
            setTimeout(function() {
                external.invoke(JSON.stringify({ function: 'start_update' }));
            }, 500);
        };
    </script>
</body>
</html>
