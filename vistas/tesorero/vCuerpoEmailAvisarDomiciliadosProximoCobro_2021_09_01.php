<?php
/*************** Inicio vCuerpoEmailAvisarDomiciliadosProximoCobro.php *************
FICHERO: vCuerpoEmailAvisarDomiciliadosProximoCobro.php
VERSION: PHP 7.3.21

DESCRIPCION: Incluye el formulario de selección para buscar los Emails y otros
datos de las cuotas de los socios que tienen domicialiación bancaria	del pago
de las cuotas mediante su cuenta IBAN, para envíar un email personalizado con
su nombre, APE1, CCIBAN, Cuota, a los socios avisando de próximo cobro de cuotas.
También incluye el texto común que se enviará en el email.

Más información en formEmailAvisarDomiciliadosProximoCobro.php
												
Después con esos datos se buscarán en la BBDD, para enviar desde "tesoreria@europalaica.org"
un email personalizado (Nombre, IBAN, cuota, ..) a los socios con mensaje de aviso de próximo 
cobro de cuota. 

LLAMDA: vistas/tesorero/vEmailAvisarDomiciliadosProximoCobroInc.php
LLAMA: vistas/tesorero/formEmailAvisarDomiciliadosProximoCobro.php

OBSERVACIONES: 2020-10-12 comentarios
***********************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
?>			
  <!-- ************************ Inicio Cuerpo central ************************ -->
		<?php 	
			echo $navegacion['cadNavegEnlaces'];
		?>
		<br /><br />
		<h3 align="center">
				ENVIAR EMAIL A SOCIOS/AS PARA AVISO DE PROXIMA ORDEN DE COBRO DE CUOTAS	DOMICILIADAS
		</h3>
		<?php require_once './vistas/tesorero/formEmailAvisarDomiciliadosProximoCobro.php';?>
		
		<!-- ********************  Inicio Form botón anterior ******************** --> 
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
<!-- ************* Fin vCuerpoEmailAvisarDomiciliadosProximoCobro.php **************** -->