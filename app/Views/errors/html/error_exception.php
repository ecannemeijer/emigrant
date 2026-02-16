<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Error') ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d9534f;
            margin-top: 0;
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #d9534f;
            margin: 0;
        }
        .error-message {
            font-size: 24px;
            margin: 10px 0 20px;
        }
        .trace {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            margin-top: 20px;
        }
        code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
        }
        a {
            color: #337ab7;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <p class="error-code"><?= esc($code ?? '500') ?></p>
        <h1><?= esc($title ?? 'Error') ?></h1>
        <p class="error-message"><?= esc($message ?? 'An error occurred') ?></p>
        
        <?php if (ENVIRONMENT === 'development' && isset($trace)): ?>
            <div class="trace">
                <h3>Stack Trace:</h3>
                <code><?= is_string($trace) ? esc($trace) : esc(print_r($trace, true)) ?></code>
            </div>
        <?php endif; ?>
        
        <p style="margin-top: 30px;">
            <a href="<?= site_url() ?>">&larr; Terug naar home</a>
        </p>
    </div>
</body>
</html>
