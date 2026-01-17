<?php 
/********************************************************************************************
FICHERO: formExcelMenuCoord.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para exportar a un fichero Excel, los socios de todas las agrupaciones 
correspondientes a un área de gestión o bien de una agrupación concreta elegida 
dentro de ese área territorial que gestiona un coordinador. 
En el caso del que el área de gestión incluya varias posibles agrupaciones, 
por ejemplo Andalucía, en este formulario se permite elegir una Agrupación territorial concreta

Se descarga en el PC en carpeta "Descargas" el archivo Excel mediante el navegador
Al abrir el archivo Excel, puede dar un aviso sobre seguridad.

LLAMADA: vistas/coordinador/vCuerpoExcelMenuCoord.php 
y a su vez vienes desde cCoordinador.php: excelSociosCoord()

OBSERVACIONES:  
        
********************************************************************************************/
?>
<div id="registro">	
  <br /><br />	

	 <!--<span class='textoAzul9C'>Área gestión:</span>			-->
			<span class="textoAzu112Left2"><strong>Área gestión:</strong></span>			
	<span class='mostrar1'><?php  echo "<b>&nbsp;$areaGestionNom</b>"; ?></span>
	<br /><br />	
	<span class="textoAzu112Left2">
	 Exporta a Excel algunos datos de los socios/as de la agrupación territorial 	
		<br /><br />
		Se incluyen tanto socios/as que tienen el campo de información email = SI y NO. 
		<br /><br />
		En el caso de que un socio/a tenga información email = NO, no se deberán utilizar los emails excepto 
		para enviar informaciones relativas al cobro de cuotas (u ocasionalmente para comunicaciones de mucha importancia). 
		<br /><br />
		Estarán ordenados por	nombre de agrupación, domicilio código de país, provincia y apellidos, nombre
		<br /><br />		 
		Nota: Después de hacer clic en "Exportar selección" es mejor elegir "Gardar archivo" 
	</span>
		<br /><br />	
		<div align="left"> 		
			<!-- ********************* Inicio selección agrupación *************** -->						
<!--	<div align="center"> -->
		 <form method="post" 
         action="./index.php?controlador=cCoordinador&amp;accion=excelSociosCoord">
				<fieldset>
	    <p>							
							<span class="error">
       <strong>PROTECCIÓN DE DATOS: Los datos personales de los socios/as descargados en Excel, no se podrán utilizar para fines ajenos a Europa Laica. 
							No serán cedidos a terceros. 
							<br /><br />Es responsabildad del gestor que no sean usados con otros fines. 	Deberán ser destruidos cuando ya carezcan de utilidad.</strong>
	      </span>
					<br /><br />	
					 <label><strong>Agrupación territorial elegida</strong></label>					
					 <?php 
					  require_once './modelos/libs/comboLista.php';
		
		     //echo "<br><br>1 formExcelMenuCoord.php:parValorComboAgrupaSocio:";print_r($parValorComboAgrupaSocio);
							
							echo comboLista($parValorComboAgrupaSocio['lista'], "datosFormSocio[CODAGRUPACION]",
						        	        $parValorComboAgrupaSocio['valorDefecto'],
																							$parValorComboAgrupaSocio['lista'][$parValorComboAgrupaSocio['valorDefecto']],"","");
		     ?> 	
						<br /><br />						
					<!--	
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

					<br /><br />					
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
  <br />
</div>		
