<?php
/*-----------------------------------------------------------------------------
FICHERO: vExcelCuotasInternoTesoreroInc.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario de selección de campos para buscar las cuotas y otros datos 
             para descargará en un archivo Excel para uso interno
													
El formulario además de las agrupaciones permite elegir:

- Un determinado año (dentro de los cinco últimos)
- Estado cuota: ABONADA,NOABONADA,EXENTO,PENDIENTE-COBRO,NOABONADA-ERROR-CUENTA,ABONADA-PARTE 												
- Estado de los socios/as (alta, baja, todos) 
- ORDENARCOBROBANCO = SI,NO,TODOS 
- Cuenta bancaria domiciliada:
  .Cuenta bancaria de España
  .Cuenta bancaria de países SEPA en Europa distintos de España
  .No tiene cuenta bancaria domiciliada
  .Todas las opciones
- Agrupaciones Territoriales

Se descargará en un archivo Excel									
									
LLAMADA: cTesorero.php: excelCuotasInternoTesorero()
LLAMA: vistas/tesorero/vCuerpoExcelCuotasInternoTesorero.php
									
OBSERVACIONES: 
La pantalla se quedará fija despues de hacer clic en "Exportar selección", 
aunque si no hay aviso de error el archivo  estará descargado.     
AVISO: Al abrir el archivo dice: La extensión y el formato del archivo no coinciden. 
Puede que el archivo esté dañado o no sea seguro. No lo abra a menos que confíe en su origen. 
¿Desea abrirlo de todos modos? 
------------------------------------------------------------------------------*/
function vExcelCuotasInternoTesoreroInc($tituloSeccion,$enlacesFuncionRolSeccId,$navegacion,$parValorComboAgrupaSocio,$parValorEstadosCuota,$datosExcelCuotas)
{
 require_once './vistas/plantillasGrales/vCabeceraSalir.php';

 require_once './vistas/tesorero/vCuerpoExcelCuotasInternoTesorero.php';		
  
 require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>