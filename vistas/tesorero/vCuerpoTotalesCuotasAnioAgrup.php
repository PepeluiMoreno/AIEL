<?php
/****************************** Inicio vCuerpoTotalesCuotasAnioAgrup.php *********************************
FICHERO: vCuerpoTotalesCuotasAnioAgrup.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario que muestra una tabla "TOTALES CUOTAS POR AGRUPACIONES" 
de un año concreto, hasta la fecha actual.
Ordenadas de modo creciente por nombre de agrupación. 
Detalles en las columnas, incluido las sumas las cuotas pagadas y pendientes de los socios, y otros.  

En la última fila de la tabla, se mostrarán los totales del año correspondiente 

LLAMADA: vistas/tesorero/vTotalesCuotasAnioAgrup.php 
y previamente desde cTesorero.php:mostrarTotalesCuotasAnioAgrup()

LLAMA: vistas/tesorero/formTotalesCuotasAnioAgrup.php
e incluye plantillasGrales

OBSERVACIONES:
**********************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central ****************************/

echo $totalesPagosCuotaAgrupAnio['navegacion']['cadNavegEnlaces'];

?>
<br /><br />
<h3 align="center">
    TOTALES PAGOS CUOTAS POR AGRUPACIONES 
    <br />										
</h3>		 

<?php require_once './vistas/tesorero/formTotalesCuotasAnioAgrup.php'; ?>
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
    <div align="center">
        <br />	
        <?php
        if (isset($totalesPagosCuotaAgrupAnio['navegacion']['anterior'])) {
            echo $totalesPagosCuotaAgrupAnio['navegacion']['anterior'];
        }
        ?>	
        <br />
    </div>				
    <br /><br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- *********************** Fin vCuerpoTotalesCuotas.php ******* -->