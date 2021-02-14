<?php

function textofull($str)
{
  // tira pontua��o maldita
  $especiais = array("/", "(", ")", ".","|","!",":");
  $str = str_replace($especiais, " ", $str);
  //troca os - por espa�o
  $str = str_replace("-", " ", $str);
  // troca os _ por -
  $str=str_replace("_", "-", $str);
  // tira os & da busca
  $str = str_replace("&","", $str);
  $str = trim($str);
  // Now remove any doubled-up whitespace
  $str = preg_replace('/\s(?=\s)/', '', $str);
  // Finally, replace any non-space whitespace, with a space
  $str = preg_replace('/[\n\r\t]/', ' ', $str);
  // separa as palavras por &
  $str = str_replace(" "," & ",$str);
  return $str;
}

function st($str) {
	
	$a = '��������������������������������������������������������������';
	$b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby';
	
	return strtolower(strtr($str, $a, $b));
}

function destaca($search, $str, $format, $like=false, $sensitivity=false, &$pos=null) {
	
	$ret = '';
	
	$len = strlen($str);
	$s_len = strlen($search);
	
	$_tag = false;
	$_ignore = -1;
	
	$format = (is_array($format)) ? $format : array($format);
	$f_len = count($format);
	$f_last = 0;
	
	$end = array(' ', ',', '.', ';',
			'(', ')','[', ']', '{', '}',
			'!', '?', '<', '>', '"', '\'', '\\', '/', '|');
	
	$f = ($sensitivity) ? 'ord' : 'st';
	
	for ($i=0; $i < $len; $i++) {
		
		if ($i < $_ignore)
			continue;
			
		if ($str[$i] == '<' && isset($str[$i+1]) && preg_match('|[A-Z\/]|i', $str[$i+1]))
			$_tag = true;
		else if ($_tag && $str[$i] == '>')
			$_tag = false;

		if (!$_tag && $f($search[0]) == $f($str[$i]) && isset($str[$i+$s_len-1])) {
			
			$tmp = null;
			$is = true;
			
			for ($j=0; $j < $s_len; $j++) {
				if ($f($search[$j]) != $f($str[$i+$j])) {
					$is = false;
					break;
				}
				else
					$tmp .= $str[$i+$j];
			}
			
			if ($is && ($like || (!isset($str[$i+$j+1]) || in_array($str[$i+$j], $end)))) {

				if (!is_null($pos))
					$pos[] = $i;

				$_ignore = $i + $s_len;
				
				if ($f_last > $f_len - 1)
					$f_last = 0;
				
				$ret .= str_replace('%s', $tmp, $format[$f_last]);
				
				$f_last++;
			}
			else
				$ret .= $str[$i];
		}
		else
			$ret .= $str[$i];
	}
	
	return $ret;
}

function gera_busca($palavra)
{
  $palavra = trim($palavra);
//	$palavra = mb_convert_encoding($palavra ,'ISO-8859-1',mb_detect_encoding($palavra ,"UTF-8, ISO-8859-1, ASCII"));
  $palavra = strtolower($palavra);
  // Now remove any doubled-up whitespace
  $palavra = preg_replace('/\s(?=\s)/', '', $palavra);
  return($palavra);  
}

function trim_text ($string, $truncation)
{
  $matches = preg_split("/\s+/", $string, $truncation + 1);
	$sz = count($matches);
	if ( $sz > $truncation ) {
    unset($matches[$sz-1]);
    return implode(' ',$matches).'...';
	}
  return $string;
}

function trata_url($parurl)
{
  $returl=urldecode($parurl);
	$returl= substr($returl,0,110);
	$returl=destaca($pesquisa,$returl,'<span style="font-weight: Bold;">%s</span>');
	return $returl;
}

// gera uma palavra sem caracteres especiais e com tra�o no lugar do espa�o
function gera_link($palavra)
{
  $palavra = trim($palavra);
  $palavra = strtolower(preg_replace("[^a-zA-Z0-9-]", "", strtr($palavra, " ", "-")));
  //remove espa�os duplicados
  $palavra = preg_replace('/-(?=-)/', '', $palavra);
  return($palavra);  
}

function tem_acento($string) 
{ 
    $regExp = "[�������������������������������������������������.]";
    return preg_match($regExp,$string); 
} 

function corta_link($var, $limit=80)
{
    if ( strlen($var) > $limit )
    {
        return substr($var, 0, $limit) . '...';
    }
    else
    {
        return $var;
    }
}

?>
