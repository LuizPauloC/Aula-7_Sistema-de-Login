<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="caixa">
        <h2>Cadastrar novo usuário</h2>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <form action="processa_registro.php" method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Senha:</label>
            <input type="password" name="senha" required>

            <label>Confirmar senha:</label>
            <input type="password" name="confirmar" required>

            <button type="submit">Cadastrar</button>
        </form>

        <p><a href="login.php">Já tenho uma conta</a></p>
    </div>
</body>
</html>
