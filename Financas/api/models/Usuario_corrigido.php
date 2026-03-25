<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $email;
    public $senha;
    public $nome;
    public $data_criacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar novo usuário
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET email=:email, senha=:senha, nome=:nome";

        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $this->senha = htmlspecialchars(strip_tags($this->senha));
        $this->nome = htmlspecialchars($this->nome, ENT_QUOTES, 'UTF-8');

        // Hash da senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        // Bind dos parâmetros
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":nome", $this->nome);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar se o email já existe
    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Login do usuário
    public function login() {
        $query = "SELECT id, email, senha, nome FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($this->senha, $row['senha'])) {
                $this->id = $row['id'];
                $this->email = $row['email'];
                $this->nome = $row['nome'];
                return true;
            }
        }
        return false;
    }

    // Obter dados do usuário
    public function readOne() {
        $query = "SELECT id, email, nome, data_criacao FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->email = $row['email'];
            $this->nome = $row['nome'];
            $this->data_criacao = $row['data_criacao'];
            return true;
        }
        return false;
    }
}
?>