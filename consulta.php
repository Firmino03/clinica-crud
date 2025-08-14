<?php
require 'db.php';

if (isset($_POST['add'])) {
    // Apenas troca o T por espaço e adiciona segundos para compatibilidade total
    $dataHora = str_replace('T', ' ', $_POST['data_hora']) . ':00';

    $stmt = $pdo->prepare("INSERT INTO consulta (id_medico, id_paciente, data_hora, observacoes) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['id_medico'], $_POST['id_paciente'], $dataHora, $_POST['observacoes']]);
    header("Location: consulta.php");
    exit;
}

if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE consulta SET observacoes=? WHERE id_medico=? AND id_paciente=? AND data_hora=?");
    $stmt->execute([$_POST['observacoes'], $_POST['id_medico'], $_POST['id_paciente'], $_POST['data_hora_original']]);
    header("Location: consulta.php");
    exit;
}

if (isset($_GET['del_medico']) && isset($_GET['del_paciente']) && isset($_GET['del_data'])) {
    $stmt = $pdo->prepare("DELETE FROM consulta WHERE id_medico=? AND id_paciente=? AND data_hora=?");
    $stmt->execute([$_GET['del_medico'], $_GET['del_paciente'], $_GET['del_data']]);
    header("Location: consulta.php");
    exit;
}

$medicos = $pdo->query("SELECT * FROM medico ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$pacientes = $pdo->query("SELECT * FROM paciente ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$edit = null;
if (isset($_GET['edit_medico']) && isset($_GET['edit_paciente']) && isset($_GET['edit_data'])) {
    $stmt = $pdo->prepare("
        SELECT c.*, m.nome AS medico_nome, p.nome AS paciente_nome
        FROM consulta c
        JOIN medico m ON c.id_medico = m.id
        JOIN paciente p ON c.id_paciente = p.id
        WHERE c.id_medico=? AND c.id_paciente=? AND c.data_hora=?
    ");
    $stmt->execute([$_GET['edit_medico'], $_GET['edit_paciente'], $_GET['edit_data']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$consultas = $pdo->query("
    SELECT c.*, m.nome AS medico_nome, p.nome AS paciente_nome
    FROM consulta c
    JOIN medico m ON c.id_medico = m.id
    JOIN paciente p ON c.id_paciente = p.id
    ORDER BY c.data_hora DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consultas</title>
</head>
<body>
<h2>Consultas</h2>

<form method="post">
    Médico:
    <select name="id_medico" required>
        <option value="">-- selecione --</option>
        <?php foreach ($medicos as $m): ?>
            <option value="<?= $m['id'] ?>"><?= ($m['nome']) ?> (<?= ($m['especialidade']) ?>)</option>
        <?php endforeach; ?>
    </select>

    Paciente:
    <select name="id_paciente" required>
        <option value="">-- selecione --</option>
        <?php foreach ($pacientes as $p): ?>
            <option value="<?= $p['id'] ?>"><?= ($p['nome']) ?> (<?= ($p['tipo_sanguineo']) ?>)</option>
        <?php endforeach; ?>
    </select>

    Data/Hora: <input type="datetime-local" name="data_hora" required>
    Observações: <input type="text" name="observacoes">
    <button type="submit" name="add">Registrar Consulta</button>
</form>

<?php if ($edit): ?>
<hr>
<h3>Editar Observações da Consulta</h3>
<form method="post">
    <input type="hidden" name="id_medico" value="<?= $edit['id_medico'] ?>">
    <input type="hidden" name="id_paciente" value="<?= $edit['id_paciente'] ?>">
    <input type="hidden" name="data_hora_original" value="<?= $edit['data_hora'] ?>">
    Médico: <strong><?= ($edit['medico_nome']) ?></strong>
    &nbsp;|&nbsp;
    Paciente: <strong><?= ($edit['paciente_nome']) ?></strong>
    &nbsp;|&nbsp;
    Data/Hora: <strong><?= ($edit['data_hora']) ?></strong><br><br>
    Observações: <input type="text" name="observacoes" value="<?= ($edit['observacoes'] ?? '') ?>" size="80">
    <button type="submit" name="update">Salvar</button>
    <a href="consulta.php">Cancelar</a>
</form>
<?php endif; ?>

<hr>
<table border="1" cellpadding="6" cellspacing="0">
<tr><th>Médico</th><th>Paciente</th><th>Data/Hora</th><th>Observações</th><th>Ações</th></tr>
<?php foreach ($consultas as $c): ?>
<tr>
    <td><?= ($c['medico_nome']) ?></td>
    <td><?= ($c['paciente_nome']) ?></td>
    <td><?= ($c['data_hora']) ?></td>
    <td><?= ($c['observacoes']) ?></td>
    <td>
        <a href="?edit_medico=<?= $c['id_medico'] ?>&edit_paciente=<?= $c['id_paciente'] ?>&edit_data=<?= urlencode($c['data_hora']) ?>">Editar</a> |
        <a href="?del_medico=<?= $c['id_medico'] ?>&del_paciente=<?= $c['id_paciente'] ?>&del_data=<?= urlencode($c['data_hora']) ?>" onclick="return confirm('Excluir esta consulta?')">Excluir</a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<p>
    <a href="index.php">Início</a> |
    <a href="medico.php">Gerenciar Médicos</a> |
    <a href="paciente.php">Gerenciar Pacientes</a>
</p>
</body>
</html>

