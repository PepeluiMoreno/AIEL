<!-- ***************************** Inicio Cuerpo actualizarSocio ***************
FICHERO: vCuerpovActualizarSocioNav.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de 
             Actualizar Socio en la BBDD y navegación
OBSERVACIONES:Se le llama desde vActualizarSocioInc.php
***************************************************************************** -->
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
<?php
if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />	
<h3 align="center">
    ACTUALIZAR DATOS DEL SOCIO	  	
</h3>
<br />		 
<!--*** Inicio formActualizarSocio (se podría pasar com parámetro)*******-->
<?php require_once './vistas/socios/formActualizarSocio.php'; ?>
<!--********************  Fin formActualizarSocio ***********************-->
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin Cuerpo actualizarSocio***************-->