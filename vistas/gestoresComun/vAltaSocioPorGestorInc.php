<?php
/*-----------------------------------------------------------------------------
FICHERO: vAltaSocioPorGestorInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para recoger los datos necesarios para dar de alta a un 
socio por un gestor en "altaSocioPorGestor()"
También se sube un archivo al servidor con la firma de autorización del socio 
como garantía de protección de datos hasta que el socio se de de baja.
	
LLAMADA: /controladores/libs/altaSocioPorGestor.php, que a su vez es llamado desde
cCoordinador.php:altaSocioPorGestorCoord(),cPresidente.php:altaSocioPorGestorPres(), 
cTesorero.php:altaSocioPorGestorTes()		

LLAMA:vistas/plantillasGrales:vCabeceraSalir.php, vPieFinal.php
vistas/gestoresComun/vCuerpoAltaSocioPorGestor.php: que incluye "formAltaSocioPorGestor.php"

OBSERVACIONES: 
2020-09-10: hago modificaciones para que este formulario pueda ser común para las 
altas de socios por los distintos tipos de gestores
------------------------------------------------------------------------------*/
function vAltaSocioPorGestorInc($tituloSeccion,$enlacesFuncionRolSeccId,$datosNavegacion,$datosSocio,$parValorComboAltaSocio)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	
  $parValorComboAgrupaSocio   = $parValorComboAltaSocio['agrupaSocio'];
  $parValorComboPaisMiembro   = $parValorComboAltaSocio['miembroPais'];
  $parValorComboPaisDomicilio = $parValorComboAltaSocio['domicilioPais']; 
	
  require_once './vistas/gestoresComun/vCuerpoAltaSocioPorGestor.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>