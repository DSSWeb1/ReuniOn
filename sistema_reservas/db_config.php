<?php
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
?>
