<?php
include 'verifica_login.php';

if (!$pode_editar) {
    header("Location: equipamentos.php?erro=" . urlencode("Você não tem permissão para alterar os equipamentos."));
    exit();
}

$id = (int) ($_POST['id_equipamento'] ?? 0);

if (($_POST['acao'] ?? '') === 'excluir') {
    $stmt = $conn->prepare("DELETE FROM equipamentos_rack WHERE id_equipamento = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: equipamentos.php?sucesso=" . urlencode("Equipamento excluído."));
    exit();
}

$id_rack = (int) ($_POST['id_rack'] ?? 0);
$id_categoria = (int) ($_POST['id_categoria'] ?? 0);
$id_fabricante = (int) ($_POST['id_fabricante'] ?? 0);
$portas = trim($_POST['quantidade_portas'] ?? '');

if ($id_rack <= 0 || $id_categoria <= 0 || $id_fabricante <= 0) {
    header("Location: equipamentos.php?erro=" . urlencode("Selecione rack, categoria e fabricante."));
    exit();
}

$portas = ($portas === '') ? null : (int) $portas;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE equipamentos_rack SET id_rack = ?, id_categoria = ?, id_fabricante = ?, quantidade_portas = ? WHERE id_equipamento = ?");
    $stmt->bind_param("iiiii", $id_rack, $id_categoria, $id_fabricante, $portas, $id);
    $mensagem = "Equipamento atualizado.";
} else {
    $stmt = $conn->prepare("INSERT INTO equipamentos_rack (id_rack, id_categoria, id_fabricante, quantidade_portas) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $id_rack, $id_categoria, $id_fabricante, $portas);
    $mensagem = "Equipamento cadastrado.";
}

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: equipamentos.php?sucesso=" . urlencode($mensagem));
exit();
