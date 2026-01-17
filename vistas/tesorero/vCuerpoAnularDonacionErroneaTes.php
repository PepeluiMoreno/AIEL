<?php
/**************** Inicio vCuerpoAnularDonacionErroneaTes ********************************************
FICHERO: vCuerpoAnularDonacionErroneaTes.php
VERSION: probada PHP 7.3.21	

DESCRIPCION: 
Formulario sólo para casos de error: Se anulan  algunos campos de la fila correspondiente a una donación
previa anotada en la tabla DONACION, ya que es un error o se ha introducido duplicada.

El formulario muestra los datos de una donación y preguntar si se quiere anular o no esa donación

El tesoreo puede introducir comentarios en el campo OBSERVACIONES, pero no valida el contenido del campo

LLAMADA: vistas/tesorero/vAnularDonacionErroneaIncTes.php y previamente desde el formulario:
vMostrarDonacionesInc.php (LISTADO DE LAS DONACIONES: Acciones-> ELIMINAR (icono papelera))

LLAMA: vistas/tesorero/formAnularDonacionErroneaTes.php
e incluye plantillasGrales

OBSERVACIONES: 
***************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />
	
<h3 align="center">
    ELIMINAR UNA DONACIÓN ERRÓNEA 	
</h3>
<br />		 

<?php require_once './vistas/tesorero/formAnularDonacionErroneaTes.php'; ?>

 <!-- ********************  Inicio form botón anterior******************** --> 		
	<div align="center">
					<br />	
					<?php
					if (isset($navegacion['anterior'])) 
					{
									echo $navegacion['anterior'];
					}
					?>	
					<br />
	</div>		
	
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--*********************** Fin  vCuerpoAnularDonacionErroneaTes ***********-->