
<!-- **************************** Inicio scriptPayPalDonaAhora.php ************
Agustin 2018_01_18: Añado el logo de EL: EL_cab_PayPal_Donacion_340_64.jpg
FICHERO: scriptPayPalDonaAhora.php  (REAL SI COBRA)

PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene el script de PayPal de DONAR './vistas/PayPal/scriptPayPalDonaAhora.php';
LLAMADO: desde  ./vistas/socios/vDonarSocioInc.php

OBSERVACIONES: Este script en PayPal aparece con 0 euros de cantidad a donar, 
               y sin ningún dato personal del donante. El socio lo tendrá que rellenar. 
              
************************************************************************************-->

<div id="registro"><!-- ******** Inicio <div id="registro"> Incluye todo ********** -->
	
	<div align="center">
				<!-- 			

  	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
			   <input type="hidden" name="hosted_button_id" value="GPE5JQDQ48X4J"> 
			 --> 
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">

			<input type="hidden" name="cmd" value="_s-xclick">	
			
   <input type="hidden" name="rm" value="2">			
			
			<input type="hidden" name="hosted_button_id" value="GPE5JQDQ48X4J"> 
			
			<input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" 
			       alt="PayPal. La forma rápida y segura de pagar en Internet.">
			
			<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
			
			<input type="hidden" name="image_url" value="https://www.europalaica.com/usuarios/vistas/images/EL_cab_PayPal_203_90.gif">	<!-- Logo Europa Laica -->

  </form>		

	
</div><!--**************************** Fin scriptPayPalDonaAhora.php ****************-->		
	
</div><!-- ******** Fin <div id="registro"> Incluye todo ********** -->	

