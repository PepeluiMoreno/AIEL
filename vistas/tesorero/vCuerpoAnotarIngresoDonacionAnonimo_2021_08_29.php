<?php
/******************* Inicio vCuerpoAnotarIngresoDonacionAnonimo.php *******************************
FICHERO: vCuerpoAnotarIngresoDonacionAnonimo.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para el anotar el ingreso de una donación realizada por donante donante ANONIMO 
en el que se añaden los datos de la donación (Donación €, Gastos, Fecha, modo pago, concepto) 
pero ninguno dato personal

LLAMADA: vistas/tesorero/vAnotarIngresoDonacionAnonimoInc.php
y previamente desde cTesorero.php:anotarIngresoDonacionMenu o cTesorero.php:anotarIngresoDonacion()

LLAMA: vistas/tesorero/formAnotarIngresoDonacionAnonimo.php
e incluye plantillasGrales

OBSERVACIONES: 

2018_10_03: cambio CONCEPTO que era textarea, para poner un selector que  incluya "GENERAL", 
"COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL", "Otros", acaso 	mas adelente añadir una nueva tabla CONCEPTO_DONACION.
**************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central ***************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	

<br /><br />
<h3 align="center">	
    ANOTAR DONACIONES 	
</h3>	 


<?php require_once './vistas/tesorero/formAnotarIngresoDonacionAnonimo.php'; ?>

<!-- ********************  Fin vCuerpoAnotarIngresoDonacionAnonimo.php *********************** -->
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin Cuerpo vCuerpoAnotarIngresoDonacionAnonimo.php*********** -->