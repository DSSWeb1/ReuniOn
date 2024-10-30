<?php
// reservar.php

// Inclui o arquivo de configuração do banco de dados
include('db_config.php');
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Inicializa uma variável para armazenar mensagens de erro ou sucesso
$message = "";

// Obtém o ID do usuário logado
$id_usuario = $_SESSION['user_id'];

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário e sanitiza as entradas
    $id_sala = $_POST['id_sala'];
    $data_reserva = $_POST['data_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    
    // Obtém a duração selecionada diretamente do formulário
    $duracao = $_POST['duracao']; // Duração já no formato correto (HH:MM:SS)

    // Converte a data para o formato YYYY-MM-DD
    $data_reserva_formatada = date('Y-m-d', strtotime($data_reserva));

    // Verifica se o horário está disponível
    $sql_check = "SELECT * FROM horarios_disponiveis 
                  WHERE id_sala = '$id_sala' 
                  AND data = '$data_reserva_formatada' 
                  AND horario = '$hora_inicio' 
                  AND disponivel = TRUE";

    $result_check = mysqli_query($conn, $sql_check);

    if ($result_check->num_rows > 0) {
        // Insere a reserva no banco de dados
        $sql = "INSERT INTO reservas (id_sala, id_usuario, data_reserva, hora_inicio, duracao) 
                VALUES ('$id_sala', '$id_usuario', '$data_reserva_formatada', '$hora_inicio', '$duracao')";

        if (mysqli_query($conn, $sql)) {
            // Atualiza o status do horário para não disponível
            $sql_update = "UPDATE horarios_disponiveis SET disponivel = FALSE 
                           WHERE id_sala = '$id_sala' AND data = '$data_reserva_formatada' AND horario = '$hora_inicio'";
            mysqli_query($conn, $sql_update);
            
            $message = "Reserva feita com sucesso!";
        } else {
            $message = "Erro ao fazer a reserva: " . mysqli_error($conn);
        }
    } else {
        $message = "Horário não disponível. Por favor, escolha outro horário.";
    }
}

// Obtém as salas disponíveis
$sql_salas = "SELECT * FROM salas";
$result_salas = mysqli_query($conn, $sql_salas);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Sala</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function atualizarHorarios() {
            const horaInicio = document.getElementById('hora_inicio').value;
            const horaFinalSelect = document.getElementById('duracao');
            horaFinalSelect.innerHTML = ''; // Limpa opções anteriores

            const [hInicio, mInicio] = horaInicio.split(':').map(Number);
            const horarios = [];

            // Adiciona 30 minutos ao horário de início para o primeiro horário de final
            const primeiroHorarioFinal = new Date();
            primeiroHorarioFinal.setHours(hInicio, mInicio + 30, 0);

            // Cria as opções de horários a partir da hora de início, com incrementos de 30 minutos
            for (let h = primeiroHorarioFinal.getHours(); h <= 17; h++) {
                for (let m = (h === primeiroHorarioFinal.getHours() ? primeiroHorarioFinal.getMinutes() : 0); m < 60; m += 30) {
                    horarios.push(`${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`);
                }
            }

            // Adiciona os horários ao select de hora final
            horarios.forEach(horario => {
                const option = document.createElement('option');
                option.value = horario;
                option.textContent = horario;
                horaFinalSelect.appendChild(option);
            });
        }

        // Chama a função ao carregar a página
        window.onload = function() {
            atualizarHorarios(); // Atualiza os horários finais inicialmente
        };
    </script>
</head>
<body>
    <h1>Reservar Sala</h1>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p> <!-- Exibe mensagem de erro ou sucesso -->
    <?php endif; ?>

    <form action="reservar.php" method="POST">
        <label for="id_sala">Escolha uma sala:</label>
        <select name="id_sala" id="id_sala" required>
            <?php while ($sala = mysqli_fetch_assoc($result_salas)): ?>
                <option value="<?php echo $sala['id']; ?>">
                    <?php echo $sala['nome'] . " - Capacidade: " . $sala['capacidade']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="data_reserva">Data da Reserva:</label>
        <input type="date" name="data_reserva" id="data_reserva" required>

        <label for="hora_inicio">Hora de Início:</label>
        <select name="hora_inicio" id="hora_inicio" required onchange="atualizarHorarios()">
            <?php
            // Cria as opções de horários das 08:00 às 17:00 com intervalos de 30 minutos
            for ($h = 8; $h <= 17; $h++) {
                for ($m = 0; $m <= 30; $m += 30) {
                    $hora = sprintf('%02d:%02d', $h, $m);
                    echo "<option value=\"$hora\">$hora</option>";
                }
            }
            ?>
        </select>

        <label for="duracao">Hora de Final:</label>
        <select name="duracao" id="duracao" required>
            <!-- As opções serão preenchidas dinamicamente pelo JavaScript -->
        </select>

        <input type="submit" value="Reservar">
    </form>

    <br>
    <a href="ver_reservas.php" class="button">Ver Minhas Reservas</a> <!-- Botão para ver reservas -->
    <a href="index.php" class="button">Sair</a> <!-- Botão para logout -->

    <?php mysqli_close($conn); // Fecha a conexão com o banco de dados ?>
</body>
</html>
