<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formExportarExcelInformeAnualPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para exportar datos soci@s a Excel para informe anual de Secretaría, 
incluye las plantillas y formulario para la exportación de socios por presidencia

En el archivo Excel se incluye todos los socios/as que en el año correspondiente estuvieron de alta,
aunque después en ese mismo año se diesen de baja.

Este formulario permite elegir agrupación (aunque lo normal es que se incluyan todas) y el año desde 2009 
(lo normal es incluir el año que finalizó)

LLAMADA:  vistas/presidente/vCuerpoExportarExcelInformeAnualPres.php
y previamente desde cPresidente.php:cExportarExcelInformeAnualPres()
													
OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>

<div id="registro">	

	<br />
	<span class="error">
			<br />	<br />
			<strong>PROTECCIÓN DE DATOS: Los datos personales de los socios/as descargados en Excel, no se podrán utilizar para fines ajenos a Europa Laica. 
			No serán cedidos a terceros. 
			<br /><br />Es responsabildad del gestor que no sean usados con otros fines. 	Deberán ser destruidos cuando ya carezcan de la utilidad para lo que fueron obtenidos</strong>
	</span>
	<br /><br /><br />	<br />	
	
	<span class="textoAzu112Left2">
		<br />	<br />
		En el archivo Excel se incluyen todos los socios/as que en el año elegido estuvieron de alta (aunque después en ese mismo año se diesen de baja),
		con la siguiente información de cada socio/a:	
		<br /><br />
		- CODSOCIO, Apellidos_Nombre, FECHAALTA, FECHABAJA, ESTADO, Agrupacion_Actual, NUMDOCUMENTOMIEMBRO, TIPODOCUMENTOMIEMBRO,
		Codigo_Pais_Documento, DIRECCION, LOCALIDAD, NOMPROVINCIA, Codigo_Pais_Domicilio
		<br /><br />
		- Estarán ordenados por	CODSOCIO (Número de Socio/a)
	
			<br /><br /><br />
	NOTA: Algún navegador y alguna versión de Excel podrían dar algún problema para descargar el archivo Excel, en ese caso prueba con otros navegadores.
	  <br /><br />		 
		Después de hacer clic en "Exportar selección" es mejor elegir "Gardar archivo" 
			<br /><br />		
	</span>

  <!-- ********************* Inicio selección agrupación y año *************** -->						

		<form method="post" action="./index.php?controlador=cPresidente&amp;accion=cExportarExcelInformeAnualPres">
			<fieldset>
				<p>	
				<br />		
					<label>En el informe por defecto se incluyen todas las agupaciones territoriales, pero opcionalmente se puede elegir sólo una agrupación 				</label>		      
					<br /><br />							
					<label>Agrupación territorial elegida</label>					
					<?php 
						require_once './modelos/libs/comboLista.php';
						
						//echo "<br><br>1 formExportarExcelInformeAnualPres.php:parValorComboAgrupaSocios:";print_r($parValorComboAgrupaSocios);
						
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
					<br /><br />	
				<!-- 	<span class='textoAzul9C'>Año</span>		-->
				<label>Año elegido</label>		
						<?php 
							require_once './modelos/libs/comboLista.php';
			
							//for ($a=date("Y")-5; $a<=date("Y")+1; $a++){$parValorAnio[$a]=$a;}
							//$parValorAnio["%"]="Todos"; 			
							
							for ($a=2009; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}
							$parValorAnio["%"]="Todos"; 
							
							if (!isset($exportarCampo['ANIOCUOTA']) || empty($exportarCampo['ANIOCUOTA']))
							{
								$exportarCampo['ANIOCUOTA'] = date('Y');
							}									
																				
							echo comboLista($parValorAnio,"exportarCampo[ANIOCUOTA]",$exportarCampo['ANIOCUOTA'],
																						$parValorAnio[$exportarCampo['ANIOCUOTA']],date('Y'),date('Y'));																																																
						?>	
				<br /><br />
				</p>
				
			</fieldset>	
				<br />								
			<input type="submit" name="SiExportarExcel" value="Exportar selección">
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			
			<input type="submit" name="NoExportarExcel" value="Cancelar"> 		
			
		</form>
		<br /><br />						
		<!-- ************************* Fin selección agrupación y año ************* -->						
		
</div>		
