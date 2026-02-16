<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 100px auto;
            background: white;
            padding: 50px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #d9534f;
            margin: 0;
            line-height: 1;
        }
        h1 {
            color: #333;
            margin: 20px 0 10px;
            font-size: 28px;
        }
        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #337ab7;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        a:hover {
            background: #286090;
        }
    </style>
</head>
<body>
    <div class="container">
        <p class="error-code">404</p>
        <h1>Pagina Niet Gevonden</h1>
        <p>De pagina die je zoekt bestaat niet of is verplaatst.</p>
        <a href="<?= site_url() ?>">Terug naar Home</a>
    </div>
</body>
</html>
