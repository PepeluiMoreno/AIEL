<?php
session_start();
/*-----------------------------------------------------------------------------
FICHERO: vTemporizadorInc.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: 
OBSERVACIONES:Llamado desde cAdmin 
------------------------------------------------------------------------------*/
function vTemporizadorInc($tituloSecc,$enlacesSeccIzda,$navegacion)
{
 if ($_SESSION['vs_autentificado']!=='SI')
 { echo '<br />Debe entrar por la página de login';
   header('Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin');
 }
 else
 {
	 //echo '<br />Dentro de vCuerpoRolFuncionInc.php. Autentificado: ',$_SESSION['vs_autentificado'];
  require_once './vistas/plantillasGrales/vCabeceraInicialTemporizador.php';
  //echo "<br>dentro vCuerpoRolFuncionInc.php enlacesSeccId=";print_r($enlacesSeccId);
  require_once './vistas/plantillasGrales/vCuerpoTemporizador.php';
  //vCuerpoFuncionRol($tituloSecc,$enlacesSeccId,$navegacion);

  require_once './vistas/plantillasGrales/vPieFinal.php';
 }
}
?>