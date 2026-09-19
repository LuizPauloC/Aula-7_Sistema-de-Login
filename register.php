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
    <title>Cadastro</title>
    <link rel="icon" href="assets/logo-icone.png">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="topo-logo">
        <img src="assets/logo-completa.jpg" alt="InfraRede UNESC — Bloco B, Gerenciamento de Racks">
    </div>

    <div class="caixa">
        <h2>Cadastrar novo usuário</h2>

        <?php if (isset($_GET['erro'])): ?>
            <p class="erro"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>

        <form action="processa_registro.php" method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($old['nome'] ?? ''); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>

            <label for="senha">Senha:</label>
            <div class="campo-senha">
                <input type="password" name="senha" id="senha" required>
                <button type="button" class="olho" data-alvo="senha" title="Mostrar senha">
                    <img src="assets/olho-fechado.png" alt="Mostrar senha">
                </button>
            </div>

            <label for="confirmar">Confirmar senha:</label>
            <div class="campo-senha">
                <input type="password" name="confirmar" id="confirmar" required>
                <button type="button" class="olho" data-alvo="confirmar" title="Mostrar senha">
                    <img src="assets/olho-fechado.png" alt="Mostrar senha">
                </button>
            </div>

            <button type="submit">Cadastrar</button>
        </form>

        <p><a href="login.php">Já tenho uma conta</a></p>
    </div>

    <script src="script.js"></script>
</body>
</html>
