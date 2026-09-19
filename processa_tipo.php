<?php
include 'verifica_login.php';

if (!$pode_editar) {
    header("Location: painel.php?erro=" . urlencode("Você não tem permissão para alterar tipos."));
    exit();
}

$id_alvo = (int) ($_POST['id'] ?? 0);
$novo_tipo = $_POST['tipo'] ?? '';

if ($novo_tipo !== 'admin' && $novo_tipo !== 'guest') {
    header("Location: painel.php?erro=" . urlencode("Tipo inválido."));
    exit();
}

$stmt = $conn->prepare("SELECT tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_alvo);
$stmt->execute();
$alvo = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$alvo) {
    header("Location: painel.php?erro=" . urlencode("Usuário não encontrado."));
    exit();
}

if ($alvo['tipo'] === 'creator') {
    header("Location: painel.php?erro=" . urlencode("O creator não pode ser retirado do cargo."));
    exit();
}

$stmt = $conn->prepare("UPDATE usuarios SET tipo = ? WHERE id = ?");
$stmt->bind_param("si", $novo_tipo, $id_alvo);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: painel.php?sucesso=" . urlencode("Tipo de usuário atualizado."));
exit();
