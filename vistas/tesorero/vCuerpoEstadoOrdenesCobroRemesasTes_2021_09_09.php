<?php
/************************* Inicio vCuerpoEstadoOrdenesCobroRemesasTes.php  *************
FICHERO: vEstadoOrdenesCobroRemesasTesInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que muestra datos de las remesas de órdenes de cobros 
emitidas a los bancos a partir de las tablas REMESAS_SEPAXML y ORDENES_COBRO.
												
Desde este formulario hay enlaces para ir a: 
- Ver (Icono Lupa)la lista de órdenes de cobro de una remesa,
- Eliminar la remesa de las tablas REMESAS_SEPAXML y ORDENES_COBRO en caso de se hubiese 
  producido un	error o por otras causas, siempre que aún no se hayan actualizado los pagos
  de la remesa  
- Descargar el Archivo SEPA de remesa para subirlo a la web del B. Santander		
- Actualizar los pagos de una remesa en la tabla la tabla "CUOTAANIOSOCIO" 

LLAMADA: vistas/tesorero/vEstadoOrdenesCobroRemesasTesInc.php
LLAMA: vistas/tesorero/formEstadoOrdenesCobroRemesasTes.php
												
OBSERVACIONES: 
2020-12-10: Añado columna para descargar Archivo SEPA-XML 
2017-08-19: creación
**************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';
	
/************************** Inicio Cuerpo central **************************/

  echo $navegacion['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">
				ESTADO DE LAS REMESAS	Y ÓRDENES DE COBRO DE LAS CUOTAS DOMICILIADAS
				<br /><br />
<span class="textoAzul9Center">	
				(Los datos proceden de las tablas "REMESAS_SEPAXML" y "ORDENES_COBRO" )
</span>
				
</h3>

<?php require_once './vistas/tesorero/formEstadoOrdenesCobroRemesasTes.php'; ?>

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
<!-- *********************** Fin vCuerpoEstadoOrdenesCobroRemesasTes.php **************** -->