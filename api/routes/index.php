<?php

// Configurações de cabeçalho
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Permitir requisições OPTIONS (para CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir arquivos necessários
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CategoriaController.php';
require_once __DIR__ . '/../controllers/TransacaoController.php';

// Obter método HTTP e caminho da URL
$method = $_SERVER['REQUEST_METHOD'];

$pathInfo = '';
if (!empty($_SERVER['PATH_INFO'])) {
    $pathInfo = $_SERVER['PATH_INFO'];
} elseif (!empty($_SERVER['REQUEST_URI'])) {
    $pathInfo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // remover prefixo do projeto, se houver
    $pathInfo = preg_replace('#^/Financas/api#', '', $pathInfo);
}

$request = explode('/', trim($pathInfo, '/'));

// Função para obter parâmetros da URL
function getParam($index, $default = null) {
    global $request;
    return isset($request[$index]) ? $request[$index] : $default;
}

// Função para obter parâmetro da query string
function getQuery($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

// Roteamento da API
try {
    switch ($method) {
        // Rotas de Autenticação
        case 'POST':
            if (getParam(0) === 'auth' && getParam(1) === 'login') {
                $controller = new AuthController();
                $controller->login();
            } elseif (getParam(0) === 'auth' && getParam(1) === 'register') {
                $controller = new AuthController();
                $controller->register();
            } elseif (getParam(0) === 'auth' && getParam(1) === 'logout') {
                $controller = new AuthController();
                $controller->logout();
            } elseif (getParam(0) === 'categories') {
                $user_id = getQuery('user_id');
                $controller = new CategoriaController();
                $controller->create($user_id);
            } elseif (getParam(0) === 'transactions') {
                $user_id = getQuery('user_id');
                $controller = new TransacaoController();
                $controller->create($user_id);
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Rota não encontrada."
                ));
            }
            break;

        case 'GET':
            if (getParam(0) === 'auth' && getParam(1) === 'user') {
                $user_id = getQuery('user_id');
                $controller = new AuthController();
                $controller->getUser($user_id);
            } elseif (getParam(0) === 'categories') {
                $user_id = getQuery('user_id');
                $tipo = getQuery('type');
                
                if ($tipo) {
                    $controller = new CategoriaController();
                    $controller->listByType($user_id, $tipo);
                } else {
                    $controller = new CategoriaController();
                    $controller->list($user_id);
                }
            } elseif (getParam(0) === 'transactions') {
                $user_id = getQuery('user_id');
                $transacao_id = getParam(1);
                
                if ($transacao_id) {
                    // Obter transação por ID
                    $controller = new TransacaoController();
                    $controller->getById($user_id, $transacao_id);
                } else {
                    // Listar transações
                    $limite = getQuery('limit', 50);
                    $offset = getQuery('offset', 0);
                    $data_inicio = getQuery('start_date');
                    $data_fim = getQuery('end_date');
                    $categoria_id = getQuery('category_id');
                    
                    $controller = new TransacaoController();
                    
                    if ($data_inicio && $data_fim) {
                        $controller->listByPeriod($user_id, $data_inicio, $data_fim);
                    } elseif ($categoria_id) {
                        $controller->listByCategory($user_id, $categoria_id);
                    } else {
                        $controller->list($user_id, $limite, $offset);
                    }
                }
            } elseif (getParam(0) === 'dashboard') {
                $user_id = getQuery('user_id');
                $controller = new TransacaoController();
                $controller->getResumo($user_id);
            } elseif (getParam(0) === 'reports') {
                $user_id = getQuery('user_id');
                $controller = new TransacaoController();
                $controller->getResumoPorCategoria($user_id);
            } elseif (getParam(0) === 'api' && getParam(1) === 'transactions' && getParam(2) === 'add') {
                // Rota para registro via GET conforme solicitado
                $controller = new TransacaoController();
                $controller->addViaGet();
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Rota não encontrada."
                ));
            }
            break;

        case 'PUT':
            if (getParam(0) === 'categories') {
                $user_id = getQuery('user_id');
                $controller = new CategoriaController();
                $controller->update($user_id);
            } elseif (getParam(0) === 'transactions') {
                $user_id = getQuery('user_id');
                $transacao_id = getParam(1);
                $controller = new TransacaoController();
                $controller->update($user_id, $transacao_id);
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Rota não encontrada."
                ));
            }
            break;

        case 'DELETE':
            if (getParam(0) === 'categories') {
                $user_id = getQuery('user_id');
                $controller = new CategoriaController();
                $controller->delete($user_id);
            } elseif (getParam(0) === 'transactions') {
                $user_id = getQuery('user_id');
                $transacao_id = getParam(1);
                $controller = new TransacaoController();
                $controller->delete($user_id, $transacao_id);
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Rota não encontrada."
                ));
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(array(
                "status" => "error",
                "message" => "Método não permitido."
            ));
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => "Erro interno do servidor: " . $e->getMessage()
    ));
}
?>