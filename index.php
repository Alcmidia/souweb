<?php
include ("funcoes/conexao.php");
include ("funcoes/trata_strings.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!DOCTYPE HTML>

<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SouWeb - O buscador que aprende com você!</title>	
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
  <link rel="stylesheet" type="text/css" href="scripts/home.css">
</head>

<body>

<div id="wrapper">
  <div id="logo"><div id="beta"></div></div>
  <form action="busca.php">
	  <input id="inppes" name="pesquisa" type="Text" autofocus>
		<input type="Submit" value=" Pesquisar ">
	  <div id="slogan">O buscador 100% nacional que aprende com você!</div>  
  </form>
  <?php
  // lista as buscas
  $sqldic  = "SELECT * FROM buscas ORDER BY random() LIMIT 8";	
  $tabdic = pg_query($conn, $sqldic);
  $qtddic = pg_num_rows($tabdic);
  if ($qtddic>1)
  {
    echo '<div id="ultbox"><div id="ulttit">Últimas buscas:</div>';
    while ($regdic = pg_fetch_array($tabdic))
    {
      $txtbus=$regdic["bustxt"];
      echo '<a id="ultlnk" href="/'.gera_link($regdic["bustxt"]).'">'.$txtbus.'</a> ';
    }
    echo '</div>';
  }
  ?>
</div>  

</body>

</html>
