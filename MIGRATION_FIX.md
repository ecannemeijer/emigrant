# Database Migratie Fix - Productie

## Probleem
De productie database heeft inconsistente kolommen in de `incomes` tabel door eerdere mislukte migraties.

## Oplossingen (kies één)

### Optie 1: Gebruik de veilige migratie (AANBEVOLEN)
De nieuwe migratie `2026-02-19-063000_FixIncomeColumnsIfNeeded.php` checkt welke kolommen bestaan en repareert automatisch.

```bash
# Op productie server
cd /var/www/html/emigrant
php spark migrate
```

Deze migratie is veilig om meerdere keren uit te voeren.

### Optie 2: Handmatig SQL script
Als de migratie nog steeds faalt, gebruik dan het handmatige SQL script:

```bash
# Op productie server
cd /var/www/html/emigrant
mysql -u <username> -p <database_name> < database_repair.sql
```

Dit script:
- Checkt welke kolommen bestaan
- Hernoemt `wao_*` naar `aow_*` indien nodig
- Voegt ontbrekende kolommen toe
- Is veilig om meerdere keren uit te voeren

### Optie 3: Migration history reset (LAATSTE REDMIDDEL)
Als beide bovenstaande opties niet werken:

```bash
# Bekijk huidige migration status
php spark migrate:status

# Reset specifieke migration
php spark migrate:rollback -b 2026-02-19-061747

# Of reset alle 2026-02-19 migrations
mysql -u <username> -p
> USE <database_name>;
> DELETE FROM migrations WHERE version LIKE '2026-02-19%';
> exit

# Dan opnieuw migrate
php spark migrate
```

## Wat is er veranderd?
1. **Verwijderd**: `2026-02-19-061747_RenameWaoToAowInIncomes.php` (veroorzaakte crashes)
2. **Toegevoegd**: `2026-02-19-063000_FixIncomeColumnsIfNeeded.php` (veilige reparatie)
3. **Aangepast**: `2026-02-19-060737_AddPartnerWiaFlagToIncomes.php` (checkt nu of kolom bestaat)

## Verwachte database structuur
Na een succesvolle fix moet de `incomes` tabel deze kolommen hebben:
- `id`
- `user_id`
- `wia_wife`
- `partner_has_wia` ← nieuw
- `own_income`
- `aow_future` ← hernoemd van wao_future (of nieuw)
- `aow_start_age` ← hernoemd van wao_start_age (of nieuw)
- `pension`
- `pension_start_age`
- `other_income`
- `own_aow` ← hernoemd van own_wao (of nieuw)
- `minimum_monthly_income`
- `created_at`
- `updated_at`

## Verificatie
Controleer of alle kolommen correct zijn:

```sql
SHOW COLUMNS FROM incomes;
```

Of gebruik:
```bash
php spark db:table incomes
```

## Support
Als je nog steeds errors krijgt, check:
1. `/var/www/html/emigrant/writable/logs/` voor gedetailleerde errors
2. MySQL error log
3. Run `database_repair.sql` opnieuw
