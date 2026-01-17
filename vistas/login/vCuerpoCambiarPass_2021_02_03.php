<?php
/********************************* Inicio CuerpoCambiarPass ********************
FICHERO: vCuerpoCambiarPass.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de 
             cambiar Password para rodo tipo de usuarios
OBSERVACIONES: Se le llama desde vCambiarPassInc.php
********************************************************************************/
?>
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
<!--************************ Inicio Cuerpo central ************************-->
<?php
if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	

<h3 align="center">
    <br />
    CAMBIAR CONTRASEÑA	  	
</h3>		 

<!--*** Inicio formCambiarPass (se podría pasar com parámetro)*******-->
<?php require_once './vistas/login/formCambiarPass.php'; ?>
<!--********************  Fin formCambiarPass ***********************-->

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************ Fin Cuerpo CuerpoCambiarPass ***************-->