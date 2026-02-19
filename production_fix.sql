-- ============================================================
-- PRODUCTION FIX: emigrant_italy database
-- Run this ONCE on the production server before php spark migrate
-- Safe to run multiple times (uses conditional checks)
-- ============================================================

-- Step 1: Rename wao_future -> aow_future (only if wao_future exists and aow_future does not)
SET @col_exist = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'wao_future'
);
SET @col_target = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'aow_future'
);
SET @sql = IF(@col_exist > 0 AND @col_target = 0,
    'ALTER TABLE incomes CHANGE wao_future aow_future DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Toekomstige AOW per maand''',
    'SELECT ''aow_future: no rename needed'' AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Step 2: Add aow_future if it still doesn't exist
SET @col_target2 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'aow_future'
);
SET @sql2 = IF(@col_target2 = 0,
    'ALTER TABLE incomes ADD COLUMN aow_future DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Toekomstige AOW per maand'' AFTER own_income',
    'SELECT ''aow_future: column already exists'' AS info'
);
PREPARE stmt FROM @sql2; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Step 3: Rename own_wao -> own_aow (only if own_wao exists and own_aow does not)
SET @col_exist3 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'own_wao'
);
SET @col_target3 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'own_aow'
);
SET @sql3 = IF(@col_exist3 > 0 AND @col_target3 = 0,
    'ALTER TABLE incomes CHANGE own_wao own_aow DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Eigen AOW per maand''',
    'SELECT ''own_aow: no rename needed'' AS info'
);
PREPARE stmt FROM @sql3; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Step 4: Add own_aow if it still doesn't exist
SET @col_target4 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'own_aow'
);
SET @sql4 = IF(@col_target4 = 0,
    'ALTER TABLE incomes ADD COLUMN own_aow DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Eigen AOW per maand''',
    'SELECT ''own_aow: column already exists'' AS info'
);
PREPARE stmt FROM @sql4; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Step 5: Rename wao_start_age -> aow_start_age (only if wao_start_age exists and aow_start_age does not)
SET @col_exist5 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'wao_start_age'
);
SET @col_target5 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'aow_start_age'
);
SET @sql5 = IF(@col_exist5 > 0 AND @col_target5 = 0,
    'ALTER TABLE incomes CHANGE wao_start_age aow_start_age INT(3) NULL COMMENT ''Leeftijd waarop AOW (partner) ingaat''',
    'SELECT ''aow_start_age: no rename needed'' AS info'
);
PREPARE stmt FROM @sql5; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Step 6: Add aow_start_age if it still doesn't exist
SET @col_target6 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'aow_start_age'
);
SET @sql6 = IF(@col_target6 = 0,
    'ALTER TABLE incomes ADD COLUMN aow_start_age INT(3) NULL COMMENT ''Leeftijd waarop AOW (partner) ingaat'' AFTER aow_future',
    'SELECT ''aow_start_age: column already exists'' AS info'
);
PREPARE stmt FROM @sql6; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Step 7: Add partner_has_wia if it doesn't exist
SET @col_target7 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'partner_has_wia'
);
SET @sql7 = IF(@col_target7 = 0,
    'ALTER TABLE incomes ADD COLUMN partner_has_wia TINYINT(1) NOT NULL DEFAULT 1 COMMENT ''Is partner WIA (1) or regular income (0)'' AFTER wia_wife',
    'SELECT ''partner_has_wia: column already exists'' AS info'
);
PREPARE stmt FROM @sql7; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Step 8: Add minimum_monthly_income if it doesn't exist
SET @col_target8 = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'incomes'
      AND COLUMN_NAME  = 'minimum_monthly_income'
);
SET @sql8 = IF(@col_target8 = 0,
    'ALTER TABLE incomes ADD COLUMN minimum_monthly_income DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT ''Minimum maandinkomen''',
    'SELECT ''minimum_monthly_income: column already exists'' AS info'
);
PREPARE stmt FROM @sql8; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Step 9: Create audit_logs table if it doesn't exist
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT(11) UNSIGNED NULL,
    `username`   VARCHAR(100)     NULL,
    `action`     VARCHAR(255)     NOT NULL,
    `method`     VARCHAR(10)      NOT NULL,
    `url`        VARCHAR(500)     NOT NULL,
    `ip_address` VARCHAR(45)      NULL,
    `user_agent` TEXT             NULL,
    `extra_data` TEXT             NULL,
    `created_at` DATETIME         NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 10: Mark all problematic migrations as completed so php spark migrate skips them
INSERT IGNORE INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT v.version, v.class, 'default', 'App', UNIX_TIMESTAMP(), COALESCE((SELECT MAX(batch) FROM migrations), 0) + 1
FROM (
    SELECT '2024-01-01-000013' AS version, 'App\\Database\\Migrations\\AddownAowToIncomes' AS class
    UNION ALL
    SELECT '2026-02-19-061747', 'App\\Database\\Migrations\\RenameWaoToAowInIncomes'
    UNION ALL
    SELECT '2026-02-19-063000', 'App\\Database\\Migrations\\FixIncomeColumnsIfNeeded'
    UNION ALL
    SELECT '2026-02-19-090000', 'App\\Database\\Migrations\\CreateAuditLogsTable'
) v
WHERE v.version NOT IN (SELECT version FROM migrations);

-- Done! Now run: php spark migrate
SELECT 'Fix complete. Run: php spark migrate' AS status;
