<?php
include 'verifica_login.php';

$editando = null;
if ($pode_editar && isset($_GET['editar'])) {
    $id_editar = (int) $_GET['editar'];
    $stmt = $conn->prepare("SELECT * FROM manutencoes WHERE id_manutencao = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $editando = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$lista_racks = $conn->query("SELECT id_rack, identificacao FROM racks ORDER BY identificacao");
$lista_tecnicos = $conn->query("SELECT id_tecnico, nome FROM tecnicos ORDER BY nome");

$manutencoes = $conn->query("
    SELECT m.id_manutencao, m.data_servico, m.descricao,
           r.identificacao AS rack,
           t.nome AS tecnico, t.matricula
    FROM manutencoes m
    JOIN racks r ON r.id_rack = m.id_rack
    JOIN tecnicos t ON t.id_tecnico = m.id_tecnico
    ORDER BY m.data_servico DESC
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenções</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <?php include 'menu.php'; ?>

        <h1>Manutenções</h1>
        <p>Histórico de intervenções feitas pela equipe de TI nos racks do Bloco B.</p>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <p class="sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></p>
        <?php endif; ?>

        <?php if ($pode_editar): ?>
            <h2><?php echo $editando ? 'Editar manutenção' : 'Registrar manutenção'; ?></h2>
            <form action="processa_manutencoes.php" method="POST">
                <input type="hidden" name="id_manutencao" value="<?php echo $editando['id_manutencao'] ?? ''; ?>">

                <label>Rack:</label>
                <select name="id_rack" required>
                    <option value="">Selecione...</option>
                    <?php while ($r = $lista_racks->fetch_assoc()): ?>
                        <option value="<?php echo $r['id_rack']; ?>"
                            <?php if (isset($editando['id_rack']) && $editando['id_rack'] == $r['id_rack']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($r['identificacao']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Técnico responsável:</label>
                <select name="id_tecnico" required>
                    <option value="">Selecione...</option>
                    <?php while ($t = $lista_tecnicos->fetch_assoc()): ?>
                        <option value="<?php echo $t['id_tecnico']; ?>"
                            <?php if (isset($editando['id_tecnico']) && $editando['id_tecnico'] == $t['id_tecnico']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($t['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Data do serviço:</label>
                <input type="date" name="data_servico"
                       value="<?php echo htmlspecialchars($editando['data_servico'] ?? ''); ?>" required>

                <label>Descrição do serviço:</label>
                <textarea name="descricao" required><?php echo htmlspecialchars($editando['descricao'] ?? ''); ?></textarea>

                <button type="submit"><?php echo $editando ? 'Salvar alterações' : 'Registrar manutenção'; ?></button>
                <?php if ($editando): ?>
                    <p><a href="manutencoes.php">Cancelar edição</a></p>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <h2>Histórico</h2>
        <div class="tabela-scroll">
            <table>
                <tr>
                    <th>Data</th>
                    <th>Rack</th>
                    <th>Técnico</th>
                    <th>Descrição</th>
                    <?php if ($pode_editar): ?><th>Ações</th><?php endif; ?>
                </tr>
                <?php while ($m = $manutencoes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($m['data_servico'])); ?></td>
                        <td><?php echo htmlspecialchars($m['rack']); ?></td>
                        <td><?php echo htmlspecialchars($m['tecnico']); ?> (<?php echo htmlspecialchars($m['matricula']); ?>)</td>
                        <td><?php echo htmlspecialchars($m['descricao']); ?></td>
                        <?php if ($pode_editar): ?>
                            <td>
                                <form action="processa_manutencoes.php" method="POST">
                                    <a href="manutencoes.php?editar=<?php echo $m['id_manutencao']; ?>">Editar</a>
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id_manutencao" value="<?php echo $m['id_manutencao']; ?>">
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
