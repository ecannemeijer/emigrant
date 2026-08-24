<?php

namespace App\Libraries;

class DatabaseBackup
{
    public function dump(): string
    {
        $db = \Config\Database::connect();
        $name = (string) ($db->getDatabase() ?: 'database');
        $now = date('Y-m-d H:i:s');

        $sql = "-- EmigreerItalia database backup\n";
        $sql .= "-- Database: {$name}\n";
        $sql .= "-- Datum: {$now}\n\n";
        $sql .= "SET NAMES utf8mb4;\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($db->listTables() as $table) {
            if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
                continue;
            }

            $quoted = '`' . $table . '`';
            $createRow = $db->query('SHOW CREATE TABLE ' . $quoted)->getRowArray();
            $create = $createRow['Create Table'] ?? $createRow['Create View'] ?? null;
            if (!$create) {
                continue;
            }

            $sql .= "DROP TABLE IF EXISTS {$quoted};\n";
            $sql .= $create . ";\n\n";

            $rows = $db->table($table)->get()->getResultArray();
            foreach ($rows as $row) {
                $columns = [];
                $values = [];
                foreach ($row as $column => $value) {
                    if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $column)) {
                        continue;
                    }
                    $columns[] = '`' . $column . '`';
                    $values[] = $value === null ? 'NULL' : $db->escape($value);
                }
                if ($columns === []) {
                    continue;
                }
                $sql .= 'INSERT INTO ' . $quoted . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }
}
