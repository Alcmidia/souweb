<?php

include ("funcoes/conexao.php");

session_start();

// verifica se a busca ja está salva
$sqlbus = pg_exec ($conn, "SELECT * FROM buscas WHERE bustxt='".$_SESSION["pes"]."';");
if ($regbus = pg_fetch_array($sqlbus))
{
  $_SESSION["bus"]=$regbus["buscod"];
  // verifica se ja tem um relacionameto pagina busca
  $pag = pg_exec ($conn, "SELECT * FROM rel_pag_bus WHERE buscod=".$regbus["buscod"]." AND pagcod=".$_SESSION["pag"].";");
  // senao insere relacionamento pagina busca
  if (!($regrel = pg_fetch_array($pag)))
  {
    pg_exec ($conn,"INSERT INTO rel_pag_bus (pagcod, buscod) VALUES (".$_SESSION["pag"].",".$regbus["buscod"].");");
  }
}
else
{
  // senão tiver busca salva a busca
  $tabbus = pg_exec ($conn,"INSERT INTO buscas (bustxt) VALUES ('".$_SESSION["pes"]."') RETURNING buscod;");
  // pega o codigo da busca
  $regbus = pg_fetch_array($tabbus);
  $_SESSION["bus"]=$regbus[0];
	//echo 'codigo: '.$regbus[0];
  // insere o relacionamento busca pagina
  pg_exec ($conn,"INSERT INTO rel_pag_bus (pagcod, buscod) VALUES (".$_SESSION["pag"].",".$regbus["buscod"].");");
}


if ($_GET["opcao"]=="sim")
{
  //echo 'teste'; 
  pg_exec ($conn, "UPDATE rel_pag_bus SET rpbsim=rpbsim+1,rpbpon=rpbpon+1  WHERE pagcod=".$_SESSION["pag"]." AND buscod=".$_SESSION["bus"]);
}
else
{
  if ($_GET["opcao"]=="nao")
	{
    $pontos = pg_exec ($conn, "UPDATE rel_pag_bus SET rpbnao=rpbnao+1,rpbpon=rpbpon-1 WHERE pagcod=".$_SESSION["pag"]." AND buscod=".$_SESSION["bus"]);
  }
}

 print "<script>parent.location.href='http://".$_SESSION["url"]."';</script>";

?>