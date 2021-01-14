<?php
include ('funcoes/conexao.php');



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>SouWeb - Aranha</title>
</head>
<META NAME="ROBOTS" CONTENT="NOINDEX">
  <META HTTP-EQUIV="Refresh" CONTENT="2">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  

<style type="text/css">
body
{
	background-color: #000000;
	color: #33AA33;
  font-family: fixedsys, consolas, monospace;
	font-size:14px;
}
</style>	
	
<body>
  
<?
$tabdom=pg_query($conn, "SELECT * FROM dominios WHERE domrev=false ORDER BY domcod ASC LIMIT 100");
while ($arrdom=pg_fetch_array($tabdom))
{
  echo '<br><br>Dominio: '.$arrdom["domnom"];
  $tabpag=pg_query($conn, "SELECT count(pagcod) as nropag FROM paginas WHERE domcod=".$arrdom["domcod"]);
  echo '<br>Antes:'.$arrdom["dompag"];
  $arrpag=pg_fetch_array($tabpag);
  $nropag=$arrpag["nropag"];
  echo '<br>Depois:'.$nropag;
  pg_query($conn, "UPDATE dominios SET dompag=$nropag, domrev=true WHERE domcod=".$arrdom["domcod"]);
  echo '<br>Atualizado!';
}

?>  
  

</body>
</html>
  