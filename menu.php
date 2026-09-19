<nav class="menu">
    <a href="painel.php">Painel</a>
    <a href="racks.php">Racks</a>
    <a href="equipamentos.php">Equipamentos</a>
    <a href="manutencoes.php">Manutenções</a>
    <a href="tecnicos.php">Técnicos</a>
    <a href="cadastros.php">Cadastros</a>
    <span class="menu-usuario">
        <?php echo htmlspecialchars($logado['nome']); ?> (<?php echo $logado['tipo']; ?>) &middot;
        <a href="logout.php">Sair</a>
    </span>
</nav>
