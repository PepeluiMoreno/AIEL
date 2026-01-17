<?php
/*-----------------------------------------------------------------------------
FICHERO: vFormMensajeAltaSocioDatosBancos.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Se muestran los datos bancarios (si los hay) y la cuota de un socio y otra información
           y se le indica los modos de pagar la cuota anual (transferencia, metálico,e incluye
											cuentas IBAN de EL 
											 y script a enlace a PayPal con datos personalizado, cuota elegida, nombre, etc.) 
OBSERVACIONES:Es incluida desde "vCuerpoMensajeAltaSocioDatosBancos.php"
              mediante require_once './vistas/socios/vFormMensajeAltaSocioDatosBancos.php'
-------------------------------------------------------------------------------*/
//require_once './modelos/libs/comboLista.php';//este no es necesario aquí
?>

<div id="registro">
		<!-- ********************** Inicio datos bancos y enlace a PayPal  ***************** -->		
			<fieldset>
		  <legend><strong>Modos de pagar tu cuota anual a la asociación Europa Laica</strong></legend>
			<p>           
		<?php if (isset($datosSocio['datosFormCuotaSocio']['faltaPagar']) && 
		 $datosSocio['datosFormCuotaSocio']['faltaPagar']!==0)
  { 
		?>
		 <span class="textoGris8Left2">			
			
			<?php 
			 if (isset($datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'])
							         && !empty($datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo']))				
		  {echo "Como tienes domiciliado el pago de la cuota en tu cuenta: ",
				 $datosSocio['datosFormSocio']['CUENTAIBAN']['valorCampo'], 
				 ", Europa Laica efectuará la orden de cobro de tu cuota anual en tu cuenta, 
					SOLO SI AUN NO ESTUVIESE ABONADA, y previamente te avisaremos por email
					<br /><br />- También puedes pagar mediante ingreso directo o transferencia	a:";
				}
			 else 
			 {
			 	echo "<br />- Puedes pagar mediante ingreso directo o transferencia	a:";
			 }
		 ?>		
				<br />
		<!--Incio $cadBancos incluye los datos de la asociación EL siempre actualizados ya que que se leen de la BBDD -->
				
				<strong>
				<?php //Imprime las cuentas bancarias de pago de cuotas de la asociación, o de la agrupación si esta gestiona los cobros 
				
				 echo $cadBancos;			
			
				?>
				</strong>
				<br /><br />		
				Señala como concepto: Pago cuota a Europa Laica, NIF y nombre y apellidos.		
		  <br /><br /> <br />
		
		<!--Fin $cadBancos incluye los datos de la asociación EL siempre actualizados ya que que se leen de la BBDD -->
			
	
		<!--  Llamada al script de PayPal que incluye un pago personalizado para cada socio según cuota elegida -->
				
				- O bien puedes pagar ahora con tarjeta de crédito (o si tienes una cuenta de PayPal), mediante pago con <strong>PayPal</strong>.		
					<br /><br />
					
					Para pagar ahora con PayPal, haz clic en "Pagar ahora"			 
			<?php //require_once './vistas/plantillasGrales/scriptPayPalPagarAhora.php'; 
		
			  if (isset($payPalScript) && !empty($payPalScript))
				 {require_once $payPalScript; 
					}
			?>
			 </span>				
	 <span class="textoGris8Left2">		
	   Por si hubiese algún problema, nos puedes confirmar tu pago enviándo un correo electrónico 
				a <b>tesoreria@europalaica.com</b> 
				con asunto: Cuota, y dentro del mensaje los datos: NIF, nombre y apellidos, cantidad, fecha pago y entidad dónde has pagado.
	  <br />
	  </span>	
	<!-- Fin llamada al script de PayPal que incluye un pago personalizado para cada socio según cuota elegida -->
	
		<?php 
		}
		else
		{
		?>
  <span class="textoAzu112Left2">
	  - Tu cuota anual de <strong><?php echo $datosSocio['datosFormCuotaSocio']['ANIOCUOTA']['valorCampo']?></strong> 
			  ya está pagada, si quieres hacer una donación elige la opción de "Hacer una donación en el menú izquierdo" 
  <br /> <br />
  </span>		
		<?php		
		}
		?>	
			</p>
		 </fieldset>				
		<!-- ********************** Fin datos bancos y enlace a PayPal  ***************** -->		
		<br />
		
		<!-- ******************** Inicio Datos de MIEMBRO ********************************* -->
	 <fieldset>	 
	  <legend><b>Datos personales</b></legend>	
		<p>
	   <label>Nombre</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormMiembro[NOM]"
	           value='<?php if (isset($datosSocio['datosFormMiembro']['NOM']['valorCampo']))
	           {  echo $datosSocio['datosFormMiembro']['NOM']['valorCampo'];}
	           ?>'
	           size="35"
	           maxlength="100"
	    />	
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormMiembro[APE1]"
	           value='<?php if (isset($datosSocio['datosFormMiembro']['APE1']['valorCampo']))
	           {  echo $datosSocio['datosFormMiembro']['APE1']['valorCampo'];}
	           ?>'
	           size="32"
	           maxlength="100"
	    />	
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormMiembro[APE2]"
	           value='<?php if (isset($datosSocio['datosFormMiembro']['APE2']['valorCampo']))
	                 {  echo $datosSocio['datosFormMiembro']['APE2']['valorCampo'];}
	                  ?>'
	           size="32"
	           maxlength="100"
	    />						 					
		<br /> 		
		<label>Documento</label>		
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormMiembro[TIPODOCUMENTOMIEMBRO]"
	           value='<?php if (isset($datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo']))
	           {  echo $datosSocio['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];}
	           ?>'
	           size="9"
	           maxlength="20"
	    />			 
	 <label>Nº documento</label> 
	    <input type="text" readonly
											 class="mostrar"		
	           name="datosFormMiembro[NUMDOCUMENTOMIEMBRO]"
	           value='<?php if (isset($datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo']))
	           {  echo $datosSocio['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];}
	           ?>'
	           size="20"
	           maxlength="40"
	    />
	 <label>País documento</label>
			 <input type="text" readonly
									  class="mostrar"	
														     
			        name="datosFormMiembro[CODPAISDOC]"
			        value='<?php if ($datosSocio['datosFormMiembro']['CODPAISDOC']['valorCampo'])
			        {  echo $datosSocio['datosFormMiembro']['CODPAISDOC']['valorCampo'];}
			        ?>'											
			        size="30"
			        maxlength="50"
	     />	
		 <br /><br />	
		<label>Correo electrónico</label>
	    <input type="text" readonly
						      class="mostrar"		
	           name="datosFormMiembro[EMAIL]"
	           value='<?php if (isset($datosSocio['datosFormMiembro']['EMAIL']['valorCampo']))
	           {  echo $datosSocio['datosFormMiembro']['EMAIL']['valorCampo'];}
	           ?>'
	           size="60"
	           maxlength="200"
	    /> 
	  <br />	 
		</p>
	 </fieldset>	
	 <!-- ********************** Fin Datos de identificación MIEMBRO *************** -->
</div>

<!--  <div align="center">
  <form method="post" action="./index.php?controlador=cGestion&amp;accion=mostrarSocios">     
		  <input type="submit" name="ConfirmarSalir" value="Volver">
	</form>
 </div>
	-->	
		<!-- ******************* Inicio Form botón submit ******************** -->		
			<?php 
/*			if(isset($_SERVER['HTTP_REFERER'])) 
			{
	    echo "<BR><BR>_SERVER['HTTP_REFERER']:".$_SERVER['HTTP_REFERER'];
					$cadOriginal=$_SERVER['HTTP_REFERER'];
					$cadEliminar="http://www.europalaica.com/usuarios/";
					$cadAction=str_replace($cadEliminar, "",$cadOriginal);
					echo "<BR><BR>cadAction:".$cadAction;
					$botonSubmit['textoBoton']="volver";
			}	
			*/	?>		
		<!-- ********************  Fin Form botón submit  ************************* --> 
	