<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formDonacionConceptos.php
VERSION: PHP 7.3.21

DESCRIPCION:
Se obtienen los datos de los "Concepto de Donación" existentes partir de la tabla "DONACIONCONCEPTOS" 
para después formar un formulario tabla-lista  "DONACIÓN CONCEPTOS", con las columnas:
"CONCEPTO,NOMBRECONCEPTO,FECHACREACIONCONCEPTO,OBSERVACIONES" y el botón "Añadir Nuevo Concepto de Donación"

LLAMADA: vistas/tesorero/vCuerpoDonacionConceptosInc.php y previamente desde cTesorero.php:donacionConceptos()

OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>

<div id="registro">


	<form method="post" class="linea" action="./index.php?controlador=cTesorero&amp;accion=aniadirDonacionConceptoTes">

			<input type="submit" name="AniadirDonacionConcepto" title="Clic para Añadir Nuevo Concepto de Donación" value="Añadir Nuevo Concepto de Donación">
							
	</form>		
	
	<br /><br /><br /> 
	
	<br /><span class='textoNegro8Left'>
Todos los "Conceptos de Donación", de la siguiente lista aparecerán o estarán disponibles en el campo "Concepto de la Donación" 
en los procedimientos de gestionar "Donaciones" en que aparezca ese campo. 
	</span>
	
	<br /><br />  	
				
	<table width="100%" border="1" cellspacing="0" cellpadding="20" bordercolor="#99CCFF">
	
		<!-- ******************** Inicio fila cabecera ********************** -->	

		<tr height='30' bgcolor="#CCCCCC">

			<th  class="textoAzul8C"><b>Concepto de Donación</b></th>
			<th  class="textoAzul8C"><b>Nombre Concepto</b></th> 
			<th  class="textoAzul8C"><b>Fecha de creación del Concepto</b></th>
			<th  class="textoAzul8C"><b>Observaciones</b></th> 
			
		</tr>
		<!-- ******************** Fin fila cabecera ************************* -->	
		
		<!-- ******************** Inicio filas datos ************************ -->	
		<?php 
								
		//echo "<br><br>arrDonacionConceptos: ";print_r($arrDonacionConceptos);
		
		$items = $arrDonacionConceptos;
		
		//echo "<br><br>items:";print_r($items);
		
		//foreach ($items as $fila => $valFila)
		
		foreach ($items as &$fila)
		{ 
			echo ("<tr height='30'>");
			
			 echo ("<td class='textoAzul8L'><b>&nbsp;".$fila['CONCEPTO']['valorCampo']."</b></td>");
				echo ("<td class='textoAzul8L'>&nbsp;".$fila['NOMBRECONCEPTO']['valorCampo']."</td>");
				echo ("<td class='textoAzul8L'>&nbsp;".$fila['FECHACREACIONCONCEPTO']['valorCampo']."</td>");
				echo ("<td class='textoAzul8L'>&nbsp;".$fila['OBSERVACIONES']['valorCampo']."</td>");
			

			echo ("</tr>");
		}
		?>
		
		<!-- ******************** Fin filas datos **************************** -->	
			
	</table>

</div>		
