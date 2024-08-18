<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

function formatar_cpf($cpf) {
    $cpf_formatado = preg_replace('/[^0-9]/', '', $cpf); 
    $cpf_formatado = substr_replace($cpf_formatado, '-', 3, 0); 
    $cpf_formatado = substr_replace($cpf_formatado, '.', 7, 0); 
    $cpf_formatado = substr_replace($cpf_formatado, '.', 11, 0); 
    return $cpf_formatado;
}

$sql = "SELECT * FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualização de Usuários</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Usuários Cadastrados</h2>
            <a href="logout.php" class="button">Sair</a>
        </div>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Email</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['nome']; ?></td>
                <td><?php echo formatar_cpf($user['cpf']); ?></td>
                <td><?php echo $user['email']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
