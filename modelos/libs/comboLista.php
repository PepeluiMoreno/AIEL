<?php
/*-----------------------------------------------------------------------------
FICHERO: comboLista.php
VERSION: PHP 7.2.31
DESCRIPCION: Forma un Combo list dinámico "select"a partir de un array "$parValor"             

OBSERVACIONES: Es incluida desde los formularios mediante require_once 
               './vistas/usuarios/formRegistrarUsuario.php' válido XHTML
-------------------------------------------------------------------------------*/
function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
{ 
 //echo "<br><br>0-1 comboLista: identificadorCampo: $identificadorCampo, valorPrevio: $valorPrevio,descPrevio:$descPrevio, valorInicial:$valorInicial, descInicial:$descInicial";
	
	
	$lista = "<select name=\"$identificadorCampo\" size=\"1\">"; // desplegable 
	
	foreach ($parValor as $valorCod => $descCod)
	{
		$lista.= "<option value=\"$valorCod\">$descCod</option>";
	}


	if ((!isset($valorPrevio)) || empty($valorPrevio))	
	{
		 $lista.='<option value='.$valorInicial.' selected=\"selected\"	>'.$descInicial.'</option>'; 	
	}
	else
	{ 
   $lista.='<option value='."$valorPrevio".' selected=\"selected\"	>'.$descPrevio.'</option>';		  
	}
	
	$lista.="</select>";

 //echo "<br><br>1 comboLista:lista: ";print_r($lista);
	
	return $lista;
}
 /*ejemplo de llamada a la funcion arrayParValor()
   $parValorComboPaisDomicilio = arrayParValor($tablasBusqueda,$cadenaCondicionesBuscar, "CODPAIS1","NOMBREPAIS","ES");
			
   echo comboLista($parValorComboPaisDomicilio['lista'], "datosFormDomicilio[CODPAISDOM]",
			                $parValorComboPaisDomicilio['valorDefecto'],$parValorComboPaisDomicilio['descDefecto'],"ES","España");		
																			
   echo comboLista($parValorComboPaisDomicilio['lista'], "datosFormDomicilio[CODPAISDOM]",
			                $parValorComboPaisDomicilio['valorDefecto'],$parValorComboPaisDomicilio['descDefecto'],"","");																					
	*/
?>