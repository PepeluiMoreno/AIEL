<?php
/******************* Inicio vCuerpoActualizarIngresoCuota *******************************************
FICHERO: vCuerpoActualizarIngresoCuota.php
VERSION: PHP 7.3.21

Se actualiza el ingreso de una cuota del socio de ese año por parte del tesorero.
 
Se dan dos situaciones que requieren un tratamiento distinto según exista en ese momento para ese socio, 
una orden de cobro de remesa en el banco pendiente de efectuar que dará lugar a dos forms distintos

Desde vActualizarIngresoCuotaInc.php-->vCuerpoActualizarIngresoCuotaInc.php se dirige a dos posibles forms: 

A- formActualizarIngresoCuotaTodos.php: "caso de NO tener una orden de cobro de remesa pendiente en el banco"

			Es el form para actualizar ingreso de cuota para año actual y anteriores donde se puede añadir y modificar datos 
			de Ingreso Cuota en CUOTAANIOSOCIO, (pero sólo cuando en tabla "ORDENES_COBRO" 
			el ESTADOCUOTA !=PENDIENTE-COBRO), para ese socio y año
			
B- formActualizarIngresoCuotaObservaciones.php: "caso de SÍ tener un cobro de remesa en el banco pendiente"

			Es el form alternativo y es para pendientes de cobro de remesa en el banco, ya emitida 
			(sólo cuando en tabla "ORDENES_COBRO" el ESTADOCUOTA =PENDIENTE-COBRO), para ese socio y año 
			y por eso solo se permite	cambiar los campos "Observaciones y Motivo devolución" para evitar 
			cambios en	cuotas, pagos y gastos producir inconsistencias respecto a la remesa ya enviada al banco. 

LLAMADA: vistas/tesorero/vActualizarIngresoCuotaInc.php y previamente desde Tesorero.php:actualizarIngresoCuota()

LLAMA: - vistas/tesorero/formActualizarIngresoCuotaTodos.php o bien a
       - vistas/tesorero/formActualizarIngresoCuotaObservaciones.php
							
OBSERVACIONES: Incluye los dos posibles formularios a los que puede dirigir según el valor d
e ESTADOCUOTA en la tabla ORDENES_COBRO
****************************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central *************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />	

<h3 align="center">
    ANOTAR INGRESO CUOTA SOCIO/A	  	
</h3>
<br />	
	 
<!--**************************** Inicio form **************************************************-->
<?php 
		//echo "<br><br>1 vCuerpoActualizarIngresoCuota:datSocio: ";print_r($datSocio);

		//--- Inicio Selección del formulario correspondiente según ORDENES-COBRO  ---------------------	
		
  if ( !isset($datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'])) //A- opción
  { 
	   //echo "<br><br>1-a cTesorero:actualizarIngresoCuota:datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo']:";
	   //print_r($datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo']);
				require_once './vistas/tesorero/formActualizarIngresoCuotaTodos.php'; 			
		}			
		else
		{	  			
				if ($datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo'] == 'PENDIENTE-COBRO')	//B- opción
				{ 
						//echo "<br><br>1-b cTesorero:actualizarIngresoCuota:datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo']: ";
						//print_r($datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo']);
						require_once './vistas/tesorero/formActualizarIngresoCuotaObservaciones.php'; 	
				}	 	
				else //A-	opción
				{ //echo "<br><br>1-c cTesorero:actualizarIngresoCuota:datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo']: ";
   			//print_r($datSocio['formOrdenesCobro']['ESTADOCUOTA']['valorCampo']);
						require_once './vistas/tesorero/formActualizarIngresoCuotaTodos.php';				
		  }
		}
//--- Fin Selección del formulario carrespondiente según ORDENES-COBRO  ------------------------	
?>
<!--**************************** Fin form ****************************************************-->

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--*********************** Fin Cuerpo vCuerpoActualizarIngresoCuota ***********-->