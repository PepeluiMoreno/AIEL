<!--***************************** Inicio vCuerpoDescargarDocsRolSocio.php **********
FICHERO: vCuerpoDescargarDocsRolSocio.php
PROYECTO: EL
VERSION: PHP 5.6.4
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de 
           Para mostrar los datos de de un socio
OBSERVACIONES:Se le llama desde vDescargarDocsRolSocioInc.php 
e incluye /vistas/socios/formDescargarDocsRolSocio.php
2020-04-21: lo añado.
*****************************************************************************-->
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
<!--************************ Inicio Cuerpo central ************************-->
<?php
if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br />	
<h3 align="center">
    MANUALES DE AYUDA Y OTROS DOCUMENTOS DOCUMENTOS DISPONIBLES  	
</h3>	
<!--*** Inicio form (se podría pasar com parámetro)*******-->

<?php require_once './vistas/socios/formDescargarDocsRolSocio.php'; ?>

<!--********************  Fin form  ***********************-->

<!-- ********************  Inicio form botón anterior******************** --> 		
<div align="center">
    <br />	
    <?php
    if (isset($navegacion['anterior'])) {
        echo $navegacion['anterior'];
    }
    ?>	
    <br />
    <!-- *************** incio cerrar venta *************** 
<form id="cerrarVentanaForm" method="post" class="linea"
         action="./index.php?controlador=controladorLogin&amp;accion=logOut"		
                     onSubmit="return confirm('¿Salir sin guardar los campos actualizados en el formulario?')">		

                    <input type="submit" name="Submit" value="Votar" class="boton2" onClick="abrirVentana()" >
                    <input type=botton value=Aceptar onclick=cerrarVenta();>
</form>
*************** fin cerrar ventana *************** 	

<form id="cerrarVentanaForm1"  method="post"
         action="./index.php?controlador=controladorLogin&amp;accion=logOut"		
                     onSubmit="cerrarVenta()">		

                    <input type="submit" name="Submit" value="Aceptar0">

</form>
<br /> <br />
     *************** incio botón con javaScript cerrarVenta() *********** 
    
<form id="cerrarVentanaForm2" method="post" 
         action="./index.php?controlador=controladorLogin&amp;accion=logOut">			
                    <input type=botton name="boton" value=Aceptar1 onclick=cerrarVenta();>
</form>
*************** fin botón con javaScript cerrarVenta()************** -->			

</div 
<!-- ********************  Fin Form botón anterior *********************** -->
</div>

<!--****************************** Fin Cuerpo central  ********************-->
</div>

<!--******************************* Fin Cuerpo actualizarSocio***************-->