<?php
/*-----------------------------------------------------------------------------
FICHERO: vInfTotalesCuotasInc.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene los includes necesarios para formar la página de información
             de los contenidos de cada columno de totales de la coutas

OBSERVACIONES: Es llamado desde las vistas de formTotalesCuotas.php que se lllama desde
               "cTesorero: mostrarTotalesCuotas()"
------------------------------------------------------------------------------*/
function  vInfTotalesCuotasInc()
{
			include './vistas/plantillasGrales/vCabeceraBlank.php';

	  include  './vistas/tesorero/vCuerpoInfTotalesCuotas.php';

	  include './vistas/plantillasGrales/vPieFinal.php';
	}
?>