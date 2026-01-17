<?php
/*-----------------------------------------------------------------------------
FICHERO: validarCampos.php
VERSION: PHP 7.3.21

DESCRIPCION: Valida los campos recibidos desde los formularios,
Llamado: desde  validarCamposSocio, ...,validarCamposSimpatizantes, 
         modeloUsuarios: validarUsuario()
									
Llama: validarTextArea(), validarCampoTexto(),validarCampoNombres()
       validarCampoNombres(),validarNumPasaporte()	,validarDom()
       ..............										
										
OBSERVACIONES: Es llamado desde "modelos" o controladores", es un librería 
               de uso general
															
NOTA: 2021-10-24: CAMBIOS EN validarTextoBodyEmailGes() y otros 
------------------------------------------------------------------------------*/
function validarCampoNoVacio($campo, $textoErrorCampo)
{
  if (!isset($campo) || $campo == NULL || strlen(trim($campo))==0)//puede tener espacios pricipio o final
  { //echo "strlen vv:",strlen(trim($campo)),"<br>";
   $resulValidarCampo['codError'] = '80201';
   $resulValidarCampo['errorMensaje'] = $textoErrorCampo. " debes introducir datos";
  }
	else
	{$resulValidarCampo['codError'] = '00000';
	 $resulValidarCampo['errorMensaje'] = '';
	}
	$resulValidarCampo['valorCampo'] = trim($campo); 
	
	return $resulValidarCampo;
}

function validarTextArea($campoTextArea, $longMin,$longMax, $textoError)//no admite que este vacío
{
	$campoTextArea = trim($campoTextArea);
	
 $resulValidarCampo = validarCampoNoVacio(trim($campoTextArea),$textoError);
	
 if ($resulValidarCampo['codError'] == '00000')
	{
		if((mb_strlen($campoTextArea) < $longMin) || (mb_strlen($campoTextArea) >$longMax))//mb_strlen()para long unicode
	 { $resulValidarCampo['codError'] = '80202';
	   $resulValidarCampo['errorMensaje'] = $textoError.
		  " longitud debe ser entre ". $longMin." y ".$longMax;
		}
		else
		{ 
	  $resulValidarCampo = validarCampoTexto($campoTextArea,$longMin,$longMax, $textoError);
		}
	}	
	return $resulValidarCampo;
}
/*-------------------------- Inicio validarCampoTexto --------------------------
2019-12-05:
Si acentuadas ñ,Ç, guiones-_,/, si punto, coma, dos puntos, si espacio, si números
No acepta ni comillas ni simples ' ni dobles " ni \ por seguridad BBDD"	
Es casi igual validarTextArea, acaso mejor usar solo una 
------------------------------------------------------------------------------*/
function validarCampoTexto($cadTexto,$longMin,$longMax, $textoErrorCampo)
{  
	//echo "<br /><br />1- validarCampoTexto:cadTexto:  $cadTexto<br />";			
	$resulValidarCampo = validarCampoNoVacio($cadTexto, $textoErrorCampo);

	if ($resulValidarCampo['codError'] == '00000')
	{
	  //$cadTexto = trim($cadTexto);//Se quitan espacios, echo "UTF-8 nombre:",$cadTexto,"<br>";

	 if((mb_strlen($cadTexto) < $longMin) || (mb_strlen($cadTexto) >$longMax))//mb_strlen()para long unicode
	 {
		 	//echo "<br /><br />2- validarCampoTexto:cadTexto: cadTexto<br />";
		  $resulValidarCampo['codError'] = '80202';
	   $resulValidarCampo['errorMensaje'] = $textoErrorCampo." longitud debe ser entre ". $longMin." y ".$longMax;
		}
  elseif(!preg_match("/^[-+0-9a-zA-Z,.:;=_@¿?()\/ñÑàáâãäåÀÁÂÃÄÅèéêëÈÉÊËìíîïÌÍÎÏòóôõöÒÓÔÕÖùúûüÙÚÛÜçÇ\s\/]+$/",$cadTexto))  		
		{ 
		  //echo "<br /><br />3- validarCampoTexto:cadTexto:  $cadTexto<br />";	
		  $resulValidarCampo['codError'] = '80220';
	   $resulValidarCampo['errorMensaje'] = $textoErrorCampo." caracteres no permitidos, entre otros comillas dobles (\") o simples (')";
		}
		else
		{
			//echo "<br /><br />4- validarCampoTexto:cadTexto: $cadTexto<br />";
		 $resulValidarCampo['codError'] = '00000';
		 $resulValidarCampo['errorMensaje'] = '';
		}
	 $resulValidarCampo['valorCampo'] = $cadTexto;
		
	};
	
	//echo "<br /><br />5- validarCampoTexto:resulValidarCampo: ";print_r($resulValidarCampo);	echo "<br /><br />";
	return $resulValidarCampo;
}
/*---------------------------- Fin validarCampoTexto -------------------------*/

/*-------------------------- Inicio validarTextoBodyEmailGes -------------------

NOTA: Actualmente quito filtros de caracteres, y solo dejo que control de 
lingitud mínma y máxima de caracteres. 
Son enviados desde GESTORES de EL, es gente de confianza y no intentarán hacer
injections, pero he comprobado que les crea incomodidad al enviar emails.

ANTERIORMENTE: 2019-12-28
Se usa para que el texto dentro del body que envían gestores (Pres, Coord) a 
socios permita las comillas dobles ", ', € (que no permite validarCampoTexto() )
por más seguridad.
Si acentuadas, Si comillas dobles y simples ", ', <,>,€,ñ,Ç, guiones-_,/, si punto, 
coma, dos puntos, si espacio, si números
No acepta % ni \ por seguridad BBDD"	
Es casi igual validarCampoTexto() con algunos caracteres más permitidos 

------------------------------------------------------------------------------*/
function validarTextoBodyEmailGes($cadTexto,$longMin,$longMax, $textoErrorCampo)
{  
	//echo "<br /><br />1- validarTextoBodyEmailGes:cadTexto:  $cadTexto<br />";			
	$resulValidarCampo = validarCampoNoVacio($cadTexto, $textoErrorCampo);

	if ($resulValidarCampo['codError'] == '00000')
	{
	  //$cadTexto = trim($cadTexto);//Se quitan espacios, echo "UTF-8 nombre:",$cadTexto,"<br>";

	 if((mb_strlen($cadTexto) < $longMin) || (mb_strlen($cadTexto) >$longMax))//mb_strlen()para long unicode
	 {
		 	//echo "<br /><br />2- validarTextoBodyEmailGes:cadTexto: cadTexto<br />";
		  $resulValidarCampo['codError'] = '80202';
	   $resulValidarCampo['errorMensaje'] = $textoErrorCampo." longitud debe ser entre ". $longMin." y ".$longMax;
		}
		/* ANTERIORMENTE: 2019-12-28
		elseif(!preg_match("/^[-+0-9a-zA-Z,.:;=_@&¿?!¡()\/\"\'<>€ñÑàáâãäåÀÁÂÃÄÅèéêëÈÉÊËìíîïÌÍÎÏòóôõöÒÓÔÕÖùúûüÙÚÛÜçÇ\s\/]+$/",$cadTexto))  		
		{ 
		  echo "<br /><br />3- validarTextoBodyEmailGes:cadTexto:  $cadTexto<br />";	
		  $resulValidarCampo['codError'] = '80220';
	   $resulValidarCampo['errorMensaje'] = $textoErrorCampo." caracteres no permitidos";
		}*/
		else
		{
			//echo "<br /><br />4- validarTextoBodyEmailGes:cadTexto: $cadTexto<br />";
		 $resulValidarCampo['codError'] = '00000';
		 $resulValidarCampo['errorMensaje'] = '';
		}
	 $resulValidarCampo['valorCampo'] = $cadTexto;
		
	};
	
	//echo "<br /><br />5- validarTextoBodyEmailGes:resulValidarCampo: ";print_r($resulValidarCampo);	echo "<br /><br />";
	return $resulValidarCampo;
}
/*---------------------------- Fin validarTextoBodyEmailGes ------------------*/

/*------------------- Inicio validarCampoNombres -------------------------------
Agustín modifica 2019-12-08, para evitar ' y mejorar APE2
Sirve para validar NOM, APE1 y APE2
SÍ guiones medios, SÍ espacio, No acepta números. 
No comillas ni simples ' ni dobles " ni \ por seguridad BBDD"
------------------------------------------------------------------------------*/
function validarCampoNombres($name,$longMin,$longMax, $textoErrorCampo)
{ 
  //echo "<br /><br />0- validarCampoNombres:name: $name<br />";	

		$resulValidarCampo['codError'] = '00000';
		$resulValidarCampo['errorMensaje'] = '';	
		
	/* APE2 no es obligatorio y puede estar vacío y su longitud puede ser cero */	
 if ($longMin == 0 && ($name == NULL || empty($name)) )
	{ 
			$name = NULL;
		
			$resulValidarCampo['valorCampo'] = $name;
			
   //echo "<br /><br />1- validarCampoNombres:name: $name<br />";			
	}	
	else    /*Si APE2 no está vacío o es AP1 o Nom se valida */
	{
			$resulValidarCampo = validarCampoNoVacio($name, $textoErrorCampo);

			if ($resulValidarCampo['codError'] == '00000')
			{
				$name = trim($name);//Se quitan espacios princio y fin , echo "UTF-8 nombre:",$name,"<br>";
				//mb_strtoupper($str,'UTF-8')instead of all that bloated PHP functions ;=)//convertir a mayusculas, ver ayda php 
				
				if((mb_strlen($name) < $longMin) || (mb_strlen($name) >$longMax))//mb_strlen()para long unicode
				{
					 $resulValidarCampo['codError'] = '80202';
						$resulValidarCampo['errorMensaje'] = $textoErrorCampo." longitud debe ser entre ". $longMin." y ".$longMax;
						//echo "<br /><br />2- validarCampoNombres:name: ", $name,"<br />";
				}
				elseif(!preg_match("/^[-a-zA-ZñÑàáâãäåÀÁÂÃÄÅèéêëÈÉÊËìíîïÌÍÎÏòóôõöÒÓÔÕÖùúûüÙÚÛÜçÇ\s]+$/",$name))
				{ 
				  //echo "<br /><br />3-validarCampoNombres:No valido nombre: $name<br>";
					
					 $resulValidarCampo['codError'] = '80220';
					 $resulValidarCampo['errorMensaje'] = $textoErrorCampo." caracteres no permitidos";
				}
				else
				{ //echo "<br /><br />4- validarCampoNombres:name: $name<br />";
					 //$resulValidarCampo['codError'] = '00000';
				}
			}
	 $resulValidarCampo['valorCampo'] = $name;
	};
	
	//echo "<br /><br />5- validarCampoNombres:name: $name<br />";
	
	return $resulValidarCampo;
}
//-------------------Fin validarCampoNombres -----------------------------------

/*----------------- Inicio validarCampoUsuario ---------------------------------
Si guiones -, no espacio, si numeros si . _ @ .
No acepta  ni comillas ni simples ' ni dobles por seguridad BBDD"
------------------------------------------------------------------------------*/
function validarCampoUsuario($name,$longMin,$longMax, $textoErrorCampo)
{ 
	$resulValidarCampo = validarCampoNoVacio($name, $textoErrorCampo);

	if ($resulValidarCampo['codError'] =='00000')
	{
		$name = trim($name);//Se quitan espacios, echo "UTF-8 nombre:",$name,"<br>";

		if((mb_strlen($name) < $longMin) || (mb_strlen($name) >$longMax))//mb_strlen()para long unicode
		{
			$resulValidarCampo['codError'] = '80202';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo.
			" longitud debe ser entre ". $longMin." y ".$longMax;
		}
		elseif(!preg_match("/^[-0-9a-zA-Z._@]+$/",$name))
		{ 
			//echo "No valido nombre:", $name,"<br>";
			$resulValidarCampo['codError'] = '80220';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo.
		   " caracteres no permitidos, solo se permiten los caracteres siguientes: ".
		   "(0-9), (a-z), (A-Z),(._-@)";
		}
		else
		{//echo "valido: ",$name,"<br>";
			$resulValidarCampo['codError'] = '00000';
			$resulValidarCampo['errorMensaje'] = '';
		}
		$resulValidarCampo['valorCampo'] = $name;
	};
	return $resulValidarCampo;
	} 
//-------------------Fin validarCampoUsuario -----------------------------------

function validarCampoPass($clave,$longMin,$longMax, $textoErrorCampo)
{ 
	$resulValidarCampo = validarCampoNoVacio($clave, $textoErrorCampo);
	if ($resulValidarCampo['codError']=='00000')
	{
	 if(mb_strlen($clave) < $longMin || mb_strlen($clave) > $longMax)
		{
		 	$resulValidarCampo['codError'] = '80202';
		  $resulValidarCampo['errorMensaje'] = $textoErrorCampo.
			 " longitud debe ser entre ". $longMin." y ".$longMax;
		}
	 else if(!preg_match("/^[-0-9a-zA-Z._@!?%&()]+$/", $clave))
		{
			//echo "No valido nombre:", $clave,"<br>";//No acepta acentuadas ni comillas ni simples ' ni dobles por seguridad BBDD"
		 $resulValidarCampo['codError'] = '80220';
	  $resulValidarCampo['errorMensaje'] = $textoErrorCampo.
	   	" caracteres no permitidos: solo se permiten los caracteres siguientes: ".
     "(0-9),(a-z),(A-Z),(._-@!?%&())";
		}
	 else
		{//echo "valido: ",$clave,"<br>";
		 $resulValidarCampo['codError'] = '00000';
	  $resulValidarCampo['errorMensaje'] = '';
		}
		$resulValidarCampo['valorCampo'] = $clave;
	};
	return $resulValidarCampo;
}



/*----------------- Inicio validarMayusculasNumerosGuiones ------------------------------
Valida una cadena para que cumpla la siguientes condiciones:
Sí letras Mayúsculas, sí guion medio -,  si números 
No minúsculas, No acentos, no espacio, ni comillas ni simples ' ni dobles "

RECIBE: $cadTexto (string para validar),$longMin (lonitud mínima),$longMax (lonitud máxima),
$textoErrorCampo (texto para mensaje en caso de error validación)
DEVUELVE: ARRAY "$resulValidarCampo['valorCampo']" y campos código de eroror

LLAMADA: validarCamposTesorero.php:validarCamposDonacionConcepto()
LLAMA: validarCampos.php: validarCampoNoVacio()
---------------------------------------------------------------------------------------*/
function validarMayusculasNumerosGuiones($cadTexto,$longMin,$longMax, $textoErrorCampo)
{ 
	$resulValidarCampo = validarCampoNoVacio($cadTexto, $textoErrorCampo);

	if ($resulValidarCampo['codError'] ==='00000')
	{
		$cadTexto = trim($cadTexto);//Se quitan espacios inciales y finales

		if((mb_strlen($cadTexto) < $longMin) || (mb_strlen($cadTexto) >$longMax))//mb_strlen()para long unicode
		{
			$resulValidarCampo['codError'] = '80202';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo.
			" longitud debe ser entre ". $longMin." y ".$longMax;
		}
		elseif(!preg_match("/^[-0-9A-Z-]+$/",$cadTexto))
		{ 
			//echo "No valido:", $cadTexto,"<br>";
			$resulValidarCampo['codError'] = '80220';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo.
		   " caracteres no permitidos, solo se permiten los caracteres siguientes: ".
		   "0-9, letras en mayúsculas sin acentos A-Z, no se permiten espacios en blanco, en su lugar utiliza (- guion medio) como separador de palabras";
		}
		else
		{//echo "valido: ",$cadTexto,"<br>";
			$resulValidarCampo['codError'] = '00000';
			$resulValidarCampo['errorMensaje'] = '';
		}
		$resulValidarCampo['valorCampo'] = $cadTexto;
	}
	
	return $resulValidarCampo;	
	} 
/* -------------------Fin validarMayusculasNumerosGuiones ------------------------------*/


function validarCampoRadio($campoRadioElegido,$textoErrorCampo)
{
	if(isset($campoRadioElegido) && !empty($campoRadioElegido))
	{$resulValidarCampoRadio['codError']='00000';
	 $resulValidarCampoRadio['errorMensaje']='';
	 $resulValidarCampoRadio['valorCampo']=$campoRadioElegido;
	}
	else
	{$resulValidarCampoRadio['codError']='80201';
	 $resulValidarCampoRadio['errorMensaje']=$textoErrorCampo. " debes elegir una opción";
	 $resulValidarCampoRadio['valorCampo']='';
	}
	return $resulValidarCampoRadio;
} 


/*----------------- Inicio letraNifNie ------------------------------------------
Algaritmo para validar la detra del NIF o del NIE

LLAMADA: validarCampos:validarNif(),validarNie()
-------------------------------------------------------------------------------*/

function letraNifNie($numDoc) 
{
  return substr("TRWAGMYFPDXBNJZSQVHLCKE",strtr($numDoc,"XYZ","012")%23,1);
}

/*----------------- Fin letraNifNie ---------------------------------------------*/

/*----------------- Inicio validarNif -------------------------------------------
Valida el NIF de los socios a partir de la letra de control (solo para España)

LLAMADA: validarCamposSocio.php:validarCamposFormAltaSocio(),validarCamposFormActualizarSocio()
validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor()

LLAMA: validarCampos.php:letraNifNie()
-------------------------------------------------------------------------------*/
function validarNif($nif)
{
 //echo "<br><br>0-1 validarCampos:validarNif:nif: ";var_dump($nif);	
	
	$resulValidarCampo['codError'] = '00000';
	$resulValidarCampo['errorMensaje'] = "";
	
	$nif = str_replace(' ', '', $nif); //quita blancos en toda la cadena		
	
	$nif = strtoupper($nif);
	
	//echo "<br><br>1 validarCampos:validarNif:nif: ";var_dump($nif);	
	
	$resulValidarCampo['valorCampo'] = $nif;

	if(empty($nif))
	{ $resulValidarCampo['codError'] = '80201';
	  $resulValidarCampo['errorMensaje'] = "debes introducir datos";
	}
	elseif(strlen($nif)!== 9)
	{ 
	  $resulValidarCampo['codError'] = '80202';
			$resulValidarCampo['errorMensaje'] = "Error NIF: debe tener 9 caracteres sin guiones, puntos, ...(8números+Letra)";
	}
	elseif(!preg_match("/^[0-9]{8}[A-Z]{1}$/",$nif))
	{ 
	  $resulValidarCampo['codError'] = '80200';
	  $resulValidarCampo['errorMensaje'] = "Error NIF(8números+Letra sin espacios ni guiones)";
	}
	else
	{
 		$letra = substr($nif,-1);//última letra

			$numeros = substr($nif,-9,8);
		
			if($letra !== letraNifNie($numeros))//calculamos que el sea correcto
			{ $resulValidarCampo['codError'] = '80200';
					$resulValidarCampo['errorMensaje'] = "Error NIF(8números+Letra sin espacios ni guiones)";
			}	
	}	

	//echo "<br><br>2 validarCampos:validarNif:resulValidarCampo: ";var_dump($resulValidarCampo);	
	
	return $resulValidarCampo;
}


/*----------------- Inicio validarNie -------------------------------------------
Valida el NIE de los socios a partir de la letra de control (solo para España)

LLAMADA: validarCamposSocio.php:validarCamposFormAltaSocio(),validarCamposFormActualizarSocio()
validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor()

LLAMA: validarCampos.php:letraNifNie()

OBSERVACIONES:
El algoritmo cambia de la siguiente manera:

Las identificaciones que empiezan por Y se suma 10.000.000 al numero (de 
máximo de 7 cifras) y se aplica el algoritmo de la letra del NIF
Para los que empiezan por Z se suma 20.000.000 al número e idem.

Para probar:
$string = "X9397602D";esta bien
$string = "Y0517653Y";esta bien
-------------------------------------------------------------------------------*/
function validarNie($nie)
{ 
 //echo "<br><br>0-1 validarCampos:validarNie:nie: ";var_dump($nie);	
	
	$resulValidarCampo['codError'] = '00000';
	$resulValidarCampo['errorMensaje'] = "";
	
	$nie = str_replace(' ', '', $nie); //quita blancos en toda la cadena		
	
	$nie = strtoupper($nie);
	
	//echo "<br><br>1 validarCampos:validarNie:nie: ";var_dump($nie);	
	
	$resulValidarCampo['valorCampo'] = $nie;

	if (empty($nie))
	{ $resulValidarCampo['codError'] = '80201';
	  $resulValidarCampo['errorMensaje'] = "debes introducir datos"; 
	}
	elseif(strlen($nie) !== 9)
	{
		 $resulValidarCampo['codError'] = '80202';
		 $resulValidarCampo['errorMensaje'] = $textoErrorCampo."Error (formato: letra(X,Y,Z)+7números+Letra sin espacios ni guiones";
	}
	elseif (!preg_match("/^[XYZ]{1}[0-9]{7}[A-Z]{1}$/",$nie))
	{
 		$resulValidarCampo['codError'] = '80200';
	  $resulValidarCampo['errorMensaje'] = "Error (formato: letra(X,Y,Z)+7números+Letra sin espacios ni guiones)";
	}
	else
	{
		
		$letra = substr($nie,-1);//ultima letra

		$numeros = substr($nie,-9,8);
		

		if ($letra !== letraNifNie($numeros))//calculamos que el dnie sea correcto
		{ $resulValidarCampo['codError']='80200';
	   $resulValidarCampo['errorMensaje']="Número de NIE o la letras incorrectos";
		}	
	}	
	
 //echo "<br><br>2 validarCampos:validarNie:resulValidarCampo: ";var_dump($resulValidarCampo);		
	
	return $resulValidarCampo;
}
/*----------------- Fin validarNie --------------------------------------------*/


/*----------------------- Inicio validarNumPasaporte ---------------------------
Solo se valida que el Num. del Pasaporteque u otro documento distinto de NIF,NIE
no esté vacío y algunos caracteres especialmente ','',\,NULL

LLAMADA: validarCamposSocio.php:validarCamposFormAltaSocio(),validarCamposFormActualizarSocio()
validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor()
LLAMA: validarCampos.php:validarCampoNoVacio()

------------------------------------------------------------------------------*/
function validarNumPasaporte($NumPasaporte,$longMin,$longMax, $textoErrorCampo)
{//$resulValidarCampo['nomFuncion']="validarNumPasaporte";

 //echo "<br /><br />1 validarNumPasaporte:", $NumPasaporte,"<br>";
	
	$resulValidarCampo = validarCampoNoVacio($NumPasaporte, $textoErrorCampo);

	if ($resulValidarCampo['codError']=='00000')
	{
		$NumPasaporte = trim($NumPasaporte);//Se quitan espacios, echo "UTF-8 nombre:",$name,"<br>";

		if((mb_strlen($NumPasaporte) < $longMin) || (mb_strlen($NumPasaporte) >$longMax))//mb_strlen()para long unicode
		{
			$resulValidarCampo['codError'] ='80202';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo." longitud debe ser entre ". $longMin." y ".$longMax;
		} //Lo siguiente para evitar esos caracteres
  elseif ( strstr($NumPasaporte, "\"") ||  strstr($NumPasaporte, "'") || strstr($NumPasaporte, "\\")
          ||strstr($NumPasaporte, "%") ||  strstr($NumPasaporte, "=") || strstr($NumPasaporte, "`") 	
          ||strstr($NumPasaporte, "$") ||  strstr($NumPasaporte, "^") || strstr($NumPasaporte, "!") 
          ||strstr($NumPasaporte, "NULL")	)
		{ 
		 //echo "<br /><br />2 validarNumPasaporte: ", $NumPasaporte,"<br>";
			$resulValidarCampo['codError'] ='80220';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo." Caracteres no válidos en Número de Documento";
		}					
		else
		{
			$resulValidarCampo['codError'] = '00000';
			$resulValidarCampo['errorMensaje'] = '';
			//echo "<br /><br />3 validarNumPasaporte: ", $NumPasaporte,"<br>";
		}
		$resulValidarCampo['valorCampo'] = $NumPasaporte;
	};
	
	//echo "<br /><br />4 validarNumPasaporte: ", $NumPasaporte,"<br>";
	
	return $resulValidarCampo;
}
/*-------------------------- Fin validarNumPasaporte -------------------------*/


/*---------------- Inicio validarCIF -----------------------------------------
DESCRIPCION: Valida los campos de "CIF"

DEVUELVE: true o false                   
LLAMADA: validarCamposFormActualizarAgrupacionPres()

OBSERVACIONES: 
Origen funcion: https://lab.fawno.com/2015/08/27/validacion-de-cif-nif/

Probada con: G19819226,S5222498G, N4957698F, B67657189, A28812618
-----------------------------------------------------------------------------*/
function validarCIF ($CIF) 
{
		//echo "<br><br>0-1 validarCampos.php:validarCIF:CIF: "; print_r($CIF);

  $CIF = strtoupper($CIF);
		
  if (preg_match('~(^[XYZ\d]\d{7})([TRWAGMYFPDXBNJZSQVHLCKE]$)~', $CIF, $parts)) 
		{
    $control = 'TRWAGMYFPDXBNJZSQVHLCKE';
    $nie = array('X', 'Y', 'Z');
				
    $parts[1] = str_replace(array_values($nie), array_keys($nie), $parts[1]);
    $cheksum = substr($control, $parts[1] % 23, 1);
				
    //echo "<br><br>1 validarCampos.php:validarCIF:cheksum: "; print_r($cheksum);				

    return ($parts[2] == $cheksum);
  } 
		elseif (preg_match('~(^[ABCDEFGHIJKLMUV])(\d{7})(\d$)~', $CIF, $parts)) 
		{
    $checksum = 0;
    foreach (str_split($parts[2]) as $pos => $val) 
				{
      $checksum += array_sum(str_split($val * (2 - ($pos % 2))));
    }
    $checksum = ((10 - ($checksum % 10)) % 10);
				
    //echo "<br><br>2 validarCampos.php:validarCIF:cheksum: "; print_r($cheksum);							
				
    return ($parts[3] == $checksum);
  } 
		elseif (preg_match('~(^[KLMNPQRSW])(\d{7})([JABCDEFGHI]$)~', $CIF, $parts)) 
		{
    $control = 'JABCDEFGHI';
    $checksum = 0;
    foreach (str_split($parts[2]) as $pos => $val) 
				{
      $checksum += array_sum(str_split($val * (2 - ($pos % 2))));
    }
    $checksum = substr($control, ((10 - ($checksum % 10)) % 10), 1);
				
    //echo "<br><br>3 validarCampos.php:validarCIF:cheksum: "; print_r($cheksum);							
				
    return ($parts[3] == $checksum);
  }
  return false;
}
/*---------------------- Fin validarCIF----------------------------------------*/


function validarFecha($fecha)
{
	$dia  = $fecha['dia'];
 $mes  = $fecha['mes'];
	$anio = $fecha['anio'];

	if (($dia =='00' && $mes =='00' && $anio =='0000') //lo siguiente para actualizar si esta vacio, no necesario
  //||(!isset($dia) && !isset($mes) && !isset($anio)) || (($dia =='') && ($mes =='') && ($anio ==''))
  //||(($dia ==NULL) && ($mes ==NULL) && ($anio ==NULL))
	   )
	{$arrFecha['codError']='00000';
		$arrFecha['errorMensaje']='';
		//echo "<br>1f:$dia-$mes-$anio<br>";
	}
	elseif (is_numeric($dia) && is_numeric($mes) && is_numeric($anio))
	{  
			if (checkdate($mes,$dia,$anio))
			{$arrFecha['codError']='00000';
				$arrFecha['errorMensaje']='';
			}
			else 
			{$arrFecha['codError']='80510';
				$arrFecha['errorMensaje']='Fecha no válida';
				//echo "<br>2ff$dia-$mes-$anio<br>";
			}
	}
	else 
	{$arrFecha['codError']='80510';
		$arrFecha['errorMensaje']='Fecha no válida';
		//echo "<br>3fff$dia-$mes-$anio<br>";
	}  
	$arrFecha['dia']['valorCampo'] =$dia;		
	$arrFecha['mes']['valorCampo'] =$mes;
	$arrFecha['anio']['valorCampo']=$anio;	
	
	 //echo "<br><br>validarCampoFecha :arrFecha:"; print_r($arrFecha);
	return $arrFecha; 
}


function validarFechaLimites($fecha,$fechInf,$fechSup,$permitirVacio = true)
{$dia=$fecha['dia'];
 $mes=$fecha['mes'];
	$anio=$fecha['anio'];	

		if (($dia =='00' && $mes =='00' && $anio =='0000') //lo siguiente para actualizar si esta vacio, no necesario
	  //||(!isset($dia) && !isset($mes) && !isset($anio)) || (($dia =='') && ($mes =='') && ($anio ==''))
	  //||(($dia ==NULL) && ($mes ==NULL) && ($anio ==NULL))
		   )
	 {if ($permitirVacio == true) 				
			{$arrFecha['codError'] = '00000';
				$arrFecha['errorMensaje'] = '';
				//echo "<br>1f:$dia-$mes-$anio<br>";
			}
			else
			{$arrFecha['codError'] = '80201';
				$arrFecha['errorMensaje'] = 'Debes introducir fecha';				
			}
		}	
		elseif (is_numeric($dia) && is_numeric($mes) && is_numeric($anio))
		{  
				if (checkdate($mes,$dia,$anio))
				{ $cadFecha=$anio."-".$mes."-".$dia;
				
		    if ($cadFecha > $fechSup)
						{ $arrFecha['codError']='80510';
					   $arrFecha['errorMensaje']='Fecha no válida, superior a fecha permitida';						
						}
						elseif ($cadFecha < $fechInf)
						{
							 $arrFecha['codError']='80510';
					   $arrFecha['errorMensaje']='Fecha no válida, inferior a fecha permitida';	
						}
						else
						{
							 $arrFecha['codError']='00000';
					   $arrFecha['errorMensaje']='';
						}
				}
				else 
				{$arrFecha['codError']='80510';
					$arrFecha['errorMensaje']='Fecha no válida';
					//echo "<br>2ff$dia-$mes-$anio<br>";
				}
		}
		else 
		{$arrFecha['codError']='80510';
			$arrFecha['errorMensaje']='Fecha no válida';
			//echo "<br>3fff$dia-$mes-$anio<br>";
		}	
		$arrFecha['dia']['valorCampo']=$dia;		
		$arrFecha['mes']['valorCampo']=$mes;
		$arrFecha['anio']['valorCampo']=$anio;		
	
	 //echo "<br><br>validarCampoFecha :arrFecha:"; print_r($arrFecha);
	return $arrFecha; 
}

function validarTelefono($telefono,$longMin,$longMax,$textoErrorCampo)//tel no es campo obligatorio
{
	$resulValidarCampo['codError'] = '00000';
	$resulValidarCampo['errorMensaje'] = '';	

 $telefono = str_replace(' ', '', $telefono);//elimina espacios en blanco en el $telefono
 
 if (isset($telefono) && $telefono !=='' && $telefono !== NULL)

	{if((mb_strlen($telefono) < $longMin) || (mb_strlen($telefono) >$longMax))//mb_strlen()para long unicode
		{	
		  $resulValidarCampo['codError'] = '80202';
			 $resulValidarCampo['errorMensaje'] = $textoErrorCampo." longitud debe ser entre ". $longMin." y ".$longMax;
		}
		//elseif(!preg_match('/^[0-9\d_]{9,11}$/i',$telefono))
  elseif(!preg_match('/^[0-9\d_]{' .$longMin. ',' .$longMax. '}$/i',$telefono))		
		{ 
		  $resulValidarCampo['codError'] = '80220';
		 	$resulValidarCampo['errorMensaje'] = $textoErrorCampo." Solo números sin puntos ni guiones u otros signos";
		}
	}
	$resulValidarCampo['valorCampo'] = $telefono;
	
	return $resulValidarCampo;
}

function validarTelefonoObligatorio($telefono,$longMin,$longMax,$textoErrorCampo)//tel es campo obligatorio
{ 
 $resulValidarCampo = validarCampoNoVacio($telefono, $textoErrorCampo);

	if ($resulValidarCampo['codError'] == '00000')
	{		
		$telefono = str_replace(' ', '', $telefono);//elimina espacios en blanco en el $telefono

		if((mb_strlen($telefono) < $longMin) || (mb_strlen($telefono) >$longMax))//mb_strlen()para long unicode
		{	$resulValidarCampo['codError'] = '80202';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo." longitud debe ser entre ". $longMin." y ".$longMax;
		}
		elseif(!preg_match('/^[0-9\d_]{9,11}$/i',$telefono))
		{$resulValidarCampo['codError'] = '80220';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo." Solo números sin espacios ni puntos";
		}
		else
		{$resulValidarCampo['codError'] = '00000';
			$resulValidarCampo['errorMensaje'] = '';
		}
		$resulValidarCampo['valorCampo'] = $telefono;
	};
	return $resulValidarCampo;
}

/*--------------------- Inicio validarEmail ------------------------------------
2019-12-11
Entre otros caracteres especiales no acepta letras acentuadas y ñ,Ç el segundo 
filtro es para evitar: ',!,#,$,%,&,*,/,=,?,^,{,|,},~, que deja pasar el anterior filtro
	
------------------------------------------------------------------------------*/
function validarEmail($email,$textoErrorCampo='') //valida RFC2822
{ 
  $resulValidarCampo = validarCampoNoVacio($email, $textoErrorCampo);//pone el mensaje de vacío, si es el caso 

		if ($resulValidarCampo['codError'] =='00000')
		{ $email = trim($email);//Se quitan espacios, echo "UTF-8 nombre:",$telefono,"<br>"; 
    //echo "<br /><br />0-1- validarEmail:email: $email<br />";
		
		 /*Entre otros caracteres especiales no acepta letras acentuadas y ñ,Ç
			  el segundo filtro es para evitar: ',!,#,$,%,&,*,/,=,?,^,{,|,},~, que deja pasar el anterior filtro
			*/
			if (!filter_var($email,FILTER_VALIDATE_EMAIL) || 
			    !preg_match("/^[a-zA-Z0-9\+]+([._-][a-zA-Z0-9\+]+)*@([a-zA-Z0-9]+([._-][a-zA-Z0-9]+))+$/",$email) ) 
			{ 
					//echo "<br /><br />1-1- validarEmail:email: $email<br />";
					$resulValidarCampo['codError'] ='81010';
					$resulValidarCampo['errorMensaje'] = $textoErrorCampo." Caracteres o formato de email no permitido";						
			} 
			else 
			{ //echo "<br /><br />1-2b- validarEmail:email: $email<br />";
		
			  $email = filter_var($email,FILTER_SANITIZE_EMAIL);
     //echo "<br /><br />1-2c- validarEmail:email: $email<br />";		
					
					$resulValidarCampo['codError'] ='00000';
					$resulValidarCampo['errorMensaje'] ='';
			}			
	  /* Antes tenía:
			if (function_exists('filter_var'))//Existe filter_var Introducida en v:PHP 5.2
	  { echo "<br /><br />0- validarEmail:email: $email<br />";		  
						if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE)				
					{echo "<br /><br />1-1- validarEmail:email: $email<br />";
						$resulValidarCampo['codError'] ='81010';
				  $resulValidarCampo['errorMensaje'] = $textoErrorCampo." Formato de email no permitido";						
	    } 
					else 
					{echo "<br /><br />1-2- validarEmail:email: $email<br />";
						$resulValidarCampo['codError'] ='00000';
				  $resulValidarCampo['errorMensaje'] ='';
	    }					
	  } 
			else //SI no existe la función filter_var Introducida en v:PHP 5.2 se usará lo siguiente:
			{	echo "<br /><br />2- validarEmail:email: $email<br />";
				if(!preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/',$email))
				{$resulValidarCampo['codError'] ='81010';
					$resulValidarCampo['errorMensaje'] = $textoErrorCampo." Formato de email no permitido";
				}
				else
				{$resulValidarCampo['codError'] ='00000';
					$resulValidarCampo['errorMensaje'] ='';
				}				
	  }
			*/
	 }
	$resulValidarCampo['valorCampo'] = $email;
			
	return $resulValidarCampo;
}
/*--------------------- Fin validarEmail -------------------------------------*/

function validarInjectionDatosEmail($campo)//???
{//para prevenir spammer  
	$validarInjectionDatosEmail['codError']='00000';
 $validarInjectionDatosEmail['errorMensaje']='';
	$validarInjectionDatosEmail['valorCampo']=$campo;
	
	//Array con las posibles cabeceras a utilizar por un spammer 
	$badHeads = array("Content-Type:", 
																			"MIME-Version:", 
																			"Content-Transfer-Encoding:", 
																			"Return-path:", 
																			"Subject:", 
																			"From:", 
																			"Envelope-to:", 
																			"To:", 
																			"bcc:", 
																			"cc:"
																			); 
	
	//Comprobamos que entre los datos no se hay alguna cadenas del array. 
	foreach($badHeads as $valor)
	{
		//if(strpos(strtolower($campo), strtolower($valor)) !== false)
		if(stripos($campo, $valor) !== false)
		{		
		 $validarInjectionDatosEmail['codError']='80220';
	  $validarInjectionDatosEmail['errorMensaje']='Contenido no permitido: prevención de spam';
		 //header("HTTP/1.0 403 Forbidden");
		 //exit;
		}
	}
	return $validarInjectionDatosEmail;
}


function validarCuotaSocio($importeCuotaAnioSocio,$textoErrorCampo)//acaso ya no se use al ser sustituida por validarCantidadDecimal()
{//$resulValidarCampo['nomFuncion']="validarCuotaSocio";
	//$resulValidarCampo['nomScript']="validarCampos.php"; 

	$resulValidarCampo = validarCampoNoVacio($importeCuotaAnioSocio, $textoErrorCampo);

	if ($resulValidarCampo['codError']=='00000')
	{
		$NumPasaporte = trim($importeCuotaAnioSocio);//Creo que no es necesario, quitan espacios, echo "UTF-8 nombre:",$name,"<br>";

		if( !is_numeric($importeCuotaAnioSocio))//
		{
			$resulValidarCampo['codError']='80300';
			$resulValidarCampo['errorMensaje']=$textoErrorCampo.
			"formato no válido: debe se un número";
		}
		elseif ($importeCuotaAnioSocio < 30) //HABRÁ QUE HACERLO CON LAS TABLAS QUE SE LEA Y SE DEJE CONSTANTE O PARECIDO
		{
		  $resulValidarCampo['codError']='80302';
			$resulValidarCampo['errorMensaje']=$textoErrorCampo.
			"debe ser igual o superior a la cuota anual de 30 euros";
		}
		else
		{//echo "valido: ",$name,"<br>";
			$resulValidarCampo['codError']='00000';
			$resulValidarCampo['errorMensaje']='';
		}
		$resulValidarCampo['valorCampo']=$importeCuotaAnioSocio;
	};
	return $resulValidarCampo;
}

/*-----------------------------------------------------------------------------
Valida que un número es entero y entre unos límites, 
según el valor de $permitirVacio permitirá que este vacio o no 

2023-01-21: Por ejemplo para validar enteros como año nacimiento.
------------------------------------------------------------------------------*/
function validarNumeroEntero($numeroEntero,$valMin,$valMax,$permitirVacio = true)
{
	 //echo "<br><br>0-1 validarNumeroEntero:numeroEntero: "; print_r($numeroEntero);
	 $resValidarEntero = array();	

  $numeroEntero = trim($numeroEntero);//faltaria un cast???
		
		$resValidarEntero['valorCampo'] = $numeroEntero;
	
	//	if ( $numeroEntero == '0000' || $numeroEntero =='' || $numeroEntero == NULL )
  if ( $numeroEntero == '' || $numeroEntero == NULL )			
	 {
			if ($permitirVacio == true) 				
			{$resValidarEntero['codError'] = '00000';
				$resValidarEntero['errorMensaje'] = '';
			}
			else
			{			
				$resValidarEntero['codError'] = '80201';
				$resValidarEntero['errorMensaje'] = 'Debes introducir datos';				
			}			
		}
		elseif ( !isset($valMin) || empty($valMin) )			
	 {
			{$resValidarEntero['codError'] = '80201';
				$resValidarEntero['errorMensaje'] = 'Falta dato de valor mínimo';				
			}			
		}		
		elseif ( !isset($valMax) || empty($valMax) )			
	 {
			{$resValidarEntero['codError'] = '80201';
				$resValidarEntero['errorMensaje'] = 'Falta dato de valor máximo';				
			}			
		}
		//elseif ( is_numeric($numeroEntero) )		
  elseif (false !== filter_var($numeroEntero, FILTER_VALIDATE_INT))
		{    
    $numeroEntero = (int)$numeroEntero;			
	 
 			if ($numeroEntero > $valMax)
				{ $resValidarEntero['codError'] = '80302';
						$resValidarEntero['errorMensaje'] = 'Superior al valor superior permitido '. $valMax;						
				}
				elseif ($numeroEntero < $valMin)
				{
						$resValidarEntero['codError'] = '80302';
						$resValidarEntero['errorMensaje'] = 'Inferior al valor inferior permitido '. $valMin;		
				}
				else
				{
						$resValidarEntero['codError'] = '00000';
						$resValidarEntero['errorMensaje'] = '';
				}
	 }
		else //no es int
		{$resValidarEntero['codError'] = '80301';
			$resValidarEntero['errorMensaje'] = 'Debe ser un número entero';
		}	
		
		$resValidarEntero['valorCampo'] = $numeroEntero;		
	
	 //echo "<br><br>1 validarNumeroEntero:resValidarEntero: "; print_r($resValidarEntero);
	return $resValidarEntero; 
}


/*------------------------------------------------------------------------------------------
Función para validar una número decimal o entero "$cantidad" 
Valida que el formato es correcto, (la parte decimal se separa por un punto para coincidir
con la BBDD) 
Los números deberán estar comprendido en  un rango entre un valor inferior $min y uno superior $max.
En caso de que no tengan valores los parámetro $min,$max le asignará por defecto 
$min=00.00; $max=1000000.00; por defecto.

También recibe un texto que se antepone al mensaje de error.
-------------------------------------------------------------------------------------------*/
function validarCantidadDecimal($cantidad,$min,$max,$textoErrorCampo)
{
 
	if (!isset($max) || $max == NULL || $max =='')
	{
		 $max = 1000000.00;
	}
 if (!isset($min) || $min == NULL || $min =='')
	{
		 $min = 00.00;
	}	
	
	if (!isset($textoErrorCampo) )
	{ $textoErrorCampo = "";} //evita notice
	
	$resulValidarCampo = validarCampoNoVacio($cantidad, $textoErrorCampo);

	if ($resulValidarCampo['codError']=='00000')
	{
		$cantidad = trim($cantidad);//Creo que no es necesario, quitan espacios, echo "UTF-8 nombre:",$name,"<br>";

		if( !is_numeric($cantidad))//
		{
			$resulValidarCampo['codError'] = '80300';
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo." formato no válido: debe ser un número con un punto '.' para separar decimales y sin nombre de moneda";
		}
		elseif ($cantidad < $min ) 
		{
		 $resulValidarCampo['codError'] = '80302';
			//$resulValidarCampo['errorMensaje'] = $textoErrorCampo."debe ser igual o superior a  ".$min. " e inferior a ".$max;
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo."  debe ser igual o superior a  ".$min;
		}
		elseif ($cantidad > $max) 
		{
		 $resulValidarCampo['codError'] = '80302';
			//$resulValidarCampo['errorMensaje'] = $textoErrorCampo."debe ser igual o superior a  ".$min. " e inferior a ".$max;
			$resulValidarCampo['errorMensaje'] = $textoErrorCampo." el número es demasiado grande";			
		}
		else
		{
			$resulValidarCampo['codError'] = '00000';
			$resulValidarCampo['errorMensaje'] = '';
		}
		$resulValidarCampo['valorCampo'] = $cantidad;
	}
	
	return $resulValidarCampo;
}

// calculoCCEX_bien_old Ya no se usa solo se permite una cuenta IBAN
function calculoCCEX_bien_old($arrCamposFormCta,$campoCCextranjera)
{$arrValidarCta['codError']='00000';
 $arrValidarCta['errorMensaje']=""; 		
	$arrValidarCta['valorCampo']=$campoCCextranjera;	

	if (($arrCamposFormCta['CODENTIDAD']!==''||$arrCamposFormCta['CODSUCURSAL']!==''|| 
		    $arrCamposFormCta['DC']!==''||$arrCamposFormCta['NUMCUENTA']!=='')&&//si tiene algun campo no vacío
			  ($campoCCextranjera!=='')
		 )
	{ $arrValidarCta['codError']='82005';
	  $arrValidarCta['errorMensaje']="Solo puedes anotar una cuenta (si introduces una 
	                       cuenta de un banco español, no puedes introducir una cuenta extranjera)";				 
	}
	//echo "<br><br>2 validarCampos:validarCuentaNoIBAN:arrValidarCta:";	print_r($arrValidarCta);
	return $arrValidarCta;	
}

/*------------------------------------------------------------------------------------------
Función para validar una cuenta no IBAN, en realidad no se valida la cuenta no IBAN, 
al ser de cualquier país, no se tiene un algoritmo para su validación. 
Solo controla que si hay cuenta No IBAN, no puede existir otra cuenta IBAN

Creo que ya no se usa en ningún lugar.
-------------------------------------------------------------------------------------------*/
function validarCuentaNoIBAN($cuentaIBAN,$cuentaNoIBAN)
{$arrValidarCta['codError'] = '00000';
 $arrValidarCta['errorMensaje'] = ''; 		
	$arrValidarCta['valorCampo'] = $cuentaNoIBAN;	//devuelve el valor que tenga

 if ((isset($cuentaIBAN) && !empty($cuentaIBAN)) && //si tiene $cuentaIBAN
			  (isset($cuentaNoIBAN) && !empty($cuentaNoIBAN)) //si tiene $cuentaNoIBAN
		  )
	{ $arrValidarCta['codError'] = '82005';
	  $arrValidarCta['errorMensaje'] = 'Solo puedes anotar una cuenta (si introduces una 
	                                    cuenta IBAN, no puedes introducir otra cuenta NO IBAN)';				 
	}
	return $arrValidarCta;	
}


/* Ya no se usa ----------------------------------
function calculoCCES($arrCamposFormCta,$campoCCextranjera)
{ 		
 $arrValidarCta['CODENTIDAD']['valorCampo']=$arrCamposFormCta['CODENTIDAD'];
	$arrValidarCta['CODSUCURSAL']['valorCampo']=$arrCamposFormCta['CODSUCURSAL'];
	$arrValidarCta['DC']['valorCampo']=$arrCamposFormCta['DC'];
	$arrValidarCta['NUMCUENTA']['valorCampo']=$arrCamposFormCta['NUMCUENTA'];	

	if (($arrCamposFormCta['CODENTIDAD']!==''||$arrCamposFormCta['CODSUCURSAL']!==''|| 
		    $arrCamposFormCta['DC']!==''||$arrCamposFormCta['NUMCUENTA']!=='')&&//si tiene algun campo no vacío pa ES
			  ($campoCCextranjera!=='') // si tiene CCextranjera
		  )
	{ $arrValidarCta['codError']='82005';
	  $arrValidarCta['errorMensaje']="Solo puedes anotar una cuenta (si introduces una 
	                       cuenta de un banco español, no puedes introducir una cuenta extranjera)";				 
	}
	elseif ($campoCCextranjera !=='') //sólo hay CCEXTRANJERA
	{	$arrValidarCta['codError']='00000';
   $arrValidarCta['errorMensaje']=""; 
	}
	else //solo hay CCespañola
	{	$arrValidarCta=calculoCC($arrCamposFormCta);
	}
	return $arrValidarCta;	
}
// validar DC de cuenta España
*/

function calculoCC($arrCamposFormCta)//creo que ya no se usa, ahora IBAN
{	//echo "<br><br>0 validarCampos:calculoCC:arrCamposFormCta: ";	print_r($arrCamposFormCta);
 
	$arrValidarCta['codError']='00000';
	$arrValidarCta['errorMensaje']="";
	
//se admite que no tenga CC domiciliada, en ese caso tiene que tener todos los campos vacios y no hace el cálculo
// if (!($arrCamposFormCta['CODENTIDAD']=='' && $arrCamposFormCta['CODSUCURSAL']=='' && 
//			     $arrCamposFormCta['DC']=='' && $arrCamposFormCta['NUMCUENTA']==''))//si NO tiene todos los campos de CC vacíos
								
 if ( !(empty($arrCamposFormCta['CODENTIDAD']) && empty($arrCamposFormCta['CODSUCURSAL']) && 
			     empty($arrCamposFormCta['DC']) && empty($arrCamposFormCta['NUMCUENTA']))
				)//si NO tiene todos los campos de CC vacíos
								
	{// si hay algún dato bancario de CC se valida con DC 
   //echo "<br><br>1a validarCampos:calculoCC:arrCamposFormCta: ";	print_r($arrCamposFormCta);
 		if ($arrCamposFormCta['CODENTIDAD']=='' || strlen(trim($arrCamposFormCta['CODENTIDAD'])) < 4 || 
		    $arrCamposFormCta['CODSUCURSAL']=='' || strlen(trim($arrCamposFormCta['CODSUCURSAL'])) < 4 ||						
			   $arrCamposFormCta['DC']=='' || strlen(trim($arrCamposFormCta['DC'])) <2 || 
						$arrCamposFormCta['NUMCUENTA']=='' || 	strlen(trim($arrCamposFormCta['NUMCUENTA'])) < 10 
					)
						//si tienen algun campo vacío o le falta algún dígito (pero no todos)
		{
		 $arrValidarCta['codError']='82000';
			$arrValidarCta['errorMensaje']="Faltan datos";
			$arrValidarCta['CODENTIDAD']['valorCampo']=$arrCamposFormCta['CODENTIDAD'];
			$arrValidarCta['CODSUCURSAL']['valorCampo']=$arrCamposFormCta['CODSUCURSAL'];
			$arrValidarCta['DC']['valorCampo']=$arrCamposFormCta['DC'];
			$arrValidarCta['NUMCUENTA']['valorCampo']=$arrCamposFormCta['NUMCUENTA'];
			//echo "<br><br>1b validarCampos:calculoCC:arrCamposFormCta: ";	print_r($arrCamposFormCta);		
		}			
		else
		{//echo "<br><br>2 validarCampos:calculoCC:arrCamposFormCta: ";	print_r($arrCamposFormCta);
			
			$arrValidarCta['CODENTIDAD']['valorCampo']=$arrCamposFormCta['CODENTIDAD'];
			$arrValidarCta['CODSUCURSAL']['valorCampo']=$arrCamposFormCta['CODSUCURSAL'];
			$arrValidarCta['DC']['valorCampo']=$arrCamposFormCta['DC'];
			$arrValidarCta['NUMCUENTA']['valorCampo']=$arrCamposFormCta['NUMCUENTA'];
	
		 $IentOfi=$arrCamposFormCta['CODENTIDAD']."".$arrCamposFormCta['CODSUCURSAL'];
			$InumCta=$arrCamposFormCta['NUMCUENTA'];
			$DC=$arrCamposFormCta['DC'];
			//echo "<br><br>IentOfi: $IentOfi,<br>IentOfi: $InumCta,<br>DC: $DC";
			$APesos = Array(1,2,4,8,5,10,9,7,3,6); // Array de "pesos"
			$DC1=0;
			$DC2=0;
			$x=8;
			
			while($x>0) {
				$digito=$IentOfi[$x-1];
				$DC1=$DC1+($APesos[$x+2-1]*($digito));
				$x = $x - 1;
			}
			$Resto = $DC1%11;
			$DC1=11-$Resto;
			if ($DC1==10) $DC1=1;
			if ($DC1==11) $DC1=0;              // Dígito control Entidad-Oficina
		
			$x=10;
			while($x>0) {
				$digito=$InumCta[$x-1];
				$DC2=$DC2+($APesos[$x-1]*($digito));
				$x = $x - 1;
			}
			$Resto = $DC2%11;
			$DC2=11-$Resto;
			if ($DC2==10) $DC2=1;
			if ($DC2==11) $DC2=0;         // Dígito Control C/C
		
			$DigControl=($DC1)."".($DC2);   // los 2 números del D.C.
			//echo "<br><br>DC: ",$DigControl;
			if ($DigControl!==$DC)
			{
				$arrValidarCta['codError']='82000';
				$arrValidarCta['errorMensaje']="Error en datos";
			}
		}		
	}
//	else
//	{	$arrValidarCta['codError']='00000';
//		$arrValidarCta['errorMensaje']="";
//	}
	return $arrValidarCta;
}

/*------------------------------------------------------------------------------------------
Agustín 2018-11-25: 	
Añado para eliminar los espacios en blanco en el $cuentaIBAN :$cuentaIBAN=str_replace(' ', '', $cuentaIBAN)

ya que parece que los bancos han adoptado la costumbre de mostrar el IBAN separado con espacios
Función para validar una cuenta IBAN, antes controla que no se ha introducido ademas una cuenta NOIBAN

Llama:  modeloSocios.php:validarIBAN()
Es llamada: validarCamposSocio.php:validarCamposFormAltaSocio() 
Recibe: $cuentaIBAN,$cuentaNoIBAN. 

OBSERVACIONES: Ademas se controla que si hay cuenta No IBAN, no puede existir otra cuenta IBAN
NOTA: ya  no se usa, porque solo se admite una SOLA cuenta IBAN y usa function "validarIBAN($iban)"
-------------------------------------------------------------------------------------------*/
function validarCuentaIBAN($cuentaIBAN,$cuentaNoIBAN)
{ 		
 if ((isset($cuentaIBAN) && !empty($cuentaIBAN)) && //si tiene $cuentaIBAN
			  (isset($cuentaNoIBAN) && !empty($cuentaNoIBAN)) //si tiene $cuentaNoIBAN
		  )
	{ $arrValidarCta['codError'] = '82005';
	  $arrValidarCta['errorMensaje'] = "Solo puedes anotar una cuenta (si introduces una 
	                                    cuenta IBAN, no puedes introducir otra cuenta NO IBAN)";	
			$arrValidarCta['valorCampo'] = $cuentaIBAN;			 
	}
	elseif (isset($cuentaNoIBAN) && !empty($cuentaNoIBAN))//sólo hay $cuentaNoIBAN, no se valida, puede ser distinta para cada país y banco
	{	$arrValidarCta['codError'] = '00000';
   $arrValidarCta['errorMensaje'] = '';
			$arrValidarCta['valorCampo'] = $cuentaIBAN; 
	}
	else //solo hay CCIBAN, se lllama a la función de validar esa cuenta IBAN
	{	$cuentaIBAN = str_replace(' ', '', $cuentaIBAN);//elimina espacios en blanco en el $cuentaIBAN
	
	  $arrValidarCta = validarIBAN($cuentaIBAN);//llama función
	}
	//echo "<br><br>2 validarCampos:validarCuentaIBAN:arrValidarCta:";	print_r($arrValidarCta);
	return $arrValidarCta;	
}

/********** Información sobre validación de IBAN ************************************
En España el IBAN tiene una longitud de 24 dígitos (distinto en otros países). 
Los dos primeros son de carácter alfabético e identifican el país. 
Los dos siguientes son los dígitos de control del IBAN, y son el elemento validador de la totalidad del IBAN. 
Los 20 caracteres restantes son los que forman actualmente el Código de Cuenta Cliente (CCC).
Debemos tener en cuenta que tenemos 2 posibles representaciones del IBAN, en papel debemos mostrarlo
separado en grupos de 4 dígitos, por ejemplo: ES00 0000 9999 9999 9999 9999
Mientras que para su uso en formato digital se debe representar todo junto sin separaciones:
ES0000009999999999999999

Para su validación debemos realizar las siguientes operaciones:
-Movemos los cuatro primeros caracteres del número IBAN a la derecha.
ES0000009999999999999999-->00009999999999999999ES00

Ahora debemos convertir las letras a números según la siguiente tabla de conversión:
-	Sobre el número resultante se calcula el módulo 97.
- Si el resultado del módulo es 1, significa que el número IBAN es correcto.
*/
/*-----------------------------------------------------------------------------------
AGUSTÍN 2018-02-10: Añado control especifico para longitud de IBAN para el caso de IBAN 
de ES: - if (substr($iban,0,2) == 'ES' && strlen($iban) != 24).  -
	
Función para validar una cuenta IBAN 

LLAMA:  funciones estandar de php
LLAMADA: validarCamposSocio.php,validarCamposSocioPorGestor.php 
antes se llamaba desde: validarCampos.php:validarCuentaIBAN(), pero ahora ya no 
porque solo se admite una SOLA cuenta IBAN

Recibe: $iban 
 
OBSERVACIONES: Sirve para validar el IBAN de cualquiera de los 28 estados de la Unión Europea 
(mas Islandia, Liechtenstein, Noruega, Mónaco, San Marino y Suiza), para los que tienen el formato
como ES: ES6000491500051234567892 (24 dígitos comprobado válido), 
y otros formatos como UK: GB29NWBK60161331926819 ( comprobado válido),
FR:FR1420041010050500013M02606( comprobado válido), CHEQUIA:CZ6508000000192000145399(24 dígitos comprobado válido)
y otros países no incluidos en estos 28 pero que se acogen a las normas IBAN.
EMIRATOS: AE460090000000123456789 (es iban válido pero no es SEPA)

Obligatorio para domiciliaciones a partir del 1 de febrero 2014.
---------------------------------------------------------------------------------------*/
function validarIBAN($iban)
{ 
 //echo "<br><br>0 validarCampos:validarIBAN:iban:-";	print_r($iban);echo "-";
	
 $arrValidarIBAN['codError'] = '00000';
 $arrValidarIBAN['errorMensaje'] = '';
		
 $iban = str_replace(' ', '', $iban); //quita blancos en toda la cadena	

	$arrValidarIBAN['valorCampo'] = $iban;	//sin blancos

 if (isset($iban) && !empty($iban))//creo que ya se controla antes, pero lo dejo por si intresa tener para otros usos
	{		
		 $letras_pesos = array('A'=>'10','B'=>'11','C'=>'12','D'=>'13','E'=>'14','F'=>'15','G'=>'16','H'=>'17','I'=>'18',
																									'J'=>'19','K'=>'20','L'=>'21','M'=>'22','N'=>'23','O'=>'24','P'=>'25','Q'=>'26','R'=>'27',
																									'S'=>'28','T'=>'29','U'=>'30','V'=>'31','W'=>'32','X'=>'33','Y'=>'34','Z'=>'35');

			//echo "<br><br>1 validarCampos:validarIBAN:array_keys(letras_pesos):";	print_r(array_keys($letras_pesos));
			
			$iban = strtoupper($iban);//Pone a mayusculas		
 
   if (substr($iban,0,2) == 'ES' && strlen($iban) !== 24)//Se poner una validación de longitud para España, según paises tienen distintas longitudes
   {  //echo "<br><br>FALSE1";
			   
						$arrValidarIBAN['codError'] = '80202';
      $arrValidarIBAN['errorMensaje'] = "Error en validación IBAN para España, has introducido ".strlen($iban)." dígitos: deben ser 24 dígitos (letras <b>ES</b> seguidas 2 números de control y los 20 números de la cuenta sin guiones o puntos)";		    
      $arrValidarIBAN['valorCampo'] = $iban;	
						//return $arrValidarIBAN;	
						//echo "<br><br>2-2 validarCampos:validarIBAN:arrValidarIBAN: ";var_dump($arrValidarIBAN);
   }
   else	
   {    
      $letra1 = substr($iban, 0, 1);
      $letra2 = substr($iban, 1, 1);						 

						$digitosControlIBAN  = substr($iban, 2, 2);							
 
						$cadAux = substr($iban, 4, strlen($iban)).$letra1.$letra2.$digitosControlIBAN; 	
						
      //convertir las letras a números según la tabla de conversión
						$dividendo = str_replace(array_keys($letras_pesos), array_values($letras_pesos), $cadAux);
      
						if ( bcmod($dividendo, 97) == "1") // ojo devuelve bcmod como string o null si modolulo es "0"
      {  //echo "<br><br>TRUE2";

	        $arrValidarIBAN['valorCampo'] = $iban;		//sobra	
         //echo "<br><br>2-3 validarCampos:validarIBAN:arrValidarIBAN: ";var_dump($arrValidarIBAN);									
      }
      else
      {  //echo "<br><br>FALSE3";
					
		       $arrValidarIBAN['codError'] = '82000';
         $arrValidarIBAN['errorMensaje'] = "Error en validación IBAN debe ser (DOS LETRAS PARA PAIS seguidas de dos dígitos de control y el número de la cuenta bancaria, sin guiones, puntos, ...)";		
         $arrValidarIBAN['valorCampo'] = $iban;// con MAYÚSCULAS y sin blancos al principio y final de la cadena 	
         //echo "<br><br>2-4 validarCampos:validarIBAN:arrValidarIBAN: ";var_dump($arrValidarIBAN);																		
      }
   }
	}//if ( isset($iban) && !empty($iban))		
 
 //echo "<br><br>3 validarCampos:validarIBAN:arrValidarIBAN:";	print_r($arrValidarIBAN);

	return $arrValidarIBAN;					
}


/*----------------------- Inicio validarDom ------------------------------------
2019-12-11: para localidad pongo la función validarCampoTexto()

Validar Código Postal domicilio: Obligatoria en altas
Válida que no esté vacío para todos los países y en el caso de 
que el domicilio sea España, validará para todas las provincias  que tenga 5 números
de longitud y que esté comprendido en el rango entre un valor mínimo "01000" 
y un valor máximo "52999"

llama:./modelos/libs/validarCampos/validarCP(),validarTextArea(),validarCampoNoVacio()
-------------------------------------------------------------------------------*/		
function validarCP($CP,$textoErrorCampo)//Cód. Postal obligatorio socio
{
	$resulValidarCampo = validarCampoNoVacio($CP, $textoErrorCampo);

	if ($resulValidarCampo['codError']=='00000')
	{
		$CP = trim($CP);//Se quitan espacios, echo "UTF-8 CP:,$CP<br>";

		if((mb_strlen($CP) !== 5))//mb_strlen()para long unicode
		{	$resulValidarCampo['codError']='80202';
		 	$resulValidarCampo['errorMensaje']=" deben ser 5 dígitos enteros";
		}
		elseif(!preg_match('/^[0-9\d_]{5}$/i',$CP))
		{ $resulValidarCampo['codError']='80220';
			 $resulValidarCampo['errorMensaje']=" 5 números sin espacios ni puntos";
		}
		elseif($CP < 01000 || $CP > 52999)
		{ $resulValidarCampo['codError']='80220';
		 	$resulValidarCampo['errorMensaje']=" Fuera del rango de valores válidos para el CP en España";
		}		
		else
		{ $resulValidarCampo['codError']='00000';
			 $resulValidarCampo['errorMensaje']='';
		}
		
		$resulValidarCampo['valorCampo'] = $CP;
	};
	return $resulValidarCampo;
}																												 

/*----------------------- Inicio validarDom ---------------------------------------
Validar domicilio socio Obligatoria la dirección ES o Extranjero
DIRECCION, LOCALIDAD, CP

Llama:./modelos/libs/validarCampos/validarCP(), validarCampoNoVacio(), 
       validarCampoTexto(), validarTextArea()
							
2019-12-11: para localidad pongo la función validarCampoTexto()							
----------------------------------------------------------------------------------*/		
function validarDom($arrCamposFormDom,$longMin=3,$longMax=255,$textoErrorCampo = "")
{//echo "<br><br>1 validarCampos:validarDom:$arrCamposFormDom";	print_r($arrCamposFormDom);
	
 foreach ($arrCamposFormDom as $campo => $valorCampo)
	{$arrValidarDom[$campo]['valorCampo'] = $valorCampo;
		$arrValidarDom[$campo]['codError'] = '00000';
		$arrValidarDom[$campo]['errorMensaje'] = '';	
	}
	
	if ($arrCamposFormDom['CODPAISDOM'] == 'ES')
	{ 
   $arrValidarDom['CP'] = validarCP($arrCamposFormDom['CP'],"");//es para ES solo
	}
	else
 {	$arrValidarDom['CP'] = validarCampoNoVacio($arrCamposFormDom['CP'],"");//para otros países riesgo no validar
 }		 
		
	$arrValidarDom['LOCALIDAD'] = validarCampoTexto($arrCamposFormDom['LOCALIDAD'],$longMin,$longMax, $textoErrorCampo);//Para evitar de comillas u otros riesgos
			
	$arrValidarDom['DIRECCION'] = validarTextArea($arrCamposFormDom['DIRECCION'],1,255," ");	 		
	
	//echo "<br><br>2 validarCampos:validarDom:arrValidarDom";	print_r($arrValidarDom);
	return $arrValidarDom;
}

/*----------------------- Inicio validarDomSimpOLD ------------------------------------
DESCRIPCION:Validar domicilio simpatizante: País obligatorio y para ES CP obligatorio
LLAMADA: ./modelos/libs/validarCamposSimp()
LLAMA: ./modelos/libs/validarCampos/validarCP() y validarTextArea
No se usa
--------------------------------------------------------------------------------------*/
function validarDomSimp($arrCamposFormDom)
{
	//echo "<br><br>0-1 validarCampos:validarDom:$arrCamposFormDom";	print_r($arrCamposFormDom);
	
 foreach ($arrCamposFormDom as $campo => $valorCampo)
	{$arrValidarDom[$campo]['valorCampo'] = $valorCampo;
		$arrValidarDom[$campo]['codError'] = '00000';
		$arrValidarDom[$campo]['errorMensaje'] = '';	
	}
	
	if ($arrCamposFormDom['CODPAISDOM']=='ES')
	{ //if (isset($arrCamposFormDom['CP']) && $arrCamposFormDom['CP']!==NULL && $arrCamposFormDom['CP']!=='')
	  {$arrValidarDom['CP']=validarCP($arrCamposFormDom['CP'],"");}//obligatorio CP
	}
	//$arrValidarDom['DIRECCION']=validarCampoNoVacio($arrCamposFormDom['DIRECCION'],"");//Tamaño o text area
	//$arrValidarDom['LOCALIDAD']=validarCampoNoVacio($arrCamposFormDom['LOCALIDAD'],"");//Tamaño 
	
 if (isset($arrCamposFormDom['DIRECCION']) && $arrCamposFormDom['DIRECCION']!==NULL && $arrCamposFormDom['DIRECCION']!=='')
	{$arrValidarDom['DIRECCION'] = validarTextArea($arrCamposFormDom['DIRECCION'],10,255," ");}//es para ES solo
		
	//echo "<br><br>****1validarCampos:validarDom:arrValidarDom";	print_r($arrValidarDom);
	return $arrValidarDom;
}

?>