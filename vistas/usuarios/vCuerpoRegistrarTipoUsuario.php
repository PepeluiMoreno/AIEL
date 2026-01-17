<!--************************* Inicio vCuerpoRegistraTipoUsuario **************
FICHERO: vCuerpoRegistrarTipoUsuario.php
PROYECTO: ong
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de entrada de
            tipo usuario para registrarse.
OBSERVACIONES:Se le llama desde vRegistraTipoUsuarioInc.php
********************************* Fin Cuerpo Login  *************************-->
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
  <!--************************ Inicio Cuerpo central ************************-->
	<div id = "content1">
		<h3 align="center">
	  	<br />
	  	REGISTRAR USUARIOS (Socios o Simpatizantes)	  	
		</h3>		 
		<!--********************  Inicio formRegistrarUsuario ********************-->
		<?php require_once './vistas/usuarios/formTipoUsuario.php';?>
		<!--********************  Fin formRegistrarUsuario ***********************-->
	</div>
	<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin Cuerpo RegistrarUsuario***************-->