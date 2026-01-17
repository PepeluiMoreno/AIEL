<?php
/************************ Inicio vCuerpovActualizarCuotasCobradasEnRemesaTes.php  *************
FICHERO: vCuerpoActualizarCuotasCobradasEnRemesaTes.php
PROYECTO: EL
VERSION: PHP 7.3.21

Formulario que muestra los datos de una remesa SEPA XML y pide confirmar para 
actualizar la tabla "CUOTAANIOSOCIOS", SOCIO (FRST->RCUR), "ORDENES_COBRO" 
y "REMESAS_SEPAXML" a partir de las filas de orden de pago de cada cuota en tabla 
"ORDENES_COBRO" de una remesa concreta que se buscará por "NOMARCHIVOSEPAXML"

Se elimina el archivo "NOMARCHIVOSEPAXML" del servidor una vez actualizas las 
tablas antes citadas

Se pide fecha pago por el banco e importe de gastos y comisiones cobrados por el banco

LLAMADA: vistas/tesorero/vistas/tesorero/vActualizarCuotasCobradasEnRemesaTes.php	
LLAMA: vistas/tesorero/vistas/tesorero/formActualizarCuotasCobradasEnRemesaTes.php
           
OBSERVACIONES: Solo se actualiza una vez que esté cobrada por el banco
***********************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';
		
/*************************** Inicio Cuerpo central ***************************/

echo $navegacion['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">   		
					
					ANOTAR LAS CUOTAS DOMICILIADAS DE LOS SOCIOS/AS DE LA REMESA YA ABONADA POR EL BANCO
</h3>

	<br /><br />	
<?php require_once './vistas/tesorero/formActualizarCuotasCobradasEnRemesaTes.php'; ?>

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
<!-- ******************************* Fin vCuerpovActualizarCuotasCobradasEnRemesaTes.php **************** -->