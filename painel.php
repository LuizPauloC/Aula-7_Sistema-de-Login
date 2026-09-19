<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

$id_logado = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT nome, tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_logado);
$stmt->execute();
$logado = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pode_gerenciar = ($logado['tipo'] === 'creator' || $logado['tipo'] === 'admin');

if ($pode_gerenciar) {
    $usuarios = $conn->query("SELECT id, nome, email, tipo FROM usuarios ORDER BY id");
}
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
    <div class="caixa">
        <h1>Bem-vindo, <?php echo htmlspecialchars($logado['nome']); ?>!</h1>
        <p>Seu tipo de usuário: <strong><?php echo $logado['tipo']; ?></strong></p>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <p class="sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></p>
        <?php endif; ?>

        <?php if ($pode_gerenciar): ?>
            <h2>Gerenciar usuários</h2>
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
                                O creator não pode ser alterado
                            <?php else: ?>
                                <form action="processa_tipo.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                    <select name="tipo">
                                        <option value="admin" <?php if ($u['tipo'] === 'admin') echo 'selected'; ?>>admin</option>
                                        <option value="guest" <?php if ($u['tipo'] === 'guest') echo 'selected'; ?>>guest</option>
                                    </select>
                                    <button type="submit">Salvar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Você está logado como <strong>guest</strong> e não pode gerenciar usuários.</p>
        <?php endif; ?>

        <p><a href="logout.php">Sair</a></p>
    </div>
</body>
</html>
