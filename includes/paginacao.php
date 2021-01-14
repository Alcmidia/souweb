<?php
// paginação
  if ($_GET["pesquisa"]) { $parametros=gera_link($buspon); } else { $parametros = $arrurl[0]; }
	
  $numpag=floor($n/10);
  if ($n%10) { $numpag++; }
 //Mostra páginas de 15 em 15
  if ($numpag>1)
  {
    if ($pag>1) { echo '&lt;&lt; <a class="Links" href="'.$parametros.'/'.($pag-1).'">Anterior</a>'; }
	  echo ' | ';
	  //Se tiver mais que 15 páginas e a página selecioada é menor que sete

    $pagini=1;
	  if ($numpag>10) { $pagfin=10; } else { $pagfin=$numpag; } 

    for ($i=$pagini ; $i<=$pagfin ; $i++ )
    {
      if (!$pag) { $pag=1; }
	    if ($pag==$i) 
	    { 
	      echo '<span class="Selecionado">'.$i.'</span> | ';
	    } 
	    else 
	    { 
	      echo '<a class="Links" href="'.$parametros.'/'.$i.'">'.$i.'</a> | ';
	    }
    }
	  if ($pag<$pagfin) { echo '<a class="Links" href="'.$parametros.'/'.($pag+1).'">Próxima</a> &gt;&gt;'; }
  }	
?>
