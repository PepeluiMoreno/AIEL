<?php
/*-----------------------------------------------------------------------------
FICHERO: formFuncionRol.php
PROYECTO: EL

VERSION: PHP 5.2.3
DESCRIPCION: Es el formulario para mostrar comentario al menu general de usuario
              y enlace a anterior
OBSERVACIONES:Es incluida desde "vCuerpoRolFuncion.php"
              mediante require_once './vistas/login/vCuerpoRolFuncionInc.php'
														
Ahora no se utiliza
-------------------------------------------------------------------------------*/
?>
	<div id="registro">
		<span class="textoAzu112Left2">
		</span>
		<br /><br /> 
				<span class="textoNegro9Left">
							Desde el menú izquierdo puedes acceder a las funciones disponibles según
							tu perfil de usuario...
				<br /><br />		 
			</span>
	</div>
	
		<!-- ******************* Inicio Form botón submit ******************** -->		

			<div align="center">
			<?php 
			if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
			($botonSubmit['enlaceBoton']!==NULL))
   {
	    echo	"<form method='post' action=./".$botonSubmit['enlaceBoton'].">".
				      " <input type='submit' value=".$botonSubmit['textoBoton'].">";
		   echo " </form>";
			}	
			?>
			 <br /><br />			
			</div>  			
		<!-- ********************  Fin Form botón submit  ************************* --> 
	