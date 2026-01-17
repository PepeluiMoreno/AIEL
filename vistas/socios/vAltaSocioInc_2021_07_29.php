<?php
/*------------------------------------------------------------------------------------------------
FICHERO: vAltaSocioInc.php
VERSION: PHP 7.3.21

DESCRIPCION: En este formulario Sse introducen los datos para registrarse un nuevo socio por el 
             propio socio, para posterioremente ser confirmado por el socio a parie del email recibido
													(o excepcionalmente por un gestor)  

RECIBE: $datosSocio: array con los valores de datos como tipos cuotas proceden de BBDD, 
        y otros por defecto, y los nuevos datos que será introducidos desde el formulario
								
        $parValorComboAltaSocio, array que contiene los valores previos de 
        'agrupaSocio','miembroPais','domicilioPais'
								
LLAMADA: controladorSocios.php:altaSocio()

LLAMA: vistas/socios/vCuerpoAltaSocio.php 
e incluye las plantillas

OBSERVACIONES:  Probado PHP 7.3.21
------------------------------------------------------------------------------*/
function vAltaSocioInc($tituloSeccion,$datosSocio,$parValorComboAltaSocio)											 
{ 
  require_once './vistas/plantillasGrales/vCabeceraInicial.php';
	
  $parValorComboAgrupaSocio  = $parValorComboAltaSocio['agrupaSocio'];
  $parValorComboPaisMiembro  = $parValorComboAltaSocio['miembroPais'];
  $parValorComboPaisDomicilio= $parValorComboAltaSocio['domicilioPais']; 
	
  require_once './vistas/socios/vCuerpoAltaSocio.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>