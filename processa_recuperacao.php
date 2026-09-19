<?php
include 'conexao.php';
session_start();

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

$_SESSION['old'] = ['email' => $email];

if ($senha !== $confirmar) {
    header("Location: recuperar_senha.php?erro=" . urlencode("As senhas não são iguais."));
    exit();
}

if (strlen($senha) < 6) {
    header("Location: recuperar_senha.php?erro=" . urlencode("A senha precisa ter pelo menos 6 caracteres."));
    exit();
}

$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    header("Location: recuperar_senha.php?erro=" . urlencode("Email não encontrado."));
    exit();
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
$stmt->bind_param("si", $senha_hash, $usuario['id']);
$stmt->execute();
$stmt->close();
$conn->close();

unset($_SESSION['old']);
header("Location: login.php?sucesso=" . urlencode("Senha alterada! Faça o login."));
exit();
