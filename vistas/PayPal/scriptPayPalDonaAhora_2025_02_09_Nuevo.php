
<!-- **************************** Inicio scriptPayPalDonaAhora.php *********************************
Agustin 2025_02_09: fue necesario crear un nuevo botón por haber cambiado la cuenta de PayPal en el año 2024.
FICHERO: scriptPayPalDonaAhora.php  (REAL SI COBRA)

PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene el script de PayPal de DONAR './vistas/PayPal/scriptPayPalDonaAhora.php';
 Incluye el botón tipo "donar" creado en PayPal en la creación del botón se incluyó el logo EL y las 
	direcciones de retorno de pago efectuado y cancelado. El botón está identificado dentro de Paypal 
	con los siguientes datos :
					DONACION A EUROPA LAICA
					Id: 6VNLBGKNR4D3L
					modificado: 09/02/2025
					
En este scritp, se incluyen inputs de variables para asignar valores del logo EL y las 
direcciones de retorno de pago efectuado y cancelado, y en caso de que no fuesen los mismos, 
los valores de contenidos en este script, son los que se aplican.


Función de la aplicación de gestión de socios, a donde vuelve cuando se ha REALIZADO CORRECTAMENTE el pago 
"cPayPal&accion:confirmarDonacionPayPal()" 
aquí no admite recibir el link mediante variables PHP y echo cosa que si admite en formulario de pago de cuota:
<!--<input type='hidden' name='return' value="https://www.europalaica.com/usuarios/index.php?controlador=cPayPal&accion=confirmarDonacionPayPal"> 

Función de la aplicación donde vuelve cuando se ha CANCELADO el pago: cPayPal&accion=cancelacionDonacionPayPal&resultado=cancelado">
<!--<input type='hidden' name='cancel_return' value="https://www.europalaica.com/usuarios/index.php?controlador=cPayPal&accion=cancelacionDonacionPayPal&resultado=cancelado">	

LLAMADO: desde  ./vistas/socios/vDonarSocioInc.php

OBSERVACIONES: Este script en PayPal aparece con 0 euros de cantidad a donar, 
               y sin ningún dato personal del donante. El socio lo tendrá que rellenar. 
															
NOTA: Este botón, sin los inputs que modifican variables, es el mismo que se envía en los emails de 
petición de donaciones a socios, para ello es suficiente con insertar en el texto del email el 
siguiente link:	https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CAWS7MBNXU6GL (Antiguo no funciona)

Nuevo link para envíar en email de petición donaciones: https://www.paypal.com/donate/?hosted_button_id=6VNLBGKNR4D3L	

Fue necesario crear un nuevo botón por haber cambiado la cuenta de PayPal en el año 2024.
													
************************************************************************************************ -->

<div id="registro"><!-- ******** Inicio <div id="registro"> Incluye todo ********** -->

	
	<div align="center">
	
		<form action="https://www.paypal.com/donate" method="post" target="_top">

		<input type="hidden" name="hosted_button_id" value="6VNLBGKNR4D3L" />

		<input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - para pagar online!" alt="Botón Donar con PayPal" />

		<img alt="" border="0" src="https://www.paypal.com/es_ES/i/scr/pixel.gif" width="1" height="1" />
		
		</form>

	</div>

</div><!-- ******** Fin <div id="registro"> Incluye todo ********** -->	

