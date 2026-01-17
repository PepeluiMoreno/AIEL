<?php
/******************* Inicio vCuerpoModificarIngresoDonacionTes.php *****************************************
FICHERO: vCuerpoModificarIngresoDonacionTes.php'
VERSION: PHP 7.3.21

DESCRIPCION:
Se crea el formulario para modificar una donación previa anotada en la tabla DONACION, pero solo los 
se podría modificar datos referentes a los pagos cantidad, gastos, y no los datos personales NIF, email,

Solo se permite modificar donaciones de año anterior y actual. (Por eso condición enero año anterior al actual)

Sólo se usará la cuando después de anotar una donación se comprueba que se ha cometido un error que exige 
una rectificación. 

LLAMADA: cTesorero.php:vistas/tesorero/vAnotarIngresoDonacion.php
y previamente desde el formulario:vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES: Acciones-> MODIFICA (icono pluma))

LLAMA: vistas/tesorero/formModificarIngresoDonacionTes.php
e incluye plantillasGrales

OBSERVACIONES: 

2018_10_03: cambio CONCEPTO que era textarea, para poner un selector que incluya "GENERAL", 
"COSTAS_MEDALLA_VIRGEN_MERITO_POLICIAL", "Otros", acasomas adelente añadir una nueva tablaCONCEPTO_DONACION.
***********************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/**************************** Inicio Cuerpo central ***************************/

if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />
<h3 align="center">	
    MODIFICAR DATOS DE UNA DONACIÓN	
</h3>	 

<?php require_once './vistas/tesorero/formModificarIngresoDonacionTes.php'; ?>

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- *************** Fin Cuerpo vCuerpoModificarIngresoDonacionTes.php *********** -->