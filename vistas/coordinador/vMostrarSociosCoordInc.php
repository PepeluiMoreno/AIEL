<?php
//session_start();
/*-----------------------------------------------------------------------------
FICHERO: vMostrarSociosCoordInc.php
PROYECTO: EL
VERSION: PHP 7.3.19
DESCRIPCION: Contiene los require_once necesarios para formar la página que  
muestra las tablas "LISTA DE SOCI@S" con algunos campos de información
de los socios de esa agrupación territorial (en realidad es el área de gestión, 
que casi siempre se corresponde con una sóla agrupación territorial, pero en 
algunos caso incluye más de una agrup.) 
También permite buscar por apellidos de socios.
  
OBSERVACIONES:Llamado desde cCoordinador.php: mostrarSociosCoord(),
El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario

2020-08-05: quito session_start() y comprobación de comprobación de 
$_SESSION['vs_autentificado'] porque ya están en el controlador cCoordinador.php
------------------------------------------------------------------------------*/
function vMostrarSociosCoordInc($tituloSeccion,$enlacesFuncionRolSeccId,
                                $resDatosSocios,$parValorComboAgrupaSocio,$nomAgrupCoord,$datosFormMiembro)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/coordinador/vCuerpoMostrarSociosCoord.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';
 
}
?>