<?php
require 'verifica_login.php';
require 'db.php';

if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO paciente (nome, data_nascimento, tipo_sanguineo) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['data_nascimento'], $_POST['tipo_sanguineo']]);
    header("Location: paciente.php");
    exit;
}

if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE paciente SET nome=?, data_nascimento=?, tipo_sanguineo=? WHERE id=?");
    $stmt->execute([$_POST['nome'], $_POST['data_nascimento'], $_POST['tipo_sanguineo'], $_POST['id']]);
    header("Location: paciente.php");
    exit;
}

if (isset($_GET['del'])) {
    $stmt = $pdo->prepare("DELETE FROM paciente WHERE id=?");
    $stmt->execute([$_GET['del']]);
    header("Location: paciente.php");
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM paciente WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pacientes = $pdo->query("SELECT * FROM paciente")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pacientes</title>
</head>
<body>
<h2>Pacientes</h2>

<form method="post">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
    Nome: <input type="text" name="nome" value="<?= ($edit['nome'] ?? '') ?>" required>
    Data Nascimento: <input type="date" name="data_nascimento" value="<?= ($edit['data_nascimento'] ?? '') ?>" required>
    Tipo Sanguíneo: <input type="text" name="tipo_sanguineo" value="<?= ($edit['tipo_sanguineo'] ?? '') ?>" required>
    <?php if ($edit): ?>
        <button type="submit" name="update">Atualizar</button>
        <a href="paciente.php">Cancelar</a>
    <?php else: ?>
        <button type="submit" name="add">Adicionar</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="6" cellspacing="0">
<tr><th>ID</th><th>Nome</th><th>Data Nascimento</th><th>Tipo</th><th>Ações</th></tr>
<?php foreach ($pacientes as $p): ?>
<tr>
    <td><?= $p['id'] ?></td>
    <td><?= ($p['nome']) ?></td>
    <td><?= ($p['data_nascimento']) ?></td>
    <td><?= ($p['tipo_sanguineo']) ?></td>
    <td>
        <a href="?edit=<?= $p['id'] ?>">Editar</a> |
        <a href="?del=<?= $p['id'] ?>" onclick="return confirm('Excluir este paciente?')">Excluir</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<p>
    <a href="index.php">Início</a> |
    <a href="medico.php">Gerenciar Médicos</a> |
    <a href="consulta.php">Gerenciar Consultas</a> |
    <a href="logout.php">Sair</a>
</p>
</body>
</html>
