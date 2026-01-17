<?php
/*-----------------------------------------------------------------------------
FICHERO: comboLista.php
VERSION: PHP 5.2.3
DESCRIPCION: Forma un Combo list dinámico a partir de un array "$parValor"             
OBSERVACIONES: Es incluida desde los formularios mediante require_once 
               './vistas/usuarios/formRegistrarUsuario.php' válido XHTML
-------------------------------------------------------------------------------*/
function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
{ //echo "<br><br>comboLista: identificadorCampo: $identificadorCampo, valorPrevio: $valorPrevio,
  //     descPrevio:$descPrevio, valorInicial:$valorInicial, descInicial:$descInicial<br><br>";
	$lista = "<select name=\"$identificadorCampo\">";

	foreach ($parValor AS $valorCod => $descCod)
	{
		$lista.= "<option value=\"$valorCod\">$descCod</option>";
	}
	//if ((isset($valorPrevio)) && ($valorPrevio !== ""))
	if ((!isset($valorPrevio)) || empty($valorPrevio))	
	{$lista.='<option value='.$valorInicial.' selected=\"selected\"	>'.$descInicial.'</option>';	
	//$lista.='<option value= selected=\"selected\"	></option>';	 	
	}
	else
	{$lista.='<option value='."$valorPrevio".' selected=\"selected\"	>'.$descPrevio.'</option>';	
	 //$lista.='<option value='."25".' selected=\"selected\"	>'."2a".'</option>';	
	}
	$lista.="</select>";

	return $lista;
};
/*ejemplo de llamada a la funcion
   $parValorComboPais=arrayParValor($tablasBusqueda,$cadenaCondicionesBuscar,
   "CODPAIS1","NOMBREPAIS","ES");*/
?>