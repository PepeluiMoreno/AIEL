<?php
session_start();
/*-----------------------------------------------------------------------------
FICHERO: vMenuExportarCuotasDonaTesoreroInc.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Muesta las opciones del submenú de generar ficheros de ordenes de pago cuotas
             para AEB19 Santander, Excel Triodos, 
													Tambien el fichero de email de aviso y Excel de cuotas y donaciociones
             Permite elegir cuotas o donaciones 
OBSERVACIONES:Llamado desde cTesorero.php: menuExportarCuotasDonaTesorero(),
------------------------------------------------------------------------------*/
//function vExcelMenuTesoreroInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion)
function vMenuExportarCuotasDonaTesoreroInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion)
{
 if ($_SESSION['vs_autentificado']!=='SI')
 { //echo '<br />Debe entrar por la página de login';
   header('Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin');
 }
 else
 {require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoMenuExportarCuotasDonaTesorero.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
 }
}
?>