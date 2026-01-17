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


LLAMADA: vistas/tesorero/vModificarIngresoDonacionIncTes.php desde cTesorero.php:modificarIngresoDonacionTes()
y previamente desde el formulario:vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES: Acciones-> MODIFICA (icono pluma))

LLAMA: vistas/tesorero/formModificarIngresoDonacionTes.php
e incluye plantillasGrales

OBSERVACIONES: 

2022 cambios en "CONCEPTO" en tabla DONACION. Ahora las distintas opciones de "$parValoresDonacionConceptos" 
están y vienen de la tabla "DONACIONCONCEPTOS" con valores como "COSTAS-MEDALLA-VIRGEN-MERITO-POLICIAL", 
VIII-CONGRESO-AILP-MADRID-2022, y otros que se puedan añadir mas adelante.
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