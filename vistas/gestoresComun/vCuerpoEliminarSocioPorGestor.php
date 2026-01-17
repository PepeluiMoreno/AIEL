<!--***************************** Inicio vCuerpoEliminarSocioPorGestor *************
FICHERO: vCuerpoBajaEliminarPorGestor.php
Agustín: 17-04-16 añadida para rol ..... eliminarSocioTes() 
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de pregunta de
             eliminar Socio en la BBDD.
OBSERVACIONES:Se le llama desde vEliminarSocioPorGestorInc.php
*******************************************************************************-->
<?php
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
    DAR DE BAJA AL SOCIO/A Y BORRAR SUS DATOS PERSONALES (ACCIÓN IRREVERSIBLE)	  	
</h3>		
<!--*** Inicio formEliminarSocioPorGestor (se podría pasar com parámetro)*******-->
<?php 

require_once './vistas/gestoresComun/formEliminarSocioPorGestor.php'; ?>

<!--********************  Fin formEliminarSocioPorGestor ***********************-->
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpoEliminarSocioPorGestor.php ***************-->