<?php
// conexão
define ("DEF_USUARIO","souweb_user");
define ("DEF_SENHA","1n0v4c4o");
define ("DEF_BASE","souweb_base");
$conn = pg_connect ("user=".DEF_USUARIO." dbname=".DEF_BASE." password=".DEF_SENHA);
setlocale(LC_CTYPE,"pt_BR");
?>
