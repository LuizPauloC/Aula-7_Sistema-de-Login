<?php
session_start();
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="assets/logo-icone.png">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="topo-logo">
        <img src="assets/logo-completa.png" alt="InfraRede UNESC — Bloco B, Gerenciamento de Racks">
    </div>

    <div class="caixa">
        <h2>Login</h2>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <p class="sucesso"><?php echo htmlspecialchars($_GET['sucesso']); ?></p>
        <?php endif; ?>

        <form action="processa_login.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>

            <label for="senha">Senha:</label>
            <div class="campo-senha">
                <input type="password" name="senha" id="senha" required>
                <button type="button" class="olho" data-alvo="senha" title="Mostrar senha">
                    <img src="assets/olho-fechado.png" alt="Mostrar senha">
                </button>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p><a href="register.php">Criar uma conta</a></p>
        <p><a href="recuperar_senha.php">Esqueci minha senha</a></p>
    </div>

    <script src="script.js"></script>
</body>
</html>
