<?php
require 'verifica_login.php';
require 'db.php';

if (isset($_POST['add'])) {
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $ext;
        $caminho = __DIR__ . '/uploads/' . $novoNome;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
            $foto = $novoNome;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO paciente (nome, data_nascimento, tipo_sanguineo, foto) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['data_nascimento'], $_POST['tipo_sanguineo'], $foto]);
    header("Location: paciente.php");
    exit;
}

if (isset($_POST['update'])) {
    $foto = $_POST['foto_atual'];

    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $ext;
        $caminho = __DIR__ . '/uploads/' . $novoNome;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
            $foto = $novoNome;
        }
    }

    $stmt = $pdo->prepare("UPDATE paciente SET nome=?, data_nascimento=?, tipo_sanguineo=?, foto=? WHERE id=?");
    $stmt->execute([$_POST['nome'], $_POST['data_nascimento'], $_POST['tipo_sanguineo'], $foto, $_POST['id']]);
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

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
    <input type="hidden" name="foto_atual" value="<?= $edit['foto'] ?? '' ?>">
    Nome: <input type="text" name="nome" value="<?= ($edit['nome'] ?? '') ?>" required><br>
    Data Nascimento: <input type="date" name="data_nascimento" value="<?= ($edit['data_nascimento'] ?? '') ?>" required><br>
    Tipo Sanguíneo: <input type="text" name="tipo_sanguineo" value="<?= ($edit['tipo_sanguineo'] ?? '') ?>" required><br>
    Foto: <input type="file" name="foto" accept="image/*"><br>
    <?php if (!empty($edit['foto'])): ?>
        <img src="uploads/<?= $edit['foto'] ?>" width="80"><br>
    <?php endif; ?>
    <?php if ($edit): ?>
        <button type="submit" name="update">Atualizar</button>
        <a href="paciente.php">Cancelar</a>
    <?php else: ?>
        <button type="submit" name="add">Adicionar</button>
    <?php endif; ?>
</form>

<table border="1" cellpadding="6" cellspacing="0">
<tr><th>ID</th><th>Nome</th><th>Data Nascimento</th><th>Tipo</th><th>Foto</th><th>Ações</th></tr>
<?php foreach ($pacientes as $p): ?>
<tr>
    <td><?= $p['id'] ?></td>
    <td><?= ($p['nome']) ?></td>
    <td><?= ($p['data_nascimento']) ?></td>
    <td><?= ($p['tipo_sanguineo']) ?></td>
    <td>
        <?php if (!empty($p['foto'])): ?>
            <img src="uploads/<?= $p['foto'] ?>" width="60">
        <?php else: ?>
            Sem foto
        <?php endif; ?>
    </td>
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
