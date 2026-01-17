<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: vConfirAltaSocioPendientePorGestorInc.php
VERSION: PHP 7.3.21

DESCRIPCION: 
En esta función se confirma el alta de un socio (aún "PENDIENTE-CONFIRMACION" su alta por el mismo),
por un gestor autorizado (presidencia,vice,secretaría,teorería), normalmente después de contacto 
con el socio y este le solicita a un gestor que le confirme el alta (email, teléfono, etc.) 
y entonces el gestor le confirma el alta ocn esta función.

Al confirmar el alta, se insertarán en todas las tablas que correspondan los datos del socio, 
se eliminan físicamente de SOCIOSCONFIRMAR para que no salga también duplicado en mostrar pendientes,
y otras posibles búsquedas y USUARIO.ESTADO ='alta'
 
Se enviará un email al socio y también a secretaria, tesoreria, coordinador y presidencia para comunicar el alta.

LLAMADA: cPresidente.php:confirmarAltaSocioPendientePorGestor()
y previamente desde Menú izquierdo: Confirmación socios/as 

LLAMA: vistas/presidente/vCuerpoConfirAltaSocioPendientePorGestor.php e incluye plantillasGrales. 

OBSERVACIONES: 
-----------------------------------------------------------------------------------------------------*/
function vConfirAltaSocioPendientePorGestorInc($tituloSeccion,$datSocioPendienteConfirmar,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';
	 
  require_once './vistas/presidente/vCuerpoConfirAltaSocioPendientePorGestor.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
		
}
?>