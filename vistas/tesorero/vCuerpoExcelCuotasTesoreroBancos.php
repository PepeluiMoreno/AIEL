<?php
/************************************* Inicio vCuerpoExcelCuotasTesoreroBancos ***********************
FICHERO: vCuerpoExcelCuotasTesoreroBancos.php
PROYECTO: EL

DESCRIPCION: Formulario para selección de opciones para generar y exportar a un archivo Excel las órdenes 
de pago de las cuotas de los socios, (se utilizaba para las remesas de órdenes de cobro en B. Tríodos) 
y ahora también es útil para uso interno de tesorería, cuando se genera y descarga a continuación de generar 
el archivo XML SEPA para el B. Santander (con los mismos criterios de selección) y así el Excel puede servir
para contrastar los totales y otros datos y como un listado para anotar las 
devoluciones e incidencias de la remesa

Permitirá elegir:
- Excluir de la orden de cobro a los socios/as con alta después de una fecha	
- Cuenta bancaria España, o Cuenta bancaria países SEPA en Europa distintos de España
- Agrupaciones Territariales seleccionadas	 y agrupaciones y otros datos necearios
													
LLAMADA: vistas/tesorero/vExcelCuotasTesoreroBancosInc.php												
LLAMA: vistas/tesorero/formExcelCuotasTesoreroBancos.php

OBSERVACIONES: Probado PHP 7.3.21				

*******************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
			
/*** ************************ Inicio Cuerpo central ***************************/

			echo $navegacion['cadNavegEnlaces'];
?>

		<br /><br />
		<h3 align="center">

				EXPORTAR A ARCHIVO EXCEL ÓRDENES DE COBRO CUOTAS DOMICILIADAS DE SOCIOS/AS<br />
				<!--(antes usado para B.TRÍODOS, no inserta esas órdenes cobro en tabla ORDENES_COBRO, útil para uso Tesorería y contrastar con archivos SEPA-XML de B.Santander) 					-->

		</h3>
		<?php require_once './vistas/tesorero/formExcelCuotasTesoreroBancos.php';?>
		
			<div align="center">
			<?php 
			/*if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
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
				{ echo $navegacion['anterior'];
				}
	 ?>				
			<br /><br />			
			</div 
		 <!-- ********************  Fin Form botón anterior *********************** --> 
	</div>
	<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoExcelCuotasTesoreroBancos **************** -->