<?php
/**************** Inicio vCuerpoMostrarListaCoordinadores.php *********************************
FICHERO: vCuerpoMostrarListaCoordinadores.php
VERSION: PHP 7.3.21

DESCRIPCION: Se forma y muestra una tabla con la lista actual de socios con el rol 
de coordinación y datos relacionados

LLAMADA: vistas/presidente/vMostrarListaCoordinadores.php
y previamente desde  cPresidente.php:mostrarListaCoordinadores(),

LLAMA: vistas/presidente/formMostrarListaCoordinadores.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.

*************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central *************************/

echo $resDatosCoordinadores['navegacion']['cadNavegEnlaces'];

?>

<br /><br />

<h3 align="center">
    LISTA DE COORDINADORES/AS	  	
				<br /><br />
				
    <!-- ******************** Inicio informa fecha ***************** --> 
    <span class='textoAzulClaro8L'>
				<?php echo "Fecha: ";echo date("d-m-Y"); ?> </span>

    <!-- ******************** Fin informa. fecha ***************** -->			
</h3>

<?php require_once './vistas/presidente/formMostrarListaCoordinadores.php'; ?>

<br />
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
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->

</div>
<!-- *********************** Fin vCuerpoMostrarListaCoordinadores.php *********** -->