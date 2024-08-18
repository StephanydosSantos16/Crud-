<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);

    
    $cpf = preg_replace('/\D/', '', $cpf);

    // Validar o formato do CPF
    if (!preg_match('/^\d{11}$/', $cpf)) {
        $error_message = 'CPF inválido.';
    } else {
        
        $sql = "SELECT * FROM users WHERE cpf = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$cpf]);
        if ($stmt->fetch()) {
            $error_message = 'CPF já cadastrado.';
        } else {
            
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error_message = 'Email já cadastrado.';
            } else {
                
                $sql = "INSERT INTO users (nome, cpf, email) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$nome, $cpf, $email]);

                
                header("Location: register_success.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="login-title">Cadastro</h2>
        <form method="POST">
            Nome: <input type="text" name="nome" required><br>
            CPF: <input type="text" name="cpf" required id="cpf" oninput="formatarCpf()"><br>
            Email: <input type="email" name="email" required><br>
            <button type="submit">Cadastrar</button>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <a href="login.php" class="button">Possui cadastro? Faça login</a>
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
