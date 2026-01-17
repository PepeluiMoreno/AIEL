<?php
/*-----------------------------------------------------------------------------
FICHERO: vMostrarOrdenesCobroUnaRemesaInc.php
VERSION: PHP 7.3.19

Se forma y muestra una tabla-lista páginada "MOSTRAR ÓRDENES COBRO DE UNA REMESA" 
con los detalles de las órdenes de cobro individuales correspondientes a una remesa
concreta (pendiente de enviar, enviada o actualizada). Y un Link "Ver" para ver los 
datos del socio corresnpondiente a la orden de cobro.
Se mostrarán y ordenadas por APELLIDOS.  

Incluye un botón para elegir, ESTADO CUOTA (por defecto el Todos) y AGRUPACION,
y otro botón para elegir por APE1, APE2.

Recibe varios paramétros, parte de ellos se podrían unificar en un array

LLAMADA: cTesorero:mostrarOrdenesCobroUnaRemesaTes()
LLAMA: vistas/tesorero/vCuerpoMostrarOrdenesCobroUnaRemesa.php 

OBSERVACIONES:         
------------------------------------------------------------------------------*/
function vMostrarOrdenesCobroUnaRemesaInc($tituloSeccion,$enlacesFuncionRolSeccId,$resCuotasSocios,$parValorComboAgrupaSocio,$datosFormMiembro,$datosRemesa)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoMostrarOrdenesCobroUnaRemesa.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>