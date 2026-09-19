<?php
include 'verifica_login.php';

$editando = null;
if ($pode_editar && isset($_GET['editar'])) {
    $id_editar = (int) $_GET['editar'];
    $stmt = $conn->prepare("SELECT * FROM equipamentos_rack WHERE id_equipamento = ?");
    $stmt->bind_param("i", $id_editar);
    $stmt->execute();
    $editando = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$lista_racks = $conn->query("SELECT id_rack, identificacao FROM racks ORDER BY identificacao");
$lista_categorias = $conn->query("SELECT id_categoria, nome FROM categorias_equipamento ORDER BY nome");
$lista_fabricantes = $conn->query("SELECT id_fabricante, nome FROM fabricantes ORDER BY nome");

$equipamentos = $conn->query("
    SELECT e.id_equipamento, e.quantidade_portas,
           r.identificacao AS rack,
           c.nome AS categoria,
           f.nome AS fabricante
    FROM equipamentos_rack e
    JOIN racks r ON r.id_rack = e.id_rack
    JOIN categorias_equipamento c ON c.id_categoria = e.id_categoria
    JOIN fabricantes f ON f.id_fabricante = e.id_fabricante
    ORDER BY r.identificacao, c.nome
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipamentos</title>
    <link rel="icon" href="assets/logo-icone.png">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <?php include 'menu.php'; ?>

        <h1>Equipamentos</h1>
        <p>Dispositivos instalados dentro dos racks, com categoria e fabricante.</p>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <p class="sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></p>
        <?php endif; ?>

        <?php if ($pode_editar): ?>
            <h2><?php echo $editando ? 'Editar equipamento' : 'Novo equipamento'; ?></h2>
            <form action="processa_equipamentos.php" method="POST">
                <input type="hidden" name="id_equipamento" value="<?php echo $editando['id_equipamento'] ?? ''; ?>">

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

                <label>Categoria:</label>
                <select name="id_categoria" required>
                    <option value="">Selecione...</option>
                    <?php while ($c = $lista_categorias->fetch_assoc()): ?>
                        <option value="<?php echo $c['id_categoria']; ?>"
                            <?php if (isset($editando['id_categoria']) && $editando['id_categoria'] == $c['id_categoria']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($c['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Fabricante:</label>
                <select name="id_fabricante" required>
                    <option value="">Selecione...</option>
                    <?php while ($f = $lista_fabricantes->fetch_assoc()): ?>
                        <option value="<?php echo $f['id_fabricante']; ?>"
                            <?php if (isset($editando['id_fabricante']) && $editando['id_fabricante'] == $f['id_fabricante']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($f['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Quantidade de portas (deixe vazio se não tiver):</label>
                <input type="number" name="quantidade_portas" min="0" max="500"
                       value="<?php echo htmlspecialchars($editando['quantidade_portas'] ?? ''); ?>">

                <button type="submit"><?php echo $editando ? 'Salvar alterações' : 'Cadastrar equipamento'; ?></button>
                <?php if ($editando): ?>
                    <p><a href="equipamentos.php">Cancelar edição</a></p>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        <h2>Equipamentos cadastrados</h2>
        <div class="tabela-scroll">
            <table>
                <tr>
                    <th>Rack</th>
                    <th>Categoria</th>
                    <th>Fabricante</th>
                    <th>Portas</th>
                    <?php if ($pode_editar): ?><th>Ações</th><?php endif; ?>
                </tr>
                <?php while ($e = $equipamentos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($e['rack']); ?></td>
                        <td><?php echo htmlspecialchars($e['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($e['fabricante']); ?></td>
                        <td>
                            <?php echo $e['quantidade_portas'] !== null
                                ? $e['quantidade_portas']
                                : '—'; ?>
                        </td>
                        <?php if ($pode_editar): ?>
                            <td>
                                <form action="processa_equipamentos.php" method="POST">
                                    <a href="equipamentos.php?editar=<?php echo $e['id_equipamento']; ?>">Editar</a>
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id_equipamento" value="<?php echo $e['id_equipamento']; ?>">
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
