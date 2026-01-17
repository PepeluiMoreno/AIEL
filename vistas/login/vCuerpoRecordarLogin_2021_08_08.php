<?php
/****************** Inicio vCuerpoRecordarLogin.php  **********************
FICHERO: vCuerpoRecordarLogin.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de email para
             recordar usuario y contraseña mediante envío de email.
													
OBSERVACIONES: Se le llama desde vRecordarLoginInc.php
**************************************************************************/

//$mensajeIzquierda = "Recordar<br /><br />usuario/a <br />y<br /> contraseña";
$mensajeIzquierda = "<strong>Europa Laica</strong><br /><br /><br />Recordar<br /><br />usuario/a <br />y<br /> contraseña";
require_once './vistas/plantillasGrales/vContent.php';
?>

<!--************************ Inicio Cuerpo central ************************-->
<br />
<h3 align="center">
    RECORDAR USUARIO/A Y CONTRASEÑA
</h3>

<!--**************  Inicio Formulario Recordar Login  *******************-->
<?php require_once './vistas/login/formularioRecordarLogin.php'; ?>
<!--********************  Fin Formulario Recorda Login  *****************-->
</div>

<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--************************* Fin vCuerpoRecordarLogin **********************-->