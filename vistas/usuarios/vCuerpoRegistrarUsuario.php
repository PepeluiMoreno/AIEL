<!--***************************** Inicio Cuerpo RegistrarUsuario **************
FICHERO: vCuerpoRegistrarUsuario_old.php//new
PROYECTO: ong
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de entrada de
            usuario y contraseña para validar el usuario en la BBDD.
OBSERVACIONES:Se le llama desde vLoginInc.php
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
		<?php require_once './vistas/usuarios/formRegistrarUsuario.php';?>
		<!--********************  Fin formRegistrarUsuario ***********************-->
	</div>
	<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin Cuerpo RegistrarUsuario***************-->