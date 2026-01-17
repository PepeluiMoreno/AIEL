
<?php
/******************************************************************************
FICHERO: vPieFinal.php
PROYECTO: Europa Laica
VERSION: PHP 7.3.21
DESCRIPCION: Escribe el pie de todas las páginas
OBSERVACIONES:
*******************************************************************************/
?>
<script language=javascript>
<!-- 
function ventanaSecundaria (URL)
{
   window.open(URL,"ventana1","width=960,height=600,scrollbars=yes")
}
-->
</script>	

<div id="siteInfoPie" style="clear:both;">
<!-- si no esta activado javascript, salen pantalla entera -->	
	<a href="./index.php?controlador=cEnlacesPie&amp;accion=infAplicacion" 
				   target="_blank" title="Sobre esta aplicación" 
							onclick="ventanaSecundaria(this); return false">
							Sobre esta aplicación           
 </a> |			
<!-- si no esta activado javascript, salen pantalla entera -->	
	<a href="./index.php?controlador=cEnlacesPie&amp;accion=privacidad" 
				   target="_blank" title="Privacidad de datos" 
							onclick="ventanaSecundaria(this); return false">
							Protección de datos           
 </a> |		
<!-- si no esta activado javascript, salen pantalla entera -->	
<!--	<a href="./index.php?controlador=cEnlacesPie&amp;accion=contactar" 
				   target="ventana1" title="Contactar con nosotros" 
							onclick="window.open('','ventana1','width=800,height=600,scrollbars=yes')">
							Contactar           
 </a> |	
	-->		
	<a href="./index.php?controlador=cEnlacesPie&amp;accion=contactarEmail" 
				   target="ventana1" title="Contactar con nosotros" 
							onclick="window.open('','ventana1','width=800,height=600,scrollbars=yes')">
							Contactar         
 </a> |			
		   <span class="textoNegro8Left"> &copy;  Europa Laica</span>	
       <span class="right">
        <!--antiguo <img style="border:0;width:88px;height:31px"
            src="http://jigsaw.w3.org/css-validator/images/vcss"
            alt="¡CSS Válido!" />	
        -->
       </span>   
</div>
</div><!--cierre de <div class="container-fluid">	que está en vCabeceraInicial-->
                
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="./vistas/js/jquery-1.11.0.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="./vistas/js/bootstrap.min.js"></script>
    
    <!-- Script para ajustar el tamaño de la columna izquierda al de la derecha
         cuando la columna derecha es menor que la izquierda y solo si el ancho
         de la pantalla es mayor de 768 px -->
    <script>
        function ajustaAltura() {
            if ($(window).width() > 768) {
                if ($('#secciones').height() < $('#content1').height()) {
                    var alturaContent1 = $('#content1').height();
                    $('#secciones').css('height', alturaContent1);
                } 
            } else {
                 $('#secciones').css('height', '100%');
            }
        }
       
        $(window).resize(function() {
                ajustaAltura();
            });
            
        ajustaAltura();
    </script>
</body>
</html>