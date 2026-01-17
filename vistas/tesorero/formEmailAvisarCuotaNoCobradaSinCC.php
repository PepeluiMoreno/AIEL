<?php
/*-----------------------------------------------------------------------------------------------------
FICHERO: formEmailAvisarCuotaNoCobradaSinCC.php
VERSION: PHP 7.3.21

DESCRIPCION: Incluye el formulario de selección de campos para buscar Emails y 
otros datos de todos las cuotas de los socios, que tienen NO domicialiación 
bancaria	del pago	de las cuotas mediante su cuenta IBAN, para envíar un email
personalizado a los socios comunicándoles que aún no han pagado la cuota anual
de la asociación Europa Laica.
Envía nombre y APE1, cuota, datos bancos Europa Laica, enlace a PayPal con 
cuota del socio que debe pagar y también enlace a web de laicismo.org con 
informacion gastos-ingresos.

Se incluyen siguiente los casos posibles tipo cuenta banco: SIN CUENTA, 
CUENTA-NOIBAN (actualmente ya no hay) y CUENTA-IBAN país SEPA distinto de ES y además con 
condicción "Ordenar cobro banco = NO". Y ESTADOCUOTA de socios/as:'PENDIENTE-COBRO',
'ABONADA-PARTE','NOABONADA-DEVUELTA','NOABONADA-ERROR-CUENTA' siempre que estén 
de "alta" en el momento actual y NO INCLUYE las cuotas "abonadas, exentas, 
y socios/as que estén de baja" y de sólo de las "AGRUPACIONES" elegidas 
en el formulario. 

A partir de una fecha de alta que se introduce en el formulario se excluye el 
envío a esos socios ( para excluir pagar cuotas altas en noviembre y diciembre)
Se envía con cuenta "tesoreria@europalaica.org"

Para formar la lista de emails, desde formEmailAvisarCuotaNoCobradaSinCC.php 
se puede elegir:
-No tiene cuenta bancaria domiciliada
-Tiene cuenta bancaria de países NO SEPA (o no está en formato IBAN)
-Cuenta bancaria de países SEPA (distintos de España).
													
LLAMADA: vistas/tesorero/vCuerpoEmailAvisarCuotaNoCobradaSinCC.php 
a su vez se llama desde cTesorero.php:emailAvisarCuotaNoCobradaSinCC()
													
OBSERVACIONES:
2020-10-11: Aquí no necesita cambios para PDO, lo incluyen internamente las 
funciones que utiliza. Cambio Nombre parámetro $camposFormDatosElegidos	 
He cambiado el formulario, para ver y recoger aquí el texto común del email
mediante input readonly:"camposFormDatosElegidos[textoEmail][asunto]" [nota]y 
"camposFormDatosElegidos[textoEmail][bodyN]" y pasarlo a
modeloEmail:emailAvisarDomiciliadosProxCobro(), para que lo envíe.

Antes el texto estaba modeloEmail:emailAvisarDomiciliadosProxCobro(). 
Ahora habrá más consistencia entre lo mostrado en el formulario y lo enviado 
2015-12-10: Para socios otros paises SEPA distintos de ES por ahora no se cobran, 
ya que falta cálculo BIC, pero se les puede enviar email.   
2023-10-01: Cambios para mejorar presentación al quitar Banco Tríodos de la BBDD y otras mejoras de información    
-----------------------------------------------------------------------------------------------------*/
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
 
<!-- Añado esta modificación de estilos para quitar los bordes de los inputs y textarea, pero sería mejor pasarlo al CSS -->			
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
				  LAS CASILLAS DE LAS AGRUPACIONES </strong>" ;					
				 }					
				?>
			</span>
	  <!-- ************************* Fin Mensaje en caso de error ************************* -->					

		<br /><br />
  <span class="textoAzu112Left2">
			<br />El contenido del email está personalizado con los apellidos y nombre del socio/a, y la cuota pendiente de pagar. Son leídos de la base de datos.
			<br />	<br />
				Incluye la cuenta bancaria de Europa Laica Estatal (o la cuenta bancaria de la agrupación si la agrupación cobra directamente la cuota al socio/a), 
				y también incluye un enlace personalizado a PayPal que el socio/a puede elegir opcionalmente como forma de pago.
				<br />	<br />
    <br /> 
				<strong>SE ENVIARÁ A:</strong>
				<br />- Socios/as con estado de cuota: "PENDIENTE-COBRO, ABONADA-PARTE, NOABONADA-DEVUELTA, NOABONADA-ERROR-CUENTA"
				<br /><br />- Socios/as que están de alta
				<br /><br />- Ordenar cobro domiciliado a banco: <strong>NO</strong>
				<br />(En el caso de que tenga cuenta bancaria de país SEPA, Tesorería lo puede modificar para un socio/a y poner (SI/NO), en
				      <i>-Cuotas socios/as->Acciones: Pago cuota, o en Actualiza cuota</i>, en el campo <i>*Incluir en la próxima lista de órdenes de cobro a los bancos</i>
				<br /><br />- Socios/as con campo EMAILERROR = "NO"				
				<br /><br />
				<strong>NO SE ENVIARÁ A:</strong>
				<br />- Socios/as con estado de cuota: "ABONADA, EXENTO (Honorario/a)"
				<br />- Socios/as que estén dados de baja, u orden de cobro a banco: SI
				<br />- Socios/as con valor campo EMAILERROR distinto de "NO"				
				<br />- Además se pueden excluir de la lista a los/as socias/os que se dieron de alta después de una determinada fecha
    <br />			
	 </span>
				
		<div align="left"> 
		
			<!-- ********************* Inicio selección  *************** -->						
 	<br />						
	<form method="post" action="./index.php?controlador=cTesorero&amp;accion=emailAvisarCuotaNoCobradaSinCC"> 
												
 	<fieldset>
			<legend><strong>Contenido del email personalizado que recibirán los socios/as</strong></legend>
				<br />		
				
				<span class="textoAzu112Left2">					
					 En el primer recuadro escribe el enlace a la página web de laicismo.org donde está la información de gastos e ingresos (PREGUNTA al responsable de laicismo.org). 
						Si no cambia es la URL: https://laicismo.org/cuentas-de-europa-laica-transparencia-e-informacion
					<br /><br />En el segundo recuadro, que es opcional, puedes escribir el tel. de tesorería y otra información.
				</span>	
				<br /><br />	

			<!--************ Inicio contenido del texto común email a envíar a socios **************-->	
			<fieldset> 												
				<br />						
				<span class="textoGris8Left1"> 
							<i>Asunto: </i>
					</span>	
				<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][asunto]"
					value="Europa Laica. Pendiente el pago de la cuota anual de socio/a"
					size="70"
					maxlength="80"
					/>	
					<br /><br />			
				<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body1]"
					value="Estimado/a socio/a "
					size="15"
					maxlength="30"
					/>	
					<span class="textoGris8Left1"> 
							<strong>APELLIDOS, NOMBRE	( Agrupación )	</strong>
					</span>	
					<br /><br />				
				<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body2]"
					value="Te informamos, salvo error, que aún no has pagado tu cuota de "
					size="54"
					maxlength="255"
					/>
					<span class="textoGris8Left1"> 
						<strong>NN,00 euros </strong>	
					</span>								

				<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body3]"
					value="correspondiente al año <?php echo date('Y') ?>. Por lo cual te rogamos que abones tu cuota en una de las cuentas de Europa Laica: "
					size="130"
					maxlength="255"
					/>				
					<br /><br />							
					
					<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body4]"
					value="<?php echo 'Titular cuenta: '?>"
					size="13"
					maxlength="255"
					/>			
					
					<span class="textoGris8Left1"> 
							<strong>ASOCIACIÓN EUROPA LAICA o NOMBRE	DE LA AGRUPACIÓN</strong> (en caso de que la agrupación cobre la cuota directamente)
							<br />
							<strong>Nombre del banco e IBAN del banco para ingresar la cuota el socio/a.</strong>
					</span>	
					<br />

				<textarea type="text" class="sinborde" readonly name="camposFormDatosElegidos[textoEmail][body5]" rows="6" cols="180" wrap="hard"> 
					<?php echo "		
Señala como concepto: Pago cuota a Europa Laica, NIF y nombre y apellidos.

Por si hubiese algún problema, nos puedes confirmar tu pago enviando un correo electrónico a - tesoreria@europalaica.org - con asunto: Cuota, 
y dentro del mensaje los datos: NIF, nombre y apellidos, cantidad, fecha pago y entidad dónde has pagado."
					?>
					</textarea> 	
					<br /><br />
					<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body6]"
					value="También puedes pagar ahora con PayPal: con tarjeta de crédito o con una cuenta de PayPal. Para pagar ahora con PayPal, haz clic en el siguiente enlace: "
					size="180"                                                            
					maxlength="255"		
					/>
					<!-- &amp; -->
					<!--<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body7]"					
					value=<?php //echo htmlspecialchars("https://www.europalaica.com/usuarios/index.php?controlador=controladorSocios&accion=pagarCuotaSocioSinCC&parametro=");?>
					size="105"                                                            
					maxlength="255"		
				/>-->
				
				<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body7]"					
					value=<?php echo htmlspecialchars("https://www.europalaica.org/usuarios/index.php?controlador=controladorSocios&amp;accion=pagarCuotaSocioSinCC&amp;parametro=");?>
					size="105"                                                            
					maxlength="255"		
				/>
					<span class="textoGris8Left1"> 
						<strong>Socio/a</strong>	
					</span>
					<br /><br />
					<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body8]"
					value="Si al hacer clic no se abre la ventana de PayPal, puedes copiar y pegar el enlace en tu navegador web."
					size="98"                                                            
					maxlength="255"		
					/>
				<br />
				<textarea type="text" class="sinborde" readonly name="camposFormDatosElegidos[textoEmail][body9]" rows="5" cols="180" wrap="hard"> 
					<?php echo "		
- Por decisión de la asamblea, Europa Laica NO solicita subvenciones públicas ni de entidades privadas.
- Nuestros ingresos provienen de las cuotas, donaciones de socios y socias, y de las donaciones de simpatizantes. 
- Las actividades de las personas de la junta directiva y otros órganos de coordinación son totalmente voluntarias, no percibiendo pagos de salarios u otras compensaciones económicas.	"
					?>
					</textarea> 
					<br />	
				<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body10]"
					value="- Puedes ver información sobre los ingresos y gastos de Europa Laica en nuestra página web "
					size="75"
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

				<textarea type="text" class="sinborde" readonly  name="camposFormDatosElegidos[textoEmail][body11]" rows="4" cols="180" wrap="hard">				
Para más facilidad de pago y que no se te olvide abonar la cuota anual, ahora puedes domiciliar tu cuota anotando tu cuenta bancaria en nuestra base de datos (con el número de tu cuenta IBAN). 
Para ello debes entrar en el -ÁREA DE SOCI@S- desde la página web de www.laicismo.org , o bien haciendo clic en el siguiente enlace: 
</textarea>	

				<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body12]"
					value="https://www.europalaica.org/usuarios"
					size="75"
					maxlength="255"
					/>				

					<br /><br />

						<span class="textoGris8Left1"><i>Opcional: Escribir nota de tesorería con tel. contacto para dudas y otra información que se añadiría en el email en esta posición</i></span>
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
				<textarea type="text" class="sinborde" readonly  name="camposFormDatosElegidos[textoEmail][body13]" rows="13" cols="180" wrap="hard"> 
					<?php echo "
Si necesitas ayuda para alguna aclaración, puedes ponerte en contacto con Tesorería
email: tesoreria@europalaica.org
----------------------
Gracias por tu colaboración en la lucha por el Laicismo
Un saludo,
Tesorería Europa Laica				

Protección de datos personales: En Europa Laica cumplimos el Reglamento General de Protección de Datos (RGPD, 2016/679), aplicable a partir del 25/05/2018 en la Unión Europea. 
TUS DATOS PERSONALES NO SERÁN UTILIZADOS CON FINES AJENOS A EUROPA LAICA. Tienes derecho a conocer tus datos personales, modificarlos, cancelarlos y suprimirlos. 
Si quieres más información haz clic en el siguiente enlace: "?>
</textarea> 				
				<input type="text" class="sinborde" readonly 
					name="camposFormDatosElegidos[textoEmail][body14]"
					value="https://www.europalaica.com/usuarios/index.php?controlador=cEnlacesPie&accion=privacidad"
					size="75"
					maxlength="255"
					/>				
				<br /><br />		
				</fieldset>
			<!--************ Fin contenido del texto común email a envíar a socios **************-->	
		</fieldset>			

		<br /><br />	
				
  <!-- ******************* Inicio Año de la cuota a cobrar ******************  --> 						
		<fieldset>
			<p>
				<label><strong>AÑO DE LA CUOTA PENDIENTE DE PAGAR</strong></label>		
					<input type="text" readonly
												class="mostrar"			          
												name="camposFormDatosElegidos[anioCuotasElegido]"
												value='<?php echo date("Y");?>'
												size="4"
												maxlength="4"
					/> 
			</p>
		</fieldset>						
  <!-- ******************* Fin Año de la cuota a cobrar ******************  --> 
		<br />
				
  <!-- Inicio Excluir de la lista los que se dieron de alta después de la fecha --->				 
		<fieldset><legend><strong>Excluir del envío de emails a los socios/as que se dieron de alta a finales de año</strong></legend>
			<p>
			
				<span class="textoAzu112Left2">			
					- Se pueden excluir de la lista a las/los socias/os que se dieron de alta cerca del final de año <strong> <?php echo date("Y");?></strong> 
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
										
					<span class="error">
					<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
					if (isset($camposFormDatosElegidos['fechaAltaExentosPago']['errorMensaje']))
					{echo "<strong>".$camposFormDatosElegidos['fechaAltaExentosPago']['errorMensaje']."</strong>";}
					?>
				</span>
				
				</p>
		</fieldset>
			<br /><br />	
  <!--Fin Excluir de la lista los que se dieron de alta después de la fecha ---->
				

  <!--Inicio Elegir opción de tipo cuenta banco ---->
		<fieldset><legend><strong>Elegir opción tipo cuenta banco del socio/a</strong></legend>
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
	    Al enviar los emails con el contenido personalizado para cada socio/a, el proceso requiere bastante tiempo de trabajo en el servidor, y más tiempo cuantos más emails se envíen a la vez. 					
					Por eso es aconsejable fraccionar el envío, enviándolo por separado a las agrupaciones con más socios/as (Madrid, Valencia, Granada, Asturias, Sevilla, ...)
				</span>	
						<br />
				<span class="error">							
					<?php //echo "<br>parValorAnio:";print_r($parValorAnio);
					if (isset($camposFormDatosElegidos['agrupaciones']['errorMensaje']))
					{echo "<br /><strong>"		.$camposFormDatosElegidos['agrupaciones']['errorMensaje']."</strong>";}
					?>
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
  <!--Fin Elegir agrupaciones territoriales ---->	
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
		<input type="submit" name="SiEnviar" value="Enviar emails">
		&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
		<input type="submit" name="NoEnviar" value="Cancelar"> 	
		
	</form>
	<!-- ************************* Fin selección  ************ -->	
 </div>
</div>		
