<?php
require 'verifica_login.php';
require 'db.php';

if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO medico (nome, especialidade) VALUES (?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['especialidade']]);
    header("Location: medico.php");
    exit;
}

if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE medico SET nome=?, especialidade=? WHERE id=?");
    $stmt->execute([$_POST['nome'], $_POST['especialidade'], $_POST['id']]);
    header("Location: medico.php");
    exit;
}

if (isset($_GET['del'])) {
    $stmt = $pdo->prepare("DELETE FROM medico WHERE id=?");
    $stmt->execute([$_GET['del']]);
    header("Location: medico.php");
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM medico WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$medicos = $pdo->query("SELECT * FROM medico ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Médicos</title>
</head>
<body>
<h2>Médicos</h2>

<form method="post">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
    Nome: <input type="text" name="nome" value="<?= ($edit['nome'] ?? '') ?>" required>
    Especialidade: <input type="text" name="especialidade" value="<?= ($edit['especialidade'] ?? '') ?>" required>
    <?php if ($edit): ?>
        <button type="submit" name="update">Atualizar</button>
        <a href="medico.php">Cancelar</a>
    <?php else: ?>
        <button type="submit" name="add">Adicionar</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="6" cellspacing="0">
<tr><th>ID</th><th>Nome</th><th>Especialidade</th><th>Ações</th></tr>
<?php foreach ($medicos as $m): ?>
<tr>
    <td><?= $m['id'] ?></td>
    <td><?= ($m['nome']) ?></td>
    <td><?= ($m['especialidade']) ?></td>
    <td>
        <a href="?edit=<?= $m['id'] ?>">Editar</a> |
        <a href="?del=<?= $m['id'] ?>" onclick="return confirm('Excluir este médico?')">Excluir</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
<p>
    <a href="index.php">Início</a> |
    <a href="paciente.php">Gerenciar Pacientes</a> |
    <a href="consulta.php">Gerenciar Consultas</a>
    <p><a href="logout.php">Sair</a></p>
</p>
</body>
</html>
