<?php
session_start();

$usuario_padrao = "admin";
$senha_padrao = "1234";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    if ($usuario === $usuario_padrao && $senha === $senha_padrao) {
        $_SESSION['usuario'] = $usuario;
        header("Location: index.php");
        exit;
    } else {
        $erro = "Usuário ou senha inválidoss!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<form method="post">
    Usuário: <input type="text" name="usuario" required><br><br>
    Senha: <input type="password" name="senha" required><br><br>
    <button type="submit">Entrar</button>
</form>
</body>
</html>
