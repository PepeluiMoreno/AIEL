<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vMostrarEstadoConfirSociosInc.php
VERSION: PHP 7.3.21

Se forma y muestra una tabla con el estado de confirmación del alta, datos de contacto y otros
de los socios para las diferentes situaciones en 

"Pendientes confirmar alta por socio/a":
"alta-sin-password-gestor"=>"Altas por gestor sin confirmar email por socio/a",
"alta-sin-password-excel"=>"Altas antiguos socio/as aún sin confirmar email",
"pendiente_confirmar_algo"=>"Todos los pendientes de alguna confirmación",
"alta_por_socio_confirmada"=>"Altas ya confirmadas por socio/a",
"alta_por_gestor_confirmada"=>"Altas por gestor ya confirmado email por socio/a

Al final de la tabla según el estado de confirmación permite  las siguientes "Acciones":
- Reenviar email	
- Confirmar soci@	
- Borrar pendiente confirmar

LLAMADA: cPresidente.php:mostrarEstadoConfirmacionSocios() 
y previamente desde Menú izquierdo: Confirmación socios/as 

LLAMA: vistas/presidente/vCuerpoMostrarEstadoConfirSocios.php e incluye plantillasGrales. 

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
-----------------------------------------------------------------------------------------------------*/
function vMostrarEstadoConfirSociosInc($tituloSeccion,$enlacesFuncionRolSeccId,$resDatosSocios,$datosFormElegirApeEstadoConf)
{
	
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
 
  require_once './vistas/presidente/vCuerpoMostrarEstadoConfirSocios.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>