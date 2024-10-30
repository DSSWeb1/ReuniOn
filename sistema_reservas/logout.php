<?php
session_start();

// Finaliza a sessão
session_unset(); // Libera todas as variáveis da sessão
session_destroy(); // Destrói a sessão

// Redireciona para a página de login
header("Location: index.php");
exit();
?>
