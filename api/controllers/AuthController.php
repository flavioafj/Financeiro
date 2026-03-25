<?php
// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $database;
    private $db;
    private $usuario;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    // Login de usuário
    public function login() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->email) && !empty($data->senha)) {
            $this->usuario->email = $data->email;
            $this->usuario->senha = $data->senha;

            if ($this->usuario->login()) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Login realizado com sucesso!",
                    "data" => array(
                        "id" => $this->usuario->id,
                        "email" => $this->usuario->email,
                        "nome" => $this->usuario->nome
                    )
                ));
            } else {
                http_response_code(401);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Credenciais inválidas!"
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "Dados incompletos. É necessário email e senha."
            ));
        }
    }

    // Cadastro de usuário
    public function register() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->email) && !empty($data->senha) && !empty($data->nome)) {
            $this->usuario->email = $data->email;
            $this->usuario->senha = $data->senha;
            $this->usuario->nome = $data->nome;

            if ($this->usuario->emailExiste()) {
                http_response_code(400);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Email já cadastrado!"
                ));
            } else {
                if ($this->usuario->create()) {
                    http_response_code(201);
                    echo json_encode(array(
                        "status" => "success",
                        "message" => "Usuário cadastrado com sucesso!"
                    ));
                } else {
                    http_response_code(503);
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Erro ao cadastrar usuário."
                    ));
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "Dados incompletos. É necessário email, senha e nome."
            ));
        }
    }

    // Obter dados do usuário
    public function getUser($user_id) {
        if (!empty($user_id)) {
            $this->usuario->id = $user_id;

            if ($this->usuario->readOne()) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => "success",
                    "data" => array(
                        "id" => $this->usuario->id,
                        "email" => $this->usuario->email,
                        "nome" => $this->usuario->nome,
                        "data_criacao" => $this->usuario->data_criacao
                    )
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Usuário não encontrado."
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "status" => "error",
                "message" => "ID do usuário não fornecido."
            ));
        }
    }

    // Logout de usuário (para compatibilidade futura com sessões)
    public function logout() {
        // Atualmente, o logout é feito apenas no frontend (limpando localStorage)
        // Esta função serve para manter a consistência da API e preparar para futuras implementações
        // como invalidação de tokens JWT ou limpeza de sessões
        
        http_response_code(200);
        echo json_encode(array(
            "status" => "success",
            "message" => "Logout realizado com sucesso!"
        ));
    }
}
?>
