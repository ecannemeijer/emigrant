-- Manual database repair script for production
-- Run this if migrations are failing with "Duplicate column" errors
-- This script is safe to run multiple times (idempotent)

-- First, let's see what columns exist
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'incomes'
ORDER BY ORDINAL_POSITION;

-- Add or rename columns as needed
-- Check if wao_future exists and rename to aow_future
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'wao_future'
);

SET @col_target = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'aow_future'
);

-- Only rename if wao_future exists and aow_future doesn't
SET @sql = IF(@col_exists > 0 AND @col_target = 0,
    'ALTER TABLE incomes CHANGE COLUMN wao_future aow_future DECIMAL(10,2) DEFAULT 0.00 COMMENT "Toekomstige AOW per maand"',
    'SELECT "wao_future rename not needed" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Rename own_wao to own_aow if needed
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'own_wao'
);

SET @col_target = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'own_aow'
);

SET @sql = IF(@col_exists > 0 AND @col_target = 0,
    'ALTER TABLE incomes CHANGE COLUMN own_wao own_aow DECIMAL(10,2) DEFAULT 0.00 COMMENT "Eigen AOW per maand"',
    'SELECT "own_wao rename not needed" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Rename wao_start_age to aow_start_age if needed
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'wao_start_age'
);

SET @col_target = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'aow_start_age'
);

SET @sql = IF(@col_exists > 0 AND @col_target = 0,
    'ALTER TABLE incomes CHANGE COLUMN wao_start_age aow_start_age INT(3) NULL COMMENT "Leeftijd waarop AOW (partner) ingaat"',
    'SELECT "wao_start_age rename not needed" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add aow_future if it doesn't exist at all
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'aow_future'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE incomes ADD COLUMN aow_future DECIMAL(10,2) DEFAULT 0.00 COMMENT "Toekomstige AOW per maand" AFTER own_income',
    'SELECT "aow_future already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add own_aow if it doesn't exist at all
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'own_aow'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE incomes ADD COLUMN own_aow DECIMAL(10,2) DEFAULT 0.00 COMMENT "Eigen AOW per maand"',
    'SELECT "own_aow already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add aow_start_age if it doesn't exist at all
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'aow_start_age'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE incomes ADD COLUMN aow_start_age INT(3) NULL COMMENT "Leeftijd waarop AOW (partner) ingaat" AFTER aow_future',
    'SELECT "aow_start_age already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add partner_has_wia if it doesn't exist
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'incomes' 
    AND COLUMN_NAME = 'partner_has_wia'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE incomes ADD COLUMN partner_has_wia TINYINT(1) NOT NULL DEFAULT 1 COMMENT "Is partner WIA (1) or regular income (0)" AFTER wia_wife',
    'SELECT "partner_has_wia already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify final column structure
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'incomes'
ORDER BY ORDINAL_POSITION;

SELECT 'Database repair complete!' as status;
