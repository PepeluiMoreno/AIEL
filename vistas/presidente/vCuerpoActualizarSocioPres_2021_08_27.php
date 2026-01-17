<?php
/******************* Inicio vCuerpovActualizarSocioPres ********************************************
FICHERO: vCuerpovActualizarSocioPres.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para actualizar datos personales de un socio, cuotas, IBAN, agrupación y afectará 
a varias varias tablas

LLAMADA: vistas/presidente/vActualizarSocioPresInc.php
y previamente desde cPresidente.php:actualizarSocioPres()

LLAMA: vistas/presidente/formActualizarSocioPres.php
incluye plantillasGrales

OBSERVACIONES:
****************************************************************************************************/
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
    ACTUALIZAR DATOS SOCIA/O	  	
</h3>
<br />		 

<?php require_once './vistas/presidente/formActualizarSocioPres.php'; ?>


</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpovActualizarSocioPres **************-->