<?php
session_start();
include 'db_config.php';

// Verifica se o usuário está logado e se é um admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: index.php"); // Redireciona para a página de login se não for admin
    exit();
}

// Obtém as reservas pendentes do banco de dados
$sql_reservas = "
    SELECT reservas.id, salas.nome AS sala, usuarios.nome AS usuario, reservas.data_reserva, reservas.hora_inicio, reservas.duracao, reservas.status 
    FROM reservas 
    JOIN salas ON reservas.id_sala = salas.id
    JOIN usuarios ON reservas.id_usuario = usuarios.id
    WHERE reservas.status = 'pendente'
    ORDER BY reservas.data_reserva, reservas.hora_inicio";

$result_reservas = $conn->query($sql_reservas);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    
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
    <h1>Painel Admin - Solicitações Pendentes</h1>

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
                    <th>Ações</th>
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
                        <td>
                            <form action="atualizar_reserva.php" method="POST">
                                <input type="hidden" name="id_reserva" value="<?php echo $reserva['id']; ?>">
                                <select name="status" required>
                                    <option value="">Ação</option>
                                    <option value="confirmada">Reservar</option>
                                    <option value="indisponivel">Indisponível</option>
                                    <option value="pendente">Continuar Pendente</option>
                                </select>
                                <input type="submit" value="Atualizar">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma solicitação pendente encontrada.</p>
    <?php endif; ?>

    <a href="logout.php">Sair</a> <!-- Botão para logout -->

    <?php $conn->close(); // Fecha a conexão com o banco de dados ?>
</body>
</html>
