<?php
/*---------------------------------------------------------------------------------------
FICHERO: vConfirmarSocioInc.php
VERSION: PHP 7.3.21

DESCRIPCION: formulario que pide confirmar o anular el alta de un 
socio (pendiente de confirmar), a petición del mismo, desde el link que recibió al registrase 
como nuevo socio. 
Desde el formulario según la elección se llamará a:
-controladorSocios:confirmarAltaSocio() 
-controladorSocios:anularAltaSocioPendienteConfirmar()

Si se elige confirmar, se llama a la función controladorSocios:confirmarAltaSocio()
Si se elige anular, se controladorSocios:anularAltaSocioPendienteConfirmar()

RECIBE: el array $datosSocioConfirmar con los datos del socio a confirmar o anular su alta
incluye el codUserEncriptado.

								
LLAMADA: controladorSocios.php:confirmarAnularAltaSocio()

LLAMA: vistas/socios/vCuerpoConfirmarSocio.php
e incluye las plantillas

OBSERVACIONES:  Probado PHP 7.3.21
----------------------------------------------------------------------------------------*/
function vConfirmarSocioInc($tituloSeccion,$datosSocioConfirmar,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/socios/vCuerpoConfirmarSocio.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>