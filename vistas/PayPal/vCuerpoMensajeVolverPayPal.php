<!-- ****************** Inicio Cuerpo vCuerpoMensajeVolverPayPal.php *************
FICHERO: vCuerpoMensajeVolverPayPal.php
Válido para donaciones por el propio socio, como retorno de cancelación o pago de la donación
El boton aceptar cierra la ventana informativa
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones" y botón submit
OBSERVACIONES:Se le llama desde vMensajeVolverPayPalInc.php
********************************* Fin Cuerpo Login  ************************* -->	
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- ************************ Inicio Cuerpo central ************************ -->
<?php
if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />
<!-- Cabecera de la donación -->
<!-- <h3 align="center">	  	
        <strong>Donar a la asociación Europa Laica</strong> 	
</h3>
-->
<h3 align="center">	  	
    <strong>
        <?php
        if (isset($arrayParamMensaje['textoCabecera'])) {
            echo $arrayParamMensaje['textoCabecera'];
        }//es la cabecera del mensaje después de pagar o concelar con PayPal
        ?>	
    </strong>	  	
</h3>
<span class="textoAzu112Left2"> 
    <?php
    if (isset($arrayParamMensaje['textoComentarios'])) {
        echo "<br /><br />" . $arrayParamMensaje['textoComentarios'];
    }//es el mensaje después de pagar o concelar con PayPal
    ?>	

</span>			

<br /><br />

<!-- ******************* Inicio Formulario botón submit ******************** -->		
<div align="center">

   <img src="./vistas/images/EscuelaPublicaT5small.jpg" alt="Escuela Publica y Laica" align="middle">

 <!--   <input type="Button" name="Boton2" value="     Aceptar      " 
onClick="window.close()">		-->
</div 

<!-- ********************  Fin Formulario botón submit  ************************* -->			

<!-- ****************************** Fin Cuerpo central  *************************** -->		
</div>
</div>