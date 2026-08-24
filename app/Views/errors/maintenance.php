<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onderhoud — EmigreerItalia</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --green: #008C45; --red: #CD212A; --cream: #F7F1E8; --ink: #1B1B1B; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Source Sans 3", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                linear-gradient(90deg, #008C45 0 33.33%, #F4F5F0 33.33% 66.66%, #CD212A 66.66% 100%) top / 100% 6px no-repeat,
                var(--cream);
            padding: 24px;
        }
        .card {
            max-width: 480px;
            background: #fff;
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 12px 32px rgba(0, 80, 40, .12);
            text-align: center;
        }
        .flag {
            width: 64px;
            height: 42px;
            margin: 0 auto 20px;
            border-radius: 4px;
            background: linear-gradient(90deg, #008C45 0 33.3%, #fff 33.3% 66.6%, #CD212A 66.6%);
        }
        h1 { font-family: "Fraunces", Georgia, serif; margin: 0 0 8px; }
        .brand { color: var(--green); font-weight: 700; letter-spacing: .04em; margin-bottom: 16px; }
        p { color: #5C5854; line-height: 1.5; }
        a { color: var(--green); font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="flag" aria-hidden="true"></div>
        <div class="brand">EmigreerItalia</div>
        <h1>Even in onderhoud</h1>
        <p>We werken aan de site. Probeer het later opnieuw. Je gegevens blijven bewaard.</p>
        <?php if (!empty($showLogout)): ?>
            <form action="/logout" method="post">
                <?= csrf_field() ?>
                <button type="submit" style="background:#008C45;color:#fff;border:0;border-radius:8px;padding:10px 18px;font-weight:600;cursor:pointer;">Uitloggen</button>
            </form>
        <?php else: ?>
            <p><a href="/login">Beheerder? Inloggen</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
