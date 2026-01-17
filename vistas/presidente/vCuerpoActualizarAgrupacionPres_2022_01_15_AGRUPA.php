<?php
/******************* Inicio vCuerpoActualizarAgrupacionPres ********************************************
FICHERO: vCuerpoActualizarAgrupacionPres.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario en el que se muestran los datos de de una agrupación procedentes de la tabla "AGRUPACIONTERRITORIAL" 
para permitir modificar algunos de ellos. 
Los datos CIF, CUENTAAGRUPIBAN, TELFIJOTRABAJO, TELMOV,  se validan previamente 

RECIBE: array arrDatosAgrupacion con los datos de una agrupación de tabla "AGRUPACIONTERRITORIAL" 
y $navegación

LLAMADA: cPresidente.php:vActualizarAgrupacionPres.php()

LLAMA: vistas/presidente/formActualizarAgrupacionPres.php
incluye plantillasGrales

OBSERVACIONES:
******************************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';
?>

<!--************************ Inicio Cuerpo central ************************-->
<?php
if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />	
<h3 align="center">
    ACTUALIZAR DATOS AGRUPACIÓN TERRITORIAL
</h3>
<br />		 

<?php require_once './vistas/presidente/formActualizarAgrupacionPres.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpoActualizarAgrupacionPres **************-->