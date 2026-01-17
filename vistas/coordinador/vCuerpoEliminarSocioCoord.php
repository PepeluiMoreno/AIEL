<?php
/***************************** Inicio vCuerpovEliminarSocioCoord *******************************
FICHERO: vCuerpoEliminarSocioCoord.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para eliminar los datos de de un socio, por parte de gestor coordinador. 
             Previamente muestra algunos campos de datos y se puede introducir un comentario
													Incluye dos botones: "Baja socio/a"  y "Baja socio/a por fallecimiento y guardar su nombre"  
													Además de botón "No dar baja socio/a".
													
LLAMADA: vistas/coordinador/vEliminarSocioCoord.php 
que a su vez se llama desdecCoordinador.php.eliminarSocioCoord()

LLAMA: vistas/coordinador/formEliminarSocioCoord.php
Contiene menú idz de "Secciones"  

OBSERVACIONES:
*************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************ Inicio Cuerpo central ***************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />		
<h3 align="center">
    DAR DE BAJA AL SOCIO/A Y BORRAR SUS DATOS PERSONALES (ACCIÓN IRREVERSIBLE)	  	
</h3>		
<!--*** Inicio formEliminarSocioCoord (se podría pasar com parámetro)*******-->
<?php require_once './vistas/coordinador/formEliminarSocioCoord.php'; ?>
<!--********************  Fin formEliminarSocioPres ***********************-->
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpoEliminarSocioCoord ***************-->