<?php
// Force logout - supprime la session
session_start();
session_destroy();

// Supprimer le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Rediriger vers login
header('Location: frontend/login.php?logged_out=1');
exit;
?>
