// script.js

// Função para validar a entrada do formulário antes de enviar
function validarFormulario() {
    const usuario = document.getElementById('usuario').value;
    const dataReserva = document.getElementById('data_reserva').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFim = document.getElementById('hora_fim').value;

    // Verifica se todos os campos estão preenchidos
    if (!usuario || !dataReserva || !horaInicio || !horaFim) {
        alert("Por favor, preencha todos os campos.");
        return false;
    }

    // Aqui você pode adicionar mais validações, como verificar se a hora de início é antes da hora de fim

    return true; // Se tudo estiver correto, retorna verdadeiro
}

// Adiciona o evento de validação ao formulário
document.getElementById('formReserva').onsubmit = function() {
    return validarFormulario();
};
