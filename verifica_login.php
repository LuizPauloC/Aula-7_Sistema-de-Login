<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

$id_logado = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT nome, tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_logado);
$stmt->execute();
$logado = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pode_editar = ($logado['tipo'] === 'creator' || $logado['tipo'] === 'admin');
