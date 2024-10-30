<?php
include 'db_config.php'; // Inclui a conexão com o banco de dados
session_start(); // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografa a senha
    $tipo_usuario = 'normal'; // Usuários que se registram são 'normais' por padrão

    // Insere o novo usuário no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES ('$nome', '$email', '$senha', '$tipo_usuario')";

    if ($conn->query($sql) === TRUE) {
        // Registro bem-sucedido, exibe alerta e redireciona para o login (index.php)
        echo "<script>
                alert('Registro realizado com sucesso!');
                window.location.href = 'index.php';
              </script>";
        exit();
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Reservas</title>
</head>
<body>
    <h2>Registro</h2>
    <form action="register.php" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>
        
        <button type="submit">Registrar</button>
    </form>
</body>
</html>
