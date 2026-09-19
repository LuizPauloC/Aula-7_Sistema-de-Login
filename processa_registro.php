<?php
include 'conexao.php';

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

if ($nome === '' || $email === '' || $senha === '') {
    header("Location: register.php?erro=" . urlencode("Preencha todos os campos."));
    exit();
}

if ($senha !== $confirmar) {
    header("Location: register.php?erro=" . urlencode("As senhas não são iguais."));
    exit();
}

if (strlen($senha) < 6) {
    header("Location: register.php?erro=" . urlencode("A senha precisa ter pelo menos 6 caracteres."));
    exit();
}

$total = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$tipo = ($total == 0) ? 'creator' : 'guest';

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $email, $senha_hash, $tipo);

try {
    $stmt->execute();
    header("Location: login.php?sucesso=" . urlencode("Cadastro realizado! Faça o login."));
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        header("Location: register.php?erro=" . urlencode("Este email já está cadastrado."));
    } else {
        header("Location: register.php?erro=" . urlencode("Erro ao cadastrar usuário."));
    }
}

$stmt->close();
$conn->close();
exit();
