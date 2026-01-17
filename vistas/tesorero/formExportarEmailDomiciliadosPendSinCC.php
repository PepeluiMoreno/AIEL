<?php
/*------------------------------------------------------------------------------------------------
FICHERO: formExportarEmailDomiciliadosPendSinCC.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario de selección de campos para buscar Emails y otros datos de todos 
las cuotas de los socios y exportar los emails en forma de lista separados por (;)
a un fichero ".txt" para copiar y pegar en el correo de NODO50 (tesoreria@europalica.org), 
y enviar un email a los socios de la lista, con texto libre para avisar a los socios 
que aún "no han abonado la cuota" que están de alta en el momento actual y según
la siguiente selección:

- No tiene cuenta bancaria domiciliada, - Tiene cuenta bancaria de países NO SEPA (o no es IBAN, ya 
  no se permite CUENTAS NO IBAN, este caso devolverá 0 socios), - Cuenta bancaria de países SEPA (distintos
 	de España), junto con "Ordenar cobro banco = NO" (por falta de BIC necesario para otros países SEPA, 
		por eso se envía este email de aviso no pagado)
- FechaAltaExentosPago		
- Agrupaciones seleccionadas

RECIBE: 
- $camposFormDatosElegidos desde controlador:(año cuota, fecha exención pago, cuenta banco .., agrupaciones)

LLAMADA: vistas/tesorero/vCuerpoExportarEmailDomiciliadosPendSinCC.php, 
         que viene de cTesorero.php: exportarEmailDomiciliadosPendSinCC()
													
OBSERVACIONES:
-------------------------------------------------------------------------------------------------*/
?>
<div id="registro">	
		
			<!-- ************************* Inicio Mensaje en caso de error ************************* -->
			<span class="error">
				<?php 
				 if (isset($camposFormDatosElegidos) && !empty($camposFormDatosElegidos))
				 {
				  if (isset($camposFormDatosElegidos['codError']) && $camposFormDatosElegidos['codError'] !== '00000')
			   {echo "<br /><br /><strong>ERROR: en color rojo se indican los errores que debes corregir, revisa de nuevo las agrupaciones elegidas</strong>";
					 }		
				 }
					else
     {echo "<br /><br />    <strong>AVISO: ANTES DE PULSAR EL BOTÓN - Enviar emails - REVISA QUE ESTAN MARCADAS CORRECTAMENTE 
				  LAS CASILLAS DE LAS AGRUPACIONES CORRESPONDIENTES</strong>" ;					
				 }					
				?>
			</span>
	  <!-- ************************* Fin Mensaje en caso de error ************************* -->		
			
 <br /><br /><br />

  <span class="textoAzu112Left2">			
		   Se genera y descarga un archivo de texto (.txt) con los emails de los socios separados por (;) listo para copiar y pegar en CCO
					(BCC), en una cuenta de EL (tesoreria@europalaica.org), para enviar emails para avisar que aún no han pagado su cuota.
			
 		 <br /><br /><strong>AVISO:</strong>La pantalla se quedará fija despues de hacer clic en "Exportar selección", 
				aunque si no hay aviso de error el archivo -txt- estará descargado.
    <br /><br /> 
				<strong>INCLUIRÁ:</strong>
				<br />- Socios/as con estado de cuota: "PENDIENTE-COBRO, ABONADA-PARTE, NOABONADA-DEVUELTA, NOABONADA-ERROR-CUENTA"
				<br /><br />- Socios/as están de alta
				<br /><br />- Ordenar cobro domiciliado a banco: <strong>NO</strong>
				<br />(En el caso de que tenga cuenta bancaria de país SEPA, Tesorería lo puede modificar para un socio/a y poner (SI/NO), en
				      <i>-Cuotas socios/as->Acciones: Pago cuota, o en Actualiza cuota</i>, en el campo <i>*Incluir en la próxima lista de órdenes de cobro a los bancos</i>
				<br /><br />- Socios/as con campo EMAILERROR = "NO"
				<br /><br /> 
				<strong>NO INCLUIRÁ: </strong>
				<br />- Socios/as con estado de cuota: "ABONADA, EXENTO (honorario/a)"		
    <br />- Socios/as que estén dados de baja, u orden de cobro a banco: SI
				<br />- Socios/as con valor campo EMAILERROR distinto de "NO"	
		 	<br />- Además se pueden excluir de la lista a los/as socias/os que se dieron de alta después de una determinada fecha 
	  </span>		
   <br /><br /><br />		
			<!-- ********************* Inicio selección  *************** -->						
					
		 <form method="post" action="./index.php?controlador=cTesorero&amp;accion=exportarEmailDomiciliadosPendSinCC"> 
					 
				<fieldset><legend><strong>Selecciona las opciones para exportar emails 
				                   las cuotas a un archivo de texto ".txt" separados por (;)  
														        </strong>
														</legend>
	 	  <p>

						<label><strong>AÑO DE LA CUOTA A COBRAR</strong></label>		
			    <input type="text" readonly
								      class="mostrar"			          
														name="camposFormDatosElegidos[anioCuotasElegido]"
			           value='<?php echo date("Y");?>'
			           size="4"
			           maxlength="4"
			    /> 
						<br /><br />		
						
		   <!-- Inicio Excluir de la lista los que se dieron de alta después de la fecha --->
				  <span class="textoAzu112Left2">			
							- Para segundas, terceras órdenes de cobro, se pueden excluir de la lista las/los socias/os 
					  que se dieron de alta cerca del final de año <strong> <?php echo date("Y");?></strong> 
							<br /><br />- Para incluir a todos/as hay que poner la fecha de hoy <strong> <?php echo date("d-m-Y");?></strong>
						</span>
						
						<br /> <br />
						<label>*Excluir de la lista los que se dieron de alta después de la fecha
						</label> 
						
						<?php		
					  	require_once './modelos/libs/comboLista.php';

				   //lo referente a fecha podría ser un requiere_once parValorFechas
				 		$parValorDia["00"]="día"; 
						 for ($d=1;$d<=31;$d++) 
						 {if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
							 else {$valor="$d";}
							 $parValorDia[$valor]=$valor;
						 }
							
							if (!isset($camposFormDatosElegidos['fechaAltaExentosPago']['dia']['valorCampo']) || empty($camposFormDatosElegidos['fechaAltaExentosPago']['dia']['valorCampo']))
       {		$camposFormDatosElegidos['fechaAltaExentosPago']['dia']['valorCampo'] = '00';		}//evita Notice				
													
						 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
						 echo comboLista($parValorDia, "camposFormDatosElegidos[fechaAltaExentosPago][dia]",$camposFormDatosElegidos['fechaAltaExentosPago']['dia']['valorCampo'],
															$parValorDia[$camposFormDatosElegidos['fechaAltaExentosPago']['dia']['valorCampo']],"00","día");
				 
						 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
						 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
							
							if (!isset($camposFormDatosElegidos['fechaAltaExentosPago']['mes']['valorCampo']) || empty($camposFormDatosElegidos['fechaAltaExentosPago']['mes']['valorCampo']))
       {		$camposFormDatosElegidos['fechaAltaExentosPago']['mes']['valorCampo'] = '00';		}//evita Notice												
								
						 echo comboLista($parValorMes,"camposFormDatosElegidos[fechaAltaExentosPago][mes]",$camposFormDatosElegidos['fechaAltaExentosPago']['mes']['valorCampo'],
						 $parValorMes[$camposFormDatosElegidos['fechaAltaExentosPago']['mes']['valorCampo']],"00","mes");		 
						
						 //$parValorAnio["0000"]="año::"; 
							//$anioAnterior=date("Y")-1;
							$anioActual=date("Y");	 
						 
						 $parValorAnio=array('0000'=>"año",$anioActual=>$anioActual/*,$anioAnterior=>$anioAnterior*/);
				
	      if (!isset($camposFormDatosElegidos['fechaAltaExentosPago']['anio']['valorCampo']) || empty($camposFormDatosElegidos['fechaAltaExentosPago']['anio']['valorCampo']))
       {		$camposFormDatosElegidos['fechaAltaExentosPago']['anio']['valorCampo'] = '0000';		}//evita Notice		
						
						 echo comboLista($parValorAnio,"camposFormDatosElegidos[fechaAltaExentosPago][anio]",
							                $camposFormDatosElegidos['fechaAltaExentosPago']['anio']['valorCampo'],
							                $parValorAnio[$camposFormDatosElegidos['fechaAltaExentosPago']['anio']['valorCampo']],"0000","año");
										
						 ?>	
				  	<span class="error"><strong>
							<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
					 		if (isset($camposFormDatosElegidos['fechaAltaExentosPago']['errorMensaje']))
						 	{echo $camposFormDatosElegidos['fechaAltaExentosPago']['errorMensaje'];}
							?></strong>
						</span>
						</p>
				</fieldset>
	  	<!--Fin Excluir de la lista los que se dieron de alta después de la fecha ---->
    <br /><br />	

		  <!--Inicio Elegir opción de tipo cuenta banco ---->
				<fieldset><legend><strong>Elegir opción tipo cuenta banco</strong></legend>
	 	  <p>			   
	      <input type="radio"
	             name="camposFormDatosElegidos[paisCC]"
	             value='NO' 
															
														<?php 
														if (!isset($camposFormDatosElegidos['paisCC']['valorCampo'])|| $camposFormDatosElegidos['paisCC']['valorCampo']=='NO')
							       { echo 'checked';
														}
														?>																		
	      />
					<label>No tiene cuenta bancaria domiciliada</label>	
       <br />
					  <input type="radio"
	             name="camposFormDatosElegidos[paisCC]"
														value='SEPA' 
														<?php 
														if (isset($camposFormDatosElegidos['paisCC']['valorCampo'])&& $camposFormDatosElegidos['paisCC']['valorCampo']=='SEPA')
							       { echo 'checked';
														}
													
														?>																	
	      />	
					<label>Cuenta bancaria en países SEPA (distintos de España) y <strong>" Ordenar cobro banco = NO "</strong> (POR AHORA NO SE PUEDE GENERAR EL ARCHIVO "SEPA_ISO200022CORE_fecha.xml")</label>
		   <br /> <br />								
					 
				  <span class="textoAzu112Left2">NOTA: Con "Cuenta bancaria países SEPA (distintos de España)" no se puede domiciliar el pago automático 
						en el B. Santander (aunque se podría hacer una remesa manualmente si se tienen los BICs de esas cuentas SEPA)
						</span>										
							
					</p>	
				</fieldset>
			   <br /><br />	
    <!--Fin Elegir opción de tipo cuenta banco ---->

    <!--*****Inicio Elegir agrupaciones territoriales ---->		
				<fieldset>
				<legend><strong>Elegir agrupaciones territoriales para incluir los emails de socios/as en el archivo a exportar</strong>
				</legend>
	 	  <p>					 

						 <span class="textoAzu112Left2">       		
						  	- Para incluir una o más agrupaciones en el archivo XML, marcar la casilla correspondiente.
							</span>	
							<br />
				  	<span class="error"><strong>							
							<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
							if (isset($camposFormDatosElegidos['agrupaciones']['errorMensaje']))
							{echo $camposFormDatosElegidos['agrupaciones']['errorMensaje'];}
							?></strong>
						</span>
						
						<span class="textoAzu112Left2">										
						 	<?php 
							   unset($parValorComboAgrupaSocio['lista']["%"]);//Para que no salgan la opcion de todas en el formulario
										
										unset($parValorComboAgrupaSocio['lista']["00000000"]);//elimino para que no salga en medio de la lista
										$parValorComboAgrupaSocio['lista']["00000000"] = 'Europa Laica Estatal e Internacional';//añado para que salga al final
										
							 		foreach ($parValorComboAgrupaSocio['lista'] as $codAgrupacion => $nomAgrupacion)                         
			       { 							  
							 ?>
												<br />
									   <input type="checkbox" 
	                  name="camposFormDatosElegidos[agrupaciones][<?php echo $codAgrupacion ?>]"
						             value='<?php echo $codAgrupacion; ?>'
																			
																			<?php 
																			if (isset($camposFormDatosElegidos['agrupaciones']['valorCampo'][$codAgrupacion]))
													       { echo 'checked';
																				}
																			?>
	           />												
							<?php
										echo "<span class='comentario12'>$nomAgrupacion</span>";													
									}									
							?>       
							</span>
							<br />					
				 </p>
				</fieldset>	
    <!-- ******Fin Elegir agrupaciones territoriales ---->					

				<!-- ************************* Inicio Mensaje en caso de error ************************* -->
				<span class="error">
					<?php 
							if (isset($camposFormDatosElegidos['codError']) && $camposFormDatosElegidos['codError']!=='00000')
							{echo "<br /><br /><strong>ERROR: en color rojo se indican los errores que debes corregir, revisa de nuevo las agrupaciones elegidas</strong>";
							}						
					?>
				</span>
				<!-- ************************* Fin Mensaje en caso de error ************************* -->	
					<br /><br />													
    <input type="submit" name="SiExportar" value="Exportar selección">
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				<input type="submit" name="NoExportar" value="Cancelar"> 												
   </form>
		 <!-- ************************* Fin selección  ************ -->	

</div>		
