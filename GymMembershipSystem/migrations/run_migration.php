<?php
/**
 * Migration Runner Script
 * Execute this to create the email_logs table
 * Usage: Run via browser or CLI: php migrations/run_migration.php
 */

require_once __DIR__ . '/../classes/database.php';

class MigrationRunner extends Database {
    
    public function runMigration($migration_file) {
        if (!file_exists($migration_file)) {
            return ['success' => false, 'message' => 'Migration file not found: ' . $migration_file];
        }

        $sql = file_get_contents($migration_file);
        if (empty($sql)) {
            return ['success' => false, 'message' => 'Migration file is empty'];
        }

        try {
            $conn = $this->connect();
            
            // Split by semicolon to handle multiple statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $conn->exec($statement);
                }
            }

            return [
                'success' => true,
                'message' => 'Migration executed successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage()
            ];
        }
    }
}

// Execute migration if called directly
if (php_sapi_name() === 'cli' || (isset($_GET['run_migration']) && $_GET['run_migration'] === '1')) {
    $runner = new MigrationRunner();
    $result = $runner->runMigration(__DIR__ . '/email_logs_table.sql');
    
    if (php_sapi_name() === 'cli') {
        echo ($result['success'] ? '[SUCCESS] ' : '[ERROR] ') . $result['message'] . "\n";
        exit($result['success'] ? 0 : 1);
    } else {
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
?>
