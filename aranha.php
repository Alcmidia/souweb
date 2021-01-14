<?php
error_reporting(E_ERROR | E_PARSE);

//ini_set('memory_limit', '256M');


header('Content-Type: text/html; charset=utf-8');

include ('funcoes/conexao.php');

ini_set('max_execution_time',900); # set execution to 60 seconds

//ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

function remove_http($url) {
   $disallowed = array('http://', 'https://');
   foreach($disallowed as $d) {
      if(strpos($url, $d) === 0) {
         return str_replace($d, '', $url);
      }
   }
   return $url;
}

?> 

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>SouWeb - Aranha</title>
</head>
<META NAME="ROBOTS" CONTENT="NOINDEX">
<?php
  if (!$_POST["urlsug"]) { echo '<META HTTP-EQUIV="Refresh" CONTENT="1">'; }
?>

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

<?php
// otimizar querys aproveitando as mesmas (ex query do dominio)
// criar uma tabela com estatísticas de rastreamento
// aumentar a veolocidade do rastreador pegando varias páginas de dominios diferentes ao mesmo tempo
// fazer robo tratar ../
// fazer pegar link e rastrear https

$tabtmp=pg_query($conn, "SELECT * FROM temporaria ORDER BY random() LIMIT 1");
while ($arrtmp=pg_fetch_array($tabtmp))
{


  if ($_POST["urlsug"]) {
		$url_atual=rtrim($_POST["urlsug"],"/");
		$url_atual = remove_http($url_atual);
	} else {
		$url_atual=rtrim($arrtmp["tmpurl"],"/");		
	}
	
  echo $url_atual;
	
	$dom = new DomDocument('1.0', 'UTF-8');
	libxml_use_internal_errors(true);
	// se estiver dando muito fatal error de memória limitar os caracteres do arquivo pego
	# $str = file_get_contents('https://'.$url_atual, FALSE, NULL, 0, 10000);
	$str = file_get_contents('https://'.$url_atual);
	$dom->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));
	libxml_clear_errors();

  
  //$dom = new DOMDocument;
  //$dom->loadhtmlfile('https://'.$url_atual);
    
	if ($dom)                                                          
  { 

		// verifica se a página já foi salva
		$tabpag=pg_query($conn, "SELECT * FROM paginas WHERE pagurl='".$url_atual."'");
		if ($arrpag=pg_fetch_array($tabpag))
		{
			//AQUI VAI O UPDATE DOS DADOS DA PÁGINA
		  echo "<br>ja existe: ".$arrpag["pagcod"];
		  pg_query($conn,"DELETE FROM temporaria WHERE tmpcod=".$arrtmp["tmpcod"]);
		}
		else
		{
			// atualiza temporaia como rastreada se travar e carregar de novo apaga.
			if ($arrtmp["tmplid"]==true)
			{
		    echo "<br>Excedeu memória: ".$arrtmp["tmpcod"];
		    pg_query($conn,"DELETE FROM temporaria WHERE tmpcod=".$arrtmp["tmpcod"]);				
			}
			else
			{
		    pg_query($conn,"UPDATE temporaria SET tmplid=true WHERE tmpcod=".$arrtmp["tmpcod"]);
			}
			// se existe a página ele começa a distrinchar pra salvar
        
  
      $nodtit = $dom->getElementsByTagName('title');
      $titulo = $nodtit->item(0)->nodeValue;
      //echo '<br>teste'.$titulo;
			//echo $dom->saveHTML();
  
			if ($nodtit && 0<$nodtit->length)
			{
				$tipo = 0;
		    // pega o titulo do página
				$titulo=trim($titulo);
				// se a página não tiver titulo nao rastreia
					
					// se o número de urls for mais que 10 mil na tabela temporaria ele nao salva outras urls
					$tabqtd=pg_query($conn, "SELECT COUNT (tmpcod) as qtd FROM temporaria");
					$counttmp=pg_fetch_array($tabqtd);
					// pega o dominio para montar as outras urls
					$dominio_atual=parse_url('https://'.$url_atual, PHP_URL_HOST);
										
					
					// verifica se o domínio raiz nome.com.br tem mais páginas que o permitido
					$tabdom=pg_query($conn, "SELECT * FROM dominios WHERE domnom='".$dominio_atual."'");
					if ($arrdom=pg_fetch_array($tabdom))
					{
						if ($arrdom["dompag"]>=$arrdom["dommax"]) { $espaco_dominio=0; } else { $espaco_dominio=1; }
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
            $nodlnk = $dom->getElementsByTagName('a');
						$tipo = 0;
						foreach($nodlnk as $lnktag) 
					  {
              
					    $pag_links=$lnktag->getAttribute('href');
							// verifica se não é um link extrangeiro ou uma ancora interna do wiki, ou sem tem aspas simples
							if
							(
							    (@substr_compare($pag_links,"//",0, 2))
							AND ($titulo)		
							AND	(stripos($pag_links,"javascript:")===false)
							AND	(@substr_compare($pag_links,"#",0, 1))

							AND	(stripos($pag_links,".mp3")===false)
							AND	(stripos($pag_links,".wma")===false)
							AND	(stripos($pag_links,".wav")===false)
							AND	(stripos($pag_links,".mp4")===false)
							AND	(stripos($pag_links,".mov")===false)
							AND	(stripos($pag_links,".wmv")===false)
							AND	(stripos($pag_links,".mkv")===false)
							AND	(stripos($pag_links,".ogg")===false)
							AND	(stripos($pag_links,".3gp")===false)							
							AND	(stripos($pag_links,".rar")===false)
							AND	(stripos($pag_links,".apk")===false)
							AND	(stripos($pag_links,".pdf")===false)
							AND	(stripos($pag_links,".flv")===false)
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
							AND	(stripos($pag_links,".gz")===false)
							AND	(stripos($pag_links,".tif")===false)
							AND	(stripos($pag_links,".tar")===false)
							AND	(stripos($pag_links,".ods")===false)
							AND	(stripos($pag_links,".tar")===false)
							AND	(stripos($pag_links,".mpg")===false)

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
							AND	(stripos($pag_links,"http://")===false)
							AND	(stripos($pag_links,"../")===false)
							AND	(stripos($pag_links,"clique.php")===false)
							AND	(stripos($pag_links,"bbb13")===false)
							AND	(stripos($pag_links,"omelete.uol.com.br/perfil")===false)
							AND	(stripos($pag_links,"uol.com.br")===false)
							AND	(stripos($pag_links,"click.uol.com.br")===false)
							AND	(stripos($pag_links,"pagseguro.uol.com.br")===false)
							AND	(stripos($pag_links,"clicklogger.rm.uol.com.br")===false)						
							AND	(stripos($pag_links,"maps.google.com")===false)
							AND	(stripos($pag_links,"orkut.com")===false)						
							AND	(stripos($pag_links,"facebook.com")===false)
							AND	(stripos($pag_links,"abril.com.br")===false)				
							AND	(stripos($pag_links,"jusbrasil.com.br")===false)				
							AND	(stripos($pag_links,"google.com")===false)
							AND	(stripos($pag_links,"loja2.com.br")===false)
							AND	(stripos($pag_links,"olx.com.br")===false)
							AND	(stripos($pag_links,"ibooked.com.br")===false)
							AND	(stripos($pag_links,"softonic.com.br")===false)
							AND	(stripos($pag_links,"diariodasleis.com.br")===false)
							)
							{
					      //verifica se o link vai com barra ou sem barra
					      if (substr_compare($pag_links,"/",0, 1)) { $barra='/'; } else { $barra=''; }
					      // verifica se tem https se não tiver colocar o dominio no link
					      if (@substr_compare($pag_links,"https://",0, 7)) { $pag_links=$dominio_atual.$barra.$pag_links; } else { $pag_links=str_replace("https://", "", $pag_links);  }
					      //tira a barra no final do link
							  $pag_links=rtrim($pag_links,"/");
								
							  // pega o dominios dos links da página para verificação
							  $dominio_link=parse_url('https://'.$pag_links, PHP_URL_HOST);
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
												// salva apenas 10 links não salvos por página
												if ($i<10)
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

          // tira scripts do texto          
          $scriptTags = $dom->getElementsByTagName('script');
          while($scriptTags->length > 0){
              $scriptTag = $scriptTags->item(0);
              $scriptTag->parentNode->removeChild($scriptTag);
          }          

          // tira scripts do texto          
          $styleTags = $dom->getElementsByTagName('style');
          while($styleTags->length > 0){
              $styleTag = $styleTags->item(0);
              $styleTag->parentNode->removeChild($styleTag);
          }     

				  //remove os scripts
					$script = $dom->getElementsByTagName('script');
					$remove = [];
					foreach($script as $item)
					{
						$remove[] = $item;
					}
					
					foreach ($remove as $item)
					{
						$item->parentNode->removeChild($item); 
					}
	
					// texto sem o html
          $nodbod = $dom->getElementsByTagName('body');
          $semtags = $nodbod->item(0)->nodeValue;
					
					
          
          //echo $semtags;
	
				  //$titulo=mb_convert_encoding($titulo ,'UTF-8',mb_detect_encoding($titulo ,"UTF-8, ISO-8859-1, ASCII"));
				  //$semtags=mb_convert_encoding($semtags ,'UTF-8',mb_detect_encoding($semtags ,"UTF-8, ISO-8859-1, ASCII"));
				  //$url_atual=mb_convert_encoding($url_atual ,'UTF-8',mb_detect_encoding($url_atual ,"UTF-8, ISO-8859-1, ASCII"));
					
					// converte os caracteres html(ex: $nbsp) em caracteres especiais				
					//$semtags=html_entity_decode($semtags,ENT_NOQUOTES,"UTF-8");
					//$titulo=html_entity_decode($titulo,ENT_NOQUOTES,"UTF-8");						

					// remove o titulo do texto, tira os espaços antes pra dar certo o tamanho
					//$semtags=substr(trim($semtags), strlen($titulo));
				
					//$semtags=str_replace("'","\'",$semtags);
					//$titulo=str_replace("'","\'",$titulo);

					$semtags=pg_escape_string(trim($semtags));
					$titulo=pg_escape_string($titulo);

					echo '<br>Titulo:'.$titulo;			
					//echo '<br>'.$semtags;
					
					// pega audio e vídeo
					$nodsrc = $dom->getElementsByTagName('source');
					//print_r($nodsrc);
					if (($nodsrc->length)>0)
					{
			      $file = $nodsrc->item(0)->getAttribute('src');
						echo '<br>Aquivo'.$file;
						// monta url da midia
						if (stripos($file,"http")===false)
						{
							if (stripos($file,"upload.wikimedia.org"))
							{
								echo '<br> é da wiki é nosso';
								$midia='https:'.$file;								
							}
							else
							{
                // se está na raiz do site não precisa pegar a url da midia
								if (stripos($url_atual,"/")===false)
								{
									$midia = 'https://'.$url_atual.'/'.$file;																								
								}
								else
								{
									echo '<br>tem só o nome: '.$file;
									//$arrurl = explode("/",$url_atual);
									//array_pop($arrurl);
									//print_r($arrurl);
									//$midurl = implode("/",$arrurl);
									$midia = 'https://'.$dominio_atual.'/'.$file;																								
								}
							}
						}
						else
						{
							echo '<br>tem url inteira';
							$midia=$file;
						}
					}
					
					// se for audio ou vídeo salva em midia
					//echo $file;
					if(
									(stripos($file,".mp3"))
							OR	(stripos($file,".wma"))
							OR	(stripos($file,".wav"))						
							OR	(stripos($file,".ogg"))						
						)
					{
						$tipo=1;
					}
					else
					{
						if(
										(stripos($file,".mp4"))
								OR	(stripos($file,".mov"))
								OR	(stripos($file,".wmv"))						
								OR	(stripos($file,".mkv"))						
								OR	(stripos($file,".3gp"))						
							)
						{
							$tipo=2;
						}		
					}					
					//echo '<br><br>midia: '.$midia;

		

				
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

						// pega o subdominio pois se for diferente diferente de www ou diferente de vazio permite apenas 10 páginas
						$auxdom = explode(".",$dominio_atual);
						end($auxdom);
						$a1 = prev($auxdom);
						$a2 = prev($auxdom);
						$a3 = prev($auxdom);
						$subdom =  $a3;
						if (($subdom=='www') or ($subdom=='')) { $totaldepaginas=100; } else { $totaldepaginas=10; }
						echo '<br><br>dominio sub: '.$subdom.' - '.$totaldepaginas.'<br>';
						
						// salva o domínio e pega o codigo
						$tabcod=pg_query($conn,"INSERT INTO dominios (domnom,dompag,dommax) VALUES ('".$dominio_atual."',1,".$totaldepaginas.") RETURNING domcod");
						$arrcod = pg_fetch_row($tabcod);
						$coddom = $arrcod[0];
						echo '<br>Domínio salvo: '.$coddom.' - '.$dominio_atual;
					}
					
		      // insere página no banco
		      $tabcod = pg_query($conn,"INSERT INTO paginas (pagurl,pagtit,pagtex,domcod,pagmid) VALUES ('$url_atual','".$titulo."','$semtags',$coddom, $tipo) RETURNING pagcod");
					$arrcod = pg_fetch_row($tabcod);
					$codpag = $arrcod[0];
		      pg_query($conn,"INSERT INTO rel_pag_ara (pagcod) VALUES (".$codpag.")");
					if ($tipo>0)
					{
						echo '<br>Mídia salva:'.$midia;
						pg_query($conn,"INSERT INTO midias (pagcod,midurl) VALUES (".$codpag.",'$midia')");						
					}
					
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
		    echo '<br>Página não tem título!';
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
