<?php
include 'verifica_login.php';

if (!$pode_editar) {
    header("Location: racks.php?erro=" . urlencode("Você não tem permissão para alterar os racks."));
    exit();
}

$id = (int) ($_POST['id_rack'] ?? 0);

if (($_POST['acao'] ?? '') === 'excluir') {
    try {
        $stmt = $conn->prepare("DELETE FROM racks WHERE id_rack = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: racks.php?sucesso=" . urlencode("Rack excluído."));
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() === 1451) {
            header("Location: racks.php?erro=" . urlencode("Não é possível excluir: este rack tem equipamentos ou manutenções ligados a ele."));
        } else {
            header("Location: racks.php?erro=" . urlencode("Erro ao excluir o rack."));
        }
    }
    exit();
}

$identificacao = trim($_POST['identificacao'] ?? '');
$tamanho_us = (int) ($_POST['tamanho_us'] ?? 0);
$data_instalacao = $_POST['data_instalacao'] ?? '';
$id_ambiente = (int) ($_POST['id_ambiente'] ?? 0);

if ($identificacao === '' || $tamanho_us <= 0 || $id_ambiente <= 0) {
    header("Location: racks.php?erro=" . urlencode("Preencha identificação, tamanho e ambiente."));
    exit();
}

if ($data_instalacao === '') {
    $data_instalacao = null;
}

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE racks SET identificacao = ?, tamanho_us = ?, data_instalacao = ?, id_ambiente = ? WHERE id_rack = ?");
    $stmt->bind_param("sisii", $identificacao, $tamanho_us, $data_instalacao, $id_ambiente, $id);
    $mensagem = "Rack atualizado.";
} else {
    $stmt = $conn->prepare("INSERT INTO racks (identificacao, tamanho_us, data_instalacao, id_ambiente) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sisi", $identificacao, $tamanho_us, $data_instalacao, $id_ambiente);
    $mensagem = "Rack cadastrado.";
}

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: racks.php?sucesso=" . urlencode($mensagem));
exit();
