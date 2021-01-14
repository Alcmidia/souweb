<?php

$sqldic = "SELECT * FROM buscas WHERE bustxt_ft @@ to_tsquery('portuguese','$buscom%') AND
            buscod NOT IN ( SELECT buscod FROM buscas WHERE bustxt='$buspon' )
            ORDER BY random() LIMIT 6";
						
						//echo $sqldic;
						
$tabdic = pg_query($conn, $sqldic);
$qtddic = pg_num_rows($tabdic);

if ($qtddic>=1)
{
	echo '<ul id="lisdic"><li>Buscas de sugeridas: </li>';
  while ($regdic = pg_fetch_array($tabdic))
  {
    if ($regdic["busdic"]) { $txtbus=$regdic["busdic"]; } else { $txtbus=$regdic["bustxt"]; }		
	  echo '<li><a href="/'.gera_link($regdic["bustxt"]).'">'.$txtbus.'</a> </li>';
  }
	echo '</ul>';
}
?>
