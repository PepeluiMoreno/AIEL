<?php
/***************************** Inicio CuerpoRestablecerPass *******************
FICHERO: vCuerpoRestablecerPass.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de 
             RestablecerPass del usuario.
OBSERVACIONES: Se le llama desde vRestablecerPassInc.php
*******************************************************************************/
?>

<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
  <!--************************ Inicio Cuerpo central ************************-->
		<h3 align="center">
	  	<br />
	  	RESTABLECER CONTRASEÑA	  	
		</h3>		 
		
		<!--*** Inicio formRestablecerPass (se podría pasar com parámetro)*******-->
		<?php require_once './vistas/login/formRestablecerPass.php';?>
		<!--********************  Fin formCambiarPass ***********************-->
		
	</div>
	<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin Cuerpo CuerpoRestablecerPass ***************-->