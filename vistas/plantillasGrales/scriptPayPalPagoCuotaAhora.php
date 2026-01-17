
<!-- **************************** Inicio scriptPayPalPagoCuotaAhora.php ************
FICHERO: scriptPayPalPagoCuotaAhora.php
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
		
<!-- inicio pago paypal bueno *************************************************************** -->
<!-- ojo es necesario añadir  target="_blank" para que paypal se abra en una nueva ventana -->
<!--Inicio 2013-->
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"  target="_blank">
<!--<form action="https://www.paypal.com/cgi-bin/webscr" method="post">-->
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="SUV5J7UXLNK8Y">
<table>
 <tr>
  <td>
		 <input type="hidden" name="on0" value="pagar"><span class="textoGris8Left2"><strong>Pagar con PayPal ahora</strong></span>
	 </td>
	</tr>
	<tr>
	 <td>
		 <select name="os0">
				<option value="Cuota 40 + 0:">40 cuota + &nbsp;0 donación. Total: 40,00 EUR</option>
				<option value="Cuota 40 + 20:">40 cuota + 20 donación. Total: 60,00 EUR</option>
				<option value="Cuota 40 + 40:">40 cuota + 40 donación. Total: 80,00 EUR</option>
				<option value="Cuota 40 + 60:">40 cuota + 60 donación. Total:100,00 EUR</option>
   </select>
	 </td>
	</tr>
</table>
<input type="hidden" name="currency_code" value="EUR">
<input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
</form>
<!--Fin  2013-->

<!--	Inicio 2012
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"  target="_blank">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="SUV5J7UXLNK8Y">
<table>
 <tr>
  <td>
   <input type="hidden" name="on0" value="pagar"><span class="textoGris8Left2"><b>Pagar con PayPal ahora</b></span>
  </td>
	</tr>
	<tr>
	 <td>
			<select name="os0">
				<option value="Cuota 30 + 0:">Cuota 30 + 0: €30,00 EUR</option>
				<option value="Cuota 30 + 20:">Cuota 30 + 20: €50,00 EUR</option>
				<option value="Cuota 30 + 45:">Cuota 30 + 45: €75,00 EUR</option>
				<option value="Cuota 30 + 70:">Cuota 30 + 70: €100,00 EUR</option>
			</select> 
		</td>
 </tr>
</table>
<input type="hidden" name="currency_code" value="EUR">
<input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
</form>
Fin 2012 -->
<!-- fin pago paypal bueno *************************************************************** -->

<!-- inicio pruebas paypal  con pagos pequeños ***********************************			
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"  target="_blank">

<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="TAX7MVFCPMFSU">
<table>
<tr><td><input type="hidden" name="on0" value="pagar">
<span class="textoGris8Left2"><b>Pagar con PayPal ahora</b></span>
</td>
</tr>
<tr>
<td>
<select name="os0">
	<option value="Cuota 30 + 0:">Cuota 30 + 0: €0,13 EUR</option>
	<option value="Cuota 30 + 25:">Cuota 30 + 25: €0,15 EUR</option>
	<option value="Cuota 30 + 45:">Cuota 30 + 45: €0,17 EUR</option>
	<option value="Cuota 30 + 70:">Cuota 30 + 70: €0,10 EUR</option>
</select>
 </td>
	</tr>
</table>
<input type="hidden" name="currency_code" value="EUR">
<input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
</form>

 ****** fin pruebas paypal  con pagos pequeños*************************************** -->		
	
</div><!--**************************** Fin scriptPayPalPagoCuotaAhora.php ****************-->		
	
</div><!-- ******** Fin <div id="registro"> Incluye todo ********** -->	

