<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1><i class="bi bi-question-circle"></i> Help & Documentatie</h1>
    <p class="text-muted">Uitgebreide handleiding voor het gebruik van de Emigrant Calculator</p>
</div>

<!-- Search Box -->
<div class="card mb-4">
    <div class="card-body">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="helpSearch" class="form-control" placeholder="Zoek in de help documentatie...">
        </div>
        <small class="text-muted">Typ een zoekterm om direct naar relevante secties te gaan</small>
    </div>
</div>

<!-- Table of Contents -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list"></i> Inhoudsopgave</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="#getting-started" class="list-group-item list-group-item-action">Aan de slag</a>
                <a href="#profile" class="list-group-item list-group-item-action">Profiel instellen</a>
                <a href="#start-position" class="list-group-item list-group-item-action">Startpositie Nederland</a>
                <a href="#income" class="list-group-item list-group-item-action">Inkomsten</a>
                <a href="#expenses" class="list-group-item list-group-item-action">Uitgaven</a>
                <a href="#properties" class="list-group-item list-group-item-action">Italiaans Vastgoed</a>
                <a href="#taxes" class="list-group-item list-group-item-action">Belastingen</a>
                <a href="#bnb" class="list-group-item list-group-item-action">B&B Module</a>
                <a href="#dashboard" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="#faq" class="list-group-item list-group-item-action">Veelgestelde Vragen</a>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div id="helpContent">
            <!-- Getting Started -->
            <div class="help-section" id="getting-started" data-keywords="beginnen starten eerste stap nieuw account registreren aanmelden">
                <h2><i class="bi bi-play-circle"></i> Aan de slag</h2>
                <p>Welkom bij de Emigrant Calculator! Deze tool helpt je om je financiële situatie na emigratie naar Italië door te rekenen.</p>
                
                <h4>Eerste stappen</h4>
                <ol>
                    <li><strong>Maak een account aan</strong> — Registreer via de registratiepagina</li>
                    <li><strong>Vul je profiel in</strong> — Start met je geboortedatum en emigratiedatum (zeer belangrijk!)</li>
                    <li><strong>Vul de modules in volgorde in</strong>:
                        <ul>
                            <li>Startpositie Nederland (je huidige vermogen)</li>
                            <li>Inkomsten (alle inkomstenbronnen)</li>
                            <li>Uitgaven (maandelijkse lasten)</li>
                            <li>Properties (Italiaanse woningen)</li>
                            <li>Belastingen (TARI, IMU, etc.)</li>
                        </ul>
                    </li>
                    <li><strong>Bekijk het Dashboard</strong> — Zie je complete financiële overzicht en meerjarige projecties</li>
                </ol>

                <div class="alert alert-info">
                    <strong>Tip:</strong> Begin met realistische schattingen. Je kunt alle gegevens later aanpassen.
                </div>
            </div>

            <hr class="my-5">

            <!-- Profile -->
            <div class="help-section" id="profile" data-keywords="profiel geboortedatum leeftijd emigratie emigratiedatum partner pensioen pensioendatum">
                <h2><i class="bi bi-person-circle"></i> Profiel instellen</h2>
                <p>Je profiel bevat essentiële persoonlijke gegevens die gebruikt worden voor alle berekeningen.</p>

                <h4>Belangrijke velden</h4>
                <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Veld</th>
                            <th>Beschrijving</th>
                            <th>Impact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Geboortedatum</strong></td>
                            <td>Je eigen geboortedatum</td>
                            <td>Gebruikt voor leeftijdsberekeningen en pensioendatum</td>
                        </tr>
                        <tr>
                            <td><strong>Emigratiedatum</strong></td>
                            <td>Wanneer je naar Italië verhuist</td>
                            <td>Bepaalt AOW-reductie percentage (vroege emigratie = lagere AOW)</td>
                        </tr>
                        <tr>
                            <td><strong>Pensioenleeftijd</strong></td>
                            <td>Je verwachte pensioenleeftijd (standaard 67)</td>
                            <td>Start van pensioenuitkering en AOW</td>
                        </tr>
                        <tr>
                            <td><strong>Partner gegevens</strong></td>
                            <td>Geboortedatum en pensioenleeftijd partner</td>
                            <td>Voor partner AOW berekeningen en gezamenlijke projecties</td>
                        </tr>
                    </tbody>
                </table>
                </div>

                <div class="alert alert-warning">
                    <strong>Let op:</strong> Emigratiedatum vóór je 67e verjaardag resulteert in lagere AOW-uitkering!
                </div>
            </div>

            <hr class="my-5">

            <!-- Start Position -->
            <div class="help-section" id="start-position" data-keywords="startpositie vermogen woning verkoop hypotheek spaargeld overwaarde nederland">
                <h2><i class="bi bi-house-door"></i> Startpositie Nederland</h2>
                <p>Bereken je netto vermogen bij vertrek uit Nederland.</p>

                <h4>Velden uitleg</h4>
                <ul>
                    <li><strong>Verkoopprijs woning</strong> — Verwachte verkoopprijs van je Nederlandse woning</li>
                    <li><strong>Hypotheekrestschuld</strong> — Hoeveel je nog moet aflossen op je hypotheek</li>
                    <li><strong>Spaargeld</strong> — Je huidige spaargeld en beleggingen</li>
                    <li><strong>Rentevoet</strong> — Interest die je vermogen oplevert (standaard 2%)</li>
                </ul>

                <h4>Automatische berekeningen</h4>
                <p>Het systeem berekent automatisch:</p>
                <ul>
                    <li><strong>Netto overwaarde</strong> = Verkoopprijs - Hypotheekrestschuld</li>
                    <li><strong>Totaal startvermogen</strong> = Netto overwaarde + Spaargeld</li>
                </ul>

                <div class="alert alert-info">
                    <strong>Tip:</strong> De rentevoet wordt gebruikt in meerjarige projecties om interest op je vermogen te berekenen.
                </div>
            </div>

            <hr class="my-5">

            <!-- Income -->
            <div class="help-section" id="income" data-keywords="inkomen inkomsten wia AOW pensioen salaris uitkering maandinkomen">
                <h2><i class="bi bi-cash-coin"></i> Inkomsten</h2>
                <p>Vul al je maandelijkse inkomstenbronnen in (netto bedragen).</p>

                <h4>Inkomstensoorten</h4>
                <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Inkomstenbron</th>
                            <th>Omschrijving</th>
                            <th>Wanneer actief</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>WIA (partner)</strong></td>
                            <td>WIA-uitkering van je partner</td>
                            <td>Tot partner de pensioenleeftijd bereikt</td>
                        </tr>
                        <tr>
                            <td><strong>Eigen inkomen</strong></td>
                            <td>Salaris, freelance, etc.</td>
                            <td>Altijd actief</td>
                        </tr>
                        <tr>
                            <td><strong>AOW partner (toekomstig)</strong></td>
                            <td>AOW-uitkering partner na pensioen</td>
                            <td>Start op partner's pensioenleeftijd (vervangt WIA)</td>
                        </tr>
                        <tr>
                            <td><strong>Eigen AOW</strong></td>
                            <td>Je eigen AOW-uitkering</td>
                            <td>Start op jouw pensioenleeftijd</td>
                        </tr>
                        <tr>
                            <td><strong>Pensioen</strong></td>
                            <td>Je pensioenuitkering</td>
                            <td>Start op jouw pensioenleeftijd</td>
                        </tr>
                        <tr>
                            <td><strong>Overig inkomen</strong></td>
                            <td>Andere vaste inkomsten</td>
                            <td>Altijd actief</td>
                        </tr>
                    </tbody>
                </table>
                </div>

                <div class="alert alert-warning">
                    <strong>AOW-reductie:</strong> Als je vóór je 67e emigreert, wordt je AOW-uitkering gekort. Hoe eerder je emigreert, hoe lager de uitkering!
                </div>

                <h4>Voorbeeld berekening</h4>
                <p>Partner 60 jaar, emigreert op 62, pensioen op 67:</p>
                <ul>
                    <li>Opgebouwd in NL: 62 jaar (vanaf 18) = 44 jaar</li>
                    <li>Totaal mogelijk: 67 jaar (vanaf 18) = 49 jaar</li>
                    <li>AOW-percentage: 44/49 = <strong>89,8%</strong></li>
                </ul>
            </div>

            <hr class="my-5">

            <!-- Expenses -->
            <div class="help-section" id="expenses" data-keywords="uitgaven kosten lasten maandlasten energie water internet boodschappen auto">
                <h2><i class="bi bi-wallet2"></i> Uitgaven</h2>
                <p>Vul al je vaste maandelijkse lasten in.</p>

                <h4>Categorieën</h4>
                <ul>
                    <li><strong>Energie</strong> — Elektriciteit en gas (hoofdwoning)</li>
                    <li><strong>Water</strong> — Waterverbruik (hoofdwoning)</li>
                    <li><strong>Internet</strong> — Internetabonnement</li>
                    <li><strong>Zorgverzekering</strong> — Maandelijkse premie Italiaanse zorgverzekering</li>
                    <li><strong>Auto verzekering</strong> — Autoverzekering kosten per maand</li>
                    <li><strong>Brandstof</strong> — Gemiddelde brandstofkosten per maand</li>
                    <li><strong>Auto onderhoud</strong> — Reserve voor onderhoud/reparaties</li>
                    <li><strong>Boodschappen</strong> — Supermarkt en dagelijkse boodschappen</li>
                    <li><strong>Vrije tijd</strong> — Uitjes, hobby's, restaurants</li>
                    <li><strong>Onvoorzien</strong> — Buffer voor onverwachte kosten</li>
                    <li><strong>Overige kosten</strong> — Alle andere vaste lasten</li>
                </ul>

                <div class="alert alert-info">
                    <strong>Tip:</strong> Gebruik "Overige kosten" om je gewenste uitgavenpatroon in te stellen. Als je netto negatief wordt, trekt het systeem automatisch van je vermogen af!
                </div>
            </div>

            <hr class="my-5">

            <!-- Properties -->
            <div class="help-section" id="properties" data-keywords="vastgoed woning huis hoofdwoning tweede property aankoopprijs aankoopkosten onderhoud">
                <h2><i class="bi bi-building"></i> Italiaans Vastgoed</h2>
                <p>Vul de gegevens in van je Italiaanse woning(en).</p>

                <h4>Hoofdwoning</h4>
                <ul>
                    <li><strong>Aankoopprijs</strong> — Prijs van de woning</li>
                    <li><strong>Aankoopkosten %</strong> — Notariskosten, belastingen (standaard 10%)</li>
                    <li><strong>Jaarlijkse vaste lasten</strong> — Vaste kosten (excl. energie/water die bij Uitgaven staan)</li>
                    <li><strong>Jaarlijks onderhoud</strong> — Reserve voor onderhoud en reparaties</li>
                </ul>

                <h4>Tweede woning (optioneel)</h4>
                <p>Als je een tweede woning hebt (bijv. voor verhuur of B&B), vul dan ook deze gegevens in:</p>
                <ul>
                    <li><strong>Aankoopprijs & kosten</strong> — Zelfde als hoofdwoning</li>
                    <li><strong>Maandelijkse energie</strong> — Energiekosten tweede woning</li>
                    <li><strong>Overige maandkosten</strong> — Andere lopende kosten</li>
                    <li><strong>IMU belasting</strong> — Jaarlijkse vastgoedbelasting</li>
                    <li><strong>TARI jaarlijks</strong> — Afvalstoffenheffing</li>
                </ul>

                <div class="alert alert-warning">
                    <strong>Let op:</strong> De totale aankoopkosten (prijs + kosten) worden automatisch afgetrokken van je startvermogen!
                </div>
            </div>

            <hr class="my-5">

            <!-- Taxes -->
            <div class="help-section" id="taxes" data-keywords="belastingen forfettario imu tari sociale bijdrage motorrijtuigenbelasting">
                <h2><i class="bi bi-receipt"></i> Belastingen</h2>
                <p>Italiaanse belastingen en heffingen.</p>

                <h4>Forfettario regeling</h4>
                <p>Een voordelige belastingregeling voor ZZP'ers / kleine ondernemers in Italië:</p>
                <ul>
                    <li><strong>Forfettario ingeschakeld</strong> — Gebruik je deze regeling?</li>
                    <li><strong>Forfettario percentage</strong> — Standaard 15% over omzet</li>
                    <li><strong>Normaal belastingtarief</strong> — Voor als je géén Forfettario hebt (standaard 23%)</li>
                </ul>

                <h4>Overige belastingen</h4>
                <ul>
                    <li><strong>IMU percentage</strong> — Vastgoedbelasting (ca. 0.76% van kadastrale waarde)</li>
                    <li><strong>TARI jaarlijks</strong> — Afvalstoffenheffing hoofdwoning</li>
                    <li><strong>Sociale bijdragen</strong> — Maandelijkse INPS bijdragen</li>
                    <li><strong>Motorrijtuigenbelasting</strong> — Jaarlijkse autobelasting</li>
                </ul>

                <div class="alert alert-info">
                    <strong>Tip:</strong> Raadpleeg altijd een Italiaanse accountant voor actuele belastingtarieven en regelgeving!
                </div>
            </div>

            <hr class="my-5">

            <!-- B&B Module -->
            <div class="help-section" id="bnb" data-keywords="bed breakfast bnb verhuur bezetting seizoen omzet kosten">
                <h2><i class="bi bi-shop"></i> B&B Module</h2>
                <p>Reken je B&B-inkomsten en kosten door.</p>

                <h4>Bezettingsgraad</h4>
                <ul>
                    <li><strong>Aantal kamers</strong> — Hoeveel kamers verhuur je?</li>
                    <li><strong>Prijs per nacht</strong> — Gemiddelde prijs per kamer per nacht</li>
                    <li><strong>Hoogseizoen</strong> — Maanden en bezettingspercentage (bijv. juni-september, 80%)</li>
                    <li><strong>Laagseizoen</strong> — Overige maanden en bezettingspercentage (bijv. 30%)</li>
                </ul>

                <h4>Kosten</h4>
                <p>Het systeem berekent automatisch kosten op basis van je omzet:</p>
                <ul>
                    <li><strong>Schoonmaak</strong> — % van omzet</li>
                    <li><strong>Linnengoed</strong> — % van omzet</li>
                    <li><strong>Ontbijt</strong> — % van omzet</li>
                    <li><strong>Marketing</strong> — % van omzet (Booking.com, Airbnb commissies)</li>
                    <li><strong>Overige kosten</strong> — % van omzet</li>
                </ul>

                <h4>Break-even analyse</h4>
                <p>Het dashboard toont automatisch:</p>
                <ul>
                    <li>Minimale bezettingsgraad om break-even te draaien</li>
                    <li>Netto inkomen met/zonder B&B</li>
                </ul>

                <div class="alert alert-success">
                    <strong>Handig:</strong> Experimenteer met verschillende bezettingspercentages om realistische scenario's door te rekenen!
                </div>
            </div>

            <hr class="my-5">

            <!-- Dashboard -->
            <div class="help-section" id="dashboard" data-keywords="dashboard overzicht grafiek projectie tabel vermogen kapitaal resterend netto">
                <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
                <p>Het dashboard toont je complete financiële overzicht.</p>

                <h4>Statistieken (bovenkant)</h4>
                <ul>
                    <li><strong>Resterend Vermogen</strong> — Je kapitaal na aankoop woningen</li>
                    <li><strong>Maandinkomen</strong> — Totaal maandelijks inkomen (incl. B&B, interest)</li>
                    <li><strong>Maandkosten</strong> — Totaal uitgaven + belastingen</li>
                    <li><strong>Netto Maandelijks</strong> — Inkomen - Kosten (positief = vermogen groeit, negatief = vermogen daalt)</li>
                </ul>

                <h4>Grafieken</h4>
                <ul>
                    <li><strong>Inkomen vs. Uitgaven</strong> — Visueel overzicht maandelijkse cash flow</li>
                    <li><strong>Vermogen over tijd</strong> — 15+ jaar projectie van je kapitaal</li>
                </ul>

                <h4>Meerjarige projectie tabel</h4>
                <p>De tabel toont jaar-voor-jaar:</p>
                <ul>
                    <li>Je leeftijd en die van je partner</li>
                    <li>Maandelijks inkomen (incl. AOW/pensioen transitions)</li>
                    <li>Maandelijkse kosten en belastingen</li>
                    <li>Netto per maand en per jaar</li>
                    <li><strong>Vermogen</strong> — Hoe je kapitaal zich ontwikkelt</li>
                    <li>Status (actief, gepensioneerd, AOW-reductie)</li>
                </ul>

                <div class="alert alert-info">
                    <strong>Klik op een rij</strong> in de projectie tabel voor een gedetailleerde breakdown van dat specifieke jaar!
                </div>

                <h4>Waarschuwingen</h4>
                <p>Het dashboard waarschuwt je bij:</p>
                <ul>
                    <li><strong>Negatief netto</strong> — Je geeft meer uit dan je verdient (trekt van vermogen af)</li>
                    <li><strong>AOW-reductie</strong> — Als emigratie vóór pensioen leidt tot lagere AOW</li>
                    <li><strong>Incompleet profiel</strong> — Als essentiële profiel velden ontbreken</li>
                </ul>
            </div>

            <hr class="my-5">

            <!-- FAQ -->
            <div class="help-section" id="faq" data-keywords="faq veelgesteld vraag antwoord probleem help">
                <h2><i class="bi bi-patch-question"></i> Veelgestelde Vragen</h2>

                <h4>Hoe werkt de AOW-reductie?</h4>
                <p>AOW (buitenland) wordt opgebouwd vanaf je 18e tot je pensioenleeftijd. Als je vóór je pensioen emigreert, bouw je minder jaren op in Nederland, dus krijg je een lager percentage.</p>
                <p><strong>Voorbeeld:</strong> Emigratie op 60, pensioen op 67 → je bouwt 42 jaar op (van 18 tot 60) van de 49 jaar mogelijk (18-67) = 85,7% AOW.</p>

                <h4>Waarom is mijn vermogen negatief?</h4>
                <p>Dit kan gebeuren als de aankoopprijs van je woning(en) + aankoopkosten hoger is dan je startvermogen. Check je Startpositie Nederland en vastgoedprijzen.</p>

                <h4>Hoe werkt de kapitaalafname bij negatief netto?</h4>
                <p>Als je meer uitgeeft dan je verdient (netto per maand < 0), trekt het systeem automatisch dat bedrag maandelijks van je vermogen af. Je ziet dit terug in de meerjarige projecties.</p>

                <h4>Kan ik meerdere scenario's maken?</h4>
                <p>Momenteel is er één actief scenario. Je kunt wel al je gegevens handmatig aanpassen en de effecten direct zien in het dashboard.</p>

                <h4>Wat als ik geen B&B start?</h4>
                <p>Laat de B&B module uitgeschakeld (checkbox "B&B actief" uit). Het dashboard toont dan automatisch scenario's met/zonder B&B voor vergelijking.</p>

                <h4>Hoe vaak wordt interest berekend?</h4>
                <p>Interest op je vermogen wordt jaarlijks berekend in de projecties. De rentevoet stel je in bij "Startpositie Nederland".</p>

                <h4>Zijn de belastingberekeningen accuraat?</h4>
                <p>De calculator gebruikt standaard percentages en regelingen. <strong>Raadpleeg altijd een erkend fiscalist</strong> voor officieel belastingadvies!</p>

                <h4>Kan ik mijn data exporteren?</h4>
                <p>Ja, via het dashboard kun je je financiële overzicht exporteren naar CSV voor gebruik in Excel of voor je adviseur.</p>

                <h4>Hoe veilig is mijn data?</h4>
                <p>Je data wordt veilig opgeslagen met password hashing, CSRF-bescherming en SQL injection preventie. Alleen jij hebt toegang tot jouw financiële gegevens.</p>

                <h4>Ik vergeet mijn wachtwoord, wat nu?</h4>
                <p>Gebruik de "Wachtwoord vergeten" link op de inlogpagina. Je ontvangt een reset-link per email.</p>
            </div>

            <hr class="my-5">

            <div class="text-center text-muted mb-5">
                <p>Heb je nog vragen die hier niet beantwoord worden?</p>
                <a href="/contact" class="btn btn-outline-primary">
                    <i class="bi bi-envelope"></i> Neem contact op
                </a>
            </div>
        </div>

        <div id="noResults" class="alert alert-warning" style="display: none;">
            <i class="bi bi-exclamation-triangle"></i> Geen resultaten gevonden voor je zoekopdracht.
        </div>
    </div>
</div>

<script>
// Help search functionality
document.getElementById('helpSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    const sections = document.querySelectorAll('.help-section');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;

    if (searchTerm === '') {
        // Show all sections
        sections.forEach(section => {
            section.style.display = 'block';
        });
        noResults.style.display = 'none';
        return;
    }

    sections.forEach(section => {
        const text = section.textContent.toLowerCase();
        const keywords = section.dataset.keywords || '';
        
        if (text.includes(searchTerm) || keywords.includes(searchTerm)) {
            section.style.display = 'block';
            visibleCount++;
        } else {
            section.style.display = 'none';
        }
    });

    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
});

// Smooth scroll for TOC links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>

<?= $this->endSection() ?>
