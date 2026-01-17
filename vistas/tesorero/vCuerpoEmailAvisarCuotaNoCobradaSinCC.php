<?php
/*************** Inicio vCuerpoEmailAvisarCuotaNoCobradaSinCC.php ***********************
FICHERO: vCuerpoEmailAvisarCuotaNoCobradaSinCC.php
VERSION: PHP 7.3.21

DESCRIPCION: Incluye el formulario de selección de campos para buscar Emails y otros datos
de todos las cuotas de los socios, que NO tienen domiciliado pago en cuenta IBAN, con posible 
exclusión por fechas, y	elegir agrupaciones territoriales. 	

Más información en formEmailAvisarCuotaNoCobradaSinCC.php
													
Después con esos datos se buscarán en la BBDD, para enviar desde "tesoreria@europalaica.org"
un email personalizado (Nombre, cuota, ..) a los socios con mensaje de no haber abonado la cuota. 
													
LLAMADA:vistas/tesorero/vEmailAvisarCuotaNoCobradaSinCCInc.php
LLAMA: vistas/tesorero/formEmailAvisarCuotaNoCobradaSinCC.php

OBSERVACIONES: 2020-10-12 comentarios
****************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/*************************** Inicio Cuerpo central *************************/

	echo $navegacion['cadNavegEnlaces'];
?>
		
		<br /><br />
		<h3 align="center">	
				ENVIAR EMAIL A SOCIOS/AS SIN DOMICILIACIÓN BANCARIA PARA INFORMAR DE CUOTA ANUAL AÚN NO PAGADA	
		</h3>
		<?php require_once './vistas/tesorero/formEmailAvisarCuotaNoCobradaSinCC.php';?>
		
		<!-- ******************* Inicio Form botón anterior *********************** -->
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
<!-- ****************** Fin vCuerpoEmailAvisarCuotaNoCobradaSinCC.php **************** -->