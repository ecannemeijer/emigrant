<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Google Agenda</title>
    <meta http-equiv="refresh" content="0;url=/renovation/planning">
</head>
<body>
    <p>Google Agenda wordt in een nieuw tabblad geopend. Deze pagina blijft in de app.</p>
    <p><a href="<?= esc($url, 'attr') ?>" target="_blank" rel="noopener noreferrer">Open Google Agenda</a> als het nieuwe tabblad geblokkeerd is.</p>
    <script>
        window.open(<?= json_encode($url, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>, '_blank', 'noopener,noreferrer');
        window.location.replace('/renovation/planning');
    </script>
</body>
</html>
