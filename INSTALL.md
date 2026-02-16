# Installation Guide - Emigratie Italië Calculator

## Prerequisites

Zorg dat je het volgende hebt geïnstalleerd:

### Required
- **PHP 8.0+**
  - Windows: [XAMPP](https://www.apachefriends.org/) of [Laragon](https://laragon.org/)
  - Controleer versie: `php -v`
- **Composer**
  - Download: [getcomposer.org](https://getcomposer.org/download/)
  - Controleer: `composer -V`
- **MySQL 5.7+** of **MariaDB 10.3+**
  - Meestal meegeleverd met XAMPP/Laragon

### Recommended Tools
- **Git** (voor version control)
- **Visual Studio Code** (IDE)
- **HeidiSQL** of **phpMyAdmin** (database management)

## Installation Steps

### 1. Project Setup

Als je het project al hebt gedownload/gekloneerd naar `j:\coding\emigrant`, ga dan naar die directory:

```bash
cd j:\coding\emigrant
```

### 2. Install Dependencies

```bash
composer install
```

Dit installeert CodeIgniter 4 en alle benodigde packages.

**Mogelijke problemen:**
- `composer: command not found` → Installeer Composer opnieuw en voeg toe aan PATH
- SSL errors → Run: `composer config -g -- disable-tls true` (alleen development!)

### 3. Database Configuration

#### 3.1 Create Database

Open MySQL/MariaDB (via HeidiSQL, phpMyAdmin, of command line):

```sql
CREATE DATABASE emigrant_italy 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;
```

#### 3.2 Create Database User (optioneel, maar recommended)

Voor productie is het beter een dedicated user te maken:

```sql
CREATE USER 'emigrant_user'@'localhost' IDENTIFIED BY 'veilig_wachtwoord';
GRANT ALL PRIVILEGES ON emigrant_italy.* TO 'emigrant_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 3.3 Configure .env File

Het `.env` bestand is al aangemaakt. Pas de database settings aan:

```env
# Environment
CI_ENVIRONMENT = development

# Database
database.default.hostname = localhost
database.default.database = emigrant_italy
database.default.username = root
database.default.password = jouw_wachtwoord
database.default.DBDriver = MySQLi
database.default.port = 3306
```

**Let op:** Vervang `jouw_wachtwoord` met je MySQL root wachtwoord (of de user die je hebt aangemaakt).

### 4. Run Migrations

Maak de database tabellen aan:

```bash
php spark migrate
```

Je zou moeten zien:
```
Running: 2024-01-01-000001_CreateUsersTable
Running: 2024-01-01-000002_CreateUserProfilesTable
...
Done.
```

**Probleem? "Table already exists":**
```bash
php spark migrate:rollback
php spark migrate
```

### 5. Seed Database (Optional but Recommended)

Voeg testdata toe:

```bash
php spark db:seed DatabaseSeeder
```

Dit maakt aan:
- Admin user: `admin@example.com` / `admin123`
- Demo user: `demo@example.com` / `demo123` (met voorbeelddata)

### 6. Set Permissions (Linux/Mac only)

Op Linux/Mac moet de `writable` folder beschrijfbaar zijn:

```bash
chmod -R 777 writable
```

Op Windows is dit meestal niet nodig.

### 7. Start Development Server

```bash
php spark serve
```

De applicatie draait nu op: **http://localhost:8080**

**Alternatief (met specifieke poort):**
```bash
php spark serve --host=localhost --port=8000
```

### 8. Test de Applicatie

1. Open browser: `http://localhost:8080`
2. Klik op "Inloggen"
3. Gebruik demo account:
   - Email: `demo@example.com`
   - Wachtwoord: `demo123`
4. Je ziet nu het Dashboard met voorbeelddata!

## Production Setup (Apache/Nginx)

Voor productie gebruik je geen `php spark serve`, maar een echte webserver.

### Apache Setup

1. **Virtual Host maken**

Maak een file: `C:\xampp\apache\conf\extra\httpd-vhosts.conf` (Windows) of `/etc/apache2/sites-available/emigrant.conf` (Linux)

```apache
<VirtualHost *:80>
    ServerName emigrant.local
    DocumentRoot "j:/coding/emigrant/public"
    
    <Directory "j:/coding/emigrant/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/emigrant-error.log"
    CustomLog "logs/emigrant-access.log" common
</VirtualHost>
```

2. **Hosts file aanpassen**

Windows: `C:\Windows\System32\drivers\etc\hosts`
Linux/Mac: `/etc/hosts`

Voeg toe:
```
127.0.0.1  emigrant.local
```

3. **Herstart Apache**

Windows (XAMPP): Stop & Start in XAMPP Control Panel
Linux: `sudo service apache2 restart`

4. **Bezoek**: `http://emigrant.local`

### Nginx Setup

```nginx
server {
    listen 80;
    server_name emigrant.local;
    root j:/coding/emigrant/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Security Checklist (Production)

Voordat je live gaat:

### 1. Environment
```env
CI_ENVIRONMENT = production
```

### 2. Database User
Maak dedicated user (niet root):
```sql
CREATE USER 'emigrant_prod'@'localhost' IDENTIFIED BY 'STERKE_WACHTWOORD';
GRANT SELECT, INSERT, UPDATE, DELETE ON emigrant_italy.* TO 'emigrant_prod'@'localhost';
```

### 3. Encryption Key
Genereer nieuwe key:
```bash
php spark key:generate
```

Of handmatig in `.env`:
```env
encryption.key = hex2bin:JE_EIGEN_RANDOM_64_CHAR_HEX_STRING
```

### 4. HTTPS
Gebruik altijd HTTPS in productie:
```env
app.forceGlobalSecureRequests = true
```

### 5. Disable Registration
Als je geen nieuwe users wilt toelaten, verwijder de route in `app/Config/Routes.php`:
```php
// Commentaar deze regels:
// $routes->get('register', 'Auth::register');
// $routes->post('register', 'Auth::attemptRegister');
```

### 6. Remove Debug Data
Verwijder de seeder data:
```bash
# Truncate alle tabellen en start opnieuw
php spark migrate:refresh
```

Maak handmatig je eerste admin user via database.

## Troubleshooting

### "Class 'X' not found"
```bash
composer dump-autoload
```

### Migration errors
```bash
# Reset alles
php spark migrate:rollback
php spark migrate
```

### Session errors
```bash
# Leeg session folder
rm -rf writable/session/*
```

### 500 Internal Server Error
1. Check `writable/logs/log-*.php`
2. Controleer folder permissions
3. Check .htaccess in public folder

### Database connection failed
1. Check MySQL draait (XAMPP: groene indicator)
2. Test connection:
```bash
php spark db:table users
```
3. Controleer .env database credentials

## Backup & Restore

### Backup Database
```bash
mysqldump -u root -p emigrant_italy > backup.sql
```

### Restore Database
```bash
mysql -u root -p emigrant_italy < backup.sql
```

### Backup Uploaded Files
```bash
# Zip writable folder
zip -r writable_backup.zip writable/uploads/
```

## Updates

Als je de applicatie update:

```bash
# Pull nieuwe code (git)
git pull

# Update dependencies
composer update

# Run nieuwe migrations
php spark migrate

# Clear cache
rm -rf writable/cache/*
```

## Need Help?

- Check de [README.md](README.md) voor functionaliteit
- Check de [QUICKSTART.md](QUICKSTART.md) voor snel gebruik
- CodeIgniter 4 docs: [codeigniter.com/user_guide](https://codeigniter.com/user_guide/)

Veel succes met de installatie! 🚀
