<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>eProcurement System</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <?php if (!empty($cssFile)): ?>
    <link rel="stylesheet" href="<?= $cssFile ?>">
    <?php endif; ?>
</head>
<body>
    <div id="root">
        <!-- Loading state while React initializes -->
        <div style="display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;color:#666;">
            <div style="text-align:center;">
                <div style="width:40px;height:40px;border:4px solid #e2e8f0;border-top-color:#3b82f6;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 16px;"></div>
                <p>Memuat eProcurement System...</p>
            </div>
        </div>
        <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
    </div>

    <?php if (!empty($isDev)): ?>
    <script type="module">
        import RefreshRuntime from 'http://127.0.0.1:5173/@react-refresh'
        RefreshRuntime.injectIntoGlobalHook(window)
        window.$RefreshReg$ = () => {}
        window.$RefreshSig$ = () => (type) => type
        window.__vite_plugin_react_preamble_installed__ = true
    </script>
    <script type="module" src="http://127.0.0.1:5173/@vite/client"></script>
    <script type="module" src="<?= $jsFile ?>"></script>
    <?php elseif (!empty($jsFile)): ?>
    <script type="module" src="<?= $jsFile ?>"></script>
    <?php endif; ?>
</body>
</html>
