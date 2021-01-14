<?php

session_start();

include ("funcoes/conexao.php");

include ("funcoes/trata_strings.php");

$sqlpag = pg_exec ($conn, "SELECT * FROM paginas WHERE pagcod='".$_GET["codpag"]."';");
$regpag = pg_fetch_array($sqlpag);

$sqldom = pg_exec ($conn, "SELECT * FROM dominios WHERE domcod='".$regpag["domcod"]."';");
$regdom = pg_fetch_array($sqldom);

$_SESSION["pag"] = $_GET["codpag"];
$_SESSION["pes"] = gera_busca($_GET["pagpes"]);
$_SESSION["url"] = $regpag["pagurl"];
$_SESSION["dom"] = $regdom["domnom"];

$barra = '
<div style="z-index: 1000000001; position: fixed; top: 0; background-color: #eee; padding: 15px; right: 0; color: #555;">
<form action="https://www.souweb.com.br/voto.php">
  <label>Achou o que procura?</label>
  <label><input type="radio" name="opcao" value="sim"> Sim</label>
  <label><input type="radio" name="opcao" value="nao"> Nao</label>
	<label><input type="Submit" name="OK" value="  OK  "></label>
	<label><a href="http:'.$_SESSION["url"].'" target="_top">Fechar Aba</a></label>
</form>
</div>
';




$dom = new DomDocument('1.0', 'UTF-8');
libxml_use_internal_errors(true);
if ($str = file_get_contents('https://'.$_SESSION["url"]))
{
	$dom->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));
	libxml_clear_errors();
	
	$element = $dom->createElement('base');
	$element->setAttribute( 'href', 'https://'.$_SESSION["dom"].'/' );
	$nodehead = $dom->getElementsByTagName('head')->item(0);
	$nodehead->insertBefore($element,$nodehead->firstChild);
	
	// transforma o html em nó
	$newDiv = $dom->createElement('div');
	$tmpDoc = new DOMDocument();
	$tmpDoc->loadHTML($barra);
	foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
			$node = $dom->importNode($node, true);
			$newDiv->appendChild($node);
	}
	// insere o novo nó na página
	$firstItem = $dom->getElementsByTagName('body')->item(0);
	$firstItem->insertBefore($newDiv,$firstItem->firstChild);
	
	$out = $dom->saveHTML(); //save the changes
	echo $out;		
}
else
{
	 // remove a página que não existe
	 pg_query($conn,"DELETE FROM paginas WHERE pagcod=".$_GET["codpag"]);
	 echo "Essa página não existe mais!";
}


?>
