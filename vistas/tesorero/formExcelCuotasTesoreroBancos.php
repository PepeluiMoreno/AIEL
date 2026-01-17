<?php
/*---------------------------------------------------------------------------------------------------- 
FICHERO: formExcelCuotasTesoreroBancos.php

DESCRIPCION: Formulario para selección de opciones para generar y exportar a un archivo Excel las órdenes 
de pago de las cuotas de los socios, (se utilizaba para las remesas de órdenes de cobro en B. Tríodos) 
y ahora también es útil para uso interno de tesorería, cuando se genera y descarga a continuación de generar 
el archivo XML SEPA para el B. Santander (con los mismos criterios de selección) y así el Excel puede servir
para contrastar los totales y otros datos y como un listado para anotar las 
devoluciones e incidencias de la remesa

Permitirá elegir:
- Excluir de la orden de cobro a los socios/as con alta después de una fecha	
- Cuenta bancaria España, o Cuenta bancaria países SEPA en Europa distintos de España
- Agrupaciones Territariales seleccionadas	 y agrupaciones y otros datos necearios
													
LLAMADA: vistas/tesorero/vCuerpoExcelCuotasTesoreroBancos.php												

OBSERVACIONES: Probado PHP 7.3.21			

NOTA: Incluye campos readonly entre otros:  
Fecha creación archivo = datosExcelCuotas[fechacobro] (aunque solo es informativo)         
-----------------------------------------------------------------------------------------------------*/
?>
<div id="registro">	
		 
	<!-- ************************* Inicio Mensaje en caso de error ************************* -->
	<span class="error"> 
			<?php 						
					if (isset($datosExcelCuotas['codError']) && $datosExcelCuotas['codError'] !=='00000')										
					{	echo "<br /><strong>ERROR: en color rojo se indican los errores que debes corregir</strong><br />";			 
					}
					else
					{ echo "<br /><strong>PRIVACIDAD DE DATOS:</strong>  Los datos descargados en el archivo EXCEL, se podrán utilizar como herramienta de trabajo o 
																											para generar manualmente las órdenes de pago. Es responsabilidad del gestor que no sean usados con otros fines.";					
					}					 
			?>
	</span>
	<!-- ************************* Fin Mensaje en caso de error ************************* -->			
 
	<br />
	
	<span class="textoAzu112Left2"><br />
		<strong>No modifica ninguna tabla "ORDENES_COBRO"," REMESAS_SEPAXML"</strong>, útil para uso de Tesorería y contrastar con archivos SEPA-XML de B.Santander, (Antes usado para B.TRÍODOS) 
	</span>
	
	<br />

	<span class="textoAzu112Left2"><br />
		<b>AVISO:</b> Para poder realizar la exportación a Excel, necesitas tener instalado el programa Microsoft Excel en tu ordenador. 
		Algunos navegadores pueden tener problemas para generar el archivo EXCEL correctamente con esta aplicación.

	</span>

		<br /><br /><br />

	<span class="textoAzu112Left2">
		<strong>INCLUIRÁ:</strong>
			<br /><br />- Socios/as con estado de cuota: "PENDIENTE-COBRO y ABONADA-PARTE" 
			<br /><br />- Socios/as están de alta
			<br /><br />- Ordenar cobro domiciliado a banco: <strong>SI</strong>
			<br />(En el caso de que tenga cuenta bancaria de país SEPA, Tesorería lo puede modificar para un socio/a en 
			      <i>-Cuotas socios/as->Acciones: Pago cuota, o en Actualiza cuota</i>, 
			       en el campo <i>*Incluir en la próxima lista de órdenes de cobro a los bancos</i>: si selecciona NO, 
										a ese socio/a no se le incluirá en la lista de órdenes de cobro al banco)
			<br /><br />- Agrupaciones territoriales elegidas (Sí marcadas) para cobrar las cuotas domiciliadas					
	
			<br /><br /><strong>NO INCLUIRÁ:</strong>			
			<br />- Socios/as con estado de cuota: "ABONADA, NOABONADA, NOABONADA-DEVUELTA, NOABONADA-ERROR-CUENTA, EXENTO (honorario/a)"
			<br />- Socios/as que estés dados de baja, u orden de cobro a banco: NO
			<br />- Además se pueden excluir de la lista a los/as socias/os que se dieron de alta después de una determinada fecha
			<br /><br />		
			
		<strong>NOTAS:</strong>
		<br />- El archivo incluirá los nombres de esos soci@s, sus cuentas IBAN, cuotas, emails, tél. y otros datos.
  <br /><br />- El archivo Excel cuando se genera y descarga a continuación  de generar el archivo "SEPA_ISO200022CORE_fecha.xml",	
		              de las remesas al B. Santander, es muy útil para contrastar datos y totales.																
	 <br /><br />- El archivo "Excel" descargado, también es útil como herramienta de trabajo para hacer anotaciones, por ejemplo anotar devoluciones.																															
	</span>				
 	<br /><br />				
	<div align="left"> 	

  <!-- ------------------------- Inicio readonly --------------------------------------------- -->				
						
		<form method="post" action="./index.php?controlador=cTesorero&amp;accion=excelCuotasTesoreroBancos">				
					<br />			
					
			<fieldset><legend><strong>Datos del cobro de Europa Laica para generar el archivo cuotas a Excel para B. Tríodos</strong></legend>
	 	  <p>					
					 <label>PRESENTADOR DEL ADEUDO DOMICILIADO</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosExcelCuotas[empresa_presentador]"
	            value="ASOCIACION EUROPA LAICA"
	            size="30"	           
	     />				
				 <label>CIF DEL PRESENTADOR</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosExcelCuotas[cif_presentador]"
	            value="G45490414"
	            size="10"	           
	     />
						<br />
				 <label>CONCEPTO</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosExcelCuotas[concepto]"
	            value="CUOTA ANUAL ASOCIACION EUROPA LAICA"
	            size="45"	           
	     />			
						<br />			
						<label>*IVA(% decimales separados por punto: "nn.nn")</label>
			    <input type="text" readonly
								      class="mostrar"		        
			           name="datosExcelCuotas[IVA]"
			           value='0.00'
			           size="12"
			           maxlength="30"
			     />			
						<span class="error">
							<?php
							  if (isset($datosAEB19Cuotas['IVA']['errorMensaje']))
						    {echo $datosAEB19Cuotas['IVA']['errorMensaje'];}
							?>
							</span>
							<br />	<br />		
							<label><strong>AÑO DE LA CUOTA A COBRAR</strong></label>									 	 
			    <input type="text" readonly
								      class="mostrar"			           
														name="datosExcelCuotas[anioCuotasElegido]"
			           value='<?php echo date("Y");?>'
			           size="4"
			           maxlength="4"
			    /> 
						
       <!-- Nota: aunque este campo no es necesario, además de su valor informativo, lo pongo para compatibilidad 
            con con función "validarCamposTesorero.php:validarFormOrdenCuotasBancos()", 
												que se comparte con "formXMLCuotas.php" -->
							<label><strong>Fecha creación archivo</strong></label>	

			    <input type="text" readonly
								      class="mostrar"			           
														name="datosExcelCuotas[fechacobro][anio]"
			           value='<?php echo date("Y");?>'
			           size="4"
			           maxlength="4"
			    />
       			    <input type="text" readonly
								      class="mostrar"			           
														name="datosExcelCuotas[fechacobro][mes]"
			           value='<?php echo date("m");?>'
			           size="2"
			           maxlength="2"
			    />
			    <input type="text" readonly
								      class="mostrar"			           
														name="datosExcelCuotas[fechacobro][dia]"
			           value='<?php echo date("d");?>'
			           size="2"
			           maxlength="2"
			    /> 							

	 	  </p>							
			</fieldset>
				<br /><br />
   <!-- ------------------------- Fin readonly ------------------------------------------------ -->						

		 <!-- Inicio Excluir de la lista los que se dieron de alta después de la fecha -------------- -->
			<fieldset><legend><strong>Excluir de la orden de cobro a los socios/as con alta después de la fecha</strong></legend>
	 	  <p>					
     <span class="textoAzu112Left2">			
							- Para segundas, terceras órdenes de cobro, se pueden excluir de la lista las/los socias/os 
					  que se dieron de alta cerca del final de año <strong> <?php echo date("Y");?></strong> 
							<br /><br />- Para incluir a todos/as hay que poner la fecha de hoy <strong> <?php echo date("d-m-Y");?></strong>
						</span>
						<br /> <br />
   
						<?php					
				   //lo referente a fecha podría ser un requiere_once parValorFechas
				 		$parValorDia["00"]="día"; 
						 for ($d=1;$d<=31;$d++) 
						 {if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
							 else {$valor="$d";}
							 $parValorDia[$valor]=$valor;
						 }
       require_once './modelos/libs/comboLista.php';							
						 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
							
							if (!isset($datosExcelCuotas['fechaAltaExentosPago']['dia']['valorCampo']) || empty($datosExcelCuotas['fechaAltaExentosPago']['dia']['valorCampo']))
       {		$datosExcelCuotas['fechaAltaExentosPago']['dia']['valorCampo'] = '00';		}//evita Notice							
						 echo comboLista($parValorDia, "datosExcelCuotas[fechaAltaExentosPago][dia]",$datosExcelCuotas['fechaAltaExentosPago']['dia']['valorCampo'],
															$parValorDia[$datosExcelCuotas['fechaAltaExentosPago']['dia']['valorCampo']],"00","día");
				   
							if (!isset($datosExcelCuotas['fechaAltaExentosPago']['mes']['valorCampo']) || empty($datosExcelCuotas['fechaAltaExentosPago']['mes']['valorCampo']))
       {		$datosExcelCuotas['fechaAltaExentosPago']['mes']['valorCampo'] = '00';		}//evita Notice
						 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
						 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
								
       if (!isset($datosExcelCuotas['fechaAltaExentosPago']['anio']['valorCampo']) || empty($datosExcelCuotas['fechaAltaExentosPago']['anio']['valorCampo']))
       {		$datosExcelCuotas['fechaAltaExentosPago']['anio']['valorCampo'] = '0000'; }//evita Notice								
						 echo comboLista($parValorMes,"datosExcelCuotas[fechaAltaExentosPago][mes]",$datosExcelCuotas['fechaAltaExentosPago']['mes']['valorCampo'],
						 $parValorMes[$datosExcelCuotas['fechaAltaExentosPago']['mes']['valorCampo']],"00","mes");		 
						
						 //$parValorAnio["0000"]="año::"; 
							//$anioAnterior=date("Y")-1;
							$anioActual=date("Y");	 
						 
						 $parValorAnio=array('0000'=>"año",$anioActual=>$anioActual/*,$anioAnterior=>$anioAnterior*/);
				
						 echo comboLista($parValorAnio,"datosExcelCuotas[fechaAltaExentosPago][anio]",
							                $datosExcelCuotas['fechaAltaExentosPago']['anio']['valorCampo'],
							                $parValorAnio[$datosExcelCuotas['fechaAltaExentosPago']['anio']['valorCampo']],"0000","año");
									
						 ?>	
				  	<span class="error"><strong>
							<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
							if (isset($datosExcelCuotas['fechaAltaExentosPago']['errorMensaje']))
							{echo $datosExcelCuotas['fechaAltaExentosPago']['errorMensaje'];}
							?></strong>
						</span>	
					<br />
						</p>
   </fieldset>	
	 	<!--Fin Excluir de la lista los que se dieron de alta después de la fecha ---->
					 <br />		
			<!-- ************ Inicio Elegir país de domiciliación de pago ****************** -->
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
			
							<!--								
					  <input type="radio"
	             name="datosExcelCuotas[paisCC]"
	             value='EX'
														<?php 
														//if (isset($datosExcelCuotas['paisCC']['valorCampo']) && $datosExcelCuotas['paisCC']['valorCampo'] =='EX')
							       //{ echo 'checked';
														//}
														?>															
	     
						<label>Cuenta bancaria de paises NO SEPA (aún no es posible domiciliarlos, solo por información)</label>
						 /> -->										
						</p>	
			</fieldset>	
			<!-- ************ Fin Elegir país de domiciliación de pago ****************** -->			
			<br />
			<!-- ************ Inicio Elegir agrupaciones para cobrar cuotas ************* -->	
			<fieldset><legend><strong>Elegir agrupaciones territoriales para incluir en las órdenes de cobro de cuotas domiciliadas</strong></legend>
	 	  <p>
					
						 <span class="textoAzu112Left2">
						  	- Para incluir una o más agrupaciones en el archivo EXCEL, marcar la casilla correspondiente.
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
									
							 		foreach ($parValorComboAgrupaSocio['lista'] as $codAgrupacion => $nomAgrupacion)                         
			       { 									 
										 ?>
												<br />
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
				 </p>
			</fieldset>		
				<!-- ************ Fin Elegir agrupaciones para cobrar cuotas **************** -->
				
				<br />												
    <input type="submit" name="SiExportarExcel" value="Exportar selección">
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				<input type="submit" name="NoExportarExcel" value="Cancelar"> 
				
  </form>
		 <!-- ************************* Fin selección  ************ -->	
   <!--				</div>	-->  
	</div>
</div>		
