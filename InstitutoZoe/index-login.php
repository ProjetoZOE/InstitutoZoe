<?php
// Arquivo obsoleto - redireciona para novo local
// Arquivo novo: auth/login.php
header('Location: auth/login.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
