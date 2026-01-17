<?php
/********************* vCuerpoMenuOrdenesCobroCuotasTes.php ********************
FICHERO: vCuerpoMenuOrdenesCobroCuotasTes.php 
         (antes vCuerpoMenuExportarCuotasDonaTesorero.php)
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

LLAMA: vistas/tesorero/formMenuOrdenesCobroCuotasTes.php
LLAMADA: vistas/tesorero/vMenuOrdenesCobroCuotasTesInc.php
													
OBSERVACIONES:
2020-10-12: Cambio nombre archivo
***********************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/*************************** Inicio Cuerpo central ***************************/

	 echo $navegacion['cadNavegEnlaces'];
?>
		<br /><br />
		<h3 align="center">
	  	GENERAR ARCHIVO "SEPA-XML" PARA ÓRDENES COBRO DE CUOTAS DOMICILIADAS EN BANCO Y OTRAS OPERACIONES RELACIONADAS	
		</h3>
		 		
		<?php 
		      require_once './vistas/tesorero/formMenuOrdenesCobroCuotasTes.php';
		?>
		
		 <!-- ********************  Inicio form botón anterior******************** --> 		
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
<br /><br />			
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
<!-- ******************************* vCuerpoMenuOrdenesCobroCuotasTes.php **************** -->