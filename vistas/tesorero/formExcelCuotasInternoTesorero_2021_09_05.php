<?php
/*------------------------------------------------------------------------------------------------ 
FICHERO: formExcelCuotasInternoTesorero.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Formulario de selección de campos para buscar las cuotas y otros datos 
             para descargará en un archivo Excel para uso interno
													
El formulario además de las agrupaciones permite elegir:

- Un determinado año (dentro de los cinco últimos)
- Estado cuota: ABONADA,NOABONADA,EXENTO,PENDIENTE-COBRO,NOABONADA-ERROR-CUENTA,ABONADA-PARTE 												
- Estado de los socios/as (alta, baja, todos) 
- ORDENARCOBROBANCO = SI,NO,TODOS 
- Cuenta bancaria domiciliada:
  .Cuenta bancaria de España
  .Cuenta bancaria de países SEPA en Europa distintos de España
  .No tiene cuenta bancaria domiciliada
  .Todas las opciones
- Agrupaciones Territoriales											
									
LLAMADA: vCuerpoExcelCuotasInternoTesorero.php.php, previo cTesorero.php:excelCuotasInternoTesorero()

OBSERVACIONES:  
La pantalla se quedará fija despues de hacer clic en "Exportar selección", 
aunque si no hay aviso de error el archivo  estará descargado.     
AVISO: Al abrir el archivo dice: La extensión y el formato del archivo no coinciden. 
Puede que el archivo esté dañado o no sea seguro. No lo abra a menos que confíe en su origen. 
¿Desea abrirlo de todos modos? 
-------------------------------------------------------------------------------------------------*/
?>
<div id="registro">	

	<!-- ************************* Inicio Mensaje en caso de error ************************* -->
	<span class="error"> 
			<?php 						
					if (isset($datosExcelCuotas['codError']) && $datosExcelCuotas['codError'] !=='00000')										
					{	echo "<br /><strong>ERROR: en color rojo se indican los errores que debes corregir</strong><br />";			 
					}
					else
					{ echo "<br /><strong>PRIVACIDAD DE DATOS:</strong>  Los datos descargados en un archivo EXCEL, se podrán utilizar como herramienta interna de trabajo.
				                Es responsabildad del gestor que no sean usados con otros fines.";					
					}					 
			?>
	</span>
	<!-- ************************* Fin Mensaje en caso de error ************************* -->					
		<br /><br />	

		<span class="textoAzu112Left2"><br />
					<strong>AVISO:</strong> Para poder realizar la exportación a Excel, necesitas tener instalado 
					el programa Microsoft Excel en tu ordenador. Algunos navegadores pueden tener problemas para generar el archivo EXCEL
					correctamente con esta aplicación.
					<br /><br /><br />

					<strong>Selecciona las opciones para exportar los datos de las cuotas al archivo Excel</strong>			
		</span>
		<br />	<br />
		
		<div align="left"> 		
			<!-- ********************* Inicio selección  *************** -->	
						
		 <form method="post" action="./index.php?controlador=cTesorero&amp;accion=excelCuotasInternoTesorero">			
				<fieldset>
	 	  <p>
						<label><b>Elige año cuota</b></label>					
					 <?php 
					  require_once './modelos/libs/comboLista.php';
			
			    for ($a=date("Y")-5; $a<=date("Y"); $a++){$parValorAnio[$a]=$a;}
					 	//$parValorAnio["%"]="Todos"; 
						
				   if (!isset($datosExcelCuotas['anioCuotasElegido']['valorCampo']) || empty($datosExcelCuotas['anioCuotasElegido']['valorCampo']))
       {	$datosExcelCuotas['anioCuotasElegido']['valorCampo'] = date('Y');	}//evita Notice								
					 	/*																
			    echo comboLista($parValorAnio,"datosExcelCuotas[anioCuotasElegido]",$datosExcelCuotas['anioCuotasElegido'],
				                  $parValorAnio[$datosExcelCuotas['anioCuotasElegido']],"%","Todos");
																						*/
							echo comboLista($parValorAnio,"datosExcelCuotas[anioCuotasElegido]",$datosExcelCuotas['anioCuotasElegido']['valorCampo'],
				                  $parValorAnio[$datosExcelCuotas['anioCuotasElegido']['valorCampo']],date('Y'),date('Y'));		
			   ?>
						</p>		
				</fieldset>	
				<br /><br /
				<!--  -----------------------------------------------  -->	
		 	<fieldset><legend><strong>Estado cuota</strong></legend>
	 	  <p>
						 <span class="textoAzu112Left2">
						  - Selecciona los estados de pago de las cuotas que quieras incluir en el archivo Excel, para ello marca las casillas correspondientes
       </span>		 								
						  <br />
						 		
				  	 <span class="error"><strong>
									<?php 
									if (isset($datosExcelCuotas['estadosCuotas']['errorMensaje']))
									{echo $datosExcelCuotas['estadosCuotas']['errorMensaje'];}
									?>
									</strong>
	       </span>										

						 	<?php //se tendrá que recibir o poner aquí todos los estados posibles							 
												
							   unset($parValorEstadosCuota['lista']["%"]);//Para que no saga todas en la lista
										//$parValorEstadosCuota['lista']["%"]='TODOS';//Para que no saga todas en la lista
										foreach ($parValorEstadosCuota['lista'] as $codEstadoCuota => $nomEstadoCuota)
			       {
							 ?> 
								    <br />
												<!--
									   <input type="checkbox" 
	                  name="datosExcelCuotas[estadosCuotas][<?php echo $codEstadoCuota ?>]"
						             value=<?php //echo $codEstadoCuota; ?>
												       checked	
	           />-->
									   <input type="checkbox" 
	                  name="datosExcelCuotas[estadosCuotas][<?php echo $codEstadoCuota ?>]"
						             value=<?php echo $codEstadoCuota; ?>
																		<?php 
																		if (isset($datosExcelCuotas['estadosCuotas']['valorCampo'][$codEstadoCuota]) )
																		{ echo 'checked';
																		}													
																		?>
	           />
					
							<?php		
            echo "<span class='comentario12'>$nomEstadoCuota</span>";								
									}
							?>       

				 </p>
				</fieldset>	
				<br />
				<!--  -----------------------------------------------  -->		
    <fieldset><legend><strong>Elegir estado de los socios/as (alta, baja, todos)</strong></legend>
	 	  <p>	
	    <input type="radio"
	           name="datosExcelCuotas[ESTADO]"
	           value='alta' 
												<?php 
												if (!isset($datosExcelCuotas['ESTADO']['valorCampo']) || $datosExcelCuotas['ESTADO']['valorCampo'] =='alta')
												{ echo 'checked';
												}
												?>			
	    /><label>Alta</label>
	    <br />
					<input type="radio"
	           name="datosExcelCuotas[ESTADO]"
	           value='baja' 
												<?php 
												if (!isset($datosExcelCuotas['ESTADO']['valorCampo']) || $datosExcelCuotas['ESTADO']['valorCampo'] =='baja')
												{ echo 'checked';
												}
												?>													
	    /><label>Baja</label>	
					<br />				
	    <input type="radio"
	           name="datosExcelCuotas[ESTADO]"
	           value='%'		
												<?php 
												if (!isset($datosExcelCuotas['ESTADO']['valorCampo']) || $datosExcelCuotas['ESTADO']['valorCampo'] =='%')
												{ echo 'checked';
												}
												?>														
	    /><label>Todos</label>
	 	  </p>					
    </fieldset>			
    <br />
		  <!--  -----------------------------------------------  -->		
		  <!--  ----------- Elegir campo órdenes de pagos a los bancos-------------  -->		
						
    <fieldset><legend><strong>Elegir campo órdenes de pagos a los bancos</strong></legend>
	 	  <p>						
	      <input type="radio"
	             name="datosExcelCuotas[ORDENARCOBROBANCO]"
	             value='SI' 
														<?php 
														if (!isset($datosExcelCuotas['ORDENARCOBROBANCO']['valorCampo']) || $datosExcelCuotas['ORDENARCOBROBANCO']['valorCampo'] =='SI')
							       { echo 'checked';
														}
														?>																	
						        							
	      />
						<label>Órdenes de pagos a los bancos: <strong>SI</strong></label>

	      <br />
					  <input type="radio"
	             name="datosExcelCuotas[ORDENARCOBROBANCO]"
	             value='NO'
														<?php 
														if (!isset($datosExcelCuotas['ORDENARCOBROBANCO']['valorCampo']) || $datosExcelCuotas['ORDENARCOBROBANCO']['valorCampo'] =='NO')
							       { echo 'checked';
														}
														?>															
														
	      />							
					 <label>Órdenes de pagos a los bancos: <strong>NO</strong></label>	
	      <br />
					  <input type="radio"
	             name="datosExcelCuotas[ORDENARCOBROBANCO]"
	             value='TODOS'
														<?php 
														if (!isset($datosExcelCuotas['ORDENARCOBROBANCO']['valorCampo']) || $datosExcelCuotas['ORDENARCOBROBANCO']['valorCampo'] =='TODOS')
							       { echo 'checked';
														}
														?>	
	      />							
					 <label>Todos los valores de órdenes de pagos a los bancos: <strong>SI</strong> y <strong>NO</strong></label>							
					 <br />					 
				  <label>NOTA: Tesorería en "- Cuotas socios/as->Pago cuota, o Actualiza cuota ", 
						      puede elegir para cada socio/a: *Incluir en lista de órdenes de pagos a los bancos: SI/NO
	     </label>								
					
					</p>
				</fieldset>	
    <!--  -----------------------------------------------  -->		

    <!--  -------- Elegir país de domiciliación de pago de cuota----------------  -->		

	 		<br />
		 	<fieldset><legend><strong>Elegir país de la cuenta bancaria domiciliada de los socios/as para cobro de cuota</strong></legend>
	 	  <p>
	      <input type="radio"
	             name="datosExcelCuotas[paisCC]"
	             value='ES' 
														<?php 
														if (!isset($datosExcelCuotas['paisCC']['valorCampo']) || $datosExcelCuotas['paisCC']['valorCampo'] =='ES')
							       { echo 'checked';
														}
														?>									
	      />	
						<label>Cuenta bancaria de España</label>		
	      <br />
							
					  <input type="radio"
	             name="datosExcelCuotas[paisCC]"
	             value='SEPA' 
														<?php 
														if (isset($datosExcelCuotas['paisCC']['valorCampo']) && $datosExcelCuotas['paisCC']['valorCampo'] =='SEPA')
							       { echo 'checked';
														}
														?>																
	      />
						<label>Cuenta bancaria de países SEPA en Europa distintos de España</label>
		      <br />
								
					  <input type="radio"
	             name="datosExcelCuotas[paisCC]"
	             value='EX' 
														<?php 
														if (isset($datosExcelCuotas['paisCC']['valorCampo']) && $datosExcelCuotas['paisCC']['valorCampo'] =='EX')
							       { echo 'checked';
														}
														?>																
	      />
							<!--
						<label>Cuenta bancaria de paises NO SEPA (aún no es posible domiciliarlos, solo por información)</label>
		      <br />
						  <input type="radio"
	             name="datosExcelCuotas[paisCC]"
	             value='NO' 
														<?php 
														/*if (isset($datosExcelCuotas['paisCC']['valorCampo']) && $datosExcelCuotas['paisCC']['valorCampo'] =='TODOS')
							       { echo 'checked';
														}*/
														?>															
														
	      />		-->					
					 <label>No tiene cuenta bancaria domiciliada</label>		
	      <br />
					  <input type="radio"
	             name="datosExcelCuotas[paisCC]"
	             value='TODOS'
														<?php 
														if (isset($datosExcelCuotas['paisCC']['valorCampo']) && $datosExcelCuotas['paisCC']['valorCampo'] =='TODOS')
							       { echo 'checked';
														}
														?>			
														
	      />							
					 <label>Todas las opciones: (Cuenta en España, en países SEPA distintos de España y sin cuenta domiciliada)</label>							
					 <br />											
					</p>	
			 </fieldset>	
		 	<br />			
		  <!--  -----------------------------------------------  -->		
			
			 <fieldset><legend><strong>Elegir agrupaciones territoriales para cobrar cuotas</strong></legend>
	 	  <p>
						
						 <span class="textoAzu112Left2">
								Por defecto están seleccionadas todas las agrupaciones existentes para incluir en el archivo EXCEL, tengan o no tengan socios/as.
        <br /><br />  
        - Si se quiere eliminar agrupaciones del archivo EXCEL, desmarca la casilla correspondiente.
						 	<br />
 	
				  	 <span class="error"><strong>
									<?php 
									if (isset($datosExcelCuotas['agrupaciones']['errorMensaje']))
									{echo $datosExcelCuotas['agrupaciones']['errorMensaje'];}
									?></strong>
	       </span>								
						 	 <?php 
								
							   unset($parValorComboAgrupaSocio['lista']["%"]);//Para que no salgan la opcion de todas en el formulario
										unset($parValorComboAgrupaSocio['lista']["00000000"]);//elimino para que no salga en medio de la lista
										$parValorComboAgrupaSocio['lista']["00000000"] = 'Europa Laica Estatal e Internacional';//añado para que salga al final		
										//$parValorComboAgrupaSocio['lista']["%"] = 'TODAS';
										
							 		foreach ($parValorComboAgrupaSocio['lista'] as $codAgrupacion => $nomAgrupacion)                         
			       { 							  
							    ?>
								    <br />
	           <!--	<input type="checkbox" 
	                  name="datosExcelCuotas[agrupaciones][<?php //echo $codAgrupacion ?>]"
						             value=<?php echo $codAgrupacion; ?>
												       checked	
	           />-->
												<input type="checkbox" 
																			name="datosExcelCuotas[agrupaciones][<?php echo $codAgrupacion ?>]"
																			value=<?php echo $codAgrupacion; ?>
																			<?php 
																		 if (isset($datosExcelCuotas['agrupaciones']['valorCampo'][$codAgrupacion]))
																			{ echo 'checked';
																			}
																			?>
												/>									
					
								  	<?php	
												echo "<span class='comentario12'>$nomAgrupacion</span>";								
										}
							    ?>       
							</span>
							<br /><br />
				 </p>
				</fieldset>			
					<br />					
    <input type="submit" name="SiExportarExcel" value="Exportar selección">
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				<input type="submit" name="NoExportarExcel" value="Cancelar"> 												
   </form>
		 <!-- ************************* Fin selección  ************ -->	

	 </div><!-- 		<div align="left"> 	-->
</div><!-- <div id="registro">	 -->		
