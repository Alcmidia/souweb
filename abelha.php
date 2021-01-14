<?php 

include ('funcoes/conexao.php');

include ('funcoes/simple_html_dom.php');

function url_exists($url){
    if ((strpos($url, "http")) === false) $url = "http://" . $url;
    if (is_array(@get_headers($url)))
         return true;
    else
         return false;
}
?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>SouWeb - Aranha</title>
</head>
<META NAME="ROBOTS" CONTENT="NOINDEX">
  <META HTTP-EQUIV="Refresh" CONTENT="1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  

<style type="text/css">
body
{
	background-color: #000000;
	color: #33AA33;
  font-family: fixedsys, consolas, monospace;
	font-size:14px;
}
</style>	
	
<body>

<?
// otimizar querys aproveitando as mesmas (ex query do dominio)
// criar uma tabela com estatísticas de rastreamento
// aumentar a veolocidade do rastreador pegando varias páginas de dominios diferentes ao mesmo tempo
// fazer robo tratar ../
// fazer pegar link e rastrear https

$tabtmp=pg_query($conn, "SELECT * FROM temporaria ORDER BY random() LIMIT 10");
while ($arrtmp=pg_fetch_array($tabtmp))
{
  $url_atual=rtrim($arrtmp["tmpurl"],"/");
	
  echo '<br><br>'.$url_atual;
	
	if (url_exists($url_atual))
  {

		// verifica se a página já foi salva
		$tabpag=pg_query($conn, "SELECT * FROM paginas WHERE pagurl='".$url_atual."'");
		if ($arrpag=pg_fetch_array($tabpag))
		{
		  echo "<br>ja existe: ".$arrpag["pagcod"];
		  pg_query($conn,"DELETE FROM temporaria WHERE tmpcod=".$arrtmp["tmpcod"]);
		}
		else
		{
			// atualiza temporaia como rastreada se travar e carregar de novo apaga.
			if ($arrtmp["tmplid"]==true)
			{
		    //echo "<br>Excedeu memória: ".$arrtmp["tmpcod"];
		    pg_query($conn,"DELETE FROM temporaria WHERE tmpcod=".$arrtmp["tmpcod"]);				
			}
			else
			{
		    pg_query($conn,"UPDATE temporaria SET tmplid=true WHERE tmpcod=".$arrtmp["tmpcod"]);
			}
			// se existe a página ele começa a distrinchar pra salvar
			if (@$content = file_get_html('http://'.$url_atual))
			{
				
		    // pega o titulo do página
				$tittag = $content->find('title',0);
				$titulo=trim($tittag->innertext);
				$meta=$content->find('meta[name=description]',0)->content;
				echo '<br>META:'.$meta;
				// se a página não tiver titulo nao rastreia
				if ($titulo)
				{
					
					// se o número de urls for mais que 10 mil na tabela temporaria ele nao salva outras urls
					$tabqtd=pg_query($conn, "SELECT COUNT (tmpcod) as qtd FROM temporaria");
					$counttmp=pg_fetch_array($tabqtd);
					// pega o dominio para montar as outras urls
					$dominio_atual=parse_url('http://'.$url_atual, PHP_URL_HOST);
					
					// verifica se o domínio tem mais de 30 páginas
					$tabdom=pg_query($conn, "SELECT * FROM dominios WHERE domnom='".$dominio_atual."'");
					if ($arrdom=pg_fetch_array($tabdom))
					{
						if ($arrdom["dompag"]>=50) { $espaco_dominio=0; } else { $espaco_dominio=1; }
					}
					else
					{
						$espaco_dominio=1;
					}

					if (($counttmp["qtd"]<10000) and ($espaco_dominio))
					{
					
						//print_r ($matches[1]);
					  $i=0;
					
						// Find all links 
						foreach($content->find('a') as $lnktag) 
					  {
					    $pag_links=$lnktag->href ;
							// verifica se não é um link extrangeiro ou uma ancora interna do wiki, ou sem tem aspas simples
							if
							(
							(@substr_compare($pag_links,"//",0, 2))
							AND	(stripos($pag_links,"javascript:")===false)
							AND	(@substr_compare($pag_links,"#",0, 1))
							AND	(stripos($pag_links,".jpg")===false)
							AND	(stripos($pag_links,".jpeg")===false)
							AND	(stripos($pag_links,".pdf")===false)
							AND	(stripos($pag_links,".gif")===false)
							AND	(stripos($pag_links,".png")===false)
							AND	(stripos($pag_links,".xls")===false)
							AND	(stripos($pag_links,".doc")===false)
							AND	(stripos($pag_links,".zip")===false)
							AND	(stripos($pag_links,".exe")===false)
							AND	(stripos($pag_links,".ppt")===false)
							AND	(stripos($pag_links,".mov")===false)
							AND	(stripos($pag_links,".wmv")===false)						
							AND	(stripos($pag_links,".mp3")===false)
							AND	(stripos($pag_links,".swf")===false)						
							AND	(stripos($pag_links,".xml")===false)
							AND	(stripos($pag_links,"/rss")===false)													
							AND	(stripos($pag_links,"mailto:")===false)
							AND	(stripos($pag_links,"/feed")===false)
							AND	(stripos($pag_links,"#feed")===false)
							AND	(stripos($pag_links,"#comment")===false)
							AND	(stripos($pag_links,"#seecomments_list")===false)
							AND	(stripos($pag_links,"#calendar")===false)
							AND	(stripos($pag_links,"#addcomment")===false)
							AND	(stripos($pag_links,"#respond")===false)
							AND	(stripos($pag_links,"?showComment")===false)
							AND	(stripos($pag_links,"ftp://")===false)
							AND	(stripos($pag_links,"https://")===false)
							AND	(stripos($pag_links,"../")===false)
							AND	(stripos($pag_links,"bbb13")===false)
							AND	(stripos($pag_links,"omelete.uol.com.br/perfil")===false)
							AND	(stripos($pag_links,"click.uol.com.br")===false)
							AND	(stripos($pag_links,"clicklogger.rm.uol.com.br")===false)						
							AND	(stripos($pag_links,"maps.google.com")===false)
							AND	(stripos($pag_links,"orkut.com")===false)						
							AND	(stripos($pag_links,"facebook.com")===false)
							AND	(stripos($pag_links,"abril.com.br")===false)				
							AND	(stripos($pag_links,"google.com")===false)				
							)
							{
					      //verifica se o link vai com barra ou sem barra
					      if (substr_compare($pag_links,"/",0, 1)) { $barra='/'; } else { $barra=''; }
					      // verifica se tem http se não tiver colocar o dominio no link
					      if (@substr_compare($pag_links,"http://",0, 7)) { $pag_links=$dominio_atual.$barra.$pag_links; } else { $pag_links=str_replace("http://", "", $pag_links);  }
					      //tira a barra no final do link
							  $pag_links=rtrim($pag_links,"/");
								
							  // pega o dominios dos links da página para verificação
							  $dominio_link=parse_url('http://'.$pag_links, PHP_URL_HOST);
								//echo '<br>testedominiodolink:'.$dominio_link.'->'.substr_compare($dominio_link,".br",-3,3);
							  // veririfca se é br ou wikipedia pt
								if ($dominio_link)
								{
									
					        if  ( (!substr_compare($dominio_link,".br",-3,3,true)) or ($dominio_link=='pt.wikipedia.org') or ($dominio_link=='pt.wikihow.com') ) 
								  {
										// verifica se a url já está na tabela temporária
										$tabtmp2=pg_query($conn, "SELECT * FROM temporaria WHERE tmpurl='".$pag_links."'");
										if ($arrtmp2=pg_fetch_array($tabtmp2))
										{
											//echo '<br>Url já está como temporaria: '.$arrtmp2["pagcod"].' - '.$pag_links;										
										}
										else
										{
											// verifica se a página já foi salva
											$tabpag=pg_query($conn, "SELECT * FROM paginas WHERE pagurl='".$pag_links."'");
											if ($arrpag=pg_fetch_array($tabpag))
											{
												//echo '<br>Página já foi salva: '.$arrpag["pagcod"].' - '.$pag_links;
											}
											else
											{
												// salva apenas 30 links não salvos por página
												if ($i<5)
												{
													$i++;
												  // salva links temporários no banco
												  @pg_query($conn,"INSERT INTO temporaria (tmpurl) VALUES ('".$pag_links."')");
												  echo '<br>'.$i.' - '.$pag_links;																					
												}
											}
										}
								  }
								}
							}
							else
							{
								//echo '<br>Não é uma URL válida: '.$arrpag["pagcod"].' - '.$pag_links;							
							}
					  }
					}
					echo '<br>Temp:'.$counttmp["qtd"];
	
					// texto sem o html
					$semtags=$content->plaintext;
	
				  $titulo=mb_convert_encoding($titulo ,'UTF-8',mb_detect_encoding($titulo ,"UTF-8, ISO-8859-1, ASCII"));
				  $semtags=mb_convert_encoding($semtags ,'UTF-8',mb_detect_encoding($semtags ,"UTF-8, ISO-8859-1, ASCII"));
				  $url_atual=mb_convert_encoding($url_atual ,'UTF-8',mb_detect_encoding($url_atual ,"UTF-8, ISO-8859-1, ASCII"));
  				if ($meta) { $meta=mb_convert_encoding($meta ,'UTF-8',mb_detect_encoding($meta ,"UTF-8, ISO-8859-1, ASCII")); } else { $meta=0; }				
					
					// converte os caracteres html(ex: $nbsp) em caracteres especiais				
					$semtags=html_entity_decode($semtags,ENT_NOQUOTES,"UTF-8");
					$titulo=html_entity_decode($titulo,ENT_NOQUOTES,"UTF-8");						

					// remove o titulo do texto, tira os espaços antes pra dar certo o tamanho
					$semtags=substr(trim($semtags), strlen($titulo));
				
					$semtags=addslashes($semtags);
					$titulo=addslashes($titulo);

					echo '<br>Titulo:'.$titulo;			
					//echo '<br>'.$semtags;				
		    }
				else
				{
					echo '<br>Página sem Título';							
				}				

				
		    // se a página tiver vazia ou semtitulo não salva
		    if (($semtags) and ($titulo))
		    {
          // verifica se dominio já foi salvo
					$tabdom=pg_query($conn, "SELECT * FROM dominios WHERE domnom='".$dominio_atual."'");
					if ($arrdom=pg_fetch_array($tabdom))
					{
						$coddom=$arrdom["domcod"];
						pg_query($conn,"UPDATE dominios SET dompag=dompag+1 WHERE domcod=$coddom");
						echo '<br>Domínio já existe: '.$coddom;
					}
					else
					{
						// salva o domínio e pega o codigo
						$tabcod=pg_query($conn,"INSERT INTO dominios (domnom,dompag) VALUES ('".$dominio_atual."',1) RETURNING domcod");
						$arrcod = pg_fetch_row($tabcod);
						$coddom = $arrcod[0];
						echo '<br>Domínio salvo: '.$coddom.' - '.$dominio_atual;
					}
					
		      // insere página no banco
		      pg_query($conn,"INSERT INTO paginas (pagurl,pagtit,pagtex,domcod,pagmet) VALUES ('$url_atual','".$titulo."','$semtags',$coddom,'$meta')");
		      echo "<br>Página Salva! ";
		    }
		    else
		    {
		      echo '<br>Página em Branco!';
		    }
				// remove url temporaria salva o em branco
		    pg_query($conn,"DELETE FROM temporaria WHERE tmpcod=".$arrtmp["tmpcod"]);
		  }
		  else
		  {
		    echo '<br>Página não é HTML!';
		    pg_query($conn,"DELETE FROM temporaria WHERE tmpcod=".$arrtmp["tmpcod"]);
			}
		}
	}
	else
	{
    echo '<br>Página não Existe!';
    pg_query($conn,"DELETE FROM temporaria WHERE tmpcod=".$arrtmp["tmpcod"]);		
	}
}

?>


</body>
</html>
