<div style="font-family: Arial, Helvetica, sans-serif; color: #222; line-height:1.5;">
    <h2 style="color:#003366;">Welkom bij Emigrant, <?= esc($username) ?>!</h2>

    <p>Je account is aangemaakt met het e-mailadres <strong><?= esc($email) ?></strong>.</p>

    <p>Een korte uitleg om snel van start te gaan:</p>
    <ul>
        <li>Vul bij <strong>Inkomsten</strong> je verwachte maandinkomsten in (salaris, uitkeringen, pensioen, etc.).</li>
        <li>Vul bij <strong>Uitgaven</strong> al je maandelijkse lasten in (energie, boodschappen, overige kosten).</li>
        <li>In het <strong>Dashboard</strong> zie je direct je resterend vermogen, maandelijkse netto en meerjarige projecties.</li>
    </ul>

    <p>Handige links:</p>
    <ul>
        <li><a href="<?= esc($loginUrl) ?>">Inloggen</a></li>
        <li><a href="<?= esc($profileUrl) ?>">Je profiel</a> — controleer leeftijd / emigratiedatum voor correcte prognoses</li>
    </ul>

    <p>Heb je vragen of hulp nodig? Antwoord op deze e-mail of contacteer <a href="mailto:<?= esc($supportEmail) ?>"><?= esc($supportEmail) ?></a>.</p>

    <p style="color:#666; font-size:0.9em;">Met vriendelijke groet,<br/>Het Emigrant team</p>
</div>