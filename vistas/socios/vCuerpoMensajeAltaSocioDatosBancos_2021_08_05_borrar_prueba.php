<!-- ***************************** Inicio Cuerpo mensaje ***********************
FICHERO: vCuerpoMensajeAltaSocioDatosBancos.php
Válido para alta por el propio socio, para solicitar o confirmar, 
     con datos bancos procedentes BBDD y paypal con requiere

PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones" y botón submit

OBSERVACIONES:Se le llama desde vMensajeAltaSocioDatosBancosInc.php
********************************* Fin Cuerpo Login  ************************* -->	
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- ************************ Inicio Cuerpo central ************************ -->
<br />
<h3 align="center">	  	
    <b><?php
        if (isset($arrayParamMensaje['textoCabecera'])) {
            echo $arrayParamMensaje['textoCabecera'];
        }
        ?>
    </b>	  	
</h3><br />
<!-- <span class="textoNegro8Left">	-->
<span class="textoAzu112Left2">  
    <?php
    echo $arrayParamMensaje['textoComentarios'];
    ?>	
</span>					
<br /><br />		

<span class="textoGris8Left2">		
    <br />
</span>			
<?php //require_once './vistas/plantillasGrales/scriptPayPalPagoCuotaAhora.php';  ?>
<!--*** Inicio formMostrarDatosSocio (se podría pasar com parámetro)*******-->

<?php
//require_once './vistas/socios/formPagarCuotaSocio.php';
require_once './vistas/socios/formMensajeAltaSocioDatosBancos.php';
?>


<!--********************  Fin formMostrarDatosSocio ***********************-->		

<!-- ******************* Inicio Formulario botón submit ******************** -->		
<div align="center">
    <?php
    if (isset($arrayParamMensaje['enlaceBoton']) &&
            ($arrayParamMensaje['enlaceBoton'] !== '') &&
            ($arrayParamMensaje['enlaceBoton'] !== NULL)) {
        echo "<form method='post' action=" . $arrayParamMensaje['enlaceBoton'] . ">" .
        " <input type='submit' value='" . $arrayParamMensaje['textoBoton'] . "'>";
        echo " </form>";
    }
    ?>				
</div 
<!-- ********************  Fin Formulario botón submit  ************************* -->			

<!-- ****************************** Fin Cuerpo central  *************************** -->		
<br />
</div>
</div>