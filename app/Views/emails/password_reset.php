<div style="font-family: Arial, Helvetica, sans-serif; color: #222; line-height:1.5;">
    <h2 style="color:#003366;">Wachtwoord resetten</h2>

    <p>Hallo <?= esc($username) ?>,</p>

    <p>Je hebt een verzoek ingediend om je wachtwoord te resetten. Klik op onderstaande knop om een nieuw wachtwoord in te stellen:</p>

    <p style="text-align: center; margin: 30px 0;">
        <a href="<?= esc($resetLink) ?>" 
           style="background-color: #003366; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
            Wachtwoord resetten
        </a>
    </p>

    <p>Of kopieer deze link naar je browser:</p>
    <p style="background: #f5f5f5; padding: 10px; word-break: break-all; font-size: 0.9em;">
        <?= esc($resetLink) ?>
    </p>

    <p><strong>Deze link is 1 uur geldig.</strong></p>

    <p style="color: #666; font-size: 0.9em;">Heb je geen wachtwoord reset aangevraagd? Negeer deze e-mail dan. Je wachtwoord blijft ongewijzigd.</p>

    <p style="color:#666; font-size:0.9em; margin-top: 30px;">Met vriendelijke groet,<br/>Het Emigrant team</p>
</div>
