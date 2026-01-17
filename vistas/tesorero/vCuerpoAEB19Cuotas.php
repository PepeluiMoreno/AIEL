<!-- ***************************** Inicio vCuerpoAEB19Cuotas.php *************
FICHERO: vCuerpoAEB19Cuotas.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Incluye al formulario de selección formAEB19Cuotas.php para la 
             formación del fichero AEB19Cuotas para pasar al banco Santander
													para el cobro de cuotas  
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
				EXPORTAR CUOTAS SOCIOS/AS A ARCHIVO AEB19 PARA LAS ÓRDENES DE COBRO EN BANCO SANTANDER	
		</h3>
		<?php require_once './vistas/tesorero/formAEB19Cuotas.php';?>
		
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
<!-- ******************************* Fin vCuerpoAEB19Cuotas.php **************** -->