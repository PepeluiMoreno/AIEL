<!-- ***************************** Inicio Cuerpo mensaje ***********************
FICHERO: vCuerpoMensajePopUp.php 
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Es el mensaje después de un PopUp (enlaces del pie)
OBSERVACIONES:Se le llama desde muchos cEnlacesPie
********************************* Fin Cuerpo Login  ************************* -->	
<div class="content0" >

    <div align="center">

        <br /><br />	
        <h3 align="center">	  	
            <b><?php
                if (isset($arrayParamMensaje['textoCabecera'])) {
                    echo $arrayParamMensaje['textoCabecera'];
                }
                ?>
            </b>	  	
        </h3>

        <br /><br /><br /><br /><br />
        <span class="textoAzul9Center">
            <?php
            echo $arrayParamMensaje['textoComentarios'];
            ?>		
        </span>
        <br /><br /><br /><br />

                        <!--<input onclick=window.close(); type=button value="Cancelar" />-->
        <input onclick=window.close(); type=button value="<?php echo $arrayParamMensaje['textoBoton']; ?>" />		
    </div>	

    <!-- ****************************** Fin Cuerpo mensaje  *************************** -->		

    <br />		<br />		<br />		<br />		<br />		<br />		<br />		<br />
</div>
<br />