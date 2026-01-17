<!-- ***************************** Inicio Cuerpo vCuerpoPagarSocio.php ***********************
FICHERO: vCuerpoPagarSocio.php
Válido para alta por el propio socio, para solicitar o confirmar
Antes: vCuerpoMensajeAltaSocioSolicitada.php 

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
</h3><br />
<span class="textoNegro8Left">	  
    <?php
    echo $arrayParamMensaje['textoComentarios'];
    ?>	
</span>					
<br /><br />		
<h3 align="center">	  	
    <b>Donar a la asociación Europa Laica</b>	  	
</h3>
<br />
<span class="textoGris8Left2">		
    - Puedes donar mediante ingreso directo o transferencia	a:
    <br /> 
    <br /><b> - Banco Santander</b>,	cuenta <b>0049 0001 52 2411813269</b>
    <br />
    <br /><b> - Triodos Bank</b>,	cuenta <b>1491 0001 20 1008919423</b>
    <br /><br />
    Señala como concepto: Donación a Europa Laica, NIF y nombre y apellidos (o si lo prefieres Donación ANÓNIMA).
    <br /><br /> 	  
    - También puedes donar ahora con tarjeta de crédito (o si tienes una cuenta de PayPal), mediante pago con <strong>PayPal</strong>.

    <br /><br />
    Para pagar ahora con PayPal, primero haz clic en la flecha de abajo para elegir la cantidad y después clic en "Pagar ahora"
    <br />
</span>			
<?php require_once './vistas/plantillasGrales/scriptPayPalPagarAhora.php'; ?>

<span class="textoGris8Left2">		
    Por si hubiese algún problema, nos puedes confirmar tu pago enviando un correo electrónico 
    a <b>tesoreria@europalaica.com</b> 
    con asunto: donación, y dentro del mensaje los datos: NIF, nombre y apellidos, cantidad, fecha pago y entidad dónde has pagado.
    <br /><br />
</span>	

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