<?php
/*-----------------------------------------------------------------------------
FICHERO: vRestablecerPassInc.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene los includes necesarios para formar del formulario para
											  restablecer la contraseña del usuario. 
													Es llamado desde el email recibido por el usuario, por la petición 
													de recordar contraseña.
OBSERVACIONES: 
------------------------------------------------------------------------------*/
function  vRestablecerPassInc($tituloSeccion,$restablecerPass)
{
	  include './vistas/plantillasGrales/vCabeceraSalir.php';

	  include  './vistas/login/vCuerpoRestablecerPass.php';
	 
	  include './vistas/plantillasGrales/vPieFinal.php';
	}
?>