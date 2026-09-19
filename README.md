# Sistema de Login em PHP — Aula 7

Sistema de autenticação e gerenciamento de sessões desenvolvido para a disciplina de
**Desenvolvimento Back-End** (Aula 7 — Autenticação e Sessões no PHP).

O sistema tem cadastro de usuários, login com sessão, página restrita, recuperação de senha
e três níveis de permissão (**creator**, **admin** e **guest**) gerenciados dentro do painel.

---

## Requisitos

| Item | Versão usada | Observação |
|---|---|---|
| PHP | 8.2 | Precisa ser 8.0 ou superior |
| MySQL ou MariaDB | 8.0 | O MariaDB que vem no XAMPP também funciona |
| Servidor web | Apache (XAMPP) | Dá para usar o servidor embutido do PHP |
| Extensão PHP | `mysqli` | Já vem habilitada no XAMPP |

A forma mais simples de ter tudo isso é instalar o [XAMPP](https://www.apachefriends.org/),
que traz Apache, PHP e MySQL juntos.

---

## Setup

### 1. Baixar os arquivos

Clone o repositório dentro da pasta `htdocs` do XAMPP:

```bash
cd C:\xampp\htdocs
git clone https://github.com/LuizPauloC/Aula-7_Sistema-de-Login.git aula7-login
```

O caminho final dos arquivos deve ficar assim:

```
C:\xampp\htdocs\aula7-login\
```

> No Linux/Mac o caminho do `htdocs` costuma ser `/opt/lampp/htdocs` ou `/var/www/html`.

### 2. Criar o banco de dados

Abra o **XAMPP Control Panel** e inicie o **MySQL**. Depois importe o script de criação por
uma das opções abaixo.

**Pelo phpMyAdmin (mais fácil):**

1. Acesse <http://localhost/phpmyadmin>
2. Vá em **Importar**
3. Escolha o arquivo `sistema_login.sql` e clique em **Executar**

**Pelo terminal:**

```bash
mysql -u root -p < sistema_login.sql
```

Isso cria o banco `sistema_login` e a tabela `usuarios`:

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('creator', 'admin', 'guest') NOT NULL DEFAULT 'guest'
);
```

### 3. Configurar a conexão

Abra o `conexao.php` e ajuste os dados de acesso ao seu MySQL:

```php
$servername = "localhost";
$username   = "root";
$password   = "";          // senha do seu MySQL — no XAMPP o padrão é vazio
$database   = "sistema_login";
```

Se o seu MySQL tem senha no usuário `root`, preencha a variável `$password`.

### 4. Subir o servidor

**Opção A — Apache do XAMPP:**

Inicie o **Apache** no XAMPP Control Panel e acesse:

```
http://localhost/aula7-login/
```

**Opção B — servidor embutido do PHP** (não precisa do Apache):

```bash
cd C:\xampp\htdocs\aula7-login
C:\xampp\php\php.exe -S 127.0.0.1:8090
```

E acesse <http://127.0.0.1:8090>.

### 5. Criar o primeiro usuário

Acesse `register.php` e cadastre-se normalmente.

> **O primeiro usuário cadastrado vira `creator` automaticamente.** Todos os cadastros
> seguintes entram como `guest` e precisam ser promovidos por um creator ou admin dentro
> do painel.

---

## Como usar

1. **Cadastro** (`register.php`) — nome, email e senha (mínimo 6 caracteres, com confirmação)
2. **Login** (`login.php`) — abre a sessão e redireciona para o painel
3. **Painel** (`painel.php`) — área restrita; creator e admin veem a lista de usuários e podem alterar os tipos
4. **Recuperar senha** (`recuperar_senha.php`) — informa o email e define a nova senha
5. **Sair** (`logout.php`) — destrói a sessão

### Tipos de usuário

| Tipo | Acessa o painel | Gerencia usuários | Pode ser rebaixado |
|---|---|---|---|
| `creator` | Sim | Sim | **Não** — o cargo é permanente |
| `admin` | Sim | Sim, inclusive outros admins | Sim |
| `guest` | Sim (só a tela de boas-vindas) | Não | — |

Regras aplicadas pelo servidor em `processa_tipo.php`:

- O `creator` **não pode** ter o tipo alterado por ninguém
- Um `admin` **pode** alterar o tipo de outro `admin`
- Um `guest` não pode alterar tipo nenhum, nem o próprio
- Ninguém pode ser promovido a `creator` — o cargo só existe no primeiro cadastro
- Quem não está logado é redirecionado para o login

As validações rodam no PHP, não apenas na interface: mesmo enviando uma requisição
direto para `processa_tipo.php` (por `curl` ou Postman, sem passar pela tela), as regras
continuam valendo.

---

## Estrutura dos arquivos

```
aula7-login/
├── conexao.php               # Conexão com o MySQL (ajuste as credenciais aqui)
├── sistema_login.sql         # Script de criação do banco e da tabela
├── estilo.css                # Estilo de todas as páginas
├── index.php                 # Menu inicial
│
├── register.php              # Formulário de cadastro
├── processa_registro.php     # Valida, define o tipo e grava o usuário
│
├── login.php                 # Formulário de login
├── processa_login.php        # Confere a senha e abre a sessão
│
├── painel.php                # Página restrita + gerenciamento de usuários
├── processa_tipo.php         # Aplica as regras de permissão e altera o tipo
│
├── recuperar_senha.php       # Formulário de nova senha
├── processa_recuperacao.php  # Valida e regrava a senha
│
└── logout.php                # Destrói a sessão
```

Os arquivos vêm em pares: uma **página** mostra o formulário, e um **processador**
(`processa_*.php`) recebe o envio, mexe no banco e redireciona. Os processadores não
imprimem HTML — eles terminam sempre com `header("Location: ...")` seguido de `exit()`,
o que evita que o F5 reenvie o formulário.

---

## Segurança implementada

| Proteção | Como | Onde |
|---|---|---|
| Senhas nunca salvas em texto | `password_hash()` / `password_verify()` (bcrypt) | cadastro, login, recuperação |
| SQL injection | Prepared statements (`prepare` + `bind_param`) em todas as queries | todos os arquivos com banco |
| XSS | `htmlspecialchars()` em tudo que é impresso na tela | páginas com saída de dados |
| Acesso indevido à área restrita | Verificação de `$_SESSION` no topo de `painel.php` | painel |
| Escalada de privilégio | 5 validações no servidor antes do `UPDATE` | `processa_tipo.php` |
| Enumeração de usuários | Email errado e senha errada retornam a mesma mensagem | `processa_login.php` |

O tipo do usuário é lido **do banco** a cada carregamento de página, não da sessão. Assim,
se um admin for rebaixado, a mudança vale imediatamente, sem precisar esperar o logout.

### Limitação conhecida

A recuperação de senha pede apenas o email cadastrado, sem confirmar que a pessoa realmente
tem acesso àquela caixa de entrada. Em um sistema real seria necessário enviar um link com
token por email. Isso ficou fora do escopo da atividade, mas é uma limitação assumida.

---

## Problemas comuns

**`Access denied for user 'root'@'localhost'`**
A senha em `conexao.php` não bate com a do seu MySQL. No XAMPP o padrão é senha vazia (`""`).

**`Uncaught mysqli_sql_exception: Duplicate entry ... for key 'usuarios.email'`**
Esse email já está cadastrado — a coluna é `UNIQUE`. Use outro email ou recupere a senha.

**A página abre mas mostra outro site (ou dá 404) em `http://localhost/`**
Já existe um Virtual Host configurado no Apache apontando para outro projeto. Use o servidor
embutido do PHP (Opção B do passo 4) ou configure um Virtual Host para o `htdocs`.

**`Unknown database 'sistema_login'`**
O script `sistema_login.sql` não foi importado. Volte ao passo 2.

**A porta 80 está ocupada e o Apache não inicia**
Normalmente é o Skype, o IIS ou outro servidor. Troque a porta do Apache nas configurações
do XAMPP ou use o servidor embutido do PHP.

---

## Equipe

Trabalho desenvolvido para a disciplina de Desenvolvimento Back-End —
Prof. Állan Stieg Candeia — Curso de Sistemas de Informação.
