<?php
include 'verifica_login.php';

if (!$pode_editar) {
    header("Location: manutencoes.php?erro=" . urlencode("Você não tem permissão para alterar as manutenções."));
    exit();
}

$id = (int) ($_POST['id_manutencao'] ?? 0);

if (($_POST['acao'] ?? '') === 'excluir') {
    $stmt = $conn->prepare("DELETE FROM manutencoes WHERE id_manutencao = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manutencoes.php?sucesso=" . urlencode("Manutenção excluída."));
    exit();
}

$id_rack = (int) ($_POST['id_rack'] ?? 0);
$id_tecnico = (int) ($_POST['id_tecnico'] ?? 0);
$data_servico = $_POST['data_servico'] ?? '';
$descricao = trim($_POST['descricao'] ?? '');

if ($id_rack <= 0 || $id_tecnico <= 0 || $data_servico === '' || $descricao === '') {
    header("Location: manutencoes.php?erro=" . urlencode("Preencha rack, técnico, data e descrição."));
    exit();
}

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE manutencoes SET id_rack = ?, id_tecnico = ?, data_servico = ?, descricao = ? WHERE id_manutencao = ?");
    $stmt->bind_param("iissi", $id_rack, $id_tecnico, $data_servico, $descricao, $id);
    $mensagem = "Manutenção atualizada.";
} else {
    $stmt = $conn->prepare("INSERT INTO manutencoes (id_rack, id_tecnico, data_servico, descricao) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $id_rack, $id_tecnico, $data_servico, $descricao);
    $mensagem = "Manutenção registrada.";
}

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: manutencoes.php?sucesso=" . urlencode($mensagem));
exit();
