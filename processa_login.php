<?php
include 'conexao.php';
session_start();

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

$stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario'] = $usuario['nome'];
    header("Location: painel.php");
} else {
    header("Location: login.php?erro=" . urlencode("Email ou senha incorretos."));
}

exit();
