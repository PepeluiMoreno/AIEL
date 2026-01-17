<?php
/*----------------------------------------------------------------------------------------
FICHERO: formEstadoOrdenesCobroRemesasTes.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que muestra datos de las remesas de órdenes de cobros 
emitidas a los bancos a partir de las tablas REMESAS_SEPAXML y ORDENES_COBRO.
												
Se puede: 
- Ver la lista de órdenes de cobro de una remesa,
- Eliminar la remesa de las tablas REMESAS_SEPAXML y ORDENES_COBRO en caso de se hubiese 
  producido un	error o por otras causas, siempre que aún no se hayan actualizado los pagos
  de la remesa  
- Descargar el Archivo SEPA de remesa para subirlo a la web del B. Santander		
- Actualizar los pagos de una remesa en la tabla la tabla "CUOTAANIOSOCIO" 

LLAMADA: vistas/tesorero/vCuerpoEstadoOrdenesCobroRemesasTes.php
												
OBSERVACIONES: 
2020-12-10: Añado columna para descargar Archivo SEPA-XML            
-------------------------------------------------------------------------------------------*/
?>	
<div id="registro">	

	<div align="left"> 

		<!-- ************** Inicio ingresos cuotas por año ******************************* -->
		<span class="textoAzu112Left2">
			<br />
			I- Cada fila se añade al exportar las órdenes cobro de cuotas a un archivo SEPA para cobro remesa por B.Santander 
			(en página anterior <i>II.2 - Generar archivo SEPA_ISO20022CORE-XML para remesa con las órdenes cobro de cuotas domiciliadas, para envío al B.Santander ...)</i>
			<br /><br />	
			II- Después de añadirse una nueva fila (tendará campo "ANOTADO_EN_CUOTAANIOSOCIO = NO"), se comprueban que los datos son correctos (en el icono "lupa" se pueden ver las órdenes de cobro una a una) 
			<br />En caso de que no sean correctos o no se quiera enviar al banco para su cobro, o si después de enviarlo al banco fue cancelado 
			o anulado el cobro de esa remesa en la web del Banco, se debe eliminar esa remesa "papelera" (se borrara de las tablas "REMESAS_SEPAXML" y "ORDENES_COBRO")	
   <br /><br />	
   III- Después de comprobar que es correcto, se descarga el archivo "SEPA_ISO20022CORE_fecha.xml"	(Icono flecha) y la persona de tesorería autorizada lo exporta a la web del B. Santander para efectuar la orden			
   <br /><br />			
			IV- Cuando se compruebe que el banco ha efecuado el cobro de esa remesa, se debe hacer clic en el icono "pluma" (Actualizar pagos remesa en CUOTAANIOSOCIO) 
			para trasladar esos pagos de cuotas de los socios/as a la tabla "CUOTAANIOSOCIO"	
		 <br /><br />	
		 V- Las devoluciones se anotarán posteriormente desde el menú "Cuotas socios/as--> Icono € (Pago cuota)", una a una cuando se vayan produciendo
		</span> 
	
	 <br /><br />			
		<!-- ************************* Inicio TABLA  ******************************************************** -->		
		
		<table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
		
		 <!--*************** Inicio cabecera tabla ************************************-->	
   <tr bgcolor="#CCCCCC">
				<th  class="textoAzul8L" width="4%">Año cuota</th>					
    <th  class="textoAzul8L" width="8%">Fecha orden cobro</th>
				<th  class="textoAzul8L" width="8%">Fecha real cobro por Banco</th>				
				
				<th  class="textoAzul8L" width="8%">Archivo Remesa SEPA XML</th>		 
				<th  class="textoAzul8L" width="12%">Fecha creación archivo SEPA XML</th>
    <th  class="textoAzul8L" width="6%">Fecha altas exentas pago<br />(últimos meses año)</th>			

    <th  class="textoAzul8L" width="4%"><strong>Anotado en CUOTA ANIO SOCIO</strong></th>
    <th  class="textoAzul8L" width="6%"><strong>Fecha anotación pago en CUOTA ANIO SOCIO</strong></th>			
				<th  class="textoAzul8L" width="6%">Número cuotas en remesa</th>
				<th  class="textoAzul8L" width="4%">Núm. cuotas devuel-tas</th>				
    <th  class="textoAzul8L" width="8%">Total importe cuotas remesa €</th>				
    <th  class="textoAzul8L" width="6%">Total gastos emisión remesa €</th>	
    <th  class="textoAzul8L" width="6%">Total gastos devueltos remesa €</th>			
    <!-- <th  class="textoAzul8L" width="4%">Directorio Archivo Remesa</th>		//decido nomostrar aquí
				<!--<th  class="textoAzul8L" width="4%">Código gestor remesa</th>	//decido no utilizar-->
    <!-- <th  class="textoAzul8L" width="20%">Observaciones</th>	//decido no utilizar-->

				<th class="textoAzul7L" width='4'>Ver</th>				
				<th class="textoAzul7L" width='4'>Eliminar órdenes cobro de remesa<br /> (error o anulada)</th>
				<th class="textoAzul7L" width='4'>Descargar Archivo Remesa SEPA</th>
				<th class="textoAzul7L" width='6'>Actualizar pagos de remesa en CUOTA ANIO SOCIO</th>
			</tr>  
			<!--*************** Fin cabecera tabla ************************************-->				
			
   <?php 		
    /*--------- Inicio foreach() ------------------------------------------------------------------*/
				foreach ($arrOrdenesCobro as $fila)
				{
					/*<!-- ************ Inicio Fila ****************************************************** -->	*/
					//echo "<br><br>0 formEstadoOrdenesCobroRemesasTes.php:fila: ";print_r($fila);
	
					echo ("<tr height='25'>");
					
					echo ("<td class='textoAzul7R'>".$fila['ANIOCUOTA']."&nbsp;&nbsp;&nbsp;"."</td>");
					echo ("<td class='textoAzul7R'>".$fila['FECHAORDENCOBRO']."&nbsp;&nbsp;"."</td>");					
					echo ("<td class='textoAzul7R'>".$fila['FECHAPAGO']."&nbsp;</td>");//VARCHAR						
					
					echo ("<td class='textoAzul7L'>".$fila['NOMARCHIVOSEPAXML']."</td>");//VARCHAR					
					//echo ("<td class='textoAzul7R'>".$fila['FECHA_CREACION_ARCHIVO_SEPA']."&nbsp;</td>");  	//formato datetime= 'Y-m-d H:i:s';			
					//echo ("<td class='textoAzul7R'>".date_format(date_create($fila['FECHA_CREACION_ARCHIVO_SEPA']),'Y-m-d\TH:i:s')."&nbsp;</td>");//formato datetime= 'Y-m-d\TH:i:s';			 
					//-- opción con la clase 	DateTime			
					//$datetime = new DateTime($fila['FECHA_CREACION_ARCHIVO_SEPA']);
					//echo ("<td class='textoAzul7R'>".$datetime->format('Y-m-d\TH:i:s')."&nbsp;</td>");  	//tambien pudiera ser format('c')= '2017-10-14T11:01:29+02:00'
					echo ("<td class='textoAzul7R'>".date_format(new DateTime($fila['FECHA_CREACION_ARCHIVO_SEPA']),'Y-m-d\TH:i:s')."&nbsp;</td>"); //formato datetime= 'Y-m-d\TH:i:s'=2017-10-14T11:01:29		

					echo ("<td class='textoAzul7R'>".$fila['FECHAALTASEXENTOSPAGO']."&nbsp;&nbsp;"."</td>");

					if ($fila['ANOTADO_EN_CUOTAANIOSOCIO'] == 'NO')
					{ echo ("<td class='textoRojo8Right'><strong>".$fila['ANOTADO_EN_CUOTAANIOSOCIO']."&nbsp;&nbsp;&nbsp;</strong></td>");
					}
					else// $fila['ANOTADO_EN_CUOTAANIOSOCIO'] == 'SI'
					{ echo ("<td class='textoAzul8Right'><strong>".$fila['ANOTADO_EN_CUOTAANIOSOCIO']."&nbsp;&nbsp;&nbsp;</strong></td>");
					}					
					
					echo ("<td class='textoAzul7R'>".$fila['FECHAANOTACIONPAGO']."&nbsp;&nbsp;"."</td>");				

					echo ("<td class='textoAzul7R'>".$fila['NUMRECIBOS']."&nbsp;&nbsp;&nbsp;</td>");
					echo ("<td class='textoAzul7R'>".$fila['NUMRECIBOSDEVUELTOS']."&nbsp;&nbsp;&nbsp;</td>");					
					echo ("<td class='textoAzul7R'>".$fila['IMPORTEREMESA']."&nbsp;&nbsp;&nbsp;</td>");
					echo ("<td class='textoAzul7R'>".$fila['IMPORTEGASTOSREMESA']."&nbsp;&nbsp;&nbsp;</td>");	
					echo ("<td class='textoAzul7R'>".$fila['IMPORTEGASTOSDEVOLUCION']."&nbsp;&nbsp;&nbsp;</td>");
     /* //decido nomostrar aquí
					if (isset($fila['DIRECTORIOARCHIVOREMESA']) && !empty($fila['DIRECTORIOARCHIVOREMESA'])) 
				 {	echo ("<td class='textoAzul7R'>".$fila['DIRECTORIOARCHIVOREMESA']."&nbsp;&nbsp;&nbsp;</td>");} 
			 	else {echo ("<td class='textoAzul7R'>"."&nbsp;&nbsp;&nbsp;</td>");};		
					*/
				 //echo ("<td class='textoAzul7R'>".$fila['CODUSER']."&nbsp;&nbsp;&nbsp;&nbsp;</td>");			
					//echo ("<td class='textoAzul7L'>".$fila['OBSERVACIONES']."</td>");//decido no utilizar
			 	?>
					
					<!--**** Inicio form mostrarOrdenesCobroUnaRemesaTes (solo necesita "NOMARCHIVOSEPAXML" ) ****--><!--  OJO no está implementada, pero sería casi igual a Eliminar-->					
				 <td>
					
						<form method="post" action="./index.php?controlador=cTesorero&accion=mostrarOrdenesCobroUnaRemesaTes">
									
							<input type="image" src="./vistas/images/lupa.gif" value="mostrarOrdenesCobroUnaRemesaTes"
														alt="Mostrar órdenes de cobro de la remesa " name="Mostrar"  
														title="Mostrar las órdenes de cobro incluidas en esta remesa" />	
														
							<input type="hidden"	name="datosFormOrdenCobroRemesa[NOMARCHIVOSEPAXML]"
														value='<?php echo $fila['NOMARCHIVOSEPAXML'];?>' />	<!-- para enviar mostrarOrdenesCobroUnaRemesaTes() para busqueda y para mostrar en el formulario-->
														
							<input type="hidden"	name="datosFormOrdenCobroRemesa[DIRECTORIOARCHIVOREMESA]"
														value='<?php if (isset($fila['DIRECTORIOARCHIVOREMESA'])) {echo $fila['DIRECTORIOARCHIVOREMESA'];} ?>' />	<!-- DIRECTORIOARCHIVOREMESA: Puede no existir, si son órdenes anteriores a 2021-09_01 envía a mostrarOrdenesCobroUnaRemesaTes() para busqueda y para mostrar en el formulario-->										

							<input type="hidden"	name="datosFormOrdenCobroRemesa[ANIOCUOTA]"
														value='<?php echo $fila['ANIOCUOTA'];?>' />	<!-- para enviar mostrarOrdenesCobroUnaRemesaTes() para mostrar en formulario-->						
														
							<input type="hidden"	name="datosFormOrdenCobroRemesa[FECHAORDENCOBRO]"
														value='<?php echo $fila['FECHAORDENCOBRO'];?>' />	<!-- para enviar mostrarOrdenesCobroUnaRemesaTes() para mostrar en formulario-->			

							<input type="hidden"	name="datosFormOrdenCobroRemesa[FECHAPAGO]"
														value='<?php echo $fila['FECHAPAGO'];?>' />	<!-- para enviar mostrarOrdenesCobroUnaRemesaTes() para mostrar en formulario-->									
							
							<input type="hidden"	name="datosFormOrdenCobroRemesa[FECHAANOTACIONPAGO]"
														value='<?php echo $fila['FECHAANOTACIONPAGO'];?>' />	<!-- para mostrarlo en el formulario-->											
																					
			  	</form> 
																
				 </td>
						<!--**** Fin form mostrarOrdenesCobroUnaRemesaTes ****************************************-->    

						
						<!--**** Inicio form eliminarOrdenesCobroUnaRemesaTes ************************************-->
					
					<td valign='center' width='10'>					
					
						<?php

						if ($fila['ANOTADO_EN_CUOTAANIOSOCIO'] == 'NO')
						{	
							?> 
							<form method="post" action="./index.php?controlador=cTesorero&accion=eliminarOrdenesCobroUnaRemesaTes">
							 
								<!-- Este hidden, en "serialize($fila)" se guarda la información contenida en el array "$fila" que después 
								en cTesorero.php:eliminarOrdenesCobroUnaRemesaTes() con la función unserialize() se recuperarán en forma de
								array original, para mostrar algunos de esos datos en el formulario "vActualizarCuotasCobradasEnRemesaTesInc.php". 
		      aunque se podría envíar el "NOMARCHIVOSEPAXML" y buscar por sólo "NOMARCHIVOSEPAXML" en una select en 
								cTesorero:eliminarOrdenesCobroUnaRemesaTes() como ya están aquí lo envío como hidden.
								-->							
								<?php
								//$fila['dirSEPAXML'] = '../../upload/_FILES/TESORERIA/SEPAXML_ISO20022';//<!-- el path del directorio donde se guardan los archivos SEPAXML_ISO20022 --> 				
								?>
								<input type='hidden' name="datosFormOrdenCobroRemesa" 	value='<?php echo serialize($fila);?>' /> 	
									
								<input type="image" src="./vistas/images/papelera.gif" value="eliminarOrdenesCobroUnaRemesaTes"
															alt="Eliminar órdenes de cobro aún no anotadas en tabla 'CUOTAANIOSOCIO' y archivo remesa" name="Eliminar"  
															title="Eliminar órdenes de cobro aún no anotadas en tabla 'CUOTAANIOSOCIO'" />	
								
							</form>
							
							<?php
						}
						else 
						{echo ("&nbsp;");
						}									
							?>	
	
				 </td> 
     	<!--**** Fin form eliminarOrdenesCobroUnaRemesaTes *****************************************-->						

      <!-- ********** Inicio form Descargar archivo SEPAXML_ISO20022 ***************************** -->		
					<td>
											
					<!--<td valign='center' width='10'>					-->
					
						<?php

						if ($fila['ANOTADO_EN_CUOTAANIOSOCIO'] == 'NO')
						{	/*
								<!-- Lo siguiente serviría para directorios que tuviesen permisos de acceso público, es decir que estén debajo del directorio /public_html/ 
								Por seguridad es aconsejable que estén por encima de ese directorio, y en ese caso sólo no se puede accesder con 	<a href =" path_to_file" download> clic </a> -->
								<a href = "<?php echo './_FILES/TESORERIA/SEPAXML_ISO20022'.'/'.$fila['NOMARCHIVOSEPAXML'];?>" download = "<?php echo $fila['NOMARCHIVOSEPAXML'];?>"title="Descargar archivo SEPA XML">
																			<img src="./vistas/images/descargar2.gif" alt ="Icono para descargar archivo"/>
								</a> 
        */								
							?> 
							<!--<form method="post" action="./index.php?controlador=cTesorero&accion=descargarAchivoOrdenesCobroRemesaTes">-->
							<form method="post" action="./index.php?controlador=cTesorero&accion=descargarAchivoOrdenesCobroSEPAXMLTes">

								<?php
								//$fila['dirSEPAXML'] = '../../upload/_FILES/TESORERIA/SEPAXML_ISO20022';//<!-- el path del directorio donde se guardan los archivos SEPAXML_ISO20022 --> 				
								?>							
        <input type='hidden' name="NOMARCHIVOSEPAXML" 	value='<?php echo $fila['NOMARCHIVOSEPAXML'];?>' />				
						 	<input type="hidden"	 name="DIRECTORIOARCHIVOREMESA"	value='<?php echo $fila['DIRECTORIOARCHIVOREMESA'];?>' />								
									
								<input type="image" src="./vistas/images/descargar2.gif" value="descargarAchivoOrdenesCobroSEPAXMLTes"
															alt="Descargar archivo remesa SEPA XML con las órdenes cobro de una remesa" name="Descargar"  
															title="Descargar archivo SEPA XML órdenes cobro de una remesa" />	
								
						 </form>
							
							<?php
						}
						else 
						{echo ("&nbsp;");													
						}									
							?>		
						
					</td>
      <!-- ******* Fin form Descargar archivo SEPAXML_ISO20022 ******************************** -->			

						<!--**** Inicio form actualizarCuotasCobradasEnRemesaTes *********************************-->
					<td>	
					
						<?php
						if ($fila['ANOTADO_EN_CUOTAANIOSOCIO'] == 'NO')
						{
							?>
							<form method="post" action="./index.php?controlador=cTesorero&accion=actualizarCuotasCobradasEnRemesaTes">
							
							 <!-- Este hidden, en "serialize($fila)" se guarda la información contenida en el array "$fila" que después 
								en cTesorero.php:actualizarCuotasCobradasEnRemesaTes() con la función unserialize() se recuperarán en forma de
								array original, para mostrar algunos de esos datos en el formulario "vActualizarCuotasCobradasEnRemesaTesInc.php". 
		      aunque se podría envíar el "NOMARCHIVOSEPAXML" y buscar por sólo "NOMARCHIVOSEPAXML" en una select en 
								cTesorero:actualizarCuotasCobradasEnRemesaTes() como ya están aquí lo envío como hidden.
								-->			
        <input type='hidden' name="datosFormOrdenCobroRemesa" 	value='<?php echo serialize($fila);?>' /> 		
								
								<input type="image" src="./vistas/images/pluma.gif" value="actualizarCuotasCobradasEnRemesaTes"
															alt="Actualizar pagos de las órdenes de cobro incluidas en esta remesa en 'CUOTAANIOSOCIO' " name="Actualizar"  
															title="Actualizar pagos de esta remesa en 'CUOTAANIOSOCIO'" />
													
							</form>					
							
							<?php
						}
						else
						{echo ("&nbsp;");							
						}		
						?>
					</td>
						<!--**** Fin form actualizarCuotasCobradasEnRemesaTes (solo necesita "NOMARCHIVOSEPAXML" ) ****-->						
							
				 </tr><!-- ************ Fin Fila ************************************************************ -->		
					
					<?php	
				}/*------------ Fin foreach() ---------------------------------------------------------------------*/
    ?>
				
		</table>	
		 <!-- ************************* Fin TABLA  *********************************************************** -->		
			<br />		

	</div><!-- <div align="left">  -->
</div><!-- <div id="registro">	 -->		
