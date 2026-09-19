<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="caixa">
        <h2>Recuperar senha</h2>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <form action="processa_recuperacao.php" method="POST">
            <label>Email cadastrado:</label>
            <input type="email" name="email" required>

            <label>Nova senha:</label>
            <input type="password" name="senha" required>

            <label>Confirmar nova senha:</label>
            <input type="password" name="confirmar" required>

            <button type="submit">Salvar nova senha</button>
        </form>

        <p><a href="login.php">Voltar ao login</a></p>
    </div>
</body>
</html>
