<?php
// Arquivo obsoleto - redireciona para novo local
// Arquivo novo: dashboard/admin/index.php
header('Location: dashboard/admin/index.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
