<div style="font-family: Arial, Helvetica, sans-serif; color: #222; line-height:1.5;">
    <h2 style="color:#003366;">Nieuw contactformulier bericht</h2>

    <p>Je hebt een nieuw bericht ontvangen via het Emigrant contactformulier:</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr style="background: #f5f5f5;">
            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Naam:</td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?= esc($name) ?></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">E-mail:</td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?= esc($email) ?></td>
        </tr>
        <tr style="background: #f5f5f5;">
            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Onderwerp:</td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?= esc($subject) ?></td>
        </tr>
    </table>

    <h3 style="color:#003366;">Bericht:</h3>
    <div style="background: #f9f9f9; border-left: 4px solid #003366; padding: 15px; margin: 20px 0;">
        <?= nl2br(esc($userMessage)) ?>
    </div>

    <p style="color:#666; font-size:0.9em; margin-top: 30px;">
        Je kunt direct antwoorden op dit e-mailadres: <strong><?= esc($email) ?></strong>
    </p>
</div>
