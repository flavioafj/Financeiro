<?php
/**
 * Configuração de conexão com o banco de dados MySQL
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'financas_app';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Conexão com o banco de dados
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            // Não exibir erro para evitar conflito com JSON response
            error_log("Erro de conexão: " . $exception->getMessage());
        }

        return $this->conn;
    }
}

?>