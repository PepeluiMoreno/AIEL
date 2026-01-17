
<!-- ************** OJO SOLO PARA PRUEBAS Inicio scriptPayPalDonaAhoraSANDBOX.php ******************
Agustin 2018_01_24: Añado el logo de EL: EL_cab_PayPal_Donacion_340_64.jpg
FICHERO: scriptPayPalDonaAhora.php  (*** NO COBRA ***)

PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene el script de PayPal de DONAR './vistas/PayPal/scriptPayPalDonaAhoraSANBOX.php'
 Incluye el botón tipo "donar" creado en PayPal en la creación del botón se incluyó el logo EL y las 
	direcciones de retorno de pago efectuado y cancelado. El botón está identificado dentro de Paypal 
	con los siguientes datos :
					DONACION A EUROPA LAICA
					Id: ACJLSBNNQKDSC
					modificado: 24/01/2018
					
En este scritp, se incluyen inputs de variables para asignar valores del logo EL y las 
direcciones de retorno de pago efectuado y cancelado, y en caso de que no fuesen los mismos, 
los valores de contenidos en este script, son los que se aplican.

LLAMADO: desde  ./vistas/socios/vDonarSocioInc.php

OBSERVACIONES: Este script en PayPal aparece con 0 euros de cantidad a donar, 
               y sin ningún dato personal del donante. El socio lo tendrá que rellenar. 
															
NOTA: Este botón, sin los inputs que modifican variables, es el mismo que se envía en los emails de 
petición de donaciones a socios, para ello es suficiente con insertar en el texto del email el 
siguiente link:	https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ACJLSBNNQKDSC
													
************************************************************************************************ -->
<div id="registro"><!-- ******** Inicio <div id="registro"> Incluye todo ********** -->
	
	<div align="center">

		<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">

			<input type="hidden" name="cmd" value="_s-xclick">
			
   <!-- identificador del botón correspondiente creado dentro de SANDBOXPayPal para donaciones -->				
			<input type="hidden" name="hosted_button_id" value="ACJLSBNNQKDSC">
			
   <!-- Función de la aplicación de gestión de socios, a donde vuelve cuando se ha REALIZADO CORRECTAMENTE el pago 
			 aquí no admite recibir el link mediante variables PHP y echo cosa que si admite en formulario de pago  -->
		 <input type='hidden' name='return' value="https://www.europalaica.com/usuarios_produccion/index.php?controlador=cPayPal&accion=confirmarDonacionPayPal"> 
		
   <!-- Función de la aplicación donde vuelve cuando se ha CANCELADO el pago  -->
   <input type='hidden' name='cancel_return' value="https://www.europalaica.com/usuarios_produccion/index.php?controlador=cPayPal&accion=cancelacionDonacionPayPal">			

			<input type="image" src="https://www.sandbox.paypal.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal, la forma rápida y segura de pagar en Internet.">

			<img alt="" border="0" src="https://www.sandbox.paypal.com/es_ES/i/scr/pixel.gif" width="1" height="1">

			<input type="hidden" name="rm" value="2">

			<input type="hidden" name="image_url" value="https://www.europalaica.com/usuarios/vistas/images/EL_cab_PayPal_203_90.gif">	<!-- Logo Europa Laica -->							

		</form>
	
 </div>
	
</div><!-- ******** Fin <div id="registro"> Incluye todo ********** -->	

