<?php
/*-----------------------------------------------------------------------------------------------------
FICHERO: formEmailAvisarDomiciliadosProximoCobro.php
VERSION: PHP 7.3.21

DESCRIPCION: Incluye el formulario de selección para buscar los Emails y otros datos de las cuotas 
de los socios que tienen domicialiación bancaria	del pago de las cuotas mediante su cuenta IBAN,
para envíar un email personalizado con su nombre, APE1, CCIBAN, Cuota, a los socios avisando de 
próximo cobro de cuotas.
También incluye el texto común que se enviará en el email.

Envía emails para socios a partir de la selección que se realiza en el formulario,
cuotas: 'PENDIENTE-COBRO','ABONADA-PARTE', con cuenta IBAN España o en país IBAN SEPA  
distinto ES (por ahora no porque hay problemas con BIC), y ORDENARCOBROBANCO=SI 
siempre que estén de "alta" en el momento actual y NO INCLUYE las cuotas "abonadas,exentas, y socios dados de baja", 
y de sólo de las "AGRUPACIONES" elegidas en el formulario.

A partir de una fecha de alta que se introduce en el formulario se excluye el envío a esos socios (para excluir pagar
 cuotas altas en noviembre y diciembre)
Se envía con cuenta "tesoreria@europalaica.org"

Se introduce un texto indicando la fecha aproximada de cobro, y un enlace a la web de EL, donde figuran los ingresos 
y gastos de EL.

Para formar la lista de emails, desde formEmailAvisarDomiciliadosProximoCobro.php
se puede elegir:
- Cuenta bancaria en España
- Cuenta bancaria de países SEPA (distintos de España)
														
LLAMADA: vistas/tesorero/vCuerpoEmailAvisarDomiciliadosProximoCobro.php
previamente desde cTesorero.php:emailAvisarDomiciliadosProximoCobro()			
						
OBSERVACIONES:
2020-10-15: Aquí no necesita cambios para PDO, lo incluyen internamente las funciones que utiliza. 
He cambiado el formulario, para ver y recoger aquí el texto común del email mediante 
input readonly:"camposFormDatosElegidos[textoEmail][asunto]" y [nota]
"camposFormDatosElegidos[textoEmail][bodyN]" y pasarlo a modeloEmail:emailAvisarDomiciliadosProxCobro(), 
para que lo envíe.

Antes el texto estaba modeloEmail:emailAvisarDomiciliadosProxCobro(). 
Ahora habrá más consistencia entre lo mostrado en el formulario y lo enviado 
2015-12-10: Para socios otros paises SEPA distintos de ES por ahora no se cobran, ya que falta cálculo BIC, pero
 se deja envíar email por si acaso se incluyese.      
-------------------------------------------------------------------------------------------------------*/
?>
<!-- Para campo textarea, además hay control interno en php -->
<script type="text/javascript">
function limitarTextoArea(max, id)
{if(max < document.getElementById(id).value.length)
	{ document.getElementById(id).value = document.getElementById(id).value.substr(0, max);
   alert("Llegó al máximo de caracteres permitidos");
	}			
}
</script>
<!-- ***************************************************** --> 

<!-- Añado esta modificación de estilos para quitar los bordes de los imputs y textarea, pero sería mejor pasarlo al CSS -->			
		<style>
				input.sinborde {
						border: 0;		margin: 0;  padding: 0; 
						font-size:1.1rem; font-family:Geneva,Verdana,Arial, Helvetica,Sans-serif,Courier-New,Times New Roman;
						color: #414141;font-weight: 400; text-align: left; font-style:Normal;
		  }		

    textarea.sinborde {
						border: 0;		margin: 0;  padding: 0; 
						font-size:1.1rem; font-family:Geneva,Verdana,Arial, Helvetica,Sans-serif,Courier-New,Times New Roman;
						color: #414141;font-weight: 400; text-align: left; font-style:Normal;
}				
 </style>	
<!-- Fin modificación de estilos para quitar los bordes de los imputs y textarea, pero sería mejor pasarlo al CSS -->	

<div id="registro">	

			<!-- ************************* Inicio Mensaje en caso de error ************************* -->
			<span class="error">
				<?php 
				 if (isset($camposFormDatosElegidos['codError']) && $camposFormDatosElegidos['codError'] !== '00000')
					{echo "<br /><br /><strong>ERROR: en color rojo se indican los errores que debes corregir, revisa de nuevo las agrupaciones elegidas</strong>";
					}		
					else
     {echo "<br /><br />    <strong>AVISO: ANTES DE PULSAR EL BOTÓN - Enviar emails - REVISA QUE ESTAN MARCADAS CORRECTAMENTE 
				  LAS CASILLAS DE LAS AGRUPACIONES A LAS QUE SE REALIZA EL COBRO DOMICILIADO</strong>" ;					
				 }					
				?>
			</span>
	  <!-- ************************* Fin Mensaje en caso de error ************************* -->		
 <br /><br />

  <span class="textoAzu112Left2">
			<br />El contenido del email está personalizado con apellidos y nombre, cuenta IBAN del socio/a y cuota que se va a cobrar en su banco
			<br />	<br />
    <br /> 
				<strong>SE ENVIARÁ A:</strong>
				<br /><br />- Socios/as con estado de cuota: "PENDIENTE-COBRO y ABONADA-PARTE"  
			 <br /><br />- Socios/as están de alta
				<br /><br />- Ordenar cobro domiciliado a banco: <strong>SI</strong>
			 <br />(En el caso de que tenga cuenta bancaria de país SEPA, Tesorería lo puede modificar para un socio/a en 
				      <i>-Cuotas socios/as->Acciones: Pago cuota, o en Actualiza cuota</i>, 
			       en el campo <i>*Incluir en la próxima lista de órdenes de cobro a los bancos</i>: si selecciona NO, 
										a ese socio/a no se le incluirá en la lista de órdenes de cobro al banco, ni se le enviará email de aviso de cobro domiciliado)	
				<br /><br />- Agrupaciones territoriales elegidas (Sí marcadas) para cobrar las cuotas domiciliadas
				<br /><br />- Socios/as con campo EMAILERROR = "NO"
				<br /><br /> 
				<strong>NO SE ENVIARÁ A:</strong>
				<br />- Socios/as con estado de cuota: "ABONADA, NOABONADA, NOABONADA-DEVUELTA, NOABONADA-ERROR-CUENTA, EXENTO (honorario/a)"
				<br />- Socios/as que estén dados de baja, u orden de cobro a banco: NO
				<br />- Socios/as con campo EMAILERROR distinto de "NO"
				<br />- Además se pueden excluir de la lista a los/as socias/os que se dieron de alta después de una determinada fecha
	  </span>
					
		<div align="left"> 																	

			<!-- ********************* Inicio Form  *************** -->						
					
		<form method="post" action="./index.php?controlador=cTesorero&amp;accion=emailAvisarDomiciliadosProximoCobro"> 
				<br /><br />	

	  <fieldset>
			
				<legend><strong>Contenido email personalizado que recibirán los socios/as</strong></legend>
					<br />
					<span class="textoAzu112Left2">		
						En este texto del email, en el primer recuadro, tienes que escribir un texto con la fecha prevista de cobro. 
						Por ejemplo: - EN LOS QUINCE PRIMEROS DÍAS DE MAYO DE 	<?php echo date("Y");?> ,
						<br /><br />En el segundo recuadro escribe el enlace a la página web de laicismo.org donde se pone la información de gastos e 
						ingresos (PREGUNTA a responsable de laicismo.org)
						<br /><br />Por ejemplo (si no cambia la URL): https://laicismo.org/cuentas-de-europa-laica-transparencia-e-informacion/
					</span>	
					
					<br /><br />
					
				<!--************ Inicio contenido del texto común email a envíar *******************************-->	
		 	<fieldset> 											
  						
					<span class="textoGris8Left1"> 
								<strong>ASUNTO: </strong>
						</span>	
					<input type="text" class="sinborde" readonly 
						name="camposFormDatosElegidos[textoEmail][asunto]"
						value="Europa Laica. Próximo cobro de la cuota anual de la asociación"
						size="70"
						maxlength="80"
						/>	
      <br /><br />			
					<input type="text" class="sinborde" readonly 
						name="camposFormDatosElegidos[textoEmail][body1]"
						value="Estimado/a socio/a "
						size="20"
						maxlength="30"
						/>	
						<span class="textoGris8Left1"> 
								<strong>APELLIDOS, NOMBRE	</strong>
						</span>	
						<br /><br />				
					<input type="text" class="sinborde" readonly 
						name="camposFormDatosElegidos[textoEmail][body2]"
						value="Te informamos que próximamente emitiremos la orden de cobro de tu cuota anual de Europa Laica del año <?php echo date('Y');?>, que salvo error aún no está abonada."
						size="180"
						maxlength="255"
						/>	
      <br /><br />	
					<input type="text" class="sinborde" readonly 
						name="camposFormDatosElegidos[textoEmail][body3]"
						value="Se cobrará en tu cuenta: "
					 size="25"
						maxlength="255"
						/>	
						<span class="textoGris8Left1"> 
							<strong>NN,00 euros</strong>	
						</span>										
      <br /><br />							
 				<input type="text" class="sinborde" readonly 
						name="camposFormDatosElegidos[textoEmail][body4]"
						value="Para el cobro de tu cuota anual tenemos anotada la siguiente cuenta bancaria: "
						size="85"
						maxlength="255"
						/>	
						<span class="textoGris8Left1"> 
								<strong>ES12****************1234</strong>
						</span>	
      <br /><br />					
					<input type="text" class="sinborde" readonly 
						name="camposFormDatosElegidos[textoEmail][body5]"
						value="Si esta cuenta ya no estuviese operativa, la puedes modificar antes de la fecha en que tenemos previsto envíar tu cuenta al banco: "
					 size="123"
						maxlength="255"
						/>		
					<input type="text" class="textoAzu112Left2"
						name="camposFormDatosElegidos[textoFechaPrevistaCobro]"
						value='<?php if (isset($camposFormDatosElegidos['textoFechaPrevistaCobro']['valorCampo']))
						{  echo $camposFormDatosElegidos['textoFechaPrevistaCobro']['valorCampo'];}
						?>'
						size="40"
						maxlength="60"
						/>			
						<span class="error"><strong>
							<?php
							if (isset($camposFormDatosElegidos['textoFechaPrevistaCobro']['errorMensaje']))
							{echo $camposFormDatosElegidos['textoFechaPrevistaCobro']['errorMensaje'];  }
							?></strong>
						</span>

					<!-- text area-->	
		   <textarea type="text" class="sinborde" readonly name="camposFormDatosElegidos[textoEmail][body6]" rows="9" cols="180" wrap="hard"> 
		    <?php echo "
Para corregirla puedes entrar en el -ÁREA DE SOCI@S- desde la página web https://laicismo.org	o bien haciendo clic en el siguiente enlace: https://www.europalaica.com/usuarios
\nSi el enlace anterior no funciona, prueba a copiarlo y pegarlo en	una nueva ventana del navegador	
						
- Por decisión de la asamblea, Europa Laica NO solicita subvenciones públicas ni de entidades privadas.
- Nuestros ingresos provienen de las cuotas, donaciones de socios y socias, y de las donaciones de simpatizantes. 
- Las actividades de las personas de la junta directiva y otros órganos de coordinación son totalmente voluntarias, no percibiendo pagos de salarios u otras compensaciones económicas.	"
		    ?>
						</textarea> 
						<br />			
					<input type="text" class="sinborde" readonly 
						name="camposFormDatosElegidos[textoEmail][body7]"
						value="- Puedes ver información sobre los ingresos y gastos de Europa Laica en nuestra página web  "
						size="85"
						maxlength="255"
						/>
					<input type="text" class="textoAzu112Left2"  name="camposFormDatosElegidos[URLgastosLaicismo]"
						value='<?php if (isset($camposFormDatosElegidos['URLgastosLaicismo']['valorCampo']))
						{  echo $camposFormDatosElegidos['URLgastosLaicismo']['valorCampo'];}
						?>'
						size="70" 
						maxlength="255"
						/>				
						<span class="error">
							<?php
							if (isset($camposFormDatosElegidos['URLgastosLaicismo']['errorMensaje']))//no se producira error, porque no lo valido
							{echo $camposFormDatosElegidos['URLgastosLaicismo']['errorMensaje'];  }
							?>
						</span> 	
						
					<br /><br />

					<span class="textoGris8Left1"><strong>Opcional: Nota de tesorería que se añadiría en el email en esta posición</strong></span>
							<br />
								<textarea  id='nota' onKeyPress="limitarTextoArea(580,'nota');"	
								class="textoAzul8Left" name="camposFormDatosElegidos[textoEmail][nota]" rows="8" cols="80"><?php 
										if (isset($camposFormDatosElegidos['textoEmail']['nota']['valorCampo']))                
									{echo htmlspecialchars($camposFormDatosElegidos['textoEmail']['nota']['valorCampo']);}   
								?></textarea>
											<span class="error">
															<?php
															if (isset($camposFormDatosElegidos['textoEmail']['nota']['errorMensaje'])) {
																			echo $camposFormDatosElegidos['textoEmail']['nota']['errorMensaje'];
															}
															?>
											</span>		
					      <br />
						<!-- text area-->	
		   <textarea type="text" class="sinborde" readonly  name="camposFormDatosElegidos[textoEmail][body8]" rows="12" cols="180" wrap="hard"> 
		    <?php echo "
Si necesitas ayuda para cambiar la cuenta o alguna aclaración, puedes ponerte en contacto con Tesorería.
email: tesoreria@europalaica.org
----------------------
Un saludo,
Tesorería Europa Laica				

Protección de datos personales: En Europa Laica cumplimos el Reglamento General de Protección de Datos (RGPD, 2016/679), aplicable a partir del 25/05/2018 en la Unión Europea. TUS DATOS PERSONALES NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA. Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. Si quieres más información haz clic en el siguiente enlace: https://www.europalaica.com/usuarios/index.php?controlador=cEnlacesPie&accion=privacidad"		
	?>
						</textarea> 
      <br />
  
				</fieldset>
				<!--************ Fin contenido del texto común email a envíar **********************************-->	
   </fieldset>
   					
		 	<br /><br />							
				<!-- Inicio Elegir: anioCuotasElegido, Tipo cuenta, fechaAltaExentosPago, Agrupaciones territoriales -->	
				
    <!-- ******************* Inicio Año de la cuota a cobrar ******************  --> 						
				<fieldset>
	 	  <p>
						<label><strong>AÑO DE LA CUOTA A COBRAR</strong></label>		
			    <input type="text" readonly
								      class="mostrar"			          
														name="camposFormDatosElegidos[anioCuotasElegido]"
			           value='<?php echo date("Y");?>'
			           size="4"
			           maxlength="4"
			    /> 
					</p>
	   </fieldset>						
    <!-- ******************* Fin Año de la cuata a cobrar ******************  --> 

			 <br />
				
    <!-- Inicio Excluir de la lista los que se dieron de alta después de la fecha --->				 
				<fieldset>
				 <legend><strong>Excluir del envío de emails a los socios/as que se dieron de alta a finales de año</strong></legend>
	 	  <p>
					
				  <span class="textoAzu112Left2">			
							- Para segundas, terceras órdenes de cobro, próximas al final del año, se pueden excluir de la lista a las/los socias/os 
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
									  	
							//echo "<br>parValorAnio:";print_r($parValorAnio);
					
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
					<br /><br />	
		  <!--Fin Excluir de la lista los que se dieron de alta después de la fecha ---->
				
	  	<!--Inicio Elegir opción de tipo cuenta banco ---->
				<fieldset><legend><strong>Elegir opción tipo cuenta banco</strong>
														</legend>
	 	  <p>
			   
	      <input type="radio"
	             name="camposFormDatosElegidos[paisCC]"
	             value='ES' 
															
														<?php 
														if (!isset($camposFormDatosElegidos['paisCC']['valorCampo'])|| $camposFormDatosElegidos['paisCC']['valorCampo']=='ES')
							       { echo 'checked';
														}
														?>																		
	      />
					<label>Cuenta bancaria en España</label>	
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
						<label>Cuenta bancaria en países SEPA (distintos de España) y <strong>" Ordenar cobro banco = SI "</strong> (POR AHORA NO SE PUEDE GENERAR EL ARCHIVO "SEPA_ISO200022CORE_fecha.xml")</label>
		      <br /> <br />								
					 
				  <span class="textoAzu112Left2">NOTA: Con "Cuenta bancaria países SEPA (distintos de España)" no se puede domiciliar el pago automático 
						en el B. Santander (se necesita BIC para otros países SEPA), sólo marcar esta opción si se piensa generar una remesa manualmente 
						 en la web del B. Santander para esos socios/as, una vez conseguidos los BICs de esas cuentas
						</span>								
					</p>	
				</fieldset>
			   <br /><br />	
    <!--Fin Elegir opción de tipo cuenta banco ---->


    <!--Inicio Elegir agrupaciones territoriales ---->		
				<fieldset>
				<legend><strong>Elegir agrupaciones territoriales para envío de emails a socios/as, informando próximo cobro cuota</strong>
				</legend>
	 	  <p>					 

						 <span class="textoAzu112Left2">
							- Para incluir una o más agrupaciones marcar la casilla correspondiente.  
							</span>	
       <br /><br />
	      <span class="error"><strong>***NOTA:</strong> </span>	
					 	<span class="textoAzu112Left2">
       <br />								
	      Al ser un email con contenido individuadalmente personalizado para cada socio/a, este proceso requiere bastante tiempo de trabajo en el servidor, 
       y más tiempo cuantos más agrupaciones se incluyan en cada selección. 
       Por eso pudiera ser aconsejable fraccionar el envío, enviando individualmente las agrupaciones con más socios/as (Madrid, Valencia, Granada, Asturias, Sevilla)
							</span>	
							<br />
				  	<span class="error"><strong>							
							<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
							if (isset($camposFormDatosElegidos['agrupaciones']['errorMensaje']))
							{echo "<br />"		.$camposFormDatosElegidos['agrupaciones']['errorMensaje'];}
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
    <!-- Fin Elegir agrupaciones territoriales ---->	
    
				<!--  Fin Elegir: anioCuotasElegido, Tipo cuenta, fechaAltaExentosPago, Agrupaciones territoriales   -->	
				
				<!-- ************************* Inicio Mensaje en caso de error ************************* -->
				<span class="error">
					<?php 
							if (isset($camposFormDatosElegidos['codError']) && $camposFormDatosElegidos['codError']!=='00000')
							{echo "<br /><br /><strong>ERROR: en color rojo se indican los errores que debes corregir, revisa de nuevo las agrupaciones elegidas</strong>";
							}						
					?>
				</span>
				<!-- ************************* Fin Mensaje en caso de error ************************* -->	
					<br />	<br />							
    <input type="submit" name="SiEnviar" value="Enviar emails">
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
				<input type="submit" name="NoEnviar" value="Cancelar">
		
  </form>
		 <!-- ************************* Fin Form  *************************************** -->   
 </div>
</div>		
