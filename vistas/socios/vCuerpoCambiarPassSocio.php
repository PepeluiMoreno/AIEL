<?php
/***************************** Inicio CuerpoCambiarPassSocio *******************
FICHERO: vCuerpoCambiarPass.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de 
             cambiar Password para todo tipo de usuarios
													
LLAMADA: vistas/socios/vCambiarPassSocio.php
LLAMA: vistas/socios/formCambiarPassSocio.php y plantillas generales				
													
OBSERVACIONES: 
*******************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central *************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>
<h3 align="center">
    <br />
    CAMBIAR CONTRASEÑA	  	
</h3>		 

<!--*** Inicio formCambiarPassSocio (se podría pasar com parámetro)*******-->
<?php require_once './vistas/socios/formCambiarPassSocio.php'; ?>
<!--********************  Fin formCambiarPassSocio ***********************-->

</div>
<!--****************************** Fin Cuerpo central  ********************-->

</div>
<!--************************ Fin Cuerpo CuerpoCambiarPassSocio *************-->