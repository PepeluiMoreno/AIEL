<?php

/*-----------------------------------------------------------------------------
FICHERO: eliminarAcentos.php
VERSION: PHP 5.2.3
DESCRIPCION: Este "Modelo" busca, inserta, y actualiza en (BBDD), pedida por
						 controladorSocios
LLAMADO: desde "controladorSocios						 
OBSERVACIONES:Necesita modeloUsuarios.php, ya que hay funciones compartidas 
              modeloSimpatizantes
------------------------------------------------------------------------------*/
function soloNumeros($string)//bien
/*elimina caracteres que no sean números   */
{//echo "<br>encoding: ",mb_detect_encoding($string); //infoma codificación	
 return preg_replace("/[^0-9]/", "", $string); 
	//return preg_replace("/[^0-9\.\-]/","",$string);//bien elimina 
	//return mb_ereg_replace("[^0-9\.\-]","",$string); //bien elimina
	
}

function eliminarEspeciales($string)//bien
/*elimina caracteres que no sean números o letras (incluidos acentos y ñ)   */
{//echo "<br>encoding: ",mb_detect_encoding($string); //infoma codificación	
 return preg_replace("/[^A-Za-z0-9]/", "", $string); 
	//return preg_replace("/[^A-Za-z0-9\.\-]/","",$string);//bien elimina 
	//return mb_ereg_replace("[^A-Za-z0-9\.\-]","",$string); //bien elimina
	
}

function cambiarAcentosEspeciales($string)//pone letras sin acentos y ñ->n, ç->c
{
 $especial = array('á','à','ä','â','é','è','ë','ê','í','ì','ï','î','ó','ò','ö','ô','ú','ù','ü','û','ñ','ç',
                   'Á','À','Ä','Â','É','È','Ë','Ê','Í','Ì','Ï','Î','Ó','Ò','Ö','Ö','Ú','Ù','Ü','Û','Ñ','Ç');
 $normal =   array('a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','u','u','u','u','n','c',
                   'A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','U','U','U','U','N','C');
																			
 return str_replace($especial, $normal, $string);
}

function cambiarParaUsuarioPass($string) //sin acentos y en mínusculas
{ 																
 return strtolower(eliminarEspeciales(cambiarAcentosEspeciales($string)));	
}	

?>