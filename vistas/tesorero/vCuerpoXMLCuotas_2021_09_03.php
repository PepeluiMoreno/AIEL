<?php
/****************************** Inicio vCuerpoXMLCuotas.php **************************************************
FICHERO: vCuerpoXMLCuotas.php
VERSION: PHP 7.3.21

DESCRIPCION: 
Formulario para introducir los datos necesarios para generar un archivo "SEPA_ISO20022CORE_fecha_orden_cobro.xml" 
para una remesa con las órdenes de cobro de cuotas domiciliadas para después descargarlo y subirlo a la web
del B. Santander.

A la vez se anotarán esas órdenes de pagos en tabla "ORDENES_COBRO", y en "REMESAS_SEPAXML" que posteriormente 
servirán para actualizar el campo		ESTADOCUOTA =ABONADA, en la tabla "CUOTAANIOASOCIO" una vez que el banco 
haya cobrado esa remesa. 

El formulario permite elegir: 
- Fecha cobro, Fecha excluir de orden de cobro a altas posteriores, 
- Cuenta bancaria España, o Cuenta bancaria países SEPA en Europa distintos de España (en este caso por la necesidad de
  BICs, actualmente no puede generar el archivo pero muestra un listado para incluilos en remesas manualmente)
- Agrupaciones Territariales seleccionadas				
- También incluye una grupo de datos fijos, relacionados con la cuenta del B. Santander, necesarios para generar 
  el archivo (están en el formulario como campos "readonly") 	
													
LLAMADA: vistas/tesorero/vXMLCuotasInc.php
LLAMA: vistas/tesorero/formXMLCuotas.php
Incluye plantillasGrales

OBSERVACIONES: 
***************************************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central ***************************/

echo $navegacion['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">
    <!-- EXPORTAR CUOTAS SOCIOS/AS A ARCHIVO SEPA XML PARA LAS ÓRDENES DE COBRO EN BANCO SANTANDER	-->
				
				GENERAR ARCHIVO "SEPA_ISO20022CORE_fecha.xml" DE CUOTAS DOMICILIADAS, PARA ENVÍO REMESA POR B.SANTANDER Y ANOTAR EN TABLA "ORDENES_COBRO"
</h3>

<?php require_once './vistas/tesorero/formXMLCuotas.php'; ?>

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
    if (isset($navegacion['anterior'])) 
				{
        echo $navegacion['anterior'];
    }
    ?>				
    <br /><br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoXMLCuotas.php **************** -->