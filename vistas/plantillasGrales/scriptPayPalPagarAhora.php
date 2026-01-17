
<!-- **************************** Inicio scriptPayPalPagarAhora.php ************
FICHERO: scriptPayPalPagarAhora.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene el script de pago de pagar cuota socio "Pago Ahora.php" 
LLAMADO: desde  ./vistas/socios/vCuerpoMensajeAltaSocioSolicitada.php      
OBSERVACIONES:Este script hay que actualizarlo en PayPal.es: 
->Servicios para vendedores->Botones comprar ahora->Vaya a mis botones guardados->
->Cuota anual 30E+(donación opcional)->Acción->Editar botón 
Hacer los cambios de precios de cuotas, y después pegar aquí (nota hay manualillo en Word) 
              
************************************************************************************-->
<div id="registro"><!-- ******** Inicio <div id="registro"> Incluye todo ********** -->
	
	<div align="center">
	

  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">

			<!--		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"> --> 

			<input type="hidden" name="cmd" value="_s-xclick">
			
			<input type="hidden" name="hosted_button_id" value="XC5YYPYBCLJZ4">
			
			<input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" 
			alt="PayPal. La forma rápida y segura de pagar en Internet.">
			<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">

  </form>


		
<!-- ****** fin pruebas paypal  con pagos pequeños*************************************** -->		
	
</div><!--**************************** Fin scriptPayPalPagarAhora.php ****************-->		
	
</div><!-- ******** Fin <div id="registro"> Incluye todo ********** -->	

