<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formExportarExcelEstadisticasAltasBajasAgrupPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para exportar a Excel informes estadísticos por "agrupaciones" y "años" 
a fecha Y-12-31 con los datos siguientes: 

Total de Alta,	ALTAS_ANIO(Total	H	%H	M	%M),BAJAS_ANIO(Total	H	%H	M	%M),	Netas	de Alta(por año)

Permite elegir agrupación (lo normal es que se incluyan todas) y "rangos de año inferior-superior" 
a fecha Y-12-31 desde el año 2009 al actual.

LLAMADA: vistas/presidente/vCuerpoExportarExcelEstadisticasAltasBajasAgrupPres.php
y previamente desde  cPresidente.php:cExportarExcelEstadisticasAltasBajasAgrupPres(),
												
OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>

<div id="registro">	

	<br /><br /><br />	
		<span class='textoAzulClaro8L'>
  NOTA: Algún navegador o alguna versión de Excel podrían dar algún problema para descargar el archivo Excel, en ese caso prueba con otros navegadores.
  <br /><br />		 
		Después de hacer clic en "Exportar selección" es mejor elegir "Gardar archivo" 
		<br /><br /><br /><br />		
	</span>
	

	<!-- ********************* Inicio selección agrupación y año *************** -->						

	<form method="post" 
							action="./index.php?controlador=cPresidente&amp;accion=cExportarExcelEstadisticasAltasBajasAgrupPres">
		<fieldset>
			<p>		
				<br />					
				<label>Agrupación territorial elegida (se puede elegir una o todas las agupaciones territoriales)</label>					
				<?php 
					require_once './modelos/libs/comboLista.php';
					
					//echo "<br><br>1 formExportarExcelEstadisticasAltasBajasAgrupPres.php:parValorComboAgrupaSocios:";print_r($parValorComboAgrupaSocios);

					//---------- Inicio reordenar listado agrupaciones --------------------------------------------
					
					unset($parValorComboAgrupaSocios['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional
					unset($parValorComboAgrupaSocios['lista']["%"]);       //elimina para después añadir 						
					
					$parValorComboAgrupaSocios['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional
					$parValorComboAgrupaSocios['lista']["%"]= "Todas"; //añade al final del array el elemento correspondiente a "Todas"
					
					//---------- Fin reordenar listado agrupaciones -----------------------------------------------							
	
					echo comboLista($parValorComboAgrupaSocios['lista'], "exportarCampo[CODAGRUPACION]",
																					$parValorComboAgrupaSocios['valorDefecto'],
																					$parValorComboAgrupaSocios['lista'][$parValorComboAgrupaSocios['valorDefecto']],"","");
					?> 	
				<br /><br /><br />	
			<!-- 	<span class='textoAzul9C'>Año</span>		-->
			
			
			<label>Se puede elegir un rango de años, a bien un solo año (en este caso se repetirá el mismo año en la siguiente selección)</label>
			<br />	
			<label>Año elegido inferior</label>		
					<?php 
						require_once './modelos/libs/comboLista.php';
		
						//for ($a=date("Y")-5; $a<=date("Y")+1; $a++){$parValorAnio[$a]=$a;}
						
						for ($a=2009; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}
						$parValorAnio["%"]="Todos"; 

						if (!isset($exportarCampo['ANIOCUOTA_Inferior']) || empty($exportarCampo['ANIOCUOTA_Inferior']))
						{
							$exportarCampo['ANIOCUOTA_Inferior'] = date('Y');
						}																	
																			
						echo comboLista($parValorAnio,"exportarCampo[ANIOCUOTA_Inferior]",$exportarCampo['ANIOCUOTA_Inferior'],
																					$parValorAnio[$exportarCampo['ANIOCUOTA_Inferior']],2009,2009);																																																
					?>	
			<br /><br />
			
							<label>Año elegido superior</label>		
					<?php 
						require_once './modelos/libs/comboLista.php';
		
						//for ($a=date("Y")-5; $a<=date("Y")+1; $a++){$parValorAnio[$a]=$a;}
						//$parValorAnio["%"]="Todos"; 			
						
						for ($a=2009; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}
						$parValorAnio["%"]="Todos"; 		

						if (!isset($exportarCampo['ANIOCUOTA_Superior']) || empty($exportarCampo['ANIOCUOTA_Superior']))
						{
							$exportarCampo['ANIOCUOTA_Superior'] = date('Y');
						}																
																			
						echo comboLista($parValorAnio,"exportarCampo[ANIOCUOTA_Superior]",$exportarCampo['ANIOCUOTA_Superior'],
																					$parValorAnio[$exportarCampo['ANIOCUOTA_Superior']],2009,2009);																																														
					?>	
			<br /><br />
			</p>
			
		</fieldset>	
		
			<br /><br /><br />												
		<input type="submit" name="SiExportarExcel" value="Exportar selección">
		&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
		
		<input type="submit" name="NoExportarExcel" value="Cancelar"> 				
		
	</form>			
	
	<!-- ************************* Fin selección agrupación y año ************* -->		
	<br /><br />			

</div>		
