# Quick Start Guide - Emigratie Italië Calculator

## 🚀 Snel aan de slag (5 minuten)

### Stap 1: Database Setup
```sql
CREATE DATABASE emigrant_italy CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### Stap 2: Dependencies Installeren
```bash
cd j:\coding\emigrant
composer install
```

### Stap 3: Environment Configureren
Pas `.env` aan:
```env
database.default.database = emigrant_italy
database.default.username = root
database.default.password = jouw_wachtwoord
```

### Stap 4: Database Migreren & Seeden
```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

### Stap 5: Start de Server
```bash
php spark serve
```

Ga naar: `http://localhost:8080`

## 🔐 Test Accounts

**Admin Account:**
- Email: `admin@example.com`
- Wachtwoord: `admin123`

**Demo Account (met voorbeelddata):**
- Email: `demo@example.com`  
- Wachtwoord: `demo123`

## 📝 Eerste Gebruik

### 1. Login met demo account
Gebruik het demo account om de applicatie te verkennen met voorbeelddata.

### 2. Bekijk het Dashboard
Het dashboard toont een overzicht van je financiële situatie met grafieken.

### 3. Pas de gegevens aan
Navigeer door de modules in de sidebar:
- **Startpositie NL**: Begin hier met je Nederlandse situatie
- **Inkomsten**: Voeg je inkomstenbronnen toe
- **Vastgoed IT**: Configureer je Italiaanse woning(en)
- **Maandlasten**: Vul alle vaste kosten in
- **Belastingen**: Stel je belastingsituatie in
- **B&B Module**: Activeer en configureer indien van toepassing

### 4. Bekijk de berekeningen
Het dashboard wordt automatisch bijgewerkt met:
- Totaal vermogen na aankoop
- Maandelijks netto bedrag
- Vermogensontwikkeling over 1, 2 en 3 jaar
- B&B rendabiliteit (indien actief)

### 5. Sla een scenario op
Ga naar **Scenario's** en sla je huidige configuratie op om verschillende situaties te vergelijken.

### 6. Exporteer je data
Klik op **Export CSV** in de sidebar om een rapport te downloaden.

## 🎯 Typische Workflow

```
1. Startpositie Nederland invullen
   ↓
2. Inkomsten toevoegen
   ↓
3. Hoofdwoning Italië configureren
   ↓
4. Maandlasten invullen
   ↓
5. Belastingen instellen
   ↓
6. (Optioneel) B&B module activeren
   ↓
7. Dashboard controleren
   ↓
8. Scenario opslaan
   ↓
9. Alternatieven uitproberen
   ↓
10. Beste scenario exporteren
```

## 💡 Tips

### Realistische Cijfers
- Zoek online naar gemiddelde kosten in jouw Italiaanse regio
- Houd 10-15% buffer aan voor onvoorziene kosten
- Reken conservatief met B&B bezettingsgraden (beter verrassingen dan teleurstellingen)

### B&B Module
- Start met lage bezettingsgraden (40-50%) voor realisme
- Gebruik de break-even analyse om te zien wat minimaal nodig is
- Houd rekening met seizoensverschillen in Italië

### Scenario's
Maak verschillende scenario's aan voor:
- ✅ Met B&B / Zonder B&B
- ✅ Met tweede woning / Zonder tweede woning
- ✅ Conservatieve cijfers / Optimistische cijfers
- ✅ Verschillende woningprijzen

### Belastingen
- **Forfettario** is ideaal voor kleine B&B (tot €85.000 omzet)
- IMU geldt alleen voor tweede woningen (hoofdwoning meestal vrijgesteld)
- TARI varieert per gemeente (check website gemeente)

## ⚠️ Veelvoorkomende Valkuilen

1. **Te optimistische B&B bezetting**
   - Realistisch: 40-60% gemiddeld
   - Hoogseizoen: 70-85%
   - Laagseizoen: 30-50%

2. **Vergeten kosten**
   - Aankoopkosten (notaris, belasting): 10-15%
   - Jaarlijks onderhoud: 1-2% van woningwaarde
   - Onvoorzien: minimaal €100/maand

3. **Belastingen onderschatten**
   - Forfettario is 15% (of 5% eerste 5 jaar)
   - Vergeet TARI (afvalbelasting) niet
   - B&B: ook belasting over omzet

## 🔧 Troubleshooting

### Database connectie mislukt
```bash
# Controleer of MySQL draait
# Controleer .env instellingen
# Test: php spark db:table users
```

### White screen
```bash
# Zet debug aan in .env:
CI_ENVIRONMENT = development
# Check: writable/logs/log-*.log
```

### 404 errors op routes
```bash
# Check .htaccess in public folder
# Of gebruik: php spark serve (development server)
```

## 📚 Meer Informatie

Zie de volledige [README.md](README.md) voor:
- Gedetailleerde installatie-instructies
- Architectuur uitleg
- Alle functies en modules
- Development guidelines
- Troubleshooting

## 🆘 Hulp Nodig?

1. Check de [README.md](README.md)
2. Bekijk de code comments in de Controllers
3. Test met het demo account voor voorbeelden

## 🎉 Klaar!

Je bent klaar om je emigratie naar Italië financieel door te rekenen!

**Veel succes! 🇮🇹**
