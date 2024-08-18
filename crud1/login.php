<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);

    
    $cpf = preg_replace('/\D/', '', $cpf);

    $sql = "SELECT * FROM users WHERE nome = ? AND cpf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $cpf]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user['nome'];
        $_SESSION['is_admin'] = $user['is_admin'];

        if ($user['is_admin']) {
            header("Location: index.php");
        } else {
            header("Location: view.php");
        }
        exit();
    } else {
        $message = "Nome ou CPF inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="login-title">Login</h2>
        <form method="POST">
            Nome: <input type="text" name="nome" required><br>
            CPF: <input type="text" name="cpf" required id="cpf" oninput="formatarCpf()"><br>
            <button type="submit">Entrar</button>
        </form>
        <?php if (isset($message)): ?>
            <p class="message error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <a href="register.php" class="button">Ainda não tem cadastro? Cadastre-se</a>
    </div>
    <script>
        function formatarCpf() {
            const cpfInput = document.getElementById('cpf');
            let cpf = cpfInput.value.replace(/\D/g, '');
            if (cpf.length <= 3) {
                cpfInput.value = cpf;
            } else if (cpf.length <= 6) {
                cpfInput.value = cpf.slice(0, 3) + '.' + cpf.slice(3);
            } else if (cpf.length <= 9) {
                cpfInput.value = cpf.slice(0, 3) + '.' + cpf.slice(3, 6) + '.' + cpf.slice(6);
            } else {
                cpfInput.value = cpf.slice(0, 3) + '.' + cpf.slice(3, 6) + '.' + cpf.slice(6, 9) + '-' + cpf.slice(9, 11);
            }
        }
    </script>
</body>
</html>
