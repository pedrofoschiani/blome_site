<?php
session_start();

// Limpa todas as variáveis de sessão
session_unset();

// Destrói a sessão ativa
session_destroy();

// Redireciona para a tela de login
header("Location: login.page.php");
exit;
?>