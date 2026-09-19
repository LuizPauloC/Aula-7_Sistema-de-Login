<?php
include 'verifica_login.php';

$ambientes = $conn->query("SELECT * FROM ambientes ORDER BY andar, nome");
$categorias = $conn->query("SELECT * FROM categorias_equipamento ORDER BY nome");
$fabricantes = $conn->query("SELECT * FROM fabricantes ORDER BY nome");
$tecnicos = $conn->query("SELECT * FROM tecnicos ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastros</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="caixa caixa-larga">
        <?php include 'menu.php'; ?>

        <h1>Cadastros de apoio</h1>
        <p>
            Tabelas normalizadas que dão suporte ao restante do sistema. Foram separadas na
            modelagem para evitar que a mesma marca ou categoria fosse escrita de formas
            diferentes em cada registro.
        </p>

        <h2>Ambientes</h2>
        <div class="tabela-scroll">
            <table>
                <tr><th>Nome</th><th>Tipo</th><th>Andar</th></tr>
                <?php while ($a = $ambientes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['nome']); ?></td>
                        <td><?php echo htmlspecialchars($a['tipo']); ?></td>
                        <td><?php echo $a['andar']; ?>º</td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <h2>Categorias de equipamento</h2>
        <div class="tabela-scroll">
            <table>
                <tr><th>Nome</th><th>Descrição</th></tr>
                <?php while ($c = $categorias->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['nome']); ?></td>
                        <td><?php echo htmlspecialchars($c['descricao']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <h2>Fabricantes</h2>
        <div class="tabela-scroll">
            <table>
                <tr><th>Nome</th><th>Contato do suporte</th></tr>
                <?php while ($f = $fabricantes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($f['nome']); ?></td>
                        <td><?php echo htmlspecialchars($f['suporte_contato']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <h2>Técnicos</h2>
        <div class="tabela-scroll">
            <table>
                <tr><th>Nome</th><th>Matrícula</th><th>Email</th></tr>
                <?php while ($t = $tecnicos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($t['nome']); ?></td>
                        <td><?php echo htmlspecialchars($t['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($t['email']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
