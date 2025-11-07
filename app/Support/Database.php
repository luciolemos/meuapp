<?php
namespace App\Support;

use PDO;
use RuntimeException;

/**
 * Fornece uma conexão PDO compartilhada usando as informações do config/config.php.
 */
class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $config = require dirname(__DIR__, 2) . '/config/config.php';
            $db = $config['db'] ?? null;

            if (!$db) {
                throw new RuntimeException('Configuração de banco de dados não encontrada.');
            }

            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $db['driver'] ?? 'mysql',
                $db['host'] ?? 'localhost',
                $db['port'] ?? 3306,
                $db['name'] ?? '',
                $db['charset'] ?? 'utf8mb4'
            );

            try {
                self::$connection = new PDO($dsn, $db['user'] ?? '', $db['pass'] ?? '', [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT         => false,
                ]);
            } catch (\PDOException $e) {
                throw new RuntimeException('Não foi possível conectar ao banco de dados: ' . $e->getMessage(), 0, $e);
            }
        }

        return self::$connection;
    }

    public static function testConnection(): bool
    {
        try {
            self::connection();
            return true;
        } catch (RuntimeException $e) {
            return false;
        }
    }
}
