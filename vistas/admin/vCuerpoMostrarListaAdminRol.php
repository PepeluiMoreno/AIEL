<?php
/**************** Inicio vCuerpoMostrarListaAdminRol.php *********************************
FICHERO: vCuerpoMostrarListaAdminRol.php
VERSION: PHP 7.3.21

DESCRIPCION: Se forma y muestra una tabla con la lista actual de socios con el rol 
de Administracióny sus datos personales 

RECIBE: el array $datosSociosAdministracionRol, que procede de la búsqueda en la función:
modelos/modeloPresCoord.php:buscarDatosGestoresRoles($codRol)

LLAMADA: vistas/admin/vMostrarListaAdminRolInc.php
y previamente desde  cAdmin.php:mostrarListaAdministracionRol(),

LLAMA: vistas/admin/formMostrarListaAdminRol.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.

*************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central *************************/

echo $datosSociosAdministracionRol['navegacion']['cadNavegEnlaces'];

?>

<br /><br />

<h3 align="center">
    LISTA DE SOCIAS/OS CON ROL DE ADMINISTRACIÓN	
				<br /><br />
				
    <!-- ******************** Inicio informa fecha ***************** --> 
    <span class='textoAzulClaro8L'>
				<?php echo "Fecha: ";echo date("d-m-Y"); ?> </span>

    <!-- ******************** Fin informa. fecha ***************** -->			
</h3>

<?php require_once './vistas/admin/formMostrarListaAdminRol.php'; ?>

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
<!-- *********************** Fin vCuerpoMostrarListaAdminRol.php *********** -->