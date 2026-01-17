<?php
/*-----------------------------------------------------------------------------
FICHERO: vLoginInc.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene los includes necesarios para formar la página inicial de
             login. 
													Recibe como parámetro "$texto1" un texto que se podría usar
             para alguna cabecera, y "$datosUsuario"
OBSERVACIONES: Es llamado desde las vistas de "controladorLogin.php,
              Función validarLogin()														
------------------------------------------------------------------------------*/
function vLoginInc($textoCuerpo,$datosUsuario)
{	
			//require_once './vistas/plantillasGrales/vCabeceraSalir.php';	
			require_once './vistas/plantillasGrales/vCabeceraInicial.php';	
			
	  require_once  './vistas/login/vCuerpoLogin.php';
	  
	  require_once './vistas/plantillasGrales/vPieFinal.php';
	}
?>