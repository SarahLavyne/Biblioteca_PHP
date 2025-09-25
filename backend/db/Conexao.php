<?php
// backend/db/Conexao.php
// VERSÃO ATUALIZADA PARA MYSQL

class Conexao {
    private static $pdo;

    public static function getConexao() {
        if (!isset(self::$pdo)) {
            // --- INÍCIO DAS ALTERAÇÕES PARA MYSQL ---
            $host = 'localhost';
            // A porta do MySQL geralmente é 3306 e não precisa ser especificada
            $dbname = 'biblioteca';
            $user = 'root'; // Usuário padrão do XAMPP, altere se o seu for diferente
            $password = ''; // Senha padrão do XAMPP (vazia), altere se tiver definido uma

            try {
                // O DSN (string de conexão) é diferente para o MySQL
                $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
                self::$pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
            // --- FIM DAS ALTERAÇÕES PARA MYSQL ---
            } catch (PDOException $e) {
                die("Erro ao conectar com o banco de dados: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}