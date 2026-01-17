<?php
/***************** Inicio Cuerpo vCuerpoLogin *****************************
FICHERO: vCuerpoLogin.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene el formulario de entrada con usuario y contraseña para
             validar el usuario en la BBDD.
OBSERVACIONES:Se le llama desde vLoginInc.php
***************************************************************************/

//$mensajeIzquierda = "Entrar<br /><br />en el<br /><br />Área <br /><br />Privada<br /><br />de<br /><br />Socios/as";
$mensajeIzquierda = "<strong>Europa Laica</strong><br /><br /><br />Área <br /><br />Privada<br /><br />de<br /><br />Socios/as";
require_once './vistas/plantillasGrales/vContent.php';
	?>
	
	<br /><br />
	
	<!--************************ Inicio Cuerpo central ************************-->
	<h3 align="center">
					<b>ACCESO SOCIOS / AS</b>					
	</h3>
 <br />

	<span class="textoNegro9Left">
	 <strong>		
		<?php 
		 if (isset($textoCuerpo) && !empty($textoCuerpo) )
			{	
			  echo "<br /><br />".$textoCuerpo;
			}
		?>	 
	 </strong>
	</span>
 <br /><br />	

<?php require_once './vistas/login/formularioLogin.php'; ?>			
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin Cuerpo vCuerpoLogin.php *************-->