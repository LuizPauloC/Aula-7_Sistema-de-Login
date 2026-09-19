document.querySelectorAll('.olho').forEach(function (botao) {
    botao.addEventListener('click', function () {
        var campo = document.getElementById(botao.dataset.alvo);
        var imagem = botao.querySelector('img');

        if (campo.type === 'password') {
            campo.type = 'text';
            imagem.src = 'assets/olho-aberto.png';
            imagem.alt = 'Esconder senha';
            botao.title = 'Esconder senha';
        } else {
            campo.type = 'password';
            imagem.src = 'assets/olho-fechado.png';
            imagem.alt = 'Mostrar senha';
            botao.title = 'Mostrar senha';
        }
    });
});
