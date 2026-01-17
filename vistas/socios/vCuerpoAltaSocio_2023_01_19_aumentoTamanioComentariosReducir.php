<?php
/**************************** Inicio vCuerpoAltaSocio ******************************************
FICHERO: vCuerpoAltaSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: En este formulario se introducen los datos para registrarse un nuevo socio por el 
             propio socio, para posterioremente ser confirmado por el socio a partie del email recibido
													(o excepcionalmente por un gestor)  
													
LLAMADA: vistas/socios/vAltaSocioInc.php y a su vez de controladorSocios.php:altaSocio()
LLAMA: vistas/socios/formAltaSocio.php
													
OBSERVACIONES: Probado PHP 7.3.21
*************************************************************************************************/

//$mensajeIzquierda = "Nuev@ soci@";
$mensajeIzquierda = "ASÓCIATE";
$mensajeIzquierda = "<strong>Europa Laica</strong><br /><br /><br />ASÓCIATE";

require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- ************************ Inicio Cuerpo central ****************** -->
<br />
<h3 align="center">	
    ASÓCIATE&nbsp;&nbsp; A &nbsp;&nbsp;EUROPA&nbsp;&nbsp; LAICA 	
</h3>	

<!-- ************************* Inicio form ********************* -->
<?php require_once './vistas/socios/formAltaSocio.php'; ?>
<!-- *************************  Fin form *********************** -->

</div><!-- ***************** Fin Cuerpo central derecho ************** -->

</div><!-- *********** Fin cuerpo central:cuerpo izdo+cuerpo decho ******** -->

<!-- ****************************** Fin vCuerpoAltaSocio  ******************** -->