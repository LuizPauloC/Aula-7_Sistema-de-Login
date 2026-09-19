<?php
include 'verifica_login.php';

$editando = null;
if ($pode_editar && isset($_GET['editar'])) {
    $id_editar = (int) $_GET['editar'];
    $stmt = $conn->prepare("SELECT * FROM racks WHERE id_rack = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $editando = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$ambientes = $conn->query("SELECT id_ambiente, nome, andar FROM ambientes ORDER BY nome");

$racks = $conn->query("
    SELECT r.id_rack, r.identificacao, r.tamanho_us, r.data_instalacao,
           a.nome AS ambiente, a.andar,
           (SELECT COUNT(*) FROM equipamentos_rack e WHERE e.id_rack = r.id_rack) AS equipamentos
    FROM racks r
    JOIN ambientes a ON a.id_ambiente = r.id_ambiente
    ORDER BY r.id_rack
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Racks</title>
    <link rel="icon" href="assets/logo-icone.png">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <?php include 'menu.php'; ?>

        <h1>Racks</h1>
        <p>Gabinetes instalados no Bloco B e o ambiente onde cada um fica.</p>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <p class="sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></p>
        <?php endif; ?>

        <?php if ($pode_editar): ?>
            <h2><?php echo $editando ? 'Editar rack' : 'Novo rack'; ?></h2>
            <form action="processa_racks.php" method="POST">
                <input type="hidden" name="id_rack" value="<?php echo $editando['id_rack'] ?? ''; ?>">

                <label>Identificação:</label>
                <input type="text" name="identificacao" maxlength="50"
                       value="<?php echo htmlspecialchars($editando['identificacao'] ?? ''); ?>" required>

                <label>Tamanho (em Us):</label>
                <input type="number" name="tamanho_us" min="1" max="60"
                       value="<?php echo htmlspecialchars($editando['tamanho_us'] ?? ''); ?>" required>

                <label>Data de instalação:</label>
                <input type="date" name="data_instalacao"
                       value="<?php echo htmlspecialchars($editando['data_instalacao'] ?? ''); ?>">

                <label>Ambiente:</label>
                <select name="id_ambiente" required>
                    <option value="">Selecione...</option>
                    <?php while ($a = $ambientes->fetch_assoc()): ?>
                        <option value="<?php echo $a['id_ambiente']; ?>"
                            <?php if (isset($editando['id_ambiente']) && $editando['id_ambiente'] == $a['id_ambiente']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($a['nome']); ?> (<?php echo $a['andar']; ?>º andar)
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit"><?php echo $editando ? 'Salvar alterações' : 'Cadastrar rack'; ?></button>
                <?php if ($editando): ?>
                    <p><a href="racks.php">Cancelar edição</a></p>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <h2>Racks cadastrados</h2>
        <div class="tabela-scroll">
            <table>
                <tr>
                    <th>Identificação</th>
                    <th>Tamanho</th>
                    <th>Instalação</th>
                    <th>Ambiente</th>
                    <th>Equipamentos</th>
                    <?php if ($pode_editar): ?><th>Ações</th><?php endif; ?>
                </tr>
                <?php while ($r = $racks->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['identificacao']); ?></td>
                        <td><?php echo $r['tamanho_us']; ?>U</td>
                        <td>
                            <?php echo $r['data_instalacao']
                                ? date('d/m/Y', strtotime($r['data_instalacao']))
                                : '—'; ?>
                        </td>
                        <td><?php echo htmlspecialchars($r['ambiente']); ?> (<?php echo $r['andar']; ?>º)</td>
                        <td><?php echo $r['equipamentos']; ?></td>
                        <?php if ($pode_editar): ?>
                            <td>
                                <form action="processa_racks.php" method="POST">
                                    <a href="racks.php?editar=<?php echo $r['id_rack']; ?>">Editar</a>
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id_rack" value="<?php echo $r['id_rack']; ?>">
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
