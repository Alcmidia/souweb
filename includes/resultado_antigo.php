<?php
  echo '<div style="padding:10px; margin:10px; background-color:'.$fundo.'">';
  if ($arrpag["pagtit"])
  {
    echo '<a class="Titulo" href="clique.php?codpag='.$arrpag["pagcod"].'&pagpes='.$pesquisa.'" target="_blank">'.destaca( $pesquisa, $arrpag["pagtit"], '<span style="font-weight: Bold;">%s</span>' ).'</a><br>';
  }
  else
  {
    echo '<a class="Titulo" href="'.$arrpag["pagurl"].'" target="_blank">Untitled</a><br>';
  }


// Finally, replace any non-space whitespace, with a space
$limpo = preg_replace('/[\n\r\t]/', ' ', $arrpag["pagtex"]);
// Now remove any doubled-up whitespace
$limpo = preg_replace('/\s(?=\s)/', '', $limpo);
//echo '<pre>'.$limpo.'</pre>';


// encontra a posição e mostra os 300 caracteres da busca completa
$pos = stripos($limpo, $pesquisa);
if (isset($pos))
{
  if ($pos>100)
  {
    $trecho = substr ( $limpo , $pos-100, 300);
    echo '...'.destaca( $pesquisa, $trecho, '<span style="font-weight: Bold;">%s</span>' ).'...'; 
  }
  else
  {
    $trecho = substr ( $limpo , $pos, 300);
    echo '...'.destaca( $pesquisa, $trecho, '<span style="font-weight: Bold;">%s</span>' ).'...'; 
  }
}
else
// se não achar a busca completa tenta encontrar os pedaços
{
  $arrpes=explode (" ",$pesquisa);
  if ($arrpes[1])
  {
    foreach ($arrpes as $pespart)
    {
	  //tira as preposições
	    if ($pespart!='de')
	    {
        // encontra a posição e mostra os 200 caracteres
        $pos = stripos($limpo, $pespart);
		    if ($pos)
		    {
          $trecho = substr ( $limpo , $pos-100, 200);
          echo '...'.destaca( $pespart, $trecho, '<span style="font-weight: Bold;">%s</span>' ).'...';
		    }
	    }
    }
  }
}


echo '<br><a style="color:#008000;" href="clique.php?codpag='.$arrpag["pagcod"].'&pagpes='.$pesquisa.'" target="_blank">'.destaca( $pesquisa, $arrpag["pagurl"], '<span style="font-weight: Bold;">%s</span>' ).'</a></div>';

?>