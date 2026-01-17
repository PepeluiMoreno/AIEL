<?php
/****************************** Inicio Cuerpo vCuerpoDonarSocio.php ***********************
FICHERO: vCuerpoDonarSocio.php
Válido para donaciones por el propio socio,
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:
Se muestran los datos de los bancos ($cadBancos) donde puede donar un socio 
(los correspondientes a sus agrupaciones de cobro de cuotas) y un enlace a 
un botón de PayPal para donar (por ahora solo el de EL).

$payPalScriptDona: incluye la dirección del script para hacer donación con PayPal 
(botón estandar para ESTATAL)

Contiene menú idz de "Secciones" y botón submit

LAMADA: vistas/sociosd/vDonarSocioInc.php y a su vez desde controladoSocios.php:donarSocio() 

OBSERVACIONES:
2022-12-28: Cambio texto
******************************************************************************************/	
require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- ************************ Inicio Cuerpo central ************************ -->
<?php
if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	 

<br /><br />
<h3 align="center">	  	<!-- Cabecera de la donación -->
    <strong>HACER UNA DONACIÓN A "Europa Laica"</strong> 	
</h3>

<br /><br />

<span class="textoAzu112Left2">
    Estimado socio/a, según nuestros estatutos, Europa Laica <strong>NO acepta subvenciones</strong>. 
    <br /><br />
				Nuestros ingresos proceden de las cuotas y donaciones de nuestras socias, socios y simpatizantes,  
    y puntualmente de la venta de los libros que Europa Laica publica.
    <!--<br /><br />				
				Por eso necesitamos tu colaboración económica para pagar los gastos de funcionamiento como: 
				alojamientos de la web y la base de datos de socias/os, imprentas para carteles, etc.-->
</span>			    

<br /><br /><br />

<div id="registro"> 

    <fieldset>
        <legend><strong>Modos de hacer una donación a la asociación Europa Laica</strong></legend>
        <p>		

            <span class="textoGris8Left2">
												<br />
                - Puedes realizar la donación mediante ingreso directo o transferencia a: 			

                <strong>
                    <?php
                    //Imprime las cuentas bancarias de pago de cuotas de la asociación, o de la agrupación si esta gestiona los cobros 
                    //echo $cadBancos;
                    echo $cadBancos['cadenaBancos'];
                    ?>
                </strong>
                <br /><br />
                Señala como concepto: Donación, NIF, nombre y apellidos.
                <br /><br /><br />

                - También puedes donar ahora con tarjeta de crédito (o si tienes una cuenta de PayPal), mediante pago con <strong>PayPal</strong>.
                <br /><br />
                Para pagar ahora con PayPal, haz clic en <strong>"Donar"</strong>		

                <?php
                //require_once './vistas/plantillasGrales/scriptPayPalPagarAhora.php'; 

                if (isset($payPalScriptDona) && !empty($payPalScriptDona)) {
                    require_once $payPalScriptDona;
                }
                ?>		

            </span>

            <span class="textoGris8Left2">		
                Por si hubiese algún problema, nos puedes confirmar tu pago enviando un correo electrónico 
                a <strong><?php echo $cadBancos['emailTesoreroAgrupacion']; ?></strong> 
                con asunto: donación, y dentro del mensaje los datos: NIF, nombre y apellidos, cantidad, fecha pago y entidad dónde has pagado.
                <br /><br />
            </span>	
        </p>
    </fieldset>
</div>
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