<!-- **************************** Inicio scriptPayPalPagoCuotaElegidaAhora.php *********************
FICHERO: scriptPayPalPagoCuotaElegidaAhora.php
PROYECTO: EL
Agustin 2018-01-04: - Añado línea 192 con logo+texto pago cuota, y  <input type="hidden" name="tax_rate" value="0.000">  
- Comento líneas DEPRECATED 95, 96
- Comentario en LLAMADO mediante: ./vistas/socios/vPagarCuotaSocioInc.php

VERSION: PHP 7.3.21
DESCRIPCION: Contiene el script de pago de pagar cuota elegida por el socio con PayPal

La versión defintiva de explotación del form:
<form action='https://www.paypal.com/cgi-bin/webscr' name='formTpv' method='post'>

-- En el script hay que cambiar la línea de prueba versión prueba SANDBOX:  
<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' name='formTpv' method='post'>

En la versión de prueba para trabajar con SANDBOX: 
que no se paga realmente, se pueden ver los ingresos ficticios en https://www.sandbox.paypal.com
Usuario: prueba1@europalaica.com
Contraseña: 2001la..mo
A veces da problemas para entrar

LLAMADO: controladorSocios.php:AltaSocio(),confirmarAltaSocio(), 
        	mediante: ./vistas/socios/vPagarCuotaSocioInc.php
									
									controladorSocios.php:pagarCuotaSocio() 
									mediante: ./vistas/socios/vPagarCuotaSocioInc.php
									
									controladorSocios.php:pagarCuotaSocioSinCC() 
									mediante: que a su vez es llamado desde link en email recibido por el socio sin CC 
									          modeloEmail.php: emailAvisarCuotaSinCobrarSinCC() y 
									          que es enviado por el tesorero recordando que no ha abonado la cuota
																			./vistas/socios/vPagarCuotaSocioInc.php
									
RECIBE: el array $datosSocioPayPal	que contiene todos los valores que se necesitan para el formulario personalizado								

OBSERVACIONES: Este script no necesita actualizarse en PayPal cada año porque el dato del 
               importe de la cuota anual se recibe de la BBDD para cada socio el suyo
															
NOTA: Desde la función del controladorSocios.php de llamada, además de las variables concretas del socio y cuota a pagar
      se pasan las propias del script que para poder elegir si es de cobro real o de prueba: 
     
					<input type='hidden' name='business' value="<php echo $datosSocioPayPal['business']; >">	
									
					 REAL COBRO: $datosSocioPayPal['business']='tesoreria@europalaica.org'
						PRUEBA CON SANDOX NO COBRA: $datosSocioPayPal['business']='prueba1@europalaica.com'
							
     <form action="<php echo $datosSocioPayPal['action']; >" name='formTpv' method='post'> 
											
      REAL COBRO: $datosSocioPayPal['action'] = 'https://www.paypal.com/cgi-bin/webscr'
						PRUEBA CON SANDOX NO COBRA: $datosSocioPayPal['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr'

*************************************************************************************************-->

<div id="registro"><!-- ******** Inicio <div id="registro"> Incluye todo ********** -->
<?php	//echo "scriptPayPalPagoCuotaElegidaAhora:datosSocioPayPal: "; print_r($datosSocioPayPal); ?>
 <div align="center">
	      
 <!--PRUEBA CON SANDOX NO COBRA	<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' name='formTpv' method='post' > --> 
	<!--REAL COBRA <form action='https://www.paypal.com/cgi-bin/webscr' name='formTpv' method='post'> -->
	
	<form action="<?php echo $datosSocioPayPal['action']; ?>" name='formTpv' method='post'> 
	 
  <input type='hidden' name='cmd' value='_xclick'>
		
  <!-- PARA PRUEBA NO COBRA<input type='hidden' name='business' value='prueba1@europalaica.com'> --> 
		<!-- PARA REAL COBRA<input type='hidden' name='business' value='tesoreria@europalaica.com'> 2025 ahora es value='tesoreria@europalaica.org'	-->
		<!-- NO HAY QUE PONER NADA PORQUE SE RECIBE EN $datosSocioPayPal['business'] -->
							
		<input type='hidden' name='business' value="<?php echo $datosSocioPayPal['business']; ?>">
  <!-- 2025 será: <input type='hidden' name='business' value='tesoreria@europalaica.org'> -->		
		
  <!-- <input type='hidden' name='item_name' value='PAGO CUOTA ANUAL EUROPA LAICA &nbsp;&nbsp;&nbsp;&nbsp;'> -->
		<input type='hidden' name='item_name' value="<?php echo $datosSocioPayPal['item_name']; ?>">
		
  <!-- <input type='hidden' name='item_number' value="CUOTA "> -->
  <input type='hidden' name='item_number' value="<?php echo $datosSocioPayPal['item_number']; ?>">
		
  <!-- <input type='hidden' name='amount' value='2.15'> -->		
	 <input type='hidden' name='amount' value="<?php echo $datosSocioPayPal['faltaPagar']; ?>">
		
  <!--  <input type="hidden" name="tax" value="1"> sería el total de iva en euros -->
  <input type="hidden" name="tax_rate" value="0.000">  
		
  <input type='hidden' name='no_shipping' value='1'>
		
  <!-- Función de la aplicación de gestión de socios, a donde vuelve cuando se ha REALIZADO CORRECTAMENTE el pago  -->
  <input type='hidden' name='return' value="<?php echo $datosSocioPayPal['returnPagado']; ?>"> 
		
  <!-- Función de la aplicación donde vuelve cuando se ha CANCELADO el pago  -->
  <input type='hidden' name='cancel_return' value="<?php echo $datosSocioPayPal['returnCancelado']; ?>"> 
		
 		
  <!-- <input type='hidden' name='no_note' value='1'> permitir poner una nota a los compradores -->
  <!-- <input type='hidden' name='no_note' value='0'>		 DEPRECATED EN 2017 -->
		<!-- <input type='hidden' name='cn' value='PP-BuyNowBF'> DEPRECATED EN 2017 -->
		
  <input type='hidden' name='currency_code' value='EUR'>
		
		<input type="hidden" name="charset" value="utf-8">
		
		<input type='hidden' name='rm' value='2'> <!-- Los valores son devueltos en variables $_POST a la aplicación de gestión de socios como $POST-->
	
  <!--  Este valor será devuelto a la aplicación de gestión de socios como $POST, <input type='hidden' name='custom' value='Retorno_Custom'> -->		
  <input type='hidden' name='custom' value="<?php 
																																													 if (isset($datosSocioPayPal['CODUSER']))
																																												  {  echo $datosSocioPayPal['CODUSER'];
																																													 }
																																													?>">
 
  <input type='hidden' name='first_name' value="<?php echo $datosSocioPayPal['NOM'];?>">
  <input type='hidden' name='last_name' 
		  value="<?php echo $datosSocioPayPal['APE1'];
				
				         if (isset($datosSocioPayPal['APE2']))
	            {  echo " ".$datosSocioPayPal['APE2'];}				
				       ?>">
  <input type='hidden' name='address1' value="<?php 
																																															 if (isset($datosSocioPayPal['DIRECCION']))
																																														  {  echo $datosSocioPayPal['DIRECCION'];
																																															 }
																																															?>">
  <input type='hidden' name='city' value="<?php 
																																												if (isset($datosSocioPayPal['LOCALIDAD']))
																																											 {  echo $datosSocioPayPal['LOCALIDAD'];
																																												}
																																										?>">

  <input type='hidden' name='zip' value="<?php 
																																											if (isset($datosSocioPayPal['CP']))
																																										 {  echo $datosSocioPayPal['CP'];
																																											}
																																										?>">
		
  <input type="hidden" name="state" value="<?php 
																																													if (isset($datosSocioPayPal['NOMPROVINCIA']))
																																												 {  echo $datosSocioPayPal['NOMPROVINCIA'];
																																													}
																																												?>">
				
  <input type='hidden' name='country' value="<?php 
																																															if (isset($datosSocioPayPal['CODPAISDOM']))
																																														 {  echo $datosSocioPayPal['CODPAISDOM'];
																																															}
																																												?>">	
		
		<!-- Optional The area code for U.S. phone numbers, or the country code for phone numbers outside the U.S. 
					    PayPal fills in the buyer's home phone number automatically. codigo tel pais =ES-> 34 -->	
									
  <!-- <input type='hidden' name='night_phone_a' value="34"> -->
			
  <input type='hidden' name='night_phone_a' value=
																																													"<?php 
																																														if (isset($datosSocioPayPal['CODPAISDOM']) && $datosSocioPayPal['CODPAISDOM']=='ES')
																																													 {  echo '34';
																																														}
																																												?>">	
			
  <!-- Optional The three-digit prefix for U.S. phone numbers, or the entire phone number for phone numbers outside the U.S.,
	 excluding country code. PayPal fills in the buyer's home phone number automatically. -->
		
  <input type='hidden' name='night_phone_b' 
			 	value="<?php  
		          if (isset($datosSocioPayPal['TELMOVIL']))
	           {  echo $datosSocioPayPal['TELMOVIL'];
		 									}
												elseif (isset($datosSocioPayPal['TELFIJOCASA']))
	           {  echo $datosSocioPayPal['TELFIJOCASA'];
		 									}
          ?>">
		 
			<!-- Optional The four-digit phone number for U.S. phone numbers. 
			PayPal fills in the buyer's home phone number automatically.   -->
  <input type='hidden' name='night_phone_c' value=''>
		
  <input type='hidden' name='email' value="<?php 
																																													if (isset($datosSocioPayPal['EMAIL']))
																																												 {  echo $datosSocioPayPal['EMAIL'];
																																													}
																																												?>">
		
  <input type='hidden' name='lc' value='es'>
		
		<!--
				<input type="image" src="https://www.sandbox.paypal.com/es_ES/ES/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">  
				<img alt="" border="0" src="https://www.sandbox.paypal.com/es_ES/i/scr/pixel.gif" width="1" height="1">
		-->
  <input type="image" src="https://www.paypal.com/es_ES/ES/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
  
		<img alt="" border="0" src="https://www.paypal.com/es_ES/i/scr/pixel.gif" width="1" height="1">
		
		<!-- <input type='hidden' name='page_style' value='primary'> DEPRECATED EN 2017 se susituye por la siguiente -->
  <input type="hidden" name="image_url" value="https://www.europalaica.com/usuarios/vistas/images/EL_cab_PayPal_PagoCuota_429_64.jpg">	<!-- Logo Europa Laica -->

	 <br /><br />			
	</form>
	
	<!-- esto sería para que lleve a paypal sin tener que pulsar el botón	
	<script type='text/javascript'>
	    document.formTpv.submit();
	</script>
	-->
		

 </div><!--**************************** Fin scriptPayPalPagoCuotaElegidaAhora.php ****************-->		
	
</div><!-- ******** Fin <div id="registro"> Incluye todo ********** -->	