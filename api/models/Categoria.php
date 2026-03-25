<?php
require_once __DIR__ . '/../config/database.php';

class Categoria {
    private $conn;
    private $table_name = "categorias";

    public $id;
    public $nome;
    public $tipo;
    public $usuario_id;
    public $is_default;
    public $data_criacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar nova categoria personalizada
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nome=:nome, tipo=:tipo, usuario_id=:usuario_id, is_default=0";

        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));

        // Bind dos parâmetros
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obter categorias de um usuário (padrão + personalizadas)
    public function readByUsuario($usuario_id) {
        $query = "SELECT id, nome, tipo, usuario_id, is_default, data_criacao 
                  FROM " . $this->table_name . " 
                  WHERE usuario_id IS NULL OR usuario_id = :usuario_id 
                  ORDER BY is_default DESC, tipo, nome";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        return $stmt;
    }

    // Obter categorias por tipo
    public function readByTipo($usuario_id, $tipo) {
        $query = "SELECT id, nome, tipo, usuario_id, is_default, data_criacao 
                  FROM " . $this->table_name . " 
                  WHERE (usuario_id IS NULL OR usuario_id = :usuario_id) 
                  AND tipo = :tipo 
                  ORDER BY is_default DESC, nome";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":tipo", $tipo);
        $stmt->execute();

        return $stmt;
    }

    // Verificar se a categoria pertence ao usuário
    public function pertenceAoUsuario($categoria_id, $usuario_id) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE id = :categoria_id 
                  AND (usuario_id IS NULL OR usuario_id = :usuario_id) 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":categoria_id", $categoria_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Obter categoria por ID
    public function readOne() {
        $query = "SELECT id, nome, tipo, usuario_id, is_default, data_criacao 
                  FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->nome = $row['nome'];
            $this->tipo = $row['tipo'];
            $this->usuario_id = $row['usuario_id'];
            $this->is_default = $row['is_default'];
            $this->data_criacao = $row['data_criacao'];
            return true;
        }
        return false;
    }

    // Atualizar categoria (apenas personalizadas)
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome 
                  WHERE id = :id AND usuario_id IS NOT NULL";

        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind dos parâmetros
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    // Deletar categoria (apenas personalizadas)
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id = :id AND usuario_id IS NOT NULL";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }
}
?>