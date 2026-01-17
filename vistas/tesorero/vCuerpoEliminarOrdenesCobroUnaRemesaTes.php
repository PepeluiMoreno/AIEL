<?php
/********************** Inicio vCuerpoEliminarOrdenesCobroUnaRemesaTes.php  ********************************************
FICHERO: vCuerpoEliminarOrdenesCobroUnaRemesaTes.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para eliminar una remesa, y se muestran algunos campos de la tabla REMESAS_SEPAXML de esa remesa
y las filas correspondientes de ORDENES_COBRO correspondientes a esa remesa concreta.  

Entre otros se muestran los campos [NOMARCHIVOSEPAXML] y [DIRECTORIOARCHIVOREMESA] y también se devuelven en el $_POST 
para usar esos datos para eliminar esa remesa, que implicará anular las órdenes de cobro correspondientes 
anotadas previamente en las tablas REMESAS_SEPAXML y ORDENES_COBRO, y después quedarán igual que estaban antes de 
generar esa remesa.
También se eliminará del servidor el correspondiente archivo de orden de cobro de esa remesa, que fue generado para 
subirlo a la web del banco para su cobro, cuyo nombre indicamos más abajo.

Se avisa de que SÓLO SE DEBE ELIMIMAR si ese archivo aún no se envió a la web del Banco, o bien si después de enviarla 
fue cancelada, o no ejecutada la orden del cobro de esa remesa una vez pasada la fecha de orden de cobro por el banco 

Tiene unos botones para "Eliminar las órdenes cobro de remesa", y para "Cancelar"
LLAMADA: vEliminarOrdenesCobroUnaRemesaTes.php ()que vendrá de cTesorero.php:eliminarOrdenesCobroUnaRemesaTes()
LLAMA: formEliminarOrdenesCobroUnaRemesaTes.php 	

OBSERVACIONES:
***********************************************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';
	
/************************* Inicio Cuerpo central ***************************/

echo $navegacion['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">
    ELIMINAR UNA REMESA AÚN NO ENVIADA AL BANCO DE LAS TABLAS "REMESAS_SEPAXML" Y "ORDENES_COBRO"
				<br /><br />	

</h3>
<?php require_once './vistas/tesorero/formEliminarOrdenesCobroUnaRemesaTes.php'; ?>

<div align="center">
    <?php
    /* if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
      ($botonSubmit['enlaceBoton']!==NULL))
      { echo	"<form method='post' action=./".$botonSubmit['enlaceBoton'].">".
      " <input type='submit' value=".$botonSubmit['textoBoton'].">";
      echo " </form>";
      }
     */
    ?>		
    <br />		
    <?php
    if (isset($navegacion['anterior'])) {
        echo $navegacion['anterior'];
    }
    ?>				
    <br /><br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoEliminarOrdenesCobroUnaRemesaTes.php **************** -->