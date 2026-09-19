<?php
include 'verifica_login.php';

if (!$pode_editar) {
    header("Location: tecnicos.php?erro=" . urlencode("Você não tem permissão para alterar os técnicos."));
    exit();
}

$id = (int) ($_POST['id_tecnico'] ?? 0);

if (($_POST['acao'] ?? '') === 'excluir') {
    try {
        $stmt = $conn->prepare("DELETE FROM tecnicos WHERE id_tecnico = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: tecnicos.php?sucesso=" . urlencode("Técnico excluído."));
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() === 1451) {
            header("Location: tecnicos.php?erro=" . urlencode("Não é possível excluir: este técnico tem manutenções registradas no histórico."));
        } else {
            header("Location: tecnicos.php?erro=" . urlencode("Erro ao excluir o técnico."));
        }
    }
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$matricula = trim($_POST['matricula'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($nome === '' || $matricula === '') {
    header("Location: tecnicos.php?erro=" . urlencode("Preencha o nome e a matrícula."));
    exit();
}

if ($email === '') {
    $email = null;
}

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE tecnicos SET nome = ?, matricula = ?, email = ? WHERE id_tecnico = ?");
    $stmt->bind_param("sssi", $nome, $matricula, $email, $id);
    $mensagem = "Técnico atualizado.";
} else {
    $stmt = $conn->prepare("INSERT INTO tecnicos (nome, matricula, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $matricula, $email);
    $mensagem = "Técnico cadastrado.";
}

try {
    $stmt->execute();
    header("Location: tecnicos.php?sucesso=" . urlencode($mensagem));
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        header("Location: tecnicos.php?erro=" . urlencode("Já existe um técnico com a matrícula $matricula."));
    } else {
        header("Location: tecnicos.php?erro=" . urlencode("Erro ao salvar o técnico."));
    }
}

$stmt->close();
$conn->close();
exit();
