<!-- ***************************** Inicio Cuerpo mensaje ***********************
FICHERO: vCuerpoMensajeAltaSocioAceptada.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones" y botón submit
OBSERVACIONES:Se le llama desde muchos controladores
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
</h3><br /><br /><br />
<span class="textoNegro9Left">	  
    <?php
    echo $arrayParamMensaje['textoComentarios'];
    ?>		
</span>
<br /><br />	 <br /><br />	
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
<br /><br />
</div>
</div>