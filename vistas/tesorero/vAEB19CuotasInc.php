<?php
//session_start();
/*-----------------------------------------------------------------------------
FICHERO: vAEB19CuotasInc.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION:Muestra el formulario de selección para la formación del fichero 
            AEB19Cuotas para pasar al banco Santander mpara el cobro de cuotas  
OBSERVACIONES:Llamado desde cTesorero.php: AEB19CuotasTesoreroSantander(),
------------------------------------------------------------------------------*/
function vAEB19CuotasInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$resValidarCamposFormAEB19)
{
 if ($_SESSION['vs_autentificado']!=='SI')
 { 
   header('Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin');
 }
 else
 {
	 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoAEB19Cuotas.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
 }
}
?>