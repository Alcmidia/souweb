<?php

echo '<div class="divres" style="background-color:'.$fundo.'">';

echo '<div class="divtit"><a href="pagina.php?codpag='.$arrpag["pagcod"].'&pagpes='.$pesquisa.'" target="_blank" rel="nofollow">'.$arrpag["headtit"].'</a></div>';


if ( ($arrpag["headmeta"]) and (strlen($arrpag["headmeta"])>100) )
{
	echo '<div class="divtex">'.$arrpag["headmeta"].'</div>';
}
else
{
  echo '<div class="divtex">'.$arrpag["headline"].'</div>';	
}

//echo '<div class="divtex">'.$arrpag["headline"].'</div>';	


echo '<a class="divlnk" href="pagina.php?codpag='.$arrpag["pagcod"].'&pagpes='.$pesquisa.'" target="_blank" rel="nofollow">'.corta_link(trata_url($arrpag["pagurl"])).'</a></div>';


?>
