<?php
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController {
    private $database;
    private $db;
    private $categoria;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->categoria = new Categoria($this->db);
    }

    // Listar categorias de um usuário
    public function list($user_id) {
        if (!empty($user_id)) {
            $stmt = $this->categoria->readByUsuario($user_id);
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "data" => $categorias
            ));
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "ID do usuário não fornecido."
            ));
        }
    }

    // Listar categorias por tipo
    public function listByType($user_id, $tipo) {
        if (!empty($user_id) && !empty($tipo)) {
            $stmt = $this->categoria->readByTipo($user_id, $tipo);
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode(array(
                "status" => "success",
                "data" => $categorias
            ));
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "ID do usuário e tipo são obrigatórios."
            ));
        }
    }

    // Criar nova categoria personalizada
    public function create($user_id) {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($user_id) && !empty($data->nome) && !empty($data->tipo)) {
            $this->categoria->nome = $data->nome;
            $this->categoria->tipo = $data->tipo;
            $this->categoria->usuario_id = $user_id;

            if ($this->categoria->create()) {
                http_response_code(201);
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Categoria criada com sucesso!"
                ));
            } else {
                http_response_code(503);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Erro ao criar categoria."
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "Dados incompletos. É necessário nome e tipo."
            ));
        }
    }

    // Atualizar categoria (apenas personalizadas)
    public function update($user_id) {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->nome)) {
            // Verificar se a categoria pertence ao usuário
            if (!$this->categoria->pertenceAoUsuario($data->id, $user_id)) {
                http_response_code(403);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Categoria não encontrada ou não pertence ao usuário."
                ));
                return;
            }

            $this->categoria->id = $data->id;
            $this->categoria->nome = $data->nome;

            if ($this->categoria->update()) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Categoria atualizada com sucesso!"
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Categoria não encontrada ou não pode ser atualizada."
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "Dados incompletos. É necessário ID e nome."
            ));
        }
    }

    // Deletar categoria (apenas personalizadas)
    public function delete($user_id) {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id)) {
            // Verificar se a categoria pertence ao usuário
            if (!$this->categoria->pertenceAoUsuario($data->id, $user_id)) {
                http_response_code(403);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Categoria não encontrada ou não pertence ao usuário."
                ));
                return;
            }

            $this->categoria->id = $data->id;

            if ($this->categoria->delete()) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Categoria deletada com sucesso!"
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Categoria não encontrada ou não pode ser deletada."
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "ID da categoria não fornecido."
            ));
        }
    }
}
?>