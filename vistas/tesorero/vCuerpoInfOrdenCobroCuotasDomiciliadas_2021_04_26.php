<?php
/*----------------------------------------------------------------------------------------------------
FICHERO: formInfOrdenCobroCuotasDomiciliadas.php
VERSION: PHP 7.3.21

DESCRIPCION: Muestra la información sobre el procedimiento de enviar remesas al B. Santander 
con las órdenes de cobro de las cuotas domiciliadas en bancos de España y bancos SEPA

LLAMADA: vistas/tesorero/vInfOrdenCobroCuotasDomiciliadas.php
LLAMA: vistas/tesorero/formInfOrdenCobroCuotasDomiciliadas.php

OBSERVACIONES: 2020-11-27 creación script
----------------------------------------------------------------------------------------------------*/

require_once './vistas/plantillasGrales/vContent.php';
?>			
<!-- ************************ Inicio Cuerpo central ************************ -->
<?php
echo $navegacion['cadNavegEnlaces'];
?>
<br /><br />
<h3 align="center">
				INFORMACIÓN SOBRE EL PROCEDIMIENTO PARA ENVIAR AL B. SANTANDER LAS ÓRDENES DE COBRO DE CUOTAS DOMICILIADAS 
</h3>
<?php require_once './vistas/tesorero/formInfOrdenCobroCuotasDomiciliadas.php'; ?>

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
<!-- *********** Fin vCuerpoInfOrdenCobroCuotasDomiciliadas.php **************** -->