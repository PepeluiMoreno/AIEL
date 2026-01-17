<!-- ***************************** Inicio Cuerpo mensaje ***********************
FICHERO: vCuerpoMensajeNav.php 
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: Contiene menú idz de "Secciones", navegación superior y botón anterior
OBSERVACIONES: Se le llama desde muchos controladores
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
<h3 align="center">	  	
    <b><?php
        if (isset($arrayParamMensaje['textoCabecera'])) {
            echo $arrayParamMensaje['textoCabecera'];
        }
        ?>
    </b>	  	
</h3><br /><br /><br />
<span class="textoNegro8Left">	  
    <?php
    echo $arrayParamMensaje['textoComentarios'];
    ?>		
</span>
<br /><br />	 <br /><br />	
<!-- ******************* Inicio Formulario botón submit ******************** -->		
<div align="center">
    <br />	
    <?php
    if (isset($navegacion['anterior'])) {
        echo $navegacion['anterior'];
    }
    ?>				

</div  			
<!-- ********************  Fin Formulario botón submit  ************************* -->
<!-- ****************************** Fin Cuerpo central  *************************** -->		
<br /><br />
</div>
</div>