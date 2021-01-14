<?php

// select pras páginas com pontos positivos pra busca exata
$sqlexa  = "SELECT T3.pagcod,T3.pagurl,
		        ts_headline(pagtex , to_tsquery('$pesfull'), 'StartSel=<b>, StopSel=&nbsp;</b>, MinWords=18, MaxWords=19, MaxFragments=2, FragmentDelimiter=\" ... \"' ) AS headline,
						ts_headline(pagtit , to_tsquery('$pesfull'), 'MinWords=18, MaxWords=20' ) AS headtit
						FROM buscas T1, rel_pag_bus T2, paginas T3 WHERE
						bustxt='$buspon' AND 
						T1.buscod=T2.buscod AND 
						T2.pagcod=T3.pagcod AND
						T2.rpbpon>0
						ORDER BY rpbpon DESC
						LIMIT 10 OFFSET $inicio";
						
						//echo $sqlexa;
						
$tabexa=pg_query($conn, $sqlexa);
$qtdexa = pg_num_rows($tabexa);
if ($qtdexa<10)
{						
  $numcom=10-$qtdexa;	
  // select pras páginas com pontos positivos	de palavras compostas				
  $sqlcom  = "SELECT DISTINCT ON (pagcod) * FROM
	            (
						    SELECT T3.pagcod,T3.pagurl,
								ts_headline(pagtex , to_tsquery('$pesfull'), 'StartSel=<b>, StopSel=&nbsp;</b>, MinWords=18, MaxWords=19, MaxFragments=2, FragmentDelimiter=\" ... \"' ) AS headline,
						    ts_headline(pagtit , to_tsquery('$pesfull'), 'MinWords=18, MaxWords=20' ) AS headtit
						    FROM buscas T1, rel_pag_bus T2, paginas T3 WHERE
						    bustxt_ft @@ to_tsquery('portuguese','$buscom%') AND 
						    T1.buscod=T2.buscod AND 
						    T2.pagcod=T3.pagcod AND
						    T2.rpbpon>0 AND
						    T3.pagcod NOT IN							
		            (
			            SELECT T2.pagcod FROM buscas T1, rel_pag_bus T2 WHERE
                  bustxt='$buspon' AND T1.buscod=T2.buscod AND T2.rpbpon<>0					
		            )
						    ORDER BY rpbpon DESC
						    LIMIT $numcom OFFSET $inicio
						  ) AS tabcom";
					
					//echo $sqlcom;
					
  $tabcom=pg_query($conn, $sqlcom);
  $qtdcom = pg_num_rows($tabcom);

  if (($qtdexa+$qtdcom)<10)
  {
    $numtit=10-($qtdcom+$qtdexa);

    // select fultext do titulo
		$sqltit  = "SELECT pagcod,pagurl,
							  ts_headline(pagtex , to_tsquery('$pesfull'), 'StartSel=<b>, StopSel=&nbsp;</b>, MinWords=18, MaxWords=19, MaxFragments=2, FragmentDelimiter=\" ... \"' ) AS headline,
							  ts_headline(pagtit , to_tsquery('$pesfull'), 'MinWords=18, MaxWords=20' ) AS headtit FROM
								(
								  SELECT * FROM
								  ( 
								    SELECT  DISTINCT ON (domcod,pagtit) *
								    FROM paginas WHERE pagtit_ft @@ to_tsquery('portuguese','$pesfull%')  AND
										pagcod NOT IN
							      (
									    SELECT T2.pagcod FROM buscas T1, rel_pag_bus T2 WHERE
									    (bustxt_ft @@ to_tsquery('portuguese','$buscom%') OR bustxt='$buspon') AND T1.buscod=T2.buscod AND T2.rpbpon<>0					
									  )
									) AS tabhead
									LIMIT $numtit OFFSET $inicio 
								) as tabrank";	
	
	  //echo $sqltit;
		
		$tabtit = pg_query($conn, $sqltit);
		$qtdtit = pg_num_rows($tabtit);
	
		if (($qtdexa+$qtdcom)<10)
		{
		  $numrnk=10-($qtdtit+$qtdcom+$qtdexa);
		  // select fultext normal com raking
		
		  $sqlrnk  = "SELECT  pagcod,pagurl,
					        ts_headline(pagtex , to_tsquery('$pesfull'), 'StartSel=<b>, StopSel=&nbsp;</b>, MinWords=18, MaxWords=19, MaxFragments=2, FragmentDelimiter=\" ... \"' ) AS headline,
				          ts_headline(pagtit , to_tsquery('$pesfull'), 'MinWords=18, MaxWords=20' ) AS headtit FROM
									(
									  SELECT * FROM
									  ( 
									    SELECT  DISTINCT ON (domcod,pagtit) *, ts_rank_cd( pagtex_ft, plainto_tsquery('portuguese', '$pesfull'), 8) AS rank
									    FROM paginas WHERE plainto_tsquery('portuguese', '$pesfull%') @@ pagtex_ft  AND
											pagcod NOT IN
							        (
								        SELECT T2.pagcod FROM buscas T1, rel_pag_bus T2 WHERE (bustxt_ft @@ to_tsquery('portuguese','$buscom%') OR bustxt='$buspon') AND T1.buscod=T2.buscod AND T2.rpbpon<>0					
							        )
											AND pagcod NOT IN
											(
												SELECT  pagcod  FROM paginas WHERE pagtit_ft @@ to_tsquery('portuguese','$pesfull%')
											)
										) AS tabhead
										ORDER BY rank DESC LIMIT $numrnk OFFSET $inicio 
									) as tabrank";
									
									
//		  $sqlrnkold  = "SELECT  pagcod,pagurl,
//					        ts_headline(pagtex , to_tsquery('$pesfull'), 'StartSel=<b>, StopSel=&nbsp;</b>, MinWords=18, MaxWords=19, MaxFragments=2, FragmentDelimiter=\" ... \"' ) AS headline,
//				          ts_headline(pagtit , to_tsquery('$pesfull'), 'MinWords=18, MaxWords=20' ) AS headtit FROM
//									(
//									  SELECT * FROM
//									  ( 
//									    SELECT  DISTINCT ON (domcod,pagtit) *, (ts_rank_cd( pagtit_ft, plainto_tsquery('portuguese', '$pesfull'), 4 /* rank */ )) AS rank1, (ts_rank_cd( pagtex_ft, plainto_tsquery('portuguese', '$pesfull'), 8 /* rank */ )) AS rank2
//									    FROM paginas WHERE plainto_tsquery('portuguese', '$pesfull%') @@ pagtex_ft  AND
//											pagcod NOT IN
//							        (
//								        SELECT T2.pagcod FROM buscas T1, rel_pag_bus T2 WHERE
//						            (bustxt_ft @@ to_tsquery('portuguese','$buscom%') OR bustxt='$buspon') AND T1.buscod=T2.buscod AND T2.rpbpon<>0					
//							        )
//										) AS tabhead
//										ORDER BY rank1 DESC, rank2 DESC LIMIT $numrnk OFFSET $inicio 
//									) as tabrank";									
			
			//echo $sqlrnk;
						
			$tabrnk = pg_query($conn, $sqlrnk);
			$qtdrnk = pg_num_rows($tabrnk);

			// verifica se é a ultima página e não uma página alem da ultima
			if ( (($qtdtit+$qtdexa+$qtdcom+$qtdrnk)>0) and (($qtdtit+$qtdexa+$qtdcom+$qtdrnk)<10) )
			{
			// select pras páginas com pontos negativos
			$sqlneg  = "SELECT T3.pagcod,T3.pagurl,
						      ts_headline(pagtex , to_tsquery('$pesfull'), 'StartSel=<b>, StopSel=&nbsp;</b>, MinWords=18, MaxWords=19, MaxFragments=2, FragmentDelimiter=\" ... \"' ) AS headline,
						      ts_headline(pagtit , to_tsquery('$pesfull'), 'MinWords=18, MaxWords=20' ) AS headtit
						      FROM buscas T1, rel_pag_bus T2, paginas T3 WHERE
						      bustxt_ft @@ to_tsquery('portuguese','$buscom%') AND 
						      T1.buscod=T2.buscod AND 
						      T2.pagcod=T3.pagcod AND
									T2.rpbpon<0
						      ORDER BY T2.rpbpon DESC LIMIT 10";
			$tabneg=pg_query($conn, $sqlneg);
			$qtdneg = pg_num_rows($tabneg);
			}
		}
	}
}
$qtdtot=$qtdexa+$qtdcom+$qtdrnk+$qtdneg;

		
// conta registro para o numero de páginas
//$sqlc = "SELECT count(pagcod) as qtd FROM paginas WHERE pagtit_ft @@ to_tsquery('portuguese','$pesfull%') OR pagtex_ft @@ to_tsquery('portuguese','$pesfull%')";
// função que criamos no postgres para estimar o número de registro
$sqlc = "SELECT count_estimate('SELECT pagcod FROM paginas WHERE pagtit_ft @@ to_tsquery(''portuguese'',''$pesfull%'') OR pagtex_ft @@ to_tsquery(''portuguese'',''$pesfull%'')') as qtd";
//echo $sqlc;
$registros = pg_query ($conn, $sqlc);
$reg = pg_fetch_array($registros,0);
$n = $reg["qtd"];
$numpag = $n;

?>