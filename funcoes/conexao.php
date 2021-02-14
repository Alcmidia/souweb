<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
// conex�o
define ("DEF_USUARIO","souweb_user");
define ("DEF_SENHA","1n0v4c4o");
define ("DEF_BASE","souweb_base");
$conn = pg_connect ("user=".DEF_USUARIO." dbname=".DEF_BASE." password=".DEF_SENHA);
setlocale(LC_CTYPE,"pt_BR");
?>
