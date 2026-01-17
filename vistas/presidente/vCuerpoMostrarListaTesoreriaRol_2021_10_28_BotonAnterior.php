<?php
/**************** Inicio vCuerpoMostrarListaTesoreriaRol.php *********************************
FICHERO: vCuerpoMostrarListaTesoreriaRol.php
VERSION: PHP 7.3.21

DESCRIPCION: Se forma y muestra una tabla con la lista actual de socios con el rol 
de Tesoreria y sus datos personales 

LLAMADA: vistas/presidente/vMostrarListaTesoreriaRolInc.php
y previamente desde cPresidente.php:mostrarListaTesoreriaRol(),

LLAMA: vistas/presidente/formMostrarListaTesoreriaRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.

*************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central *************************/

echo $datosSociosTesoreriaRol['navegacion']['cadNavegEnlaces'];

?>

<br /><br />

<h3 align="center">
    LISTA DE SOCIAS/OS CON ROL DE TESORERÍA 	
				<br /><br />
				
    <!-- ******************** Inicio informa fecha ***************** --> 
    <span class='textoAzulClaro8L'>
				<?php echo "Fecha: ";echo date("d-m-Y"); ?> </span>

    <!-- ******************** Fin informa. fecha ***************** -->			
</h3>

<?php require_once './vistas/presidente/formMostrarListaTesoreriaRol.php'; ?>

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
<!-- *********************** Fin vCuerpoMostrarListaTesoreriaRol.php *********** -->