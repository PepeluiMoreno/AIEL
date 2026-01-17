
<!-- **************************** Inicio scriptPayPalDonaAhoraSANDBOX.php ************
Agustin 2018_01_22: Añado el logo de EL: EL_cab_PayPal_Donacion_340_64.jpg
FICHERO: scriptPayPalDonaAhoraSANDBOX.php (PARA PRUEBAS NO COBRA)
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene el script de PayPal de DONAR './vistas/PayPal/scriptPayPalDonaAhoraSANDBOX.php';
LLAMADO: desde  ./vistas/socios/vDonarSocioInc.php

En la versión de prueba para trabajar con SANDBOX: 
que no se paga realmente, se pueden ver los ingresos ficticios en https://www.sandbox.paypal.com
Usuario: prueba1@europalaica.com
Contraseña: la de tesorería
A veces da problemas para entrar

OBSERVACIONES: Este script en PayPal aparece con 0 euros de cantidad a donar, 
               y sin ningún dato personal del donante. El socio lo tendrá que rellenar. 
              
************************************************************************************-->
<div id="registro"><!-- ******** Inicio <div id="registro"> Incluye todo ********** -->
	
	<div align="center">

		<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
		
		 <input type="hidden" name="cmd" value="_s-xclick">
			
   <input type="hidden" name="rm" value="2">						
			
		 <input type="hidden" name="hosted_button_id" value="XQWPH5ZXXRSYQ">
			
		 <input type="image" src="https://www.sandbox.paypal.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" 
			       alt="PayPal. La forma rápida y segura de pagar en Internet.">
										
		 <img alt="" border="0" src="https://www.sandbox.paypal.com/es_ES/i/scr/pixel.gif" width="1" height="1">
			
   <input type="hidden" name="image_url" value="https://www.europalaica.com/usuarios/vistas/images/EL_cab_PayPal_203_90.gif">	<!-- Logo Europa Laica -->			
			
		</form>
		
<!-- ****** fin pruebas paypal  con pagos pequeños*************************************** -->		
	
 </div><!--**************************** Fin scriptPayPalDonaAhoraSANDBOX.php ****************-->		
	
</div><!-- ******** Fin <div id="registro"> Incluye todo ********** -->	

