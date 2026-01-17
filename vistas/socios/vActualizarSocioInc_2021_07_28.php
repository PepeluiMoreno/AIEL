<?php
/*-----------------------------------------------------------------------------
FICHERO: vActualizarSocioInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Se actualizan los datos de un socio por el propio socio en varias tablas  
(incluye a los propios gestores como socios). 

La parte de navegación se añade, para que cuando un socio gestor CODROL >2 
(presidente, coordinador, secretaria, tesoreria, etc....) accede a sus propios datos
mantenga la navegación, si no es gestor no se muestra ninguna barra.

RECIBE: $datSocio: array con los valores previos del socio o de datos como cuotas 
        de EL, etc.  Proceden de BBDD o los nuevos introducidos desde el formulario
								hasta que se graben en la BBDD o se descarten.
        $parValorComboActualizarSocio, array que contiene los valores previos de 
        'agrupaSocio','miembroPais','domicilioPais'

LLAMADA: controladorSocios.php:actualizarSocio()
LLAMA: vistas/socios/vCuerpoActualizarSocio.php 
e incluye las plantillas

OBSERVACIONES: Probado PHP 7.3.21
------------------------------------------------------------------------------*/
function vActualizarSocioInc($tituloSeccion,$datSocio,$parValorComboActualizarSocio,$navegacion)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  $parValorComboAgrupaSocio   = $parValorComboActualizarSocio['agrupaSocio'];
  $parValorComboPaisMiembro   = $parValorComboActualizarSocio['miembroPais'];
  $parValorComboPaisDomicilio = $parValorComboActualizarSocio['domicilioPais']; 
	
  require_once './vistas/socios/vCuerpoActualizarSocio.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>