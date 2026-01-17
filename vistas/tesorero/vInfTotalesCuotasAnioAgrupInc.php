<?php
/*-----------------------------------------------------------------------------
FICHERO: vInfTotalesCuotasAnioAgrupInc.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene los includes necesarios para formar la página de información
             de los contenidos de cada columno de totales de la coutas

OBSERVACIONES: Es llamado "cTesorero:infTotalesCuotasAnioAgrup()" 
               A petición del link desde la vista de formTotalesCuotas.php que 
															se la llama desde "cTesorero: mostrarTotalesCuotasAnioAgrup()"
------------------------------------------------------------------------------*/
function  vInfTotalesCuotasAnioAgrupInc()
{
			include './vistas/plantillasGrales/vCabeceraBlank.php';

	  include  './vistas/tesorero/vCuerpoInfTotalesCuotasAnioAgrup.php';

	  include './vistas/plantillasGrales/vPieFinal.php';
	}
?>