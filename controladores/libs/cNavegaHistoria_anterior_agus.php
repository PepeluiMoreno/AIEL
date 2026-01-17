<?php
/**-----------------------------------------------------------------------------
FICHERO: cNavegaHistoria.php
VERSION: PHP 5.2.3
DESCRIPCION:Devuelve una cadena con los enlaces de navegación desde la primera
											 pág. a última, y otra cadena con un boton de enlace al pág. anterior 
RECIBE: en "$historia" la variable se session 	$_SESSION['vs_HISTORIA'], que guarda
        todos las url de la navegación y el texto, y la posición actual												          
RETORNA: Un array con las href de toda la navegación y otra con la href de pág. 
         anterior en forma de botón (usando clases de CSS)
Autor:Agustín Villacorta.
**------------------------------------------------------------------------------*/
function cNavegaHistoria($historia,$textoBotonAnterior)
{//echo "<br><br>1 cNavegaHistoria.php:historia: ";print_r($historia);
 //echo "<br><br>2 cNavegaHistoria.php:textoBotonAnterior: ";print_r($textoBotonAnterior);
	
	$pagActual = $historia['pagActual'];				  
	$historiaEnlaces = $historia['enlaces'];	
	$cadNavegEnlaces ='';
	
	for ($fila=0;$fila<$pagActual;$fila++)
 {if (isset($historiaEnlaces[$fila]['link']) && $historiaEnlaces[$fila]['link']!==NULL && 
	     $historiaEnlaces[$fila]['link']!=='')//evitar caso socios que falta la fila 1 ac0os foreach mejor
		{
		  $cadNavegEnlaces.= "<a href='".$historiaEnlaces[$fila]['link']."'>".
		                   "<span class='textoAzulClaro8L'>".">>".$historiaEnlaces[$fila]['textoEnlace']."</span></a>";
		}																			
	}
	//para la pág. actual no se pone el link, solo el texto
	//$cadNavegEnlaces.="<span class='textoGris8Left2'>".">>".$historiaEnlaces[$pagActual]['textoEnlace']."</span>";
	if (isset($cadNavegEnlaces))
	{	$cadNavegEnlaces.="<span class='textoGris8Left2'>".">>".$historiaEnlaces[$pagActual]['textoEnlace']."</span>";
	 	$arrNavegacion['cadNavegEnlaces'] = $cadNavegEnlaces;
	}
	
 /*----------- inicio asignación a anterior
 $cadBotonAnterior="<a class='buttonGris2' href='".$historiaEnlaces[$pagActual-1]['link']."'>";
  
	if (!isset($textoBotonAnterior) || $textoBotonAnterior ==NULL || $textoBotonAnterior =='')
 { $textoBotonAnterior=$historiaEnlaces[$pagActual-1]['textoEnlace'];
 }	
	//-----------	fin asignación a anterior	
	*/
	//para el botón anterior, el bucle es porque hay un problema con socios, 
	// al saltarse en historia el 1 cuando se resuelva se podrá quitar
	for ($pagAnterior=$pagActual-1;$pagAnterior>=0;$pagAnterior--)
	{ //echo "<br><br>pagAnterior:$pagAnterior";
	 	if (isset($historiaEnlaces[$pagAnterior]['link']) && $historiaEnlaces[$pagAnterior]['link']!==NULL && 
     $historiaEnlaces[$pagAnterior]['link']!=='')//evitar caso socios que falta la fila 1 ac0os foreach mejor
			{	
			 //echo "<br><br>3 cNavegaHistoria.php:historiaEnlaces[pagAnterior]['link']:";print_r($historiaEnlaces[$pagAnterior]['link']);
    //----------- asignación a anterior
			 $cadBotonAnterior="<a class='buttonGris2' href='".$historiaEnlaces[$pagAnterior]['link']."'>";
				$pagAnterior=-1;
			  
				if (!isset($textoBotonAnterior) || $textoBotonAnterior ==NULL || $textoBotonAnterior =='')
			 { $textoBotonAnterior=$historiaEnlaces[$pagAnterior]['textoEnlace'];
			 }	
				//-----------				
			}	
	}
 if (isset($cadBotonAnterior))
	{$cadBotonAnterior.=$textoBotonAnterior."</a>";
	 	$arrNavegacion['anterior'] = $cadBotonAnterior;
	}																								

	//$arrNavegacion['cadNavegEnlaces'] = $cadNavegEnlaces;
	
	/*echo "<br><br>cNavegaHistoria:pagActual";print_r($pagActual);
	echo "<br><br>cNavegaHistoria:arrNavegacion:";print_r($arrNavegacion);
	echo "<br><br>cNavegaHistoria1:_SESSION['vs_HISTORIA']['enlaces']:";print_r($_SESSION['vs_HISTORIA']['enlaces']);
	*/
	$total=count($_SESSION['vs_HISTORIA']['enlaces']);
	
	for ($fila=$pagActual+1;$fila<$total;$fila++)
 {if (isset($_SESSION['vs_HISTORIA']['enlaces'][$fila]))//evitar caso socios que falta la fila 1 acaso foreach mejor
		{
		  unset ($_SESSION['vs_HISTORIA']['enlaces'][$fila]);
		}																			
	}
	//echo "<br><br>cNavegaHistoria2:_SESSION['vs_HISTORIA']['enlaces']:";print_r($_SESSION['vs_HISTORIA']['enlaces']);
		
 return $arrNavegacion;
}		
?>