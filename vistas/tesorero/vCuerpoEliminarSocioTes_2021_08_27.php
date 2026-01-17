<?php
/***************************** Inicio  vCuerpovEliminarSocioTes *******************************
FICHERO: vCuerpoEliminarSocioTes.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario para eliminar los datos de un socio, por un gestor con rol Tesorería. 
Previamente muestra algunos campos de datos y se puede introducir un comentario

Incluye dos botones: "Baja socio/a"  y "Baja socio/a por fallecimiento y guardar su nombre"  
Además de botón "No dar baja socio/a".

Se muestran algunos datos del socio y en caso de que hubiese un archivo con la firma de un socio 
debido a alta por un gestor, también mostraría aquí para eliminaría el archivo del servidor. 									
													
LLAMADA: vistas/tesorero/vEliminarSocioTesInc.php
que a su vez se llama desde cPresidente.php.eliminarSocioPres()

LLAMA: vistas/tesorero/formEliminarSocioTes.php
y además incluye las plantillasGrales

OBSERVACIONES:
*************************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';

/************************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />	
	
<h3 align="center">
    DAR DE BAJA AL SOCIO/A Y BORRAR SUS DATOS PERSONALES (ACCIÓN IRREVERSIBLE)	  	
</h3>		


<?php require_once './vistas/tesorero/formEliminarSocioTes.php'; ?>

<!--********************  Fin formEliminarSocioTes ***********************-->
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpoEliminarSocioTes ***************-->