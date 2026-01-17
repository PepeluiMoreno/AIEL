<?php
/*-------------------------------------------------------------------------------------------------
FICHERO: vPagarCuotaSocioInc.php 
VERSION: PHP 7.3.21

DESCRIPCION: En el formulario, si la cuota anual NO está pagada, se muestran la cuota del socio
             los datos bancarios (si los hay), y otra información del socio y se le indica 
													los modos de pagar la cuota anual:
           - Se muestran las cuentas bancarias de donde se cobran a las distintas agrupaciones, 
											  se leen de las tablas de AGRUPACIONTERRITORIAL (a fecha 01_08_2021 todas menos Asturias
													están centralizadas y comparten la misma cuenta bancaria, Asturias muestra su cuenta) 
           - Además hay un botón de enlace a PayPal (a fecha 01_08_2021 todas menos Asturias), 
											  donde ya se incluye la cantidad a pagar y demás datos del socio. 

           Si la cuota anual ya está pagada se indica y se ofrece la opción de hacer una donación		

RECIBE: $datosSocioPayPal (contiene datos del socio, cuentas IBAN agrupación y script PayPal), 
y arrayParamMensaje [textoCabecera] => PAGAR CUOTA ANUAL DEL SOCIO/A y 
[textoComentarios] => Cuota anual del socio/a año: aaaa

LLAMADA: controladoSocios:altaSocio(), confirmarAltaSocio(), pagarCuotaSocio()
LLAMA: vistas/socios/vCuerpoPagarCuotaSocio.php
y incluye plantillas generales 

OBSERVACIONES:              
------------------------------------------------------------------------------------------------*/
function vPagarCuotaSocioInc($tituloSeccion,$arrayParamMensaje,$datosSocioPayPal,$navegacion)                        
{
  //echo "<br><br>1 vistas/socios:vPagarCuotaSocioIn:datosSocioPayPal: ";print_r($datosSocioPayPal);
		//echo "<br><br>2 vistas/socios:vPagarCuotaSocioIn:arrayParamMensaje: ";print_r($arrayParamMensaje);

  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
		
  require_once './vistas/socios/vCuerpoPagarCuotaSocio.php';
		
  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>