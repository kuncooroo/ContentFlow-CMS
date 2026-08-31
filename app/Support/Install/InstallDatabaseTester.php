<?php

namespace App\Support\Install;

use PDO;
use PDOException;

class InstallDatabaseTester
{
    /**
     * @param  array{
     *     host: string,
     *     port: int,
     *     database: string,
     *     username: string,
     *     password: ?string
     * }  $config
     */
    public function test(array $config): void
    {
        $this->assertSafeIdentifier($config['host'], 'host');
        $this->assertSafeIdentifier($config['database'], 'database name');
        $this->assertSafeIdentifier($config['username'], 'username');

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database'],
        );

        try {
            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 5,
                ],
            );
            $pdo->query('SELECT 1');
        } catch (PDOException $exception) {
            throw new \InvalidArgumentException(
                'Could not connect to the database. Check the credentials and ensure the database exists.',
                previous: $exception,
            );
        }
    }

    private function assertSafeIdentifier(string $value, string $label): void
    {
        if ($value === '' || preg_match('/[\x00-\x1F\x7F;]/', $value) === 1) {
            throw new \InvalidArgumentException("Invalid database {$label}.");
        }
    }
}
