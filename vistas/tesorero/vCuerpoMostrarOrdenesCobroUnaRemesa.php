<?php
/*********************** Inicio vCuerpoMostrarOrdenesCobroUnaRemesa.php ***********
FICHERO: vCuerpoMostrarOrdenesCobroUnaRemesa.php
PROYECTO: EL
VERSION: PHP 7.3.19

Se forma y muestra una tabla-lista páginada "MOSTRAR ÓRDENES COBRO DE UNA REMESA" 
con los detalles de las órdenes de cobro individuales correspondientes a una remesa
concreta (pendiente de enviar, enviada o actualizada). Y un Link "Ver" para ver los 
datos del socio corresnpondiente a la orden de cobro.
Se mostrarán y ordenadas por APELLIDOS.  

Incluye un botón para elegir, ESTADO CUOTA (por defecto el Todos) y AGRUPACION,
y otro botón para elegir por APE1, APE2.

LLAMADA: vistas/tesorero/vMostrarOrdenesCobroUnaRemesaInc.php 
         que a su vez se llama desde cTesorero:mostrarOrdenesCobroUnaRemesaTes()
LLAMA: vistas/tesorero/formMostrarOrdenesCobroUnaRemesa.php 

OBSERVACIONES:     
*********************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/************************** Inicio Cuerpo central **************************/

echo $resCuotasSocios['navegacion']['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">
    MOSTRAR ÓRDENES COBRO DE UNA REMESA 	
</h3>		 

<?php require_once './vistas/tesorero/formMostrarOrdenesCobroUnaRemesa.php'; ?>
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
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- *********************** Fin vCuerpoMostrarOrdenesCobroUnaRemesa.php *********** -->