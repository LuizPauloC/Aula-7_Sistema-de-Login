<?php
include 'verifica_login.php';

$editando = null;
if ($pode_editar && isset($_GET['editar'])) {
    $id_editar = (int) $_GET['editar'];
    $stmt = $conn->prepare("SELECT * FROM tecnicos WHERE id_tecnico = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $editando = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$tecnicos = $conn->query("
    SELECT t.id_tecnico, t.nome, t.matricula, t.email,
           (SELECT COUNT(*) FROM manutencoes m WHERE m.id_tecnico = t.id_tecnico) AS manutencoes
    FROM tecnicos t
    ORDER BY t.nome
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Técnicos</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <?php include 'menu.php'; ?>

        <h1>Técnicos</h1>
        <p>Equipe de TI responsável pela manutenção da infraestrutura do Bloco B.</p>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <p class="sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></p>
        <?php endif; ?>

        <?php if ($pode_editar): ?>
            <h2><?php echo $editando ? 'Editar técnico' : 'Novo técnico'; ?></h2>
            <form action="processa_tecnicos.php" method="POST">
                <input type="hidden" name="id_tecnico" value="<?php echo $editando['id_tecnico'] ?? ''; ?>">

                <label>Nome:</label>
                <input type="text" name="nome" maxlength="100"
                       value="<?php echo htmlspecialchars($editando['nome'] ?? ''); ?>" required>

                <label>Matrícula:</label>
                <input type="text" name="matricula" maxlength="20"
                       value="<?php echo htmlspecialchars($editando['matricula'] ?? ''); ?>" required>

                <label>Email:</label>
                <input type="email" name="email" maxlength="100"
                       value="<?php echo htmlspecialchars($editando['email'] ?? ''); ?>">

                <button type="submit"><?php echo $editando ? 'Salvar alterações' : 'Cadastrar técnico'; ?></button>
                <?php if ($editando): ?>
                    <p><a href="tecnicos.php">Cancelar edição</a></p>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <h2>Técnicos cadastrados</h2>
        <div class="tabela-scroll">
            <table>
                <tr>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>Email</th>
                    <th>Manutenções</th>
                    <?php if ($pode_editar): ?><th>Ações</th><?php endif; ?>
                </tr>
                <?php while ($t = $tecnicos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($t['nome']); ?></td>
                        <td><?php echo htmlspecialchars($t['matricula']); ?></td>
                        <td>
                            <?php echo $t['email'] !== null && $t['email'] !== ''
                                ? htmlspecialchars($t['email'])
                                : '—'; ?>
                        </td>
                        <td><?php echo $t['manutencoes']; ?></td>
                        <?php if ($pode_editar): ?>
                            <td>
                                <form action="processa_tecnicos.php" method="POST">
                                    <a href="tecnicos.php?editar=<?php echo $t['id_tecnico']; ?>">Editar</a>
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id_tecnico" value="<?php echo $t['id_tecnico']; ?>">
                                    <button type="submit" class="btn-pequeno btn-excluir">Excluir</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
