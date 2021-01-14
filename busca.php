<?php
session_start();
include ("funcoes/conexao.php");

include ("funcoes/trata_strings.php");

include ("funcoes/calcula_tempo.php");

ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

// agrupar o dominio que tem mais de um resultado
// quando começar a ficar lento separar o headline em outro select na votação também
// quando a primeira página só tiver resultado das palavras pontuadas, mostrar também outros links randomicos.

// Create new Timing class with \n break
$timing = new Timing("\n");
 
// Start timing
$timing->start();


// verifica de veio do urlfriendely ou da busca
if ($_GET["parametros"])
{
  $arrurl=explode("/",$_GET["parametros"]);
	if (isset($arrurl[1]))
	{
		if ($arrurl[0]=="humor") { $naobusca=1; }
	  if (is_numeric($arrurl[1])) {  $pag=$arrurl[1]; }	else { $arrurl[0]=$arrurl[0].' '.$arrurl[1]; }
	}
	$pesquisa=str_replace("-", " ", $arrurl[0]);
}
else
{
  $pag=$_GET["pag"];
  $pesquisa=$_GET["pesquisa"];
	if (($pesquisa=='') or ($pesquisa==' ')) { $naobusca=1; }
}

// termo de pesquisa para o rank
$pesfull=textofull($pesquisa);
// termo de pesquisa para a busca exata
$buspon=gera_busca($pesquisa);
//echo 'teste:'.$buspon;
//termo de pesquisa para a busca composta
$buscom=textofull($buspon);


// salva na sessao a busca para se tiver acento funcionar a páginação
//echo '<br>atual:'.$pesquisa;
//echo '<br>anterior:'.$_SESSION["pesquisa"];

if ($buspon==gera_busca($_SESSION["pesquisa"]))
{
	$pesquisa=$_SESSION["pesquisa"];
	$pesfull=textofull($pesquisa);
}
else
{
	if (tem_acento($pesquisa))
	{
    $_SESSION["pesquisa"]=$pesquisa;				
	}
}

//echo 'teste'.$buspon;

if (!$pag) { $pag=1; }
$inicio=10*($pag-1);
// echo "teste:".$max."-".$min;

// esquema para bloquear os robos do wordpress
if (!$naobusca)
{
  include ("funcoes/select.php");	
?>

<!DOCTYPE HTML>

<html>
<head>
<?php
/*
// pede para nao indexar se não tem a busca salva com pontos
$sqlindex  = "SELECT T1.buscod FROM buscas T1, rel_pag_bus T2 WHERE T1.bustxt='$buspon' AND T1.buscod=T2.buscod AND T2.rpbpon>0";	
$tabindex=pg_query($conn, $sqlindex);
$qtdindex = pg_num_rows($tabindex);
// nao indexa se não tiver busca, se a paginas for maior que 10, ou se nao tiver resultado
if ((!$qtdindex) or ($pag>10) or ($qtdtot==0))
{
	echo '<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">';
}
*/

?>	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo ucfirst($pesquisa).' - '; ?><?php if ($pag>1) { echo 'Página '.$pag.' - '; } ?>SouWeb - O buscador que aprende com você!</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <base href="/">

	<script> 
    document.createElement('header');
    document.createElement('nav');
    document.createElement('section');
    document.createElement('article');
    document.createElement('aside');
    document.createElement('footer');
  </script>	
		
	<script type="text/javascript">
	
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-2307987-40']);
		_gaq.push(['_trackPageview']);
	
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	
	</script>
  <link rel="stylesheet" type="text/css" href="scripts/layout.css">
</head>


<body>
<div id="wrapper">
  <header>
		<div id="beta"></div>
	  <a href="/"><img id="logo" src="souweb.png"></a>
    <form action="busca.php">
		  <input id="inppes" type="Text" name="pesquisa" value="<?php echo $pesquisa ?>"> <input style="font-family:Arial; font-size:16px;" type="Submit" value=" Pesquisar ">				
    </form>
		<div id="divinf">
		  <div id="divnum"><?php echo $n; ?> Resultados</div>			
		</div>
  </header>
	
  <article>		
	    <div id="divpal">Resultado da Pesquisa: <span style="color: #009900;"><?php echo $pesquisa; ?></span></div>	
		<?php
		include ('includes/busca_dicas.php');	
		
		if ($qtdexa)
		{
			// mostra resultado da busca por pontos
			include ('includes/resultado_exato.php');	
		}
		
		if ($qtdcom)
		{
			// mostra resultado da busca por pontos
			include ('includes/resultado_composto.php');	
		}
		
		if ($qtdtit)
		{
			// mostra resultado da busca normal
			include ('includes/resultado_titulo.php');
		}		

		if ($qtdrnk)
		{
			// mostra resultado da busca normal
			include ('includes/resultado_rank.php');
		}
		
		if ($qtdneg)
		{
			// mostra resultado da busca por pontos
			include ('includes/resultado_negativo.php');
		}

    echo '<div id="divpag">';
				
		include ('includes/paginacao.php');
		echo '<br><br>';
			// Stop/end timing
			$timing->stop();
		 
			// Print only total execution time
			$timing->printTotalExecutionTime();
		 
			// Print full stats
			//$timing->printFullStats();
			
		echo '</div>';	
		?>
		<form style="margin:30px;" action="aranha.php" method="post">
			Envie uma url para essa busca: <span style="color: #009900;"><?php echo $pesquisa; ?></span><br>
			<input id="inpind" type="text" name="urlsug">
			<input type="submit" value="Enviar">
		</form>
	</article>

</div>	

</body>

</html>
<?php pg_close($conn); ?>

<?php } ?>
