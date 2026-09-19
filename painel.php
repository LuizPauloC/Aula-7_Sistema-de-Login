<?php
include 'verifica_login.php';

if ($pode_editar) {
    $usuarios = $conn->query("SELECT id, nome, email, tipo FROM usuarios ORDER BY id");
}

$resumo = $conn->query("
    SELECT
        (SELECT COUNT(*) FROM racks) AS racks,
        (SELECT COUNT(*) FROM equipamentos_rack) AS equipamentos,
        (SELECT COUNT(*) FROM ambientes) AS ambientes,
        (SELECT COUNT(*) FROM manutencoes) AS manutencoes
")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <?php include 'menu.php'; ?>

        <h1>Bem-vindo, <?php echo htmlspecialchars($logado['nome']); ?>!</h1>
        <p>Seu tipo de usuário: <strong><?php echo $logado['tipo']; ?></strong></p>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <p class="sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></p>
        <?php endif; ?>

        <h2>Infraestrutura do Bloco B</h2>
        <div class="tabela-scroll">
            <table>
                <tr>
                    <th>Ambientes</th>
                    <th>Racks</th>
                    <th>Equipamentos</th>
                    <th>Manutenções</th>
                </tr>
                <tr>
                    <td><?php echo $resumo['ambientes']; ?></td>
                    <td><?php echo $resumo['racks']; ?></td>
                    <td><?php echo $resumo['equipamentos']; ?></td>
                    <td><?php echo $resumo['manutencoes']; ?></td>
                </tr>
            </table>
        </div>

        <?php if ($pode_editar): ?>
            <h2>Gerenciar usuários</h2>
            <div class="tabela-scroll">
                <table>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Alterar tipo</th>
                    </tr>
                    <?php while ($u = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($u['nome']); ?>
                                <?php if ($u['id'] == $id_logado) echo ' (você)'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo $u['tipo']; ?></td>
                            <td>
                                <?php if ($u['tipo'] === 'creator'): ?>
                                    <span class="vazio">O creator não pode ser alterado</span>
                                <?php else: ?>
                                    <form action="processa_tipo.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <select name="tipo">
                                            <option value="admin" <?php if ($u['tipo'] === 'admin') echo 'selected'; ?>>admin</option>
                                            <option value="guest" <?php if ($u['tipo'] === 'guest') echo 'selected'; ?>>guest</option>
                                        </select>
                                        <button type="submit" class="btn-pequeno">Salvar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php else: ?>
            <p>Você está logado como <strong>guest</strong>: pode consultar os dados da infraestrutura, mas não pode alterar nada nem gerenciar usuários.</p>
        <?php endif; ?>
    </div>
</body>
</html>
