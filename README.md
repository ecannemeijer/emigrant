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
- **Authenticatie**: Veilig login systeem met rollen (Admin/User)
- **Gebruikersbeheer**: CRUD functionaliteit voor admins

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

## 🔒 Beveiliging

- Password hashing (PHP password_hash)
- CSRF protection
- SQL injection preventie (Query Builder)
- XSS preventie (Output escaping)
- Session-based authenticatie
- Role-based access control

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
