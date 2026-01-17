<?php
/*---------------------------------------------------------------------------------------------------- 
FICHERO: formXMLCuotas.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para introducir los datos necesarios para generar un archivo "SEPA_ISO20022CORE_fecha_orden_cobro.xml" 
para una remesa con las órdenes de cobro de cuotas domiciliadas para después descargarlo y subirlo a la web
del B. Santander.

A la vez se anotarán esas órdenes de pagos en tabla "ORDENES_COBRO", y en "REMESAS_SEPAXML" que posteriormente 
servirán para actualizar el campo		ESTADOCUOTA =ABONADA, en la tabla "CUOTAANIOASOCIO" una vez que el banco 
haya cobrado esa remesa. 

El formulario permite elegir: 
- Fecha cobro, Fecha excluir de orden de cobro a altas posteriores, 
- Cuenta bancaria España, o Cuenta bancaria países SEPA en Europa distintos de España (en este caso por la necesidad de
  BICs, actualmente no puede generar el archivo pero muestra un listado para incluilos en remesas manualmente)
- Agrupaciones Territariales seleccionadas				
- También incluye una grupo de datos fijos, relacionados con la cuenta del B. Santander, necesarios para generar 
  el archivo (están en el formulario como campos "readonly") 													
													
LLAMADA: vistas/tesorero/vCuerpoXMLCuotas.php							
												
OBSERVACIONES: 
2020-11-17: Cambios para que las agrupaciones salgan desmarcadas por defecto, se hace en 
cTesorero.php:XMLCuotasTesoreroSantander, al comentar la línea:
//$arrCamposFormRemesaBancoInicial['agrupaciones']['valorCampo'] = $parValorComboAgrupa['lista']
-----------------------------------------------------------------------------------------------------*/
?>
	
<div id="registro">	
   <br />
			<!-- ************************* Inicio Mensaje en caso de error ************************* -->
			<span class="error">
				<?php 
						if (isset($datosFormRemesaBanco['codError']) && $datosFormRemesaBanco['codError'] !== '00000')	
			   {echo "<strong>ERROR: en color rojo se indican los errores que debes corregir, además debes elegir de nuevo las agrupaciones</strong>";
					 }
						else
      {echo "<strong>PRIVACIDAD DE DATOS:</strong>  Los datos descargados en un archivo XML, se podrán utilizar para generar las órdenes de 
			    	pago. Es responsabildad del gestor que no sean usados con otros fines.";					
					 }		
					
				?>
			</span>
	  <!-- ************************* Fin Mensaje en caso de error ************************* -->		
				
  <br />

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
   <br /><br />- Antes y después de este proceso es muy aconsejable hacer una copia de seguridad de toda la base de datos 
			             (lo debiera hacer la persona con permisos sobre la BBDD)	
															
			<br /><br />- Para ver el archivo "SEPA_ISO200022CORE_fecha.xml", y comprobar los datos socios/as son correctos, 
			              se puede abrir con un navegador (estructura en árbol) o con Excel (en filas), 
																	mejor comprobarlo en una copia del archivo para evitar alteraciones en el original.																	
	  </span>			
			
		<div align="left">
		
   <br /> 
			<!-- ********************* Inicio selección  ********************************************* -->						
					
		 <form method="post" action="./index.php?controlador=cTesorero&amp;accion=XMLCuotasTesoreroSantander"> 
									
				<br />					
 		 <!-- Inicio datos fijos necesarios para generar el archivo SEPA-XML o NORMA AEB19, se envian desde este formulario al contralor 
         como son fijos, los pongo como "readonly", para dejarlos visibles y si cambiasen sería fácil modificarlo aquí		--->		
									
				<fieldset><legend><strong>Datos de la cuenta de Europa Laica para generar el archivo XML (Necesarios para órdenes cobro SEPA-CORE/COR1 del B. Santander)</strong></legend>
	 	 <p>
				 <label>PRESENTADOR DEL ADEUDO DOMICILIADO</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosFormRemesaBanco[empresa_presentador]"
	            value="ASOCIACION EUROPA LAICA"
	            size="30"	           
	     />				
				 <label>CIF DEL PRESENTADOR</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosFormRemesaBanco[cif_presentador]"
	            value="G45490414"
	            size="10"	           
	     />
						<br />
				 <label>ORDENANTE DEL ADEUDO DOMICILIADO (Acreedor)</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosFormRemesaBanco[empresa_ordenante]"
	            value="ASOCIACION EUROPA LAICA ESTATAL"
	            size="45"	           
	     />

				 <label>CIF DEL ORDENANTE (Acreedor)</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosFormRemesaBanco[cif_ordenante]"
	            value="G45490414"
	            size="10"	           
	     />			
						<br />	
						
			  <label>IDENTIFICADOR DE LA EMPRESA (para el B. Santander)</label> 	 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[idEmpresa]"
			           value='ES89001G45490414'
			           size="26"
			           maxlength="35"
			    />
					<!--	
			  <label>Núm. entidad en España</label> 	 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[ctaBanco][CODENTIDAD]"
			           value='0049'
			           size="4"
			           maxlength="4"
			    /> 			
			  <label>Núm. sucursal</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[ctaBanco][CODSUCURSAL]"
			           value='0001'
			           size="4"
			           maxlength="4"
			    /> 			
			  <label>DC</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[ctaBanco][DC]"
			           value='52'
			           size="2"
			           maxlength="2"
			    /> 			
			  <label>Núm. cuenta</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[ctaBanco][NUMCUENTA]"
			           value='2411813269'
			           size="11"
			           maxlength="11"
			    /> 					
		    <br /> -->
					<label>IBAN</label> 	 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[ctaBanco][IBAN]"
			           value='ES4200490001522411813269'
			           size="26"
			           maxlength="26"
			    /> 								
			  <label>BIC B. Santander</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[BIC]"
			           value='BSCHESMMXXX'
			           size="18"
			           maxlength="18"
			    /> 					
						<br />							
			  <label>ESTADO</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[estado]"
			           value='ES'
			           size="2"
			           maxlength="2"
			    /> 
			  <label>MONEDA</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[moneda]"
			           value='EUR'
			           size="4"
			           maxlength="8"
			    />
		
						<label>*IVA(% decimales separados por punto: "nn.nn")</label>
			    <input type="text" readonly
								      class="mostrar"		        
			           name="datosFormRemesaBanco[IVA]"
			           value='0.00'
			           size="12"
			           maxlength="30"
			     />	
							<span class="error">
							<?php
							  if (isset($datosFormRemesaBanco['IVA']['errorMensaje']))
						    {echo $datosFormRemesaBanco['IVA']['errorMensaje'];}
							?>
							</span>		
		    <br /><br />		
					<label><strong>NOMBRE ARCHIVO DE LA REMESA GENERADO PARA DESCARGAR Y ENVIAR A B.SANTANDER</strong></label><span class="mostrar">SEPA_ISO20022CORE_fecha_orden_cobro.xml </span>
			    <input type="hidden"								     
			           name="datosFormRemesaBanco[nombreArchivoSEPA]"
			           value="SEPA_ISO20022CORE"
			    />	
					<label>Versión formato SEPA para el B. Santander</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosFormRemesaBanco[versionFormatoSEPA]"
			           value='pain.008.001.02'
			           size="20"
			           maxlength="25"
			    />
							<br />	

					<label><strong>DIRECTORIO EN EL SERVIDOR PARA EL ARCHIVO DE LA REMESA</strong></label>
			    <input type="text" readonly
								      class="mostrar"									     
			           name="datosFormRemesaBanco[DIRECTORIOARCHIVOREMESA]"
			           value="/../upload/TESORERIA/SEPAXML_ISO20022"
														size="50"
			           maxlength="40"
			    />								
 					<!-- <label><strong>2DIRECTORIO EN EL SERVIDOR PARA ARCHIVO SEPA_ISO20022CORE_fecha_orden_cobro.xml </strong></label>
			    <input type="text" readonly
								      class="mostrar"									     
			           name="datosFormRemesaBanco[DIRECTORIOARCHIVOREMESA]"
			           value='<?php //echo $datosFormRemesaBanco['DIRECTORIOARCHIVOREMESA'];?>'
														size="50"
			           maxlength="40"
			    />			-->					
     <br />							
					<label><strong>AÑO DE LA CUOTA A COBRAR</strong></label>									 	 
			    <input type="text" readonly
								      class="mostrar"			           
														name="datosFormRemesaBanco[anioCuotasElegido]"
			           value='<?php echo date("Y");?>'
			           size="4"
			           maxlength="4"
			    /> 
				 <label>CONCEPTO</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosFormRemesaBanco[concepto]"
	            value="-CUOTA-"
	            size="10"	           
	     />
						<br /><br />					
						<!-- Fin datos fijos necesarios para generar el archivo SEPA-XML o NORMA AEB19,-->		
						
						<!-- ************ Inicio Fecha de cobro por el banco Santander***************** -->		
						<label><strong>*Fecha de orden de cobro por el B. Santander</strong></label> 
						<?php		
						
						 require_once './modelos/libs/comboLista.php';
				   //lo referente a fecha podría ser un requiere_once parValorFechas
				 		$parValorDia["00"]="día"; 
						 for ($d=1;$d<=31;$d++) 
						 {if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
							 else {$valor="$d";}
							 $parValorDia[$valor]=$valor;
						 }                                         
       if (!isset($datosFormRemesaBanco['fechacobro']['dia']['valorCampo']) || empty($datosFormRemesaBanco['fechacobro']['dia']['valorCampo']))
       { $datosFormRemesaBanco['fechacobro']['dia']['valorCampo'] = '00'; }//evita Notice	
						 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
						 echo comboLista($parValorDia, "datosFormRemesaBanco[fechacobro][dia]",$datosFormRemesaBanco['fechacobro']['dia']['valorCampo'],
															$parValorDia[$datosFormRemesaBanco['fechacobro']['dia']['valorCampo']],"00","día");
							
       if (!isset($datosFormRemesaBanco['fechacobro']['mes']['valorCampo']) || empty($datosFormRemesaBanco['fechacobro']['mes']['valorCampo']))
       { $datosFormRemesaBanco['fechacobro']['mes']['valorCampo'] = '00';  }//evita Notice											
				 
						 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
						 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
																								 
						 echo comboLista($parValorMes,"datosFormRemesaBanco[fechacobro][mes]",$datosFormRemesaBanco['fechacobro']['mes']['valorCampo'],
						 $parValorMes[$datosFormRemesaBanco['fechacobro']['mes']['valorCampo']],"00","mes");		 
						
						 //$parValorAnio["0000"]="año::"; 
							//$anioSiguiente=date("Y")+1;
							$anioActual=date("Y");	 
						 
						 $parValorAnio=array('0000'=>"año",$anioActual=>$anioActual/*,$anioSiguiente=>$anioSiguiente*/);
							
				   if (!isset($datosFormRemesaBanco['fechacobro']['anio']['valorCampo']) || empty($datosFormRemesaBanco['fechacobro']['anio']['valorCampo']))
       {	$datosFormRemesaBanco['fechacobro']['anio']['valorCampo'] = '0000';	}//evita Notice	
						
						 echo comboLista($parValorAnio,"datosFormRemesaBanco[fechacobro][anio]",
							                $datosFormRemesaBanco['fechacobro']['anio']['valorCampo'],
							                $parValorAnio[$datosFormRemesaBanco['fechacobro']['anio']['valorCampo']],"0000","año");
										
						 ?>	
				  	<span class="error"><strong>
							<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
							if (isset($datosFormRemesaBanco['fechacobro']['errorMensaje']))
							{echo $datosFormRemesaBanco['fechacobro']['errorMensaje'];}
							?></strong>						
						</span>
						
						<br />			
						<!-- ************ Fin Fecha de cobro por el banco Santander ******************** -->		
					</p>							
			 </fieldset>				
	
    <br />
			
			 <!-- Inicio Excluir de la lista los que se dieron de alta después de la fecha --->
				
				<fieldset><legend><strong>Excluir de la orden de cobro a los socios/as con alta después de la fecha</strong></legend>
	 	  <p>
		   
      <span class="textoAzu112Left2">			
							- Para segundas, terceras órdenes de cobro, se pueden excluir de la lista las/los socias/os 
					  que se dieron de alta cerca del final de año <strong> <?php echo date("Y");?></strong> 
							<br /><br />- Para incluir a todos/as hay que poner la fecha de hoy <strong> <?php echo date("d-m-Y");?></strong>
						</span>
						<br /> <br />
      <label><strong>*Excluir de la orden de cobro a los socios/as con alta después de la fecha</strong> </label>
						<?php
				   //lo referente a fecha podría ser un requiere_once parValorFechas
				 		$parValorDia["00"]="día"; 
						 for ($d=1;$d<=31;$d++) 
						 {if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
							 else {$valor="$d";}
							 $parValorDia[$valor]=$valor;
						 }
							if (!isset($datosFormRemesaBanco['fechaAltaExentosPago']['dia']['valorCampo']) || empty($datosFormRemesaBanco['fechaAltaExentosPago']['dia']['valorCampo']))
       {		$datosFormRemesaBanco['fechaAltaExentosPago']['dia']['valorCampo'] = '00';		}//evita Notice	
						
						 echo comboLista($parValorDia, "datosFormRemesaBanco[fechaAltaExentosPago][dia]",$datosFormRemesaBanco['fechaAltaExentosPago']['dia']['valorCampo'],
															$parValorDia[$datosFormRemesaBanco['fechaAltaExentosPago']['dia']['valorCampo']],"00","día");
				 
						 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
						 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
															
							if (!isset($datosFormRemesaBanco['fechaAltaExentosPago']['mes']['valorCampo']) || empty($datosFormRemesaBanco['fechaAltaExentosPago']['mes']['valorCampo']))
       {		$datosFormRemesaBanco['fechaAltaExentosPago']['mes']['valorCampo'] = '00';		}//evita Notice
						
						 echo comboLista($parValorMes,"datosFormRemesaBanco[fechaAltaExentosPago][mes]",$datosFormRemesaBanco['fechaAltaExentosPago']['mes']['valorCampo'],
						 $parValorMes[$datosFormRemesaBanco['fechaAltaExentosPago']['mes']['valorCampo']],"00","mes");		 
						
						 //$parValorAnio["0000"]="año::"; 
							//$anioAnterior=date("Y")-1;
							$anioActual=date("Y");	 
						 
						 $parValorAnio=array('0000'=>"año",$anioActual=>$anioActual/*,$anioAnterior=>$anioAnterior*/);
							
				   if (!isset($datosFormRemesaBanco['fechaAltaExentosPago']['anio']['valorCampo']) || empty($datosFormRemesaBanco['fechaAltaExentosPago']['anio']['valorCampo']))
       {		$datosFormRemesaBanco['fechaAltaExentosPago']['anio']['valorCampo'] = '0000'; }//evita Notice
						
						 echo comboLista($parValorAnio,"datosFormRemesaBanco[fechaAltaExentosPago][anio]",
							                $datosFormRemesaBanco['fechaAltaExentosPago']['anio']['valorCampo'],
							                $parValorAnio[$datosFormRemesaBanco['fechaAltaExentosPago']['anio']['valorCampo']],"0000","año");
										
						 ?>	
				  	<span class="error"><strong>
							<?php 
							if (isset($datosFormRemesaBanco['fechaAltaExentosPago']['errorMensaje']))
							{echo $datosFormRemesaBanco['fechaAltaExentosPago']['errorMensaje'];}
							?></strong>
						</span>
						
					<br />
					</p>
    </fieldset>	
    <!-- Fin Excluir de la lista los que se dieron de alta después de la fecha ---->				
				
		 <br />			
		
		 <!-- ************ Inicio Elegir país de domiciliación de pago *************** -->		
			<fieldset><legend><strong>Elegir país de la cuenta bancaria domiciliada de los socios/as para cobro de cuota</strong></legend>
			  <p>
	      <input type="radio"
	             name="datosFormRemesaBanco[paisCC]"
	             value='ES'														
														<?php 
														if (!isset($datosFormRemesaBanco['paisCC']['valorCampo']) || $datosFormRemesaBanco['paisCC']['valorCampo'] =='ES')
							       { echo 'checked';
														}
														?>																		
	      />	
					  <label>Cuenta bancaria de España</label>
							
       <br /><br />				
					  <input type="radio"
	             name="datosFormRemesaBanco[paisCC]"
	             value='SEPA'														
														<?php 
														if (isset($datosFormRemesaBanco['paisCC']['valorCampo']) && $datosFormRemesaBanco['paisCC']['valorCampo'] =='SEPA')
							       { echo 'checked';
														}
														?>															
	      />					
							<label>Cuenta bancaria de países SEPA en Europa distintos de España (no genera el archivo "SEPA_ISO200022CORE_fecha.xml" pero muestra la lista)</label>		
							<br /><br />  
							<span class="textoAzu112Left2">NOTA: La opción  "Cuenta bancaria de países SEPA en Europa distintos de España" no genera el archivo "SEPA_ISO200022CORE_fecha.xml", 
			              pero mostrará un listado de esas cuentas IBAN y nombres de socios/as, y NO hace ninguna modificación en tablas "ORDENES_COBRO".
	          <br />Con esos datos, más los BICs de esas cuentas IBAN, Tesorería podría generar una remesa manualmente en la web del 
																	B. Santander, o mejor se les puede enviar un email sugiriendo que paguen 
											      su cuota mediante PayPal (serán pocas personas y menos costes que con pago domiciliado o transferencia).	
							</span>									
			   <br />
					</p>		
			</fieldset>	
			<!-- ************ Fin Elegir país de domiciliación de pago ****************** -->			
			
			<br />
			<!-- ************ Inicio Elegir agrupaciones para cobrar cuotas ************* -->	
			
			<fieldset><legend><strong>Elegir agrupaciones territoriales para incluir en las órdenes de cobro de cuotas domiciliadas</strong></legend>
	 	  <p>
						 <span class="textoAzu112Left2">
		
						  	- Para incluir una o más agrupaciones en el archivo XML, marcar la casilla correspondiente.
						 	<br />
 	
				  	 <span class="error"><strong>
									<?php 
									if (isset($datosFormRemesaBanco['agrupaciones']['errorMensaje']))
									{echo $datosFormRemesaBanco['agrupaciones']['errorMensaje'];}
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
	                  name="datosFormRemesaBanco[agrupaciones][<?php echo $codAgrupacion ?>]"
						             value='<?php echo $codAgrupacion; ?>'
																			
																			<?php 
																			if (isset($datosFormRemesaBanco['agrupaciones']['valorCampo'][$codAgrupacion]))
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
					
    <input type="submit" name="SiExportarRemesaBanco" value="Exportar selección a archivo SEPA_XML para B. Santander">
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				<input type="submit" name="NoExportarRemesaBanco" value="Cancelar"> 	
				
   </form>			

		 <!-- ************************* Fin selección  **************************************** -->	
			  <br />

	 </div>
</div>		
