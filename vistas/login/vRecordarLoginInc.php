<?php
/*-----------------------------------------------------------------------------
FICHERO: vRecordarLoginInc.php
PROYECTO: EL
VERSION: php 7.3.19
DESCRIPCION: Contiene los includes necesarios para introducir el email del usuario
             para recibir un correo con los datos de usuario y/o restablecer contraseña
LLAMADO:	controladorLogin.php:recordarLogin() y desde validarLogin() en caso de que sobrepase
         6 intentos de login												
OBSERVACIONES: 
2022-12-22 cambio usuario/a por usuario
------------------------------------------------------------------------------*/
function  vRecordarLoginInc($textoPrimero,$recordarPassUser)
{ 
	  require_once './vistas/plantillasGrales/vCabeceraInicial.php';

	  require_once  './vistas/login/vCuerpoRecordarLogin.php';

	  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>