<!-- **************** vCuerpoMenuExportarCuotasDonaTesorero.php *************
FICHERO: vCuerpoMenuExportarCuotasDonaTesorero.php
Agustín 2018-02-11: Cambio encabezado
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Muesta las opciones del submenú de generar ficheros de ordenes de pago cuotas
             para AEB19 Santander, Excel Triodos, 
													También el fichero de email de email aviso y Excel de cuotas y donaciones
													LLama a /vistas/tesorero/formMenuExportarCuotasDonaTesorero.php';?>
OBSERVACIONES:
***************************************************************************** -->
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>		
  <!-- ************************ Inicio Cuerpo central ************************ -->
		<?php 	
			echo $navegacion['cadNavegEnlaces'];
		?>
		<br /><br />
		<h3 align="center">
	  	MENÚ EXPORTAR ÓRDENES DE COBRO DE CUOTAS PARA BANCO Y OTRAS OPERACIONES RELACIONADAS	
		</h3>
		 		
		<?php require_once './vistas/tesorero/formMenuExportarCuotasDonaTesorero.php';?>
		
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
<!-- ******************************* vCuerpoMenuExportarCuotasDonaTesorero.php **************** -->