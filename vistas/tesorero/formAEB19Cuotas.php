<!-- ************************************************************************
FICHERO: formAEB19Cuotas.php
PROYECTO: EL
Fecha actualización: Agustin 2016-03-01
VERSION: PHP 5.2.3
DESCRIPCION:Formulario de selección de agrupaciones para la formación del fichero 
            AEB19Cuotas para pasar al banco para el cobro de cuotas  
OBSERVACIONES:Es incluida desde './vistas/tesorero/vCuerpoAEB19Cuotas.php'              
************************************************************************ -->
<div id="registro">	

			<!-- ************************* Inicio Mensaje en caso de error ************************* -->
			<span class="error">
				<?php 
				 if (isset($resValidarCamposFormAEB19) && !empty($resValidarCamposFormAEB19))
				 {$datosAEB19Cuotas = $resValidarCamposFormAEB19;
				
				  if ($resValidarCamposFormAEB19['codError']!=='00000')
			   {echo "<br /><br /><strong>ERROR: en color rojo se indican los errores que debes corregir, además debes elegir de nuevo las agrupaciones</strong>";
					 }
						else
      {echo "<br /><br /><strong>PRIVACIDAD DE DATOS:</strong>  Los datos descargados en un archivo NORMA AEB19, se podrán utilizar para generar las órdenes de 
			    	pago. Es responsabildad del gestor que no sean usados con otros fines.";					
					 }		
				 }					
				?>
			</span>
	  <!-- ************************* Fin Mensaje en caso de error ************************* -->		
 <br /><br />

  <span class="textoAzu112Left2">
    <br /> 
				<strong>INCLUIRÁ:</strong>
				<br />- SOCIO/A CON ESTADO DE CUOTA:  "pendiente de cobro, abonada solo parte, exenta que incluya donación" 
					<br /><br />				
				- Órdenes de pagos a los bancos:<strong>SI</strong>
				<br />(El tesorero en <strong>-Cuotas socios/as->Pago cuota</strong>, 
				campo  *Incluir en lista de órdenes de pagos a los bancos:<strong>SI</strong>
				<br />
				si selecciona NO, en ese caso a ese socio/a no se le incluirá en la lista de órdenes de cobro al banco) 
				<br /><br />	
					<strong>NO INCLUIRÁ:</strong>
				<br />- SOCIO/A CON ESTADO DE CUOTA: "abonada, no abonada, exenta que no tengan donación, no abonada Cuenta anotada como errónea, 
				                no abonada  devuelta, cuota del socio/a que esté de baja, orden de pago a banco:NO"
				<br /><br />			
		- Además se pueden excluir de la lista a los/as socias/os que se dieron de alta después de una determinada fecha
			<br /><br />
				
			<strong>NOTAS:</strong> 
			<br />
			- Después de descargar el archivo AEB19 hay que ir a la aplicación del web B. Santander 
			"Empresas-->Supernet" para importar el archivo y dar la órden de pago. Para más aclaraciones, consultar el manual y al banco.
			<br /><br />
			- Con anterioriadad hay que envíar un email a los soci@as que se van a incluir en el archivo NORMA AEB19, avisándoles de la fecha aproximada de cobro,
			para ello utilizar la opción IIA.2 del menú de la página anterior. (o bien IIB.2)
						<br /><br />			
					
			<strong>AVISO:</strong>Funciona bien con FireFox, y Safar, pero con algunos navegadores pueden 
			tener problemas para generar el archivo NORMA AEB19 (por ejemplo Edge 2016-02-28)
				<br />NOTA: Probar a abrir el archivo NORMA AEB19 con Notepad.
	  </span>
			
		<div align="left"> 
			<!-- ********************* Inicio selección  *************** -->						
					
		 <form method="post" 
												action="./index.php?controlador=cTesorero&amp;accion=AEB19CuotasTesoreroSantander"> 
         <!-- action="./index.php?controlador=cTesorero&amp;accion=excelCuotasTesoreroBancos" -->
 		 <!-- datos fijos necesarios para generar el archivo NORMA AEB19 --->
				<br />
				<fieldset><legend><strong>Datos del cobro de Europa Laica para generar el archivo NORMA AEB19 (Convertible a SEPA-CORE/COR1 del Santander)</strong></legend>
	 	 <p>
				 <label>PRESENTADOR DEL ADEUDO DOMICILIADO</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosAEB19Cuotas[empresa_presentador]"
	            value="ASOCIACION EUROPA LAICA"
	            size="30"	           
	     />				
				 <label>CIF DEL PRESENTADOR</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosAEB19Cuotas[cif_presentador]"
	            value="G45490414"
	            size="10"	           
	     />
						<br />
				 <label>ORDENANTE DEL ADEUDO DOMICILIADO (Acreedor)</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosAEB19Cuotas[empresa_ordenante]"
	            value="ASOCIACION EUROPA LAICA ESTATAL"
	            size="45"	           
	     />
						<br />				
				 <label>CIF DEL ORDENANTE (Acreedor)</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosAEB19Cuotas[cif_ordenante]"
	            value="G45490414"
	            size="10"	           
	     />			
						<br />
				 <label>CONCEPTO</label>
	     <input type="text" readonly
						       class="mostrar"		        
	            name="datosAEB19Cuotas[concepto]"
	            value="-CUOTA-"
	            size="10"	           
	     />			
						<br />			
			  <label>IBAN</label> 	 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosAEB19Cuotas[ctaBanco][CODENTIDAD]"
			           value='ES42'
			           size="4"
			           maxlength="4"
			    /> 												

			  <label>Núm. entidad en España</label> 	 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosAEB19Cuotas[ctaBanco][CODENTIDAD]"
			           value='0049'
			           size="4"
			           maxlength="4"
			    /> 			
			  <label>Núm. sucursal</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosAEB19Cuotas[ctaBanco][CODSUCURSAL]"
			           value='0001'
			           size="4"
			           maxlength="4"
			    /> 			
			  <label>DC</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosAEB19Cuotas[ctaBanco][DC]"
			           value='52'
			           size="2"
			           maxlength="2"
			    /> 			
			  <label>Núm. cuenta</label> 
			    <input type="text" readonly
								      class="mostrar"
			           name="datosAEB19Cuotas[ctaBanco][NUMCUENTA]"
			           value='2411813269'
			           size="11"
			           maxlength="11"
			    /> 					
		    <br /><br />
						<label>*IVA(% decimales separados por punto: "nn.nn")</label>
			    <input type="text" readonly
								      class="mostrar"		        
			           name="datosAEB19Cuotas[IVA]"
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
														name="datosAEB19Cuotas[anioCuotasElegido]"
			           value='<?php echo date("Y");?>'
			           size="4"
			           maxlength="4"
			    /> 
						<br /><br />	
						<label>*Fecha de cobro por el banco Santander</label> 
						<?php		
						
						 require_once './modelos/libs/comboLista.php';
				   //lo referente a fecha podría ser un requiere_once parValorFechas
				 		$parValorDia["00"]="día"; 
						 for ($d=1;$d<=31;$d++) 
						 {if ($d<10) {$valor="0"."$d";}//para que los días tengan el formato 01, 02,...10,...31
							 else {$valor="$d";}
							 $parValorDia[$valor]=$valor;
						 }
						 //function comboLista($parValor,$identificadorCampo,$valorPrevio,$descPrevio,$valorInicial,$descInicial)
						 echo comboLista($parValorDia, "datosAEB19Cuotas[fechacobro][dia]",$datosAEB19Cuotas['fechacobro']['dia']['valorCampo'],
															$parValorDia[$datosAEB19Cuotas['fechacobro']['dia']['valorCampo']],"00","día");
				 
						 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
						 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
																								 
						 echo comboLista($parValorMes,"datosAEB19Cuotas[fechacobro][mes]",$datosAEB19Cuotas['fechacobro']['mes']['valorCampo'],
						 $parValorMes[$datosAEB19Cuotas['fechacobro']['mes']['valorCampo']],"00","mes");		 
						
						 //$parValorAnio["0000"]="año::"; 
							//$anioSiguiente=date("Y")+1;
							$anioActual=date("Y");	 
						 
						 $parValorAnio=array('0000'=>"año",$anioActual=>$anioActual/*,$anioSiguiente=>$anioSiguiente*/);
				
						 echo comboLista($parValorAnio,"datosAEB19Cuotas[fechacobro][anio]",
							                $datosAEB19Cuotas['fechacobro']['anio']['valorCampo'],
							                $parValorAnio[$datosAEB19Cuotas['fechacobro']['anio']['valorCampo']],"0000","año");
										
						 ?>	
				  	<span class="error">
							<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
							if (isset($datosAEB19Cuotas['fechacobro']['errorMensaje']))
							{echo $datosAEB19Cuotas['fechacobro']['errorMensaje'];}
							?>
						</span>
					</p>							
			 </fieldset>
				

			<p>

		 	<span class="textoAzu112Left2"> <!-- NO QUITAR, SE DESCUADRA -->
			  <br />                         <!-- NO QUITAR, SE DESCUADRA -->
			 </span>                         <!-- NO QUITAR, SE DESCUADRA -->
			</p>

		
				<fieldset><legend><strong>Excluir de la orden de cobro a los socios/as con alta después de la fecha</strong></legend>
	 	  <p>
		<!-- Inicio Excluir de la lista los que se dieron de alta después de la fecha --->

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
						 echo comboLista($parValorDia, "datosAEB19Cuotas[fechaAltaExentosPago][dia]",$datosAEB19Cuotas['fechaAltaExentosPago']['dia']['valorCampo'],
															$parValorDia[$datosAEB19Cuotas['fechaAltaExentosPago']['dia']['valorCampo']],"00","día");
				 
						 $parValorMes=array("00"=>"mes","01"=>"Enero","02"=>"Febrero","03"=>"Marzo","04"=>"Abril","05"=>"Mayo","06"=>"Junio",
						 "07"=>"Julio","08"=>"Agosto","09"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
																								 
						 echo comboLista($parValorMes,"datosAEB19Cuotas[fechaAltaExentosPago][mes]",$datosAEB19Cuotas['fechaAltaExentosPago']['mes']['valorCampo'],
						 $parValorMes[$datosAEB19Cuotas['fechaAltaExentosPago']['mes']['valorCampo']],"00","mes");		 
						
						 //$parValorAnio["0000"]="año::"; 
							//$anioAnterior=date("Y")-1;
							$anioActual=date("Y");	 
						 
						 $parValorAnio=array('0000'=>"año",$anioActual=>$anioActual/*,$anioAnterior=>$anioAnterior*/);
				
						 echo comboLista($parValorAnio,"datosAEB19Cuotas[fechaAltaExentosPago][anio]",
							                $datosAEB19Cuotas['fechaAltaExentosPago']['anio']['valorCampo'],
							                $parValorAnio[$datosAEB19Cuotas['fechaAltaExentosPago']['anio']['valorCampo']],"0000","año");
										
						 ?>	
				  	<span class="error">
							<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
							if (isset($datosAEB19Cuotas['fechaAltaExentosPago']['errorMensaje']))
							{echo $datosAEB19Cuotas['fechaAltaExentosPago']['errorMensaje'];}
							?>

						</span>
					<br />
					</p>
  </fieldset>		
		<br />			
		<!--Fin Excluir de la lista los que se dieron de alta después de la fecha ---->
			<fieldset><legend><strong>Elegir país de domiciliación de pago de cuota</strong></legend>
	 	  <p>
	      <input type="radio"
	             name="datosAEB19Cuotas[paisCC]"
	             value='ES' 
														
														<?php 
														if (!isset($datosAEB19Cuotas['paisCC']['valorCampo']) || $datosAEB19Cuotas['paisCC']['valorCampo'] =='ES')
							       { echo 'checked';
														}
														?>																		
	      />	
						<label>Cuenta bancaria de España</label>		
	      <br />							
					  <input type="radio"
	             name="datosAEB19Cuotas[paisCC]"
	             value='SEPA' 
														
														<?php 
														if (isset($datosAEB19Cuotas['paisCC']['valorCampo']) && $datosAEB19Cuotas['paisCC']['valorCampo'] =='SEPA')
							       { echo 'checked';
														}
														?>															
	      />
						<label>Cuenta bancaria de paises SEPA (en Europa incluye España)</label>
		      <br />								
					  <input type="radio"
	             name="datosAEB19Cuotas[paisCC]"
	             value='EX' 
														
														<?php 
														if (isset($datosAEB19Cuotas['paisCC']['valorCampo']) && $datosAEB19Cuotas['paisCC']['valorCampo'] =='EX' )
							       { echo 'checked';
														}
														?>																		
	      />
						<label>Cuenta bancaria de paises NO SEPA (aún no es posible domiciliarlos, solo por información)</label>
		      <br />															
						</p>	
			</fieldset>	
			<br />
			<fieldset><legend><strong>Elegir agrupaciones territoriales para cobrar cuotas</strong></legend>
	 	  <p>
						 <span class="textoAzu112Left2">
						  	- Si se quiere incluir una o más agrupaciones en el archivo NORMA AEB19, marcar la casilla correspondiente.
						 	<br /><br />
							  - Si se quiere excluir una o más agrupaciones del archivo NORMA AEB19, 
								(agrupaciones que cobran directamente las cuotas), desmarcar la casilla correspondiente. 						
						  <br />

						 	<?php //-------------nueva version
							   unset($parValorComboAgrupaSocio['lista']["%"]);//Para que no salgan la opcion de todas en el formulario
							 		foreach ($parValorComboAgrupaSocio['lista'] as $codAgrupacion => $nomAgrupacion)                         
			       { echo "<br />",$nomAgrupacion;
							  
							 ?>
									   <input type="checkbox" 
	                  name="datosAEB19Cuotas[agrupaciones][<?php echo $codAgrupacion ?>]"
						             value='<?php echo $codAgrupacion; ?>'
																			
																			<?php 
																			if (isset($datosAEB19Cuotas['agrupaciones']['valorCampo'][$codAgrupacion]))
													       { echo 'checked';
																				}
																			?>
	           />
							 <?php		//------------
									}
							 ?> 							
							
							     
							</span>
				 </p>
				</fieldset>			
					<br />												
    <input type="submit" name="SiExportarAEB19Cuotas" value="Exportar selección">
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				<input type="submit" name="NoExportarAEB19Cuotas" value="Cancelar"> 												
   </form>
		 <!-- ************************* Fin selección  ************ -->	

	 </div>
</div>		
