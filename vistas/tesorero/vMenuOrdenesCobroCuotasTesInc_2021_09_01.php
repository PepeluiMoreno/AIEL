<?php
/*-----------------------------------------------------------------------------
FICHERO: vMenuOrdenesCobroCuotasTesInc.php
        antes vMenuExportarCuotasDonaTesoreroInc
VERSION: PHP 7.3.21

DESCRIPCION: Muesta las opciones del menú relacionadas con la órdenes de cobro 
de cuotas domiciliadas.	
I- Enviar emails para avisos de próxima orden de cobro de cuota.

II- Generar archivo SEPA-XML con órdenes cobro y otras operaciones relacionadas
- Generar archivo SEPA_ISO20022CORE-XML con las órdenes cobro B.Santander 
  e Insertar órdenes en tabla ORDENES_COBRO.
- Estado de las órdenes de cobro de cuotas domiciliadas: 
		.Ver órdenes de cobro 
		.Eliminar remesa de ORDENES_COBRO 
		.Descargar Archivo SEPA-XML
		.Actualizar pagos remesa en CUOTA ANIO SOCIO
- Exportar las órdenes de pago de cuotas a archivo Excel 
  para trabajo y contrastar con archivo XML SEPA 
- Exportar las cuotas y otros datos de los socios/as a Excel para uso interno 

III- Exportar listas emails para avisos órdenes cobro cuotas par envío desde Nodo50

LLAMADA: cTesorero.php:menuOrdenesCobroCuotasTes()
LLAMA:	vistas/tesorero/vCuerpoMenuOrdenesCobroCuotasTes.php
	
OBSERVACIONES: 
2020-10-12: Cambio nombre archivo
------------------------------------------------------------------------------*/
function vMenuOrdenesCobroCuotasTesInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/tesorero/vCuerpoMenuOrdenesCobroCuotasTes.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>