<?php
require_once __DIR__ . '/../config/database.php';

class Transacao {
    private $conn;
    private $table_name = "transacoes";

    public $id;
    public $usuario_id;
    public $categoria_id;
    public $valor;
    public $tipo;
    public $data;
    public $descricao;
    public $data_criacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar nova transação
    public function create() {
        // Validações
        $this->valor = $this->validarValor($this->valor);
        $this->tipo = $this->validarTipoTransacao($this->tipo);
        $this->data = $this->validarData($this->data) ? $this->data : date('Y-m-d');
        
        if (!$this->valor || !$this->tipo) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  SET usuario_id=:usuario_id, categoria_id=:categoria_id, 
                      valor=:valor, tipo=:tipo, data=:data, descricao=:descricao";

        $stmt = $this->conn->prepare($query);

        // Sanitização dos dados
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        $this->descricao = $this->sanitizarTexto($this->descricao);

        // Bind dos parâmetros
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":categoria_id", $this->categoria_id);
        $stmt->bindParam(":valor", $this->valor);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":data", $this->data);
        $stmt->bindParam(":descricao", $this->descricao);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obter transações de um usuário
    public function readByUsuario($usuario_id, $limite = 50, $offset = 0) {
        $query = "SELECT t.id, t.usuario_id, t.categoria_id, t.valor, t.tipo, 
                         t.data, t.descricao, t.data_criacao,
                         c.nome as categoria_nome, c.tipo as categoria_tipo
                  FROM " . $this->table_name . " t
                  INNER JOIN categorias c ON t.categoria_id = c.id
                  WHERE t.usuario_id = :usuario_id 
                  ORDER BY t.data DESC, t.data_criacao DESC
                  LIMIT :limite OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // Obter transação por ID
    public function readOne() {
        $query = "SELECT t.id, t.usuario_id, t.categoria_id, t.valor, t.tipo, 
                         t.data, t.descricao, t.data_criacao,
                         c.nome as categoria_nome, c.tipo as categoria_tipo
                  FROM " . $this->table_name . " t
                  INNER JOIN categorias c ON t.categoria_id = c.id
                  WHERE t.id = :id AND t.usuario_id = :usuario_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->categoria_id = $row['categoria_id'];
            $this->valor = $row['valor'];
            $this->tipo = $row['tipo'];
            $this->data = $row['data'];
            $this->descricao = $row['descricao'];
            $this->data_criacao = $row['data_criacao'];
            return true;
        }
        return false;
    }

    // Funções de validação e sanitização
    private function validarValor($valor) {
        $valor = filter_var($valor, FILTER_VALIDATE_FLOAT);
        return $valor > 0 ? $valor : false;
    }

    private function validarTipoTransacao($tipo) {
        $tipos_validos = ['income', 'expense'];
        return in_array($tipo, $tipos_validos) ? $tipo : false;
    }

    private function validarData($data) {
        $formato = 'Y-m-d';
        $d = DateTime::createFromFormat($formato, $data);
        return $d && $d->format($formato) === $data;
    }

    private function sanitizarTexto($texto) {
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }
}
?>