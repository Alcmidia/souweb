<?

include ("funcoes/trata_strings.php");

include ("funcoes/conexao.php");

session_start();

// fazer salvar palavra na busca minuscula e fazer uma funчуo para salvar a palavra

$sqlpag = pg_exec ($conn, "SELECT * FROM paginas WHERE pagcod='".$_GET["codpag"]."';");
$regpag = pg_fetch_array($sqlpag);

$_SESSION["pag"] = $_GET["codpag"];
$_SESSION["pes"] = gera_busca($_GET["pagpes"]);
$_SESSION["url"] = $regpag["pagurl"];

// verifica se a busca ja estс salva
$sqlbus = pg_exec ($conn, "SELECT * FROM buscas WHERE bustxt='".$_SESSION["pes"]."';");
if ($regbus = pg_fetch_array($sqlbus))
{
/*
  // verifica se a pсgina ja tem ponto
  $pag = pg_exec ($conn, "SELECT * FROM rel_pag_bus WHERE buscod=".$regbus["buscod"]." AND pagcod=".$_SESSION["pag"].";");
  if ($regrel = pg_fetch_array($pag))
  {
    // se tiver salva mais um clique para pсgina
    pg_exec ($conn,"UPDATE rel_pag_bus SET rpbcli=rpbcli+1 WHERE rpbcod='".$regrel["rpbcod"]."';");
  }
  else
  {
    // sanуo insere um clique para a pсgina
    pg_exec ($conn,"INSERT INTO rel_pag_bus (rpbcli, pagcod, buscod) VALUES (1,".$_SESSION["pag"].",".$regbus["buscod"].");");
  }
*/
}
else
{
  // senуo tiver busca salva a busca
  pg_exec ($conn,"INSERT INTO buscas (bustxt) VALUES ('".$_SESSION["pes"]."');");
  // pega o codigo da busca
  $sqlbus = pg_exec ($conn, "SELECT * FROM buscas WHERE bustxt='".$_SESSION["pes"]."';");
  $regbus = pg_fetch_array($sqlbus);
/*
  // salva um clique para a pсgina
  pg_exec ($conn,"INSERT INTO rel_pag_bus (rpbcli, pagcod, buscod) VALUES (1,".$_SESSION["pag"].",".$regbus["buscod"].");");
*/
}

$_SESSION["bus"]=$regbus["buscod"];
header("location: pagina.php");
?>