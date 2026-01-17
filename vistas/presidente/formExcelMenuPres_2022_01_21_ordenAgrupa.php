<?php 
/********************************************************************************************
FICHERO: formExcelMenuPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para exportar a un fichero Excel por un gestor con rol de Presidencia, 
Vice. y Secretaría, algunos datos de los socios de las agrupaciones elegidas
en este formulario 

Se descarga en el PC en carpeta "Descargas" el archivo Excel mediante el navegador
Al abrir el archivo Excel, puede dar un aviso sobre seguridad.

LLAMADA: vistas/presidente/vCuerpoExcelMenuPres.php 
y a su vez desde cPresidente.php: excelSociosPres()

OBSERVACIONES:   
           
********************************************************************************************/
?>
<div id="registro">	
	<br /><br />	
	
	<span class="textoAzu112Left2">
	 Puedes elegir la agrupación de la que quieres exportar los datos de sus socios/as a Excel 
		<br /><br />
		Se incluyen tanto socios/as que tienen el campo de información email = SI y NO. 
		<br /><br />
		En el caso de que un socio/a tenga información email = NO, no se deberá utilizar los emails excepto 
		para enviar informaciones relativas al cobro de cuotas (u ocasionalmente para comunicaciones de mucha importancia). 
		<br /><br />
	 Estarán ordenados por	nombre de agrupación, domicilio código de país, provincia y apellidos, nombre
		<br /><br />
		Nota: Después de hacer clic en "Exportar selección" es mejor elegir "Gardar archivo"
	</span>
		<br /><br />		

	<!-- ********************* Inicio selección agrupación *************** -->						

	<form method="post" 
							action="./index.php?controlador=cPresidente&amp;accion=excelSociosPres">
		<fieldset>
			<p>						
					<span class="error">
					<br />	<br />
					<strong>PROTECCIÓN DE DATOS: Los datos personales de los socios/as descargados en Excel, no se podrán utilizar para fines ajenos a Europa Laica. 
					No serán cedidos a terceros. 
					<br /><br />Es responsabildad del gestor que no sean usados con otros fines. 	Deberán ser destruidos cuando ya carezcan de utilidad.</strong>
					</span>
					<br /><br />	<br />	
					
				<label><strong>Agrupación territorial elegida</strong></label>					
				<?php 
					require_once './modelos/libs/comboLista.php';
					
					//echo "<br><br>1 formExcelMenuPres.php:parValorComboAgrupaSocio:";print_r($parValorComboAgrupaSocio);							
					
					//---------- Inicio reordenar listado agrupaciones --------------------------------------------
					
					unset($parValorComboAgrupaSocio['lista']['00000000']);//elimina el elemento correspondiente a Europa Laica Estatal e Internacional
					unset($parValorComboAgrupaSocio['lista']["%"]);       //elimina para después añadir 						
					
					$parValorComboAgrupaSocio['lista']['00000000']='Europa Laica Estatal e Internacional'; //añade al final del array el elemento correspondiente a Europa Laica Estatal e Internacional
					$parValorComboAgrupaSocio['lista']["%"]= "Todas"; //añade al final del array el elemento correspondiente a "Todas"
					//---------- Fin reordenar listado agrupaciones -----------------------------------------------														

					echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]",
																					$parValorComboAgrupaSocio['valorDefecto'],
																					$parValorComboAgrupaSocio['lista'][$parValorComboAgrupaSocio['valorDefecto']],"","");
					?> 	
				<br /><br />	<br />	
				<!--			
				<label>*Elegir opción recibir email</label>
				<br />

						<input type="radio"
													name="datosFormMiembro[INFORMACIONEMAIL]"
													value='SI' 
													checked							
						/><label>Los que SÍ aceptan recibir email</label>
						<br />
						<input type="radio"
													name="datosFormMiembro[INFORMACIONEMAIL]"
													value='NO' 
						/><label>Los que NO aceptan recibir email</label>	
						<br />				
						<input type="radio"
													name="datosFormMiembro[INFORMACIONEMAIL]"
													value='%'		 
						/><label>Todos</label>		
					<br /> 
					-->  						
			</p>
		</fieldset>	
		
			<br />			
			<span class="error">
				<strong>NOTA: Después de hacer clic en "Exportar" busca en la carpeta de "Descargas" el archivo Excel con el nombre "Excel_coord_Fecha del día"</strong>
			</span>
			<br /><br />			
			
		<input type="submit" name="SiExportarExcel" value="Exportar selección">
		&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
		
		<input type="submit" name="NoExportarExcel" value="Cancelar"> 		
		
	</form>
	<!-- ************************* Fin selección agrupación ************* -->						

</div>		
