<?php
/****************************** Inicio vCuerpoTotalesCuotas.php ************************************
FICHERO: vCuerpoTotalesCuotas.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
Se forma y muestra una tabla "TOTALES CUOTAS SOCIOS" con el resumen de los totales de las cuotas 
pagadas y pendientes de los socios, y deglosadas en otros detalles, hasta la fecha actual.
 
Orden decreciente por años. 
Desde la última columna de la tabla "lupa", se podrá llamar a un función, para ver para cada año 
los totales pagos cuotas por cada agrupación 

LLAMADA: vistas/tesorero/vTotalesCuotasInc.php 
y a su vez desde vistas/tesorero/vMostrarIngresosCuotas.php con botón "Totales pagos cuotas por años" 

LLAMA: vistas/tesorero/formTotalesCuotas.php
e incluye plantillasGrales

OBSERVACIONES:
***************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/**************************** Inicio Cuerpo central ***************************/

echo $totalesAniosPagosCuota['navegacion']['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">
    TOTALES PAGOS CUOTAS POR AÑOS
    <br />	 	
    <!-- ******************** Inicio informa fecha ***************** -->
				
    <span class='textoAzulClaro8L'><?php echo "Fecha: ";echo date("d-m-Y"); ?> </span>
				
    <br /><br />
    <!-- ******************** Fin informa. fecha ***************** -->							
</h3>		

<?php require_once './vistas/tesorero/formTotalesCuotas.php'; ?>
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
        if (isset($totalesAniosPagosCuota['navegacion']['anterior'])) {
            echo $totalesAniosPagosCuota['navegacion']['anterior'];
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