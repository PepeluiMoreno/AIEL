<?php
/****************************** Inicio vCuerpoExcelCuotasInternoTesorero ********************
FICHERO: vCuerpoExcelCuotasInternoTesorero.php
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
									
LLAMADA: vExcelCuotasInternoTesoreroInc.php, previo cTesorero.php:excelCuotasInternoTesorero()
LLAMA: vistas/tesorero/formExcelCuotasInternoTesorero.php

OBSERVACIONES:
La pantalla se quedará fija despues de hacer clic en "Exportar selección", 
aunque si no hay aviso de error el archivo  estará descargado.     
AVISO: Al abrir el archivo dice: La extensión y el formato del archivo no coinciden. 
Puede que el archivo esté dañado o no sea seguro. No lo abra a menos que confíe en su origen. 
¿Desea abrirlo de todos modos? 
*********************************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';
	
/*************************** Inicio Cuerpo central ***************************/
	
			echo $navegacion['cadNavegEnlaces'];
?>

		<br /><br />
		<h3 align="center">
	  		  	EXPORTAR CUOTAS Y OTROS DATOS DE LOS SOCIOS/AS A EXCEL PARA USO INTERNO
		</h3>
		
		<?php require_once './vistas/tesorero/formExcelCuotasInternoTesorero.php';?>
		
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
<!-- ******************************* Fin vCuerpoExcelCuotasInternoTesorero **************** -->