# Emigratie Italië Calculator

Een complete CodeIgniter 4 webapplicatie voor het doorrekenen van je emigratie naar Italië, inclusief woningverkoop in Nederland, vermogensberekening, maandelijkse lasten, belastingen en een uitgebreide B&B module.

## 🇮🇹 Features

- **Startpositie Nederland**: Bereken je netto overwaarde en totaal startvermogen
- **Inkomsten**: Beheer alle inkomstenbronnen (WIA, eigen inkomen, pensioen, etc.)
- **Italiaans Vastgoed**: Hoofdwoning en optionele tweede woning
- **Maandelijkse Lasten**: Alle vaste en variabele kosten
- **Belastingen**: Forfettario regeling, IMU, TARI en sociale bijdragen
- **B&B Module**: 
  - Bezettingsgraad berekeningen (hoog/laagseizoen)
  - Omzet en kosten tracking
  - Break-even analyse
  - Minimale bezettingsgraad calculator
- **Dashboard**: Visueel overzicht met grafieken (Chart.js)
- **Scenario's**: Sla verschillende financiële scenario's op en vergelijk
- **Export**: CSV export van alle data
- **Authenticatie**: Login met rollen (Admin/User), rate limiting, sessie-regeneratie
- **Abonnement**: Maand/jaar via PayPal Checkout (eenmalige betaling); tot het admin-vinkje aanstaat krijgen nieuwe gebruikers 1 maand gratis
- **Gebruikersbeheer**: CRUD, abonnementsdata, PayPal-betalingen, Config-menu

## 🚀 Technische Stack

- **Framework**: CodeIgniter 4
- **PHP**: 8.0+
- **Database**: MySQL
- **Frontend**: Bootstrap 5 + Bootstrap Icons
- **Visualisatie**: Chart.js
- **Architectuur**: MVC (Model-View-Controller)

## 📋 Vereisten

- PHP 8.0 of hoger
- MySQL 5.7+ of MariaDB 10.3+
- Composer
- Apache of Nginx webserver
- PHP extensions: intl, mbstring, mysqli

## 🛠️ Installatie

### 1. Kloon of download het project

```bash
cd j:\coding\emigrant
```

### 2. Installeer dependencies

```bash
composer install
```

### 3. Database configuratie

Maak een nieuwe MySQL database aan:

```sql
CREATE DATABASE emigrant_italy CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

Pas `.env` aan met je database gegevens:

```env
database.default.hostname = localhost
database.default.database = emigrant_italy
database.default.username = root
database.default.password = jouw_wachtwoord
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 4. Run migraties

```bash
php spark migrate
```

### 5. Seed de database (optioneel)

Voor testdata met voorbeeldgebruikers:

```bash
php spark db:seed DatabaseSeeder
```

Dit maakt aan:
- **Admin**: admin@example.com / admin123
- **Demo User**: demo@example.com / demo123

**Niet in productie seeden.** Wijzig of verwijder deze accounts; `admin123` is publiek bekend.

### 6. Start de development server

```bash
php spark serve
```

De applicatie is nu beschikbaar op: `http://localhost:8080`

## � Email Configuratie

De applicatie stuurt welkomst-emails naar nieuwe gebruikers. Configureer SMTP in je `.env` bestand:

### Development (Mailtrap)

Voor development raden we [Mailtrap.io](https://mailtrap.io) aan:

```env
email.fromEmail = 'no-reply@emigrant.local'
email.fromName = 'Emigrant Platform'
email.protocol = 'smtp'
email.SMTPHost = 'sandbox.smtp.mailtrap.io'
email.SMTPUser = 'your-mailtrap-username'
email.SMTPPass = 'your-mailtrap-password'
email.SMTPPort = 2525
email.SMTPCrypto = 'tls'
```

### Production (Gmail)

Voor productie met Gmail (gebruik een App Password):

```env
email.fromEmail = 'your-email@gmail.com'
email.fromName = 'Emigrant Platform'
email.protocol = 'smtp'
email.SMTPHost = 'smtp.gmail.com'
email.SMTPUser = 'your-email@gmail.com'
email.SMTPPass = 'your-app-password'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
```

**Let op**: Gmail App Passwords vereisen 2-factor authenticatie. Maak een app-specific password aan via [Google Account Security](https://myaccount.google.com/security).

### Andere providers

De configuratie werkt met elke SMTP provider (SendGrid, AWS SES, Mailgun, etc.). Pas de SMTP instellingen aan volgens je provider.

## �📁 Project Structuur

```
emigrant/
├── app/
│   ├── Config/           # Configuratie bestanden
│   ├── Controllers/      # Controller klassen
│   ├── Database/
│   │   ├── Migrations/   # Database migraties
│   │   └── Seeds/        # Database seeders
│   ├── Filters/          # Auth filters
│   ├── Models/           # Model klassen
│   └── Views/            # View templates
├── public/               # Public assets & entry point
├── writable/             # Logs, cache, uploads
├── .env                  # Environment configuratie
└── composer.json         # PHP dependencies
```

## 🔐 Gebruikersrollen

### Admin
- Volledige toegang tot gebruikersbeheer
- Kan gebruikers aanmaken, bewerken en verwijderen
- Toegang tot alle modules

### User
- Eigen financiële data beheren
- Alle calculator modules gebruiken
- Scenario's opslaan en exporteren

## 📊 Modules Uitleg

### Startpositie Nederland
Bereken je financiële uitgangspositie:
- Verkoopprijs woning
- Hypotheekrestschuld  
- Spaargeld
- **Automatisch**: Netto overwaarde en totaal startvermogen

### Inkomsten
Beheer al je inkomstenbronnen:
- WIA uitkering partner
- AOW (Algemene Ouderdomswet)
- Eigen inkomen
- Pensioen
- Overige inkomsten

### Italiaans Vastgoed
**Hoofdwoning:**
- Aankoopprijs
- Aankoopkosten (%)
- Jaarlijkse vaste lasten
- Onderhoud

**Tweede woning (optioneel):**
- Alle bovenstaande velden
- IMU belasting
- Eventuele huurinkomsten

### Belastingen
**Forfettario regeling:**
- Vereenvoudigd Italiaans belastingstelsel voor ondernemers
- Vast percentage (5% eerste 5 jaar, daarna 15%)
- Max. €85.000 omzet per jaar

**IMU:** Vermogensbelasting op tweede woningen  
**TARI:** Gemeentelijke afvalbelasting  
**Sociale bijdragen:** INPS voor zelfstandigen

### B&B Module
Uitgebreide berekeningen voor je B&B:

**Instellingen:**
- Aantal kamers
- Prijs per kamer per nacht
- Bezettingsgraad hoogseizoen (%)
- Bezettingsgraad laagseizoen (%)
- Aantal maanden per seizoen

**Kosten:**
- Extra energie/water
- Verzekering
- Schoonmaak
- Linnen & was
- Platform commissie (%)
- Marketing
- Onderhoud
- Administratie

**Break-even Analyse:**
- Berekent minimale bezettingsgraad om kosten te dekken
- Verschillende scenario's (40% - 80% bezetting)
- Waarschuwingen bij onrealistische cijfers

## 🎨 Dashboard Indicatoren

- **Groen**: Positief (inkomsten, winst)
- **Rood**: Negatief (kosten, verliezen)
- **Blauw**: Neutraal (informatie)
- **Geel**: Waarschuwing (lage marge, risico)

## 🔄 Scenario's

Je kunt verschillende financiële situaties opslaan als scenario's:
- Met/zonder B&B
- Met/zonder tweede woning
- Verschillende prijzen en kosten
- Vergelijk scenario's om de beste keuze te maken

## 📤 Export

Export je financiële data naar CSV voor:
- Eigen administratie
- Adviseur/accountant
- Verblijfsvergunning aanvraag
- Banken/hypotheekverstrekkers

## Abonnementen en PayPal — wat al in de code zit

Betalen is **eenmalige PayPal Checkout** (Orders API v2): maand of jaar. Er is **geen** automatische PayPal-verlenging; na afloop koopt de gebruiker opnieuw.

| Onderdeel | Status |
|-----------|--------|
| Tabellen `app_settings`, `subscriptions`, `payments` | In migratie `2026-08-24-130000_CreateBillingTables` |
| Admin **Config** (`/admin/config`): vinkje “Betaling verplicht”, prijzen | Klaar (standaard **uit**) |
| Nieuwe users: 1 maand complimentary zolang het vinkje uit staat | Klaar (na `php spark migrate`) |
| Paywall (`SubscriptionFilter`) als billing aan staat | Klaar; admins altijd door |
| Checkout `/subscription`, return/cancel, webhook `/webhooks/paypal` | Klaar in code |
| Admin: wie is geabonneerd tot wanneer, datums aanpassen, betalingenlijst | Klaar (`/admin/users`, `/admin/payments`) |

**Zolang het vinkje uit staat:** site blijft vrij toegankelijk; betalen is niet verplicht.

---

## Nog te doen (checklist livegang / PayPal)

Doe dit in deze volgorde als je écht wilt laten betalen.

### 1. Database

- [ ] `php spark migrate` op de server (billing-tabellen; bestaande users zonder abonnement krijgen 1 maand)
- [ ] Verbouw-tabellen: `CreateRenovationTables`, `CreateRenovationCategories`, `AddPlannedDateToRenovationItems`

### 2. PayPal Developer-app

- [ ] Account op [developer.paypal.com](https://developer.paypal.com)
- [ ] App aanmaken: eerst **Sandbox**, later **Live**
- [ ] REST credentials kopiëren: Client ID + Secret
- [ ] Webhook toevoegen op URL: `https://jouwdomein.nl/webhooks/paypal`
- [ ] Events minstens: `PAYMENT.CAPTURE.COMPLETED` (optioneel `CHECKOUT.ORDER.APPROVED`)
- [ ] Webhook ID kopiëren — **verplicht**. Zonder `paypal.webhookId` weigert de app alle webhooks (bewust, tegen valse “betaald”-berichten)

### 3. `.env` op de server

```env
paypal.mode = sandbox
paypal.clientId = ...
paypal.clientSecret = ...
paypal.webhookId = ...
```

Voor live: `paypal.mode = live` en de **live** client/secret/webhook-id.

Ook:

- [ ] `app.baseURL` = echte HTTPS-URL (PayPal return-URL’s hangen hiervan af)
- [ ] `encryption.key` gegenereerd (`php spark key:generate`)
- [ ] E-mail SMTP werkend (welkom / wachtwoord reset)

### 4. Pas daarna betalen aanzetten

- [ ] Inloggen als admin → **Config**
- [ ] Prijzen controleren (standaard €9,90 / maand, €69 / jaar)
- [ ] Vinkje **Betaling verplicht (PayPal)** aanzetten
- [ ] Testen: nieuw account zonder abonnement komt op `/subscription` en kan via PayPal betalen
- [ ] Testen: bestaand account met complimentary toegang blijft binnen tot de einddatum
- [ ] Sandbox-betaling controleren onder **Admin → Betalingen**

Als het vinkje aan gaat **zonder** PayPal-keys: gebruikers zien de paywall maar knoppen blijven uit (“PayPal nog niet ingesteld”).

### 5. Productie-beveiliging

Zet `CI_ENVIRONMENT = production`. Dan dwingt de app HTTPS af en `Secure` cookies.

```env
CI_ENVIRONMENT = production
app.baseURL = 'https://jouwdomein.nl/'
app.forceGlobalSecureRequests = true
cookie.secure = true
```

- [ ] Webroot van Apache/Nginx = map **`public/`** (niet de projectroot; anders kan `.env` leesbaar zijn)
- [ ] Geen `DatabaseSeeder` op productie
- [ ] Standaard admin-wachtwoord wijzigen
- [ ] Debug toolbar staat uit in production; CSRF-debug alleen in development

### 6. Bewust nog niet gedaan

- Geen PayPal-abonnementen met automatische verlenging (alleen eenmalige betaling)
- Geen strikte Content-Security-Policy (zou Bootstrap-CDN en inline JS breken)
- Notities (checklist/verbouwen) blijven beperkte HTML via `NoteSanitizer`

---

## Admin-schermen (abonnement)

| Pagina | Wat |
|--------|-----|
| `/admin/config` | Betaling verplicht, maand- en jaarprijs, PayPal-status |
| `/admin/users` | Abonnement actief/verlopen, geldig tot |
| `/admin/users/edit/{id}` | Start-/einddatum handmatig, plan, bron |
| `/admin/payments` | Wie heeft wanneer via PayPal betaald |

---

## Beveiliging (al ingebouwd)

- Wachtwoorden: `password_hash` / `password_verify`
- CSRF op alle POST’s behalve `POST /webhooks/paypal`
- Query Builder (geen ruwe SQL met user-input)
- `esc()` op flash messages en formulier-`old()`-waarden
- Login: max. 5 pogingen per minuut per IP én e-mail; register/reset/contact ook begrensd
- Sessie-ID opnieuw na login (`session()->regenerate`)
- Uitloggen alleen via POST + CSRF
- Admin-filter leest `role` + `is_active` uit de database (demotie werkt meteen)
- `role` / `is_active` niet mass-assignable via het gewone User-model
- Laatste actieve admin kan niet worden verwijderd of gedemoteerd
- PayPal-webhook: handtekening verplicht; leeg `webhookId` = weigeren
- Abonnementsfilter faalt **dicht** (geen stille toegang bij een databasefout)
- Security-headers; geen globale page cache van ingelogde pagina’s
- HTTPS + secure cookies automatisch als `CI_ENVIRONMENT = production`

## 🌐 Multi-language Support (Bonus)

De applicatie ondersteunt:
- Nederlands (standaard)
- Italiano
- English

## 🐛 Development

### Debug Mode

In `.env`:
```env
CI_ENVIRONMENT = development
```

Voor productie:
```env
CI_ENVIRONMENT = production
```

### Nieuwe migratie maken

```bash
php spark make:migration CreateTableName
```

### Nieuwe seeder maken

```bash
php spark make:seeder SeederName
```

## 📝 License

Dit project is ontwikkeld voor persoonlijk gebruik. 

## 👤 Auteur

Ontwikkeld als emigratie calculator voor Italië.

## 🤝 Contributing

Dit is een persoonlijk project, maar suggesties zijn welkom!

## ⚠️ Disclaimer

Deze calculator is bedoeld als hulpmiddel voor financiële planning. Raadpleeg altijd een erkend fiscalist of adviseur voor officieel advies over emigratie en belastingen.

## 📞 Support

Voor vragen of problemen, maak een issue aan in de repository.

---

**Veel succes met je emigratie naar Italië! 🇮🇹**
