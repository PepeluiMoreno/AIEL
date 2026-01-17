<?php
/***************************** Inicio Cuerpo Eliminar socio *********************************************
FICHERO: vCuerpovEliminarSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para darse de baja por el propio socio. 
             Permite (opcionamente) introducir los comentarios del socio al 
													darse de baja. Muestra algunos datos del socio.
													
LLAMADA: vistas/socios/vEliminarSocioInc.php y a su vez de controladorSocios.php:eliminarSocio()
LLAMA: vistas/socios/formEliminarSocio.php
													
OBSERVACIONES: Probado PHP 7.3.21
*******************************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central ************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br />	
<h3 align="center">
    DARSE DE BAJA COMO SOCIA/O  	
</h3>		
<!--*** Inicio formEliminarSocio (se podría pasar com parámetro)*******-->
<?php require_once './vistas/socios/formEliminarSocio.php'; ?>
<!--********************  Fin formEliminarSocio ***********************-->
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpoEliminarSocio******************************************-->