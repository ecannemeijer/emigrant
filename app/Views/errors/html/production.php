<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Error</title>
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
        .error-message {
            background: #fff5f5;
            border-left: 4px solid #d9534f;
            padding: 15px;
            margin: 20px 0;
        }
        code {
            background: #f8f8f8;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', Courier, monospace;
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
        <h1>Database Error</h1>
        <div class="error-message">
            <p><?= esc($message ?? 'A database error occurred') ?></p>
            <?php if (ENVIRONMENT === 'development' && isset($sql)): ?>
                <p><strong>SQL Query:</strong></p>
                <code><?= esc($sql) ?></code>
            <?php endif; ?>
        </div>
        
        <p>Er is een probleem opgetreden bij het ophalen van gegevens uit de database.</p>
        
        <?php if (ENVIRONMENT === 'development'): ?>
            <p><strong>Tips voor ontwikkelaars:</strong></p>
            <ul>
                <li>Controleer of de database draait</li>
                <li>Verifieer de database credentials in .env</li>
                <li>Zorg dat de tabellen bestaan (run php spark migrate)</li>
            </ul>
        <?php endif; ?>
        
        <p style="margin-top: 30px;">
            <a href="<?= site_url() ?>">&larr; Terug naar home</a>
        </p>
    </div>
</body>
</html>
