<?php

namespace App\Libraries;

/**
 * Uitleg per emigratie-checklistpunt: hoe, waar, en waar je op let.
 * Geen juridisch of fiscaal advies; controleer altijd de actuele regels.
 */
class ChecklistGuide
{
    public static function catalog(): array
    {
        return [
            [
                'item_key' => 'svb_aow',
                'category' => 'Nederland',
                'title' => 'SVB informeren / AOW-opbouw of vrijwillige verzekering',
                'sort_order' => 10,
            ],
            [
                'item_key' => 'zorg_nl',
                'category' => 'Nederland',
                'title' => 'Zorgverzekering NL stopzetten of voortzetten',
                'sort_order' => 20,
            ],
            [
                'item_key' => 'belasting_nl',
                'category' => 'Nederland',
                'title' => 'Belastingdienst: emigratieformulier M-formulier / C-formulier',
                'sort_order' => 30,
            ],
            [
                'item_key' => 'gemeente_nl',
                'category' => 'Nederland',
                'title' => 'Uitschrijven BRP bij gemeente',
                'sort_order' => 40,
            ],
            [
                'item_key' => 'uwv_wia',
                'category' => 'Nederland',
                'title' => 'UWV: WIA of andere uitkering in het buitenland',
                'sort_order' => 45,
            ],
            [
                'item_key' => 'codice_fiscale',
                'category' => 'Italië',
                'title' => 'Codice fiscale aanvragen',
                'sort_order' => 50,
            ],
            [
                'item_key' => 'anagrafe',
                'category' => 'Italië',
                'title' => 'Inschrijving anagrafe / residenza',
                'sort_order' => 60,
            ],
            [
                'item_key' => 'fiscale_it',
                'category' => 'Italië',
                'title' => 'Fiscale residentie Italië (Agenzia delle Entrate)',
                'sort_order' => 65,
            ],
            [
                'item_key' => 'aire',
                'category' => 'Italië',
                'title' => 'AIRE-registratie (alleen bij Italiaans staatsburgerschap)',
                'sort_order' => 70,
            ],
            [
                'item_key' => 'ssn',
                'category' => 'Italië',
                'title' => 'Tessera sanitaria / SSN-inschrijving',
                'sort_order' => 80,
            ],
            [
                'item_key' => 'bank_it',
                'category' => 'Italië',
                'title' => 'Italiaanse bankrekening',
                'sort_order' => 90,
            ],
            [
                'item_key' => 'rijbewijs',
                'category' => 'Vervoer',
                'title' => 'Rijbewijs omwisselen of EU-rijbewijs geldig houden',
                'sort_order' => 100,
            ],
            [
                'item_key' => 'auto_bollo',
                'category' => 'Vervoer',
                'title' => 'Auto importeren / bollo auto',
                'sort_order' => 110,
            ],
            [
                'item_key' => 'partita_iva',
                'category' => 'Ondernemen',
                'title' => 'Partita IVA / forfettario (indien B&B)',
                'sort_order' => 120,
            ],
        ];
    }

    public static function guide(string $itemKey): array
    {
        $all = self::guides();

        return $all[$itemKey] ?? [
            'summary' => 'Nog geen extra toelichting.',
            'how' => [],
            'where' => [],
            'watch' => [],
            'links' => [],
        ];
    }

    public static function guides(): array
    {
        return [
            'svb_aow' => [
                'summary' => 'Na emigratie stopt de verplichte AOW-verzekering meestal. Elk ontbrekend jaar in de 50 jaar vóór AOW-leeftijd kost 2% AOW.',
                'how' => [
                    'Geef je vertrek door aan de SVB zodra je woonplaats Italië vaststaat.',
                    'Vraag een overzicht van je opgebouwde AOW-jaren (verzekeringsoverzicht).',
                    'Overweeg vrijwillige AOW-verzekering als je nog jaren tot AOW-leeftijd hebt. Aanvragen kan vaak tot 1 jaar na het einde van de verplichte verzekering; daarna is instappen meestal niet meer mogelijk.',
                    'Vrijwillige premie is inkomensafhankelijk; reken dit af tegen de extra 2% AOW per verzekerd jaar.',
                ],
                'where' => [
                    'SVB: svb.nl (onderwerp wonen in het buitenland / AOW).',
                    'Mijn SVB of schriftelijk; bewaar bevestigingen.',
                ],
                'watch' => [
                    'Partner heeft een eigen AOW-opbouw; regel dit per persoon.',
                    'WIA stopt niet automatisch bij emigratie, AOW wel volgens verzekeringsjaren.',
                    'Premie vrijwillige verzekering kan hoog zijn; vergelijk met je tekort in deze calculator.',
                ],
                'links' => [
                    ['label' => 'SVB — wonen buiten Nederland', 'url' => 'https://www.svb.nl/nl/aow/wonen-buiten-nederland'],
                ],
            ],
            'zorg_nl' => [
                'summary' => 'Een Nederlandse basisverzekering mag meestal niet doorlopen als je in Italië woont. Je hebt recht op zorg via het Italiaanse SSN of een S1-verklaring.',
                'how' => [
                    'Zeg de Nederlandse zorgverzekering op per emigratiedatum (schriftelijk, met bewijs van uitschrijving).',
                    'Vraag bij het CAK of je verzekeringsplicht stopt en of een S1 (voorheen E121) van toepassing is, bijvoorbeeld bij AOW of sommige uitkeringen.',
                    'Regel in Italië zo snel mogelijk de tessera sanitaria (zie SSN-punt). Tot die tijd: reisverzekering of tijdelijke private dekking.',
                    'Tandarts en extra dekking zijn in Italië vaak privé; budgetteer dat bij maandlasten.',
                ],
                'where' => [
                    'Je Nederlandse zorgverzekeraar (opzeggen).',
                    'CAK: cak.nl (verzekeringsplicht / S1).',
                    'Lokale ASL (Azienda Sanitaria Locale) in Italië.',
                ],
                'watch' => [
                    'Niet opzeggen vóór je écht bent uitgeschreven: gat in dekking of dubbele premie.',
                    'EHIC/EHIC-kaart is voor tijdelijk verblijf, geen vervanging van een woonverzekering.',
                ],
                'links' => [
                    ['label' => 'CAK — zorgverzekering en buitenland', 'url' => 'https://www.cak.nl'],
                ],
            ],
            'belasting_nl' => [
                'summary' => 'Bij emigratie doe je aangifte over het jaar van vertrek (vaak M-biljet). Fiscale woonplaats bepaalt of NL of IT mag heffen; er is een belastingverdrag.',
                'how' => [
                    'Meld emigratie bij de Belastingdienst en lever het M-biljet / C-biljet in over het emigratiejaar.',
                    'Bepaal je fiscale woonplaats: centrum van levensbelangen (woning, gezin, werk). Na echte emigratie ben je meestal inwoner van Italië.',
                    'Nederland mag vaak nog heffen over AOW, WIA en pensioen (afhankelijk van het verdrag en het type inkomen). Italië mag heffen over wereldinkomen als je daar inwoner bent; voorkom dubbele belasting via het verdrag.',
                    'Houd in NL rekening met box 3 tot het moment van emigratie, en daarna met Italiaanse vermogens- en inkomstenbelasting (IMU, IRPEF, cedolare secca, forfettario).',
                ],
                'where' => [
                    'Belastingdienst: belastingdienst.nl — emigreren.',
                    'Mijn Belastingdienst / aangifteprogramma voor buitenlands belastingplichtigen.',
                    'Eventueel een adviseur die NL én IT kent.',
                ],
                'watch' => [
                    'Emigreren “op papier” terwijl je vaak in NL bent, kan als schijnconstructie worden gezien.',
                    'Banken en brokers vragen soms een fiscaal inwonersbewijs (residencia fiscale).',
                    'Dit is geen advies: laat grote vermogens- of pensioenbeslissingen toetsen.',
                ],
                'links' => [
                    ['label' => 'Belastingdienst — emigreren', 'url' => 'https://www.belastingdienst.nl/wps/wcm/connect/nl/buitenland/content/emigreren'],
                ],
            ],
            'gemeente_nl' => [
                'summary' => 'Ga je langer dan 8 maanden in 12 maanden buiten Nederland wonen, dan moet je je uitschrijven uit de BRP.',
                'how' => [
                    'Maak een afspraak bij de gemeente van inschrijving, meestal kort voor vertrek (vaak tot 5 dagen van tevoren, check je gemeente).',
                    'Neem ID, eventueel partner/kinderen mee, en het nieuwe adres in Italië als je dat al hebt.',
                    'Je krijgt een bewijs van uitschrijving / verblijf buiten Nederland; bewaar dit voor SVB, zorgverzekeraar, Belastingdienst en banken.',
                    'Na uitschrijving val je onder de RNI (Registratie Niet-Ingezetenen). Een DigiD blijft vaak werken, niet altijd alle loketten.',
                ],
                'where' => [
                    'Burgerzaken van je Nederlandse woongemeente.',
                ],
                'watch' => [
                    'Uitschrijven terwijl je de woning in NL aanhoudt als hoofdverblijf is risicovol (toeslagen, zorg, BRP).',
                    'Post: regel een postadres of doorzending; officiële post van SVB/Belastingdienst mag je niet missen.',
                ],
                'links' => [
                    ['label' => 'Rijksoverheid — uitschrijven bij emigratie', 'url' => 'https://www.rijksoverheid.nl/onderwerpen/immigratie-naar-nederland/vraag-en-antwoord/moet-ik-me-uitschrijven-bij-de-gemeente-als-ik-naar-het-buitenland-verhuis'],
                ],
            ],
            'uwv_wia' => [
                'summary' => 'WIA kun je in Italië vaak blijven ontvangen, maar UWV moet je emigratie kennen en er gelden EU-regels voor export van uitkeringen.',
                'how' => [
                    'Meld je verhuizing schriftelijk bij UWV vóór vertrek, met het nieuwe adres en de datum.',
                    'Vraag na of je in het woonland medische herbeoordelingen via het lokale instituut lopen (E207/E213 e.d.).',
                    'Geef wijzigingen in inkomen, samenwonen en AOW-ingang door: WIA en AOW kunnen elkaar beïnvloeden.',
                    'Laat uitbetaling omzetten naar een IBAN die je na emigratie nog kunt gebruiken (NL of IT).',
                ],
                'where' => [
                    'UWV: uwv.nl — WIA en wonen in het buitenland.',
                    'Mijn UWV / je re-integratie- of uitkeringscontact.',
                ],
                'watch' => [
                    'Niet doorgeven van emigratie kan tot terugvordering leiden.',
                    'Indexatie van WIA blijft Nederlandse wetgeving; dat is in deze app meegenomen als jaarlijkse indexatie.',
                ],
                'links' => [
                    ['label' => 'UWV', 'url' => 'https://www.uwv.nl'],
                ],
            ],
            'codice_fiscale' => [
                'summary' => 'De codice fiscale is je Italiaanse burgerservicenummer. Zonder dit regel je geen huis, bank, SSN, contracten of Partita IVA.',
                'how' => [
                    'Vraag hem aan vóór of bij aankomst: bij het Italiaanse consulaat in Nederland, of in Italië bij Agenzia delle Entrate, of soms via de comune.',
                    'Neem paspoort/ID mee. Voor niet-ingezetenen bestaat een procedure via het consulaat.',
                    'Controleer de 16-tekencode; die staat later op je tessera sanitaria.',
                ],
                'where' => [
                    'Agenzia delle Entrate (lokaal kantoor) of Italiaans consulaat in NL.',
                    'agenziaentrate.gov.it',
                ],
                'watch' => [
                    'Koop geen woning of teken geen huur zonder codice fiscale.',
                    'Let op typefouten in naam/geboortedatum; die werken door in alle registers.',
                ],
                'links' => [
                    ['label' => 'Agenzia delle Entrate', 'url' => 'https://www.agenziaentrate.gov.it'],
                ],
            ],
            'anagrafe' => [
                'summary' => 'Als EU-burger mag je in Italië wonen. Na 3 maanden moet je je inschrijven in de anagrafe van de comune (residenza).',
                'how' => [
                    'Ga naar l’Ufficio Anagrafe van de gemeente waar je woont, met ID, codice fiscale, huur- of eigendomsbewijs en eventueel nutscontract.',
                    'Voor EU-burgers: iscrizione anagrafica; soms vragen ze bewijs van middelen of werk/pensioen (richtlijn vrij verkeer).',
                    'Na inschrijving volgt vaak een huisbezoek van de polizia municipale om te checken dat je daar echt woont.',
                    'Vraag een certificato di residenza; die heb je nodig voor SSN, school, auto, bank.',
                ],
                'where' => [
                    'Comune → Anagrafe / Stato civile. Openingstijden verschillen sterk; vaak alleen op afspraak (prenotazione).',
                ],
                'watch' => [
                    'Residenza is niet hetzelfde als fiscale residentie, maar ze hangen samen.',
                    'Zonder residenza krijg je vaak geen tessera sanitaria of lokale tarieven.',
                    'Neem kopieën van alles mee; originelen blijven van jou.',
                ],
                'links' => [],
            ],
            'fiscale_it' => [
                'summary' => 'Als je meer dan 183 dagen in Italië woont of daar je centrum van belangen hebt, word je meestal Italiaans belastingplichtige.',
                'how' => [
                    'Schrijf je in bij de Agenzia delle Entrate en geef je status als residente fiscale door (vaak via CAF of commercialista).',
                    'Vraag een certificato di residenza fiscale als NL-instellingen dat willen.',
                    'Kies samen met een commercialista de juiste boxes: IRPEF, cedolare secca op verhuur, forfettario op B&B.',
                    'IMU, TARI en eventueel IVIE/IVAFE op buitenlands vermogen kunnen spelen; dit hangt van je aangifte af.',
                ],
                'where' => [
                    'Agenzia delle Entrate, CAF (patronato) of een commercialista in je regio.',
                ],
                'watch' => [
                    'Dubbele aangifte NL+IT in het emigratiejaar is normaal; stem data af.',
                    'B&B-omzet boven het forfettario-plafond (nu € 85.000) dwingt tot een ander regime.',
                ],
                'links' => [
                    ['label' => 'Agenzia delle Entrate', 'url' => 'https://www.agenziaentrate.gov.it'],
                ],
            ],
            'aire' => [
                'summary' => 'AIRE is het register van Italianen in het buitenland. Als Nederlander die in Italië gaat wonen, hoort dit punt meestal níet bij jou.',
                'how' => [
                    'Alleen doen als jij (of je partner) de Italiaanse nationaliteit hebt én buiten Italië gaat wonen. Dan schrijf je in bij het consulaat.',
                    'Woon je als Nederlander in Italië: vink dit af als “niet van toepassing” en focus op anagrafe + fiscale residentie.',
                ],
                'where' => [
                    'Italiaans consulaat / Fast It, alleen voor Italiaanse burgers.',
                ],
                'watch' => [
                    'Verwar AIRE niet met de gemeentelijke anagrafe in Italië.',
                ],
                'links' => [],
            ],
            'ssn' => [
                'summary' => 'Het Servizio Sanitario Nazionale geeft recht op huisarts en ziekenhuiszorg. Je krijgt een tessera sanitaria.',
                'how' => [
                    'Schrijf je in bij de ASL van je woonplaats, mét certificato di residenza en codice fiscale.',
                    'Pensioen of bepaalde uitkeringen: vraag in NL een S1 aan (CAK/SVB) en lever die in bij de ASL — dan val je vaak onder het SSN zonder aparte bijdrage zoals werknemers.',
                    'Kies een medico di base (huisarts) uit de lijst van de ASL.',
                    'De tessera sanitaria is ook je fiscale code-kaart; verlenging gaat vaak automatisch.',
                ],
                'where' => [
                    'ASL / distretto sanitario van je comune.',
                    'CAK voor S1 vanuit Nederland.',
                ],
                'watch' => [
                    'Zonder residenza weigert de ASL vaak inschrijving.',
                    'Niet alle zorg is gratis (ticket / specialist); tandarts meestal privé.',
                    'Private verzekering blijft zinvol voor wachttijden en repatriëring.',
                ],
                'links' => [
                    ['label' => 'Ministero della Salute', 'url' => 'https://www.salute.gov.it'],
                ],
            ],
            'bank_it' => [
                'summary' => 'Een Italiaanse rekening (conto corrente) is bijna onmisbaar voor huur, bollo, SSN-terugbetalingen en leveranciers.',
                'how' => [
                    'Maak een afspraak bij een filiaal (Intesa, UniCredit, BPM, Poste Italiane BancoPosta, of een digitale bank die IT-IBAN biedt).',
                    'Neem paspoort, codice fiscale, bewijs van adres/residenza en soms inkomen (pensioen/WIA-specificatie) mee.',
                    'Vraag meteen om internetbankieren, een Bancomat-pas en of ze SEPA-incasso voor nuts en IMU accepteren.',
                    'Houd tijdelijk een NL-rekening voor SVB/UWV als die nog naar NL uitbetalen; zet daarna over.',
                ],
                'where' => [
                    'Lokale bank of Poste Italiane. Vergelijk canone (maandkosten) en storten/opnemen.',
                ],
                'watch' => [
                    'Sommige banken willen eerst residenza; Poste is vaak soepeler.',
                    'Let op imposta di bollo op rekeningen boven een drempel.',
                    'Grote overboekingen vanuit NL: meld herkomst (woningverkoop) om witwasvragen voor te zijn.',
                ],
                'links' => [],
            ],
            'rijbewijs' => [
                'summary' => 'Een geldig EU-rijbewijs mag je in Italië gebruiken. Na vestiging gelden soms omwissel- of keuringsregels, vooral bij verlopen of 70+.',
                'how' => [
                    'Rijd in de eerste periode op je Nederlandse EU-rijbewijs; neem ook ID mee.',
                    'Als je residenza hebt en het rijbewijs verloopt of de Comune/Motorizzazione omwisseling vraagt: start conversione at the Motorizzazione Civile (UMC).',
                    'Medische keuring (certificato medico) is in IT gebruikelijk bij verlenging, via een bevoegde arts.',
                    'Brommer/auto: check of je categorieën (A/B) 1-op-1 overgaan.',
                ],
                'where' => [
                    'Motorizzazione Civile (UMC) van je provincie; soms via ACI of een autoscuola als tussenpersoon.',
                ],
                'watch' => [
                    'Een verlopen NL-rijbewijs omwisselen is lastiger vanuit IT; regel verlenging op tijd, eventueel nog in NL.',
                    'Boetes en punten: Italiaanse patente heeft een puntensysteem na omwisseling.',
                ],
                'links' => [
                    ['label' => 'MIT / Motorizzazione', 'url' => 'https://www.mit.gov.it'],
                ],
            ],
            'auto_bollo' => [
                'summary' => 'Een NL-auto mag niet eindeloos op Nederlands kenteken in IT als je er woont. Importeren betekent targa, bollo, assicurazione RCA en keuring.',
                'how' => [
                    'Meld in NL bij RDW dat de auto het land verlaat (uitvoer) als je hem meeneemt.',
                    'In IT: nationale typegoedkeuring/keuring (revisione), inschrijving bij PRA/Motorizzazione, Italiaanse targa.',
                    'Sluit RCA-verzekering (aansprakelijkheid) bij een Italiaanse verzekeraar; NL-verzekering stopt meestal na emigratie.',
                    'Bollo auto (wegenbelasting) is regionaal, vaak via ACI of de Regione; IMU is voor vastgoed, bollo voor de auto.',
                    'Zonder eigen auto: overweeg of je hem nodig hebt; bergdorpen versus stad verschilt sterk.',
                ],
                'where' => [
                    'RDW (NL uitvoer), Motorizzazione / PRA / ACI in Italië, verzekeraar.',
                ],
                'watch' => [
                    'Kosten van import + btw/accijns kunnen tegenvallen bij jongere auto’s; check vrijstellingen.',
                    'Zonder Italiaanse targa na vestiging riskeer je boetes.',
                    'Dieselbeperkingen (ZTL, euroklasse) in veel centri storici.',
                ],
                'links' => [
                    ['label' => 'RDW — uitvoer voertuig', 'url' => 'https://www.rdw.nl'],
                ],
            ],
            'partita_iva' => [
                'summary' => 'Een B&B of verhuur als onderneming vraagt vaak een Partita IVA. Forfettario is het vereenvoudigde tarief (startup 5%, daarna 15% over het forfait, plafond € 85.000).',
                'how' => [
                    'Bepaal of je occasionele verhuur (soms zonder P.IVA, met limieten) doet of een echte attività (B&B, locazioni turistiche). Gemeentelijke regels verschillen.',
                    'Vraag Partita IVA aan bij Agenzia delle Entrate (code ATECO voor alloggio / B&B) en meld je bij INPS.',
                    'Meld de structuur bij de Comune (SUAP / ISTAT / CIR-code / CIN waar verplicht) en bij Alloggiati Web (questura) voor gastenaangifte.',
                    'Kies forfettario als je mag: geen btw-aftrek, belasting over coefficiente (voor B&B vaak 67% van de omzet in deze app).',
                    'Houd een eenvoudige administratie bij: omzet, commissies Booking/Airbnb, ontbijt, nuts.',
                ],
                'where' => [
                    'Agenzia delle Entrate, commercialista, SUAP van de comune, Questura (alloggiati).',
                ],
                'watch' => [
                    'Boven het plafond val je uit forfettario.',
                    'Sommige regio’s eisen extra toeristenbelasting (imposta di soggiorno) die je voor de gemeente int.',
                    'Zonder CIN/registratie kun je platforms je listing laten verwijderen.',
                    'Cijfers in deze app zijn indicatief; laat de inschrijving door een commercialista doen.',
                ],
                'links' => [
                    ['label' => 'Agenzia delle Entrate — Partita IVA', 'url' => 'https://www.agenziaentrate.gov.it'],
                ],
            ],
        ];
    }
}
