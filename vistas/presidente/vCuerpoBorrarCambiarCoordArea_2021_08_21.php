<?php
/******************* Inicio vCuerpoBorrarCambiarCoordArea.php ****************************************
FICHERO: vCuerpoBorrarCambiarCoordArea.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario que muestra algunos datos personales, buscados previamente, de un socio que ya tiene
un área de coordinación asignada.

Mediante tres botones: "Cambiar el área asignada", "Eliminar asignación ", y para "Cancelar" 
se puede retirarle o cambiarle una coordinación de área territorial,

LLAMADA: vistas/presidente/vBorrarCambiarCoordAreaSocioInc.php
y previamente desde cPresidente.php:asignarCoordinacionAreaBuscar()

LLAMA: vistas/presidente/formBorrarCambiarCoordArea.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	

<br /><br />		

<h3 align="center">
    ELIMINAR O CAMBIAR ÁREA DE COORDINACIÓN TERRITORIAL A UN COORDINADOR/A 	
</h3>		

<?php require_once './vistas/presidente/formBorrarCambiarCoordArea.php'; ?> 

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoBorrarCambiarCoordArea.php ***************-->