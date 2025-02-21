<?php
require __DIR__ . '/vendor/autoload.php';
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailTo;

header('Content-Type: application/json');

$host     = '127.0.0.1';
$dbname   = 'login_bas';
$user     = 'root';
$pass     = '';
$charset  = 'utf8mb4';
$dsn      = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options  = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro na conexão com o banco de dados"]);
    exit;
}

function generateCode($length = 6) {
    return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function sendResetEmail($toEmail, $code) {
    $apiKey = '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'; // Substitua com sua chave de API do Brevo
    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
    $apiInstance = new TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

    $email = new SendSmtpEmail();
    $email->setSender(['email' => 'raulcrezar@gmail.com', 'name' => 'devraulczr']);
    $email->setTo([new SendSmtpEmailTo(['email' => $toEmail])]);
    $email->setSubject('Código para redefinição de senha');
    $email->setHtmlContent("Seu código para redefinição de senha é: <b>$code</b>");
    $email->setTextContent("Seu código para redefinição de senha é: $code");

    try {
        $response = $apiInstance->sendTransacEmail($email);
        return true;
    } catch (Exception $e) {
        return "Erro ao enviar o e-mail: " . $e->getMessage();
    }
}

$action = $_GET['action'] ?? '';

if ($action === 'forgot_password' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'] ?? '';
    
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(["error" => "Email é obrigatório"]);
        exit;
    }

    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(["error" => "Email inválido"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    if (!$usuario) {
        http_response_code(400);
        echo json_encode(["error" => "Usuário não encontrado"]);
        exit;
    }

    $code = generateCode();
    $expireAt = date('Y-m-d H:i:s', strtotime('+30 minutos'));

    $stmt = $pdo->prepare("UPDATE usuarios SET reset_code = ?, code_expires_at = ? WHERE email = ?");
    $stmt->execute([$code, $expireAt, $email]);

    // Envia o e-mail
    $result = sendResetEmail($email, $code);
    if ($result !== true) {
        http_response_code(500);
        echo json_encode(["error" => $result]);
        exit;
    }

    echo json_encode(["message" => "Código gerado e enviado para $email"]);
    exit;
} elseif ($action === 'reset_password' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'] ?? '';
    $code = $_GET['code'] ?? '';
    echo $code;
    $newPassword = $_GET['new_password'] ?? '';

    if (empty($email) || empty($code) || empty($newPassword)) {
        http_response_code(400);
        echo json_encode(["error" => "Email, código e nova senha são obrigatórios"]);
        exit;
    }

    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(["error" => "Email inválido"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT reset_code, code_expires_at FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    if (!$usuario) {
        http_response_code(404);
        echo json_encode(["error" => "Usuário não encontrado"]);
        exit;
    }
    echo $usuario["reset_code"];
    if ($usuario['reset_code'] !== $code){
        http_response_code(400);
        echo json_encode(["error" => "Código inválido ou expirado"]);
        exit;
    }

    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ?, reset_code = NULL, code_expires_at = NULL WHERE email = ?");
    $stmt->execute([$newPasswordHash, $email]);

    echo json_encode(["message" => "Senha redefinida com sucesso"]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint não encontrado ou método não permitido"]);
    exit;
}
?>
