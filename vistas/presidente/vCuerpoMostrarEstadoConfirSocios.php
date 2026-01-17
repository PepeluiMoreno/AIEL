<?php
/******************************************* Inicio vCuerpoMostrarEstadoConfirSocios.php ************
FICHERO: vCuerpoMostrarEstadoConfirSocios.php
VERSION: PHP 7.3.21

Se forma y muestra una tabla con el estado de confirmación del alta, datos de contacto y otros
de los socios para las diferentes situaciones en 

"Pendientes confirmar alta por socio/a":
"alta-sin-password-gestor"=>"Altas por gestor sin confirmar email por socio/a",
"alta-sin-password-excel"=>"Altas antiguos socio/as aún sin confirmar email",
"pendiente_confirmar_algo"=>"Todos los pendientes de alguna confirmación",
"alta_por_socio_confirmada"=>"Altas ya confirmadas por socio/a",
"alta_por_gestor_confirmada"=>"Altas por gestor ya confirmado email por socio/a

Al final de la tabla según el estado de confirmación permite  las siguientes "Acciones":
- Reenviar email	
- Confirmar soci@	
- Borrar pendiente confirmar


LLAMADA: vistas/presidente/vMostrarEstadoConfirSocios.php
y previamente desde cPresidente.php:mostrarEstadoConfirmacionSocios()

LLAMA: vistas/presidente/formMostrarEstadoConfirSocios.php e incluye plantillasGrales. 

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
*******************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
	
/************************** Inicio Cuerpo central **************************/

echo $resDatosSocios['navegacion']['cadNavegEnlaces'];

?>

<br /><br />

<h3 align="center">

    ESTADO DE CONFIRMACIÓN DE ALTA DE SOCIOS/AS	
</h3>		 

<?php require_once './vistas/presidente/formMostrarEstadoConfirSocios.php'; ?>

<!-- ********************  Inicio form botón anterior******************** --> 		
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
    <br /><br />			
</div>
 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoMostrarEstadoConfirSocios************* -->