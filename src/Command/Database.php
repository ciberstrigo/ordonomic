<?php

namespace Jegulnomic\Command;

use DI\Attribute\Inject;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;
use PDO;

readonly class Database extends AbstractCommand
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private DatabaseStorage $storage
    )
    {}

    public function dumpScheme(): void
    {
        $pdo = $this->storage->getPDO();

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Step 2: Retrieve Tables
        $tables = [];
        $query = $pdo->query("SHOW TABLES");
        while ($row = $query->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        // Step 3: Retrieve Columns and Construct CREATE TABLE Statements
        $schema_sql = '';
        foreach ($tables as $table) {
            $create_table_sql = "CREATE TABLE `$table` (\n";
            $columns = [];
            $query = $pdo->query("SHOW COLUMNS FROM `$table`");
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $field = $row['Field'];
                $type = $row['Type'];
                $null = $row['Null'] === 'NO' ? 'NOT NULL' : 'NULL';
                $key = $row['Key'] === 'PRI' ? 'PRIMARY KEY' : '';
                $default = $row['Default'] !== null ? "DEFAULT '{$row['Default']}'" : '';
                $extra = $row['Extra'];

                $columns[] = "`$field` $type $null $default $extra $key";
            }

            $create_table_sql .= implode(",\n", $columns);
            $create_table_sql .= "\n);\n\n";
            $schema_sql .= $create_table_sql;
        }

        // Step 4: Retrieve Indexes
        foreach ($tables as $table) {
            $indexes = [];
            $query = $pdo->query("SHOW INDEX FROM `$table`");
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $key_name = $row['Key_name'];
                $column_name = $row['Column_name'];
                $index_type = ($row['Non_unique'] == 0) ? 'UNIQUE' : 'INDEX';
                if ($key_name == 'PRIMARY') {
                    continue; // Skip primary keys since they are handled in the column definitions
                }
                $indexes[$key_name]['type'] = $index_type;
                $indexes[$key_name]['columns'][] = $column_name;
            }

            foreach ($indexes as $key_name => $index) {
                $columns = implode('`, `', $index['columns']);
                $schema_sql .= "CREATE {$index['type']} INDEX `$key_name` ON `$table` (`$columns`);\n";
            }

            $schema_sql .= "\n";
        }

        // Step 5: Retrieve Foreign Keys
        foreach ($tables as $table) {
            $query = $pdo->query("
            SELECT
                k.CONSTRAINT_NAME,
                k.COLUMN_NAME,
                k.REFERENCED_TABLE_NAME,
                k.REFERENCED_COLUMN_NAME
            FROM
                information_schema.KEY_COLUMN_USAGE k
            WHERE
                k.TABLE_SCHEMA = DATABASE() AND
                k.TABLE_NAME = '$table' AND
                k.REFERENCED_TABLE_NAME IS NOT NULL;
        ");
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $constraint_name = $row['CONSTRAINT_NAME'];
                $column_name = $row['COLUMN_NAME'];
                $referenced_table_name = $row['REFERENCED_TABLE_NAME'];
                $referenced_column_name = $row['REFERENCED_COLUMN_NAME'];

                $schema_sql .= "ALTER TABLE `$table` ADD CONSTRAINT `$constraint_name` FOREIGN KEY (`$column_name`) REFERENCES `$referenced_table_name` (`$referenced_column_name`);\n";
            }

            $schema_sql .= "\n";
        }

        echo $schema_sql;
    }
}