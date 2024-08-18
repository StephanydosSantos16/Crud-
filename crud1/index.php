<?php
include 'db.php';
session_start();

// usuário está logado?
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


if (!$_SESSION['is_admin']) {
    header("Location: view.php"); 
    exit();
}

function formatar_cpf($cpf) {
    $cpf_formatado = preg_replace('/[^0-9]/', '', $cpf); 
    $cpf_formatado = substr_replace($cpf_formatado, '-', 3, 0); 
    $cpf_formatado = substr_replace($cpf_formatado, '.', 7, 0); 
    $cpf_formatado = substr_replace($cpf_formatado, '.', 11, 0); 
    return $cpf_formatado;
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    header("Location: index.php");
    exit();
}


if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);


    if (!empty($nome) && !empty($cpf) && !empty($email)) {
        $sql = "UPDATE users SET nome = ?, cpf = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $cpf, $email, $id]);

        header("Location: index.php");
        exit();
    } else {
        echo "Por favor, preencha todos os campos.";
    }
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
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Gerenciamento de Usuários</h2>
            <a href="logout.php" class="button">Sair</a>
        </div>
        
        <a href="create.php" class="button">Cadastrar Novo Usuário</a>

        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['nome']; ?></td>
                <td><?php echo formatar_cpf($user['cpf']); ?></td>
                <td><?php echo $user['email']; ?></td>
                <td>
                    <a href="index.php?edit=<?php echo $user['id']; ?>">Editar</a>
                    <a href="index.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php if (isset($_GET['edit'])): ?>
        <?php
        $id = $_GET['edit'];
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        ?>
        <h2>Editar Usuário</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            Nome: <input type="text" name="nome" value="<?php echo $user['nome']; ?>" required><br>
            CPF: <input type="text" name="cpf" value="<?php echo $user['cpf']; ?>" required pattern="\d{11}" title="Digite um CPF válido"><br>
            Email: <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>
            <button type="submit" name="update">Atualizar</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
