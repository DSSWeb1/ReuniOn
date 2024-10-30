<?php
// atualizar_reserva.php

// Configuração do banco de dados
$servername = "localhost";
$username = "root"; // ou o usuário que você definiu para o MySQL
$password = "test"; // coloque a senha do MySQL aqui
$dbname = "sistema_reservas";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Inicializa variáveis
$id_reserva = null;
$status = null;

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se as chaves 'id_reserva' e 'status' estão definidas no array $_POST
    if (isset($_POST['id_reserva']) && isset($_POST['status'])) {
        $id_reserva = $_POST['id_reserva'];
        $status = $_POST['status'];

        // Valida o status
        $valido = ['pendente', 'confirmada', 'indisponivel'];
        if (!in_array($status, $valido)) {
            die("Status inválido.");
        }

        // Atualiza o status da reserva
        $sql = "UPDATE reservas SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id_reserva);

        if ($stmt->execute()) {
            echo "Reserva atualizada com sucesso!";
        } else {
            echo "Erro ao atualizar a reserva: " . $conn->error;
        }

        $stmt->close();
    } else {
        die("Dados do formulário inválidos.");
    }
}

// Redireciona de volta para a página do admin ou outra página relevante
header("Location: admin.php");
exit();

$conn->close(); // Fecha a conexão com o banco de dados
