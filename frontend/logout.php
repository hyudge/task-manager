<?php
/**
 * logout.php
 *
 * Déconnecte l'utilisateur en détruisant la session, puis redirige vers login.
 */
require_once __DIR__ . '/auth.php';

logout();
header('Location: login.php');
exit;
