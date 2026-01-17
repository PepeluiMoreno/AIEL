<?php
/****************************** Inicio vCuerpoPagarCuotaSocio.php *****************************
FICHERO: vCuerpoPagarCuotaSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: En el formulario, si la cuota anual NO está pagada, se muestran la cuota del socio
             los datos bancarios (si los hay), y otra información del socio y se le indica 
													los modos de pagar la cuota anual:
           - Se muestran las cuentas bancarias de donde se cobran a las distintas agrupaciones, 
											  se leen de las tablas de AGRUPACIONTERRITORIAL (a fecha 01_08_2021 todas menos Asturias
													están centralizadas y comparten la misma cuenta bancaria, Asturias muestra su cuenta) 
           - Además hay un botón de enlace a PayPal (a fecha 01_08_2021 todas menos Asturias), 
											  donde ya se incluye la cantidad a pagar y demás datos del socio. 

           Si la cuota anual ya está pagada se indica y se ofrece la opción de hacer una donación		
											
LLAMADA:	vistas/socios/vPagarCuotaSocioInc.php 
y a su desde: controladoSocios:altaSocio(), confirmarAltaSocio(), pagarCuotaSocio()
LLAMA: vistas/socios/formPagarCuotaSocio.php
													
OBSERVACIONES:Se le llama desde vPagarCuotaSocioInc.php 
              inclulle a /vistas/socios/formPagarCuotaSocio.php
**********************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/**************************** Inicio Cuerpo central ************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>
<br /><br />
<h3 align="center">
    <!-- PAGAR CUOTA SOCIO / A	 -->  
    <strong>
        <?php
        if (isset($arrayParamMensaje['textoCabecera'])) 
								{
            echo $arrayParamMensaje['textoCabecera'];
        }
        ?>						
    </strong>				
</h3>

<br />
<span class="textoAzu112Left2">  
    <?php
    if (isset($arrayParamMensaje['textoComentarios'])) 
				{
        echo $arrayParamMensaje['textoComentarios'];
    }
    ?>	
</span>					
<br />		
<!--******************* Inicio form Mostrar datos: socio, bancos EL y botón PayPal ******-->

<?php require_once './vistas/socios/formPagarCuotaSocio.php'; ?>

<!--********************  Fin Mostrar datos: socio, bancos EL y botón PayPal  ***********-->

<!-- ********************  Inicio form botón anterior******************** --> 		
<div align="center">

    <?php
    if (isset($navegacion['anterior'])) 
				{
        echo $navegacion['anterior'];
    }
    ?>	
    <br />
    <?php
    if (isset($arrayParamMensaje['enlaceBoton']) &&
            ($arrayParamMensaje['enlaceBoton'] !== '') &&
            ($arrayParamMensaje['enlaceBoton'] !== NULL)) 
				{
        echo "<form method='post' action=" . $arrayParamMensaje['enlaceBoton'] . ">" .
        " <input type='submit' value='" . $arrayParamMensaje['textoBoton'] . "'>";
        echo " </form>";
    }
    ?>				

</div 
<!-- ********************  Fin Form botón anterior *********************** -->
</div>

<!--****************************** Fin Cuerpo central  ********************-->
</div>

<!--******************************* Fin Cuerpo vCuerpoPagarCuotaSocio.php ***************-->