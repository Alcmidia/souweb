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
    if ($numpag>15) 
	{ 
      $pagini=1;
	  $pagfin=15; 
	  // Se a página selecionada for maior que sete
	  if ($pag>7) 
	  {
	    if (($pag+7)<$numpag) 
	    { 
	      $pagini=$pag-7;
	      $pagfin=$pag+7;
	    }
	    else 
	    { 
	      $pagini=$numpag-14;
	      $pagfin=$numpag;
        }
	  }
	} 
	else 
	{ 
      $pagini=1;
	  $pagfin=$numpag; 
    }
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
	if ($pag<$numpag) { echo '<a class="Links" href="'.$parametros.'/'.($pag+1).'">Próxima</a> &gt;&gt;'; }
  }	
?>
