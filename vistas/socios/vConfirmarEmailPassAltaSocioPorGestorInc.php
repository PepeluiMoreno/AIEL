<?php
/*------------------------------------------------------------------------------
FICHERO: vConfirmarEmailPassAltaSocioPorGestorInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para para que el usuario elija la contraseña.
y recibe $restablecerPass['datosFormUsuario']['CODUSER'], que contiene el 
"$codUserEncriptado" por seguridad para enviarlo como parámetro. 

Contiene los includes necesarios para formar del formulario para
que el usuario elija la contraseña. 

En /vistas/socios/vCuerpoConfirmarEmailPassAltaSocioPorGestor.php, está 
"vContent.php", que contiene menú idz de "Secciones" que en este caso aún no lo
mostrará hasta que entre después de elegir la contraseña y entre en la aplicación
													
LLAMADA: desde controladorSocios.php:confirmarEmailPassAltaSocioPorGestor()
						 		que a su vez es llamado desde el email recibido por el usuario, 
							 	al ser dado de alta por un gestor 

OBSERVACIONES:    
------------------------------------------------------------------------------*/
function  vConfirmarEmailPassAltaSocioPorGestorInc($tituloSeccion,$restablecerPass)
{
			require_once './vistas/plantillasGrales/vCabeceraSalir.php';

			require_once  './vistas/socios/vCuerpoConfirmarEmailPassAltaSocioPorGestor.php';
	 
			require_once './vistas/plantillasGrales/vPieFinal.php';
	}
?>