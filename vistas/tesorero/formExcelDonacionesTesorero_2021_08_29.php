<?php
/*-------------------------------------------------------------------------------------------------------
FICHERO: formExcelDonacionesTesorero.php
VERSION: PHP 7.3.21	
	
DESCRIPCION: Formulario que permite elegir el año de las donaciones y los tipos de donates: Todos,Socios,
Simpatizantes (no socios),Anónimos para a exportar a Excel las donaciones con información individual de cada donacion.

LLAMADA: vistas/tesorero/vCuerpoExcelCuotasTesorero.php
LLAMA: /modelos/libs/comboLista.php

OBSERVACIONES: OJO NO PONER NINGUNA SALIDA A PANTALLA (echo, etc.) daría error:
               para formar el buffer de salida a excel utiliza "header()" y no puede 
															haber ningúna salida delante.	

NOTA: Falta controlar el buffer para que vuelva el control, ahora solo se hace en caso
de error en la consulta SQL						            
---------------------------------------------------------------------------------------------------------*/
?>

<div id="registro">	
	<br /><br />	
	
	<span class="textoAzu112Left2">
	 Puedes elegir el año del que quieres exportar las donaciones a Excel
	</span>
	
		<br /><br />	<br /><br />	
		<div align="left"> 								
						
		 <form method="post"  action="./index.php?controlador=cTesorero&amp;accion=excelDonacionesTesorero">
			
				<fieldset>
	    <p>	
							<span class="error">
       PRIVACIDAD DE DATOS: Los datos descargados en Excel, se podrán utilizar para comunicarse con los donates en el caso 
		     de problemas por el filtrado de "spam" en los sistemas receptores de emails. Deberán ser destuidos 
		     una vez utilizados.		
							<br /><br /> Es responsabildad del gestor que no sean usados con otros fines. 	
	      </span>
					  <br /><br />		
					
						<!-- ********************* Inicio selección año donación *************** -->		
						<label>Año donaciones (elige año)</label>					
					 <?php 						
						 require_once './modelos/libs/comboLista.php';
			
			    //for ($a=date("Y")-5; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}
							for ($a=2012; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}
					 	$parValorAnio["%"] = "Todos"; 	
					 	$datosDonaciones['anioDonacionElegido'] = date("Y");			
							
			    echo comboLista($parValorAnio,"datosDonaciones[anioDonacionElegido]",$datosDonaciones['anioDonacionElegido'],
				                  $parValorAnio[$datosDonaciones['anioDonacionElegido']],"%","Todos");																								
			   ?>	
						<!-- ************************* Fin selección año donacion ************ -->	
						
			  	<br /><br />	
				
			  	<!-- ********************* Inicio selección tipo donante *************** -->		
				
						<label>*Elegir tipo donante</label>
						<br />
							<input type="radio"
														name="datosDonaciones[TIPODONANTE]"
														value='%'	
														checked			 
							/><label>Todos</label>							
							<br /> 
							<input type="radio"
														name="datosDonaciones[TIPODONANTE]"
														value='socio'
							/><label>Socios</label>
							<br />
							<input type="radio"
														name="datosDonaciones[TIPODONANTE]"
														value='simpatizante' 
							/><label>Simpatizantes</label>	
							<br />				
							<input type="radio"
														name="datosDonaciones[TIPODONANTE]"
														value='ANONIMO'		 
							/><label>Anonimos</label>		
							<br />
					
					  <!-- ********************* Fin selección tipo donante ****************** -->		
					</p>
				</fieldset>
				
					<br /><br />													
    <input type="submit" name="SiExportarExcel" value="Exportar selección">
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				<input type="submit" name="NoExportarExcel" value="Cancelar"> 				
				
   </form>
			
	 </div>   	

  <br />

</div>		
