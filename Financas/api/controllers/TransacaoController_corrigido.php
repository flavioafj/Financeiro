<?php
require_once __DIR__ . '/../models/Transacao.php';

class TransacaoController {
    private $database;
    private $db;
    private $transacao;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->transacao = new Transacao($this->db);
    }

    // Criar nova transação
    public function create($user_id) {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($user_id) && !empty($data->categoria_id) && !empty($data->valor) && !empty($data->tipo) && !empty($data->data)) {
            // Validações
            $valor = $this->validarValor($data->valor);
            $tipo = $this->validarTipoTransacao($data->tipo);
            $data_valida = $this->validarData($data->data);
            
            if (!$valor || !$tipo || !$data_valida) {
                http_response_code(400);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Dados inválidos. Verifique valor, tipo e data."
                ));
                return;
            }

            $this->transacao->usuario_id = $user_id;
            $this->transacao->categoria_id = $data->categoria_id;
            $this->transacao->valor = $valor;
            $this->transacao->tipo = $tipo;
            $this->transacao->data = $data->data;
            $this->transacao->descricao = !empty($data->descricao) ? $this->sanitizarTexto($data->descricao) : null;

            if ($this->transacao->create()) {
                http_response_code(201);
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Transação criada com sucesso!"
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Erro ao criar transação. Verifique os dados."
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "Dados incompletos. É necessário categoria_id, valor, tipo e data."
            ));
        }
    }

    // Listar transações de um usuário
    public function list($user_id, $limite = 50, $offset = 0) {
        if (!empty($user_id)) {
            $stmt = $this->transacao->readByUsuario($user_id, $limite, $offset);
            $transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "data" => $transacoes
            ));
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "ID do usuário não fornecido."
            ));
        }
    }

    // Obter resumo financeiro do usuário
    public function getResumo($user_id) {
        if (!empty($user_id)) {
            $resumo = $this->transacao->getResumo($user_id);

            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "data" => $resumo
            ));
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "ID do usuário não fornecido."
            ));
        }
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