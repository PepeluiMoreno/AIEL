<!--****************** vCuerpoTemporizador.php" ********************************
FICHERO: vCuerpoTemporizador.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de pregunta de
             eliminar Socio en la BBDD.
													Es el formulario que muestra algunos datos personales de un coordinador,
             y que permite cambiarles un área de coordinación.
													Tiene unos botones para "Cambiar coordinación", y para "Cancelar"
OBSERVACIONES:Se le llama desde 
              Incluye 
*******************************************************************************-->
<div class="content0" >
	<div id ="secciones">
		<!--*********************** Inicio Links Idz  ***************************-->
		<p>
      <?php 			  	  
	      //require_once './vistas/login/datosLinksSeccionesLogin.php';
	      //$enlacesSeccIzda=datosLinksSeccionesLogin();
	      require_once './vistas/login/escribirLinksSeccionIzda.php';
	      //escribirLinksSeccionIzda($enlacesSeccIzda);
				   escribirLinksSeccionIzda($tituloSeccion,$_SESSION['vs_enlacesSeccIzda']);
      ?>
    </p>
  	<!--************************* Inicio imagen  ****************************-->
    <p align="center">
   	  <img src="./vistas/images/el_susi.jpg"  width="150" align="middle"/>
		 <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
    </p>
  	<!--*****************  Fin imagen   ***************************-->
	</div>
	<!--***************************** Fin Seccciones Idz  *********************-->
  <!--************************ Inicio Cuerpo central ************************-->
	<div id = "content1">
			<?php 
				if (isset($navegacion['cadNavegEnlaces']))
		  {echo $navegacion['cadNavegEnlaces'];
				}
		?>	
	  <br /><br />		
		<h3 align="center">
	  	CAMBIAR UN ÁREA DE COORDINACIÓN TERRITORIAl A COORDINADOR/A	  	
		</h3>		

<div id="bowlG">
<div id="bowl_ringG">
<div class="ball_holderG">
<div class="ballG">
</div>
</div>
</div>
</div>

	</div>
	<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin vCuerpoTemporizador.php ***************-->