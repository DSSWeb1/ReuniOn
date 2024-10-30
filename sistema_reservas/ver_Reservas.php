<?php
// ver_reservas.php

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

session_start(); // Inicie a sessão

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Obtém o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Obtém as reservas do banco de dados apenas para o usuário logado
$sql_reservas = "
    SELECT reservas.id, salas.nome AS sala, usuarios.nome AS usuario, reservas.data_reserva, reservas.hora_inicio, reservas.duracao, reservas.status 
    FROM reservas 
    JOIN salas ON reservas.id_sala = salas.id
    JOIN usuarios ON reservas.id_usuario = usuarios.id
    WHERE reservas.id_usuario = ? -- Filtra pelas reservas do usuário logado
    ORDER BY reservas.data_reserva, reservas.hora_inicio";

$stmt = $conn->prepare($sql_reservas);
$stmt->bind_param("i", $user_id); // "i" indica que é um inteiro
$stmt->execute();
$result_reservas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Reservas</title>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#tabelaReservas').DataTable({
                "language": {
                    "lengthMenu": "Mostrar _MENU_ reservas por página",
                    "zeroRecords": "Nenhuma reserva encontrada",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "Nenhuma reserva disponível",
                    "infoFiltered": "(filtrado de _MAX_ reservas totais)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primeiro",
                        "last": "Último",
                        "next": "Próximo",
                        "previous": "Anterior"
                    }
                }
            });
        });
    </script>
</head>
<body>
    <h1>Reservas de Salas</h1>

    <?php if ($result_reservas->num_rows > 0): ?>
        <table id="tabelaReservas" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sala</th>
                    <th>Usuário</th>
                    <th>Data da Reserva</th>
                    <th>Hora de Início</th>
                    <th>Duração</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($reserva = $result_reservas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $reserva['id']; ?></td>
                        <td><?php echo $reserva['sala']; ?></td>
                        <td><?php echo $reserva['usuario']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reserva['data_reserva'])); ?></td>
                        <td><?php echo date('H:i', strtotime($reserva['hora_inicio'])); ?></td>
                        <td><?php echo $reserva['duracao']; ?></td>
                        <td><?php echo ucfirst($reserva['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma reserva encontrada.</p>
    <?php endif; ?>

    <a href="reservar.php">Agendar Sala</a> <!-- Botão para agendar sala -->
    <a href="logout.php">Sair</a> <!-- Botão para logout -->

    <?php 
    $stmt->close(); // Fecha o statement
    $conn->close(); // Fecha a conexão com o banco de dados 
    ?>
</body>
</html>
