<?php
session_start();
/*--------------------------------------------------------------------------------------------------
FICHERO: cPayPal.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: En este fichero se encuentran las funciones relacionadas con 
             PayPal, mostrar mensajes después de pago correcto con 
													PayPal, o cancelación de pago. 
													También hace anotaciones de pagos en la tabla CUATAANIOSOCIOS a partir del POST 
													recibido desde PayPal en la BBDD en el caso de que ya esté confirmada el alta del socio
													confirmarPagoPayPal().
             En el caso de socio pendiente confirmar alta confirmadoPagoAltaSocioPayPal_Registrarse, 
													solo muestra mensajes después de pago correcto con PayPal, o cancelación de pago 
													(se podría añadir actualizar datos en la tabla SOCIOSCONFIRMAR)  													
													preparado

OBSERVACIONES: 
---------------------------------------------------------------------------------------------------*/


/*--------------------------- Inicio  confirmadoPagoAltaSocioPayPal_Registrarse -------------------
Agustin 2018-01-15: Cambios en las líneas 158 a 185

Después de solicitar el alta como socio, pero antes de confirmar el alta.
En esta función se muesta un formulario que indicando que el pago con paypal ha 
sido correcto, y envía un email a tesoreria@europalaica informándole del pago por PayPal. 
Como aún no ha confimado su alta no se actualiza la tabla CUATAANIOSOCIO con ese pago.

Ojo esto solo se produce después de que pulse en PayPal el link volver a Europa Laica 
(o que por defecto deje a PayPal volver). En caso de que cierre rápidamente la ventana, 
no se ejecutará esta función. Ver OBSERVACIONES  

LLAMADO: Desde el link del texto que se pone en PayPal, en el proceso de altaSocio (en la fase 
         incial de registrarse antes de confirmar el alta), 
         después de efectuar un pago aceptado por PayPal que haya sido correcto, 
									y después pulse en PayPal el link volver a Europa Laica. 
									(si sale cerrando PayPal, no se volverá a esta función de confirmación)
										
RECIBE: un POST desde PayPal, con datos que se pueden utilizar para informar al socio, 
        en la pantalla y/o enviando un email a socio, tesorero, etc. 
								Los datos que no vienen en el POST desde PayPal habría que buscarlos con 
								la función modeloSocios.php:buscarDatosSocioConfirmar($codUser). 
								Sus datos solo estarían en la tabla SOCIOSCONFIRMAR

LLAMA A: modeloSocios.php:buscarDatosSocioConfirmar(),buscarDatosAgrupacion(),
         modeloEmai.php:emailPagoPayPalTesoria()
require_once './vistas/PayPal/vMensajeVolverPayPalInc.php'

OBSERVACIONES: 
		
- Pega: El problema de salir el socio cerrando la ventana de PayPal, en lugar de pulsar el link, 
  es que no se volverá a esta función de confirmación y no se enviará el mensaje de confirmación.
  ni se actualizrá la tabla CUATAANIOSOCIOS, por lo que el tesorero deberá revisar siempre la pago de PayPal
  Existe otro sistema de confirmar, que es el IPN, que trabaja de forma asincróna,
  necesita un listener, en Nodo50, para escuchar los sucesivos envíos desde PayPal,
		hasta que se contesta que ha sido recibido.
--------------------------------------------------------------------------------------------------*/
function confirmadoPagoAltaSocioPayPal_Registrarse()
{//echo "<br><br>0-a cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:SESSION:";print_r($_SESSION);	
 //echo "<br><br>0-b cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:_POST:";print_r($_POST);
	
 //*** COMO AÚN NO ES ALTA COMO SOCIO NO PUEDE ENTRAR CON SU ROL Y NO TIENE OPCIÓN DE NAVEGACIÓN ***
 //------------ inicio navegación para socios gestores CODROL >2 ----------------------------------	
 /*if (isset($_SESSION['vs_autentificadoGestor']))// se comenta porque antes de confirmar el alta no existe 	CODROL
 {	
 $pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
	if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio" ||
	    $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")			
	{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
	}
 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=controladorSocios&accion=pagarCuotaSocio";	
 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Pagar cuota anual";
 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;
	require_once './controladores/libs/cNavegaHistoria.php';
 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
	}
	else	*/
	{
		$navegacion = '';
	} 
	//echo "<br><br>1-a cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
 //echo "<br><br>1-b cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:SESSION: ";print_r($_SESSION);

	require_once './modelos/modeloSocios.php';
	require_once './vistas/mensajes/vMensajeCabInicialInc.php';		
	require_once './modelos/modeloEmail.php';
 
	$datosMensaje['textoCabecera'] = 'Confirmado pago con PayPal';		
 $datosMensaje['textoBoton'] = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	
			
 $tituloSeccion  = "Socios/as";		
	
	if (!isset($_POST['payment_status']) || $_POST['payment_status']!== 'Completed')//siempre entrara por aqui, si no intento directo 
	{ //($reAltaSocioConfirmar['codError'] == '00000')
	  $datosMensaje['textoCabecera'] ='Alta de socios y socias. Pago con PayPal';
	  $datosMensaje['textoComentarios'] = 'Error al efectuar el pago con PayPal';
		 vMensajeCabInicialInc($tituloSeccion,$datosMensaje,'');
	}	
 else //isset($_POST[payment_status] && $_POST[payment_status]== 'Completed')
 {
		if (isset($_POST['custom']) && !empty($_POST['custom']))//codUser enviado en formulario script './vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php';
		{$codUser = $_POST['custom'];
			
	  //---Inicio Preparar datos para email y enviar a tesorero-coordinador	confirmando pago PayPal ---
		
		 $reDatosSocio = buscarDatosSocioConfirmar($codUser);//SALE solo [CODAGRUPACION], PERO NO DATOS AGRUPACIÓN
			//Mejora posible: despues se podría anotar la cuota pagada en "SOCIOSCONFIRMAR"
   
			//echo"<br><br>2-b1 cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:reDatosSocio:";print_r($reDatosSocio);				
			
			if ($reDatosSocio['codError'] !== '00000')
	  {$reDatosSocio['textoCabecera'] ='Alta de socios y socias. Pago con PayPal';
			 vMensajeCabInicialInc($tituloSeccion,$reDatosSocio,'');
		  $resEmailErrorWMaster = emailErrorWMaster($reDatosSocio['codError'].": ".$reDatosSocio['textoComentarios']);
	  }	
			else//($reDatosSocio['codError'] == '00000')
			{				
			 $codAgrupacion = $reDatosSocio['resultadoFilas'][0]['CODAGRUPACION'];
			 
				//echo"<br><br>2-b2 cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:codAgrupacion:";print_r($codAgrupacion);
				
				$datosPagoPayPal['conceptoPago'] = "Pago al registrase como socio/a: ".$codUser;		
				
    $datosPagoPayPal['numeroDocumento']  = $reDatosSocio['resultadoFilas'][0]['NUMDOCUMENTOMIEMBRO'];//12692547C 
    $datosPagoPayPal['codPaisDocumento'] = $reDatosSocio['resultadoFilas'][0]['CODPAISDOC'];// => ES
    $datosPagoPayPal['tipoDocumento']    = $reDatosSocio['resultadoFilas'][0]['TIPODOCUMENTOMIEMBRO'];//NIF,pasaporte,otros	
				
			 //-- Inicio datos socio, obtenidos de BBDD EL que pueden ser distintos de persona que paga en paypal ---
		 	$apeNomSocio = $reDatosSocio['resultadoFilas'][0]['APE1']." ".
			                $reDatosSocio['resultadoFilas'][0]['APE2'].", ".
			                $reDatosSocio['resultadoFilas'][0]['NOM'];
    $datosPagoPayPal['apeNomSocio'] =	$apeNomSocio;		
					
    //echo"<br><br>3-a cPayPal:confirmarPagoAltaPayPalEmail:datosPagoPayPal:";print_r($datosPagoPayPal);				
			 //----- Fin Datos del socio que pueden ser distintos de la persona que paga en PayPal ----------
			
		  //---- Inicio datos que proceden del POST que envía PayPal, Despues de aceptar un pago ---------
			 //---LOS DATOS NOMBRE PAGADOR QUE ENVÍA PAYPAL SON DEL PAGADOR Y PUEDE SER DISTINTO DEL SOCIO --				
				$datosPagoPayPal['nombre'] = $_POST['first_name'];	
				$datosPagoPayPal['apellidos'] = $_POST['last_name'];		
		  $datosPagoPayPal['emailPagadorPayPal']  = $_POST['payer_email'];				
				//$datosPagoPayPal['numReciboFactura'] = $_POST['receipt_id'];//Su número de recibo para este pago es: 3519-6435-6087-9186.	 y daría warning		
				$datosPagoPayPal['IdentificadorTransaccion'] = $_POST['txn_id'];//Id. de transacción exclusivo	tipo 7D454324S40097843	
						 		
				$strFecha = $_POST['payment_date']; //formato PayPal //11:38:53 Jan 03, 2015 PST    
				$date = new DateTime($strFecha);
				$date->setTimezone(new DateTimezone('UTC'));//El tiempo universal coordinado, o UTC
				//$payment_date = $date->format('Y-m-d\T H:i:s\ Z');
		  //$datosPagoPayPal['fechaPago'] = $date->format('Y-m-d: H:i:s');//formato 2015-03-01: 11:38:53
				$datosPagoPayPal['fechaPago'] = $date->format('Y-m-d');//formato 2015-03-01		
						
			 $datosPagoPayPal['producto'] = $_POST['item_name'];//'PAGAR CUOTA ANUAL A EUROPA LAICA' 
				$datosPagoPayPal['identificadorProducto'] = $_POST['item_number'];	//'CUOTA 2018'
		
				//Total pagado por el socio = $_POST['mc_gross']
				$datosPagoPayPal['importe'] = $_POST['mc_gross']." ".$_POST['mc_currency'];//con EUR al final				
	
				//$datosPagoPayPal['importe'] = $_POST['mc_gross']." ".$_POST['mc_currency'];//con EUR al final
				//devuelve $_POST['tax'] si hay <input type="hidden" name="tax_rate" value="10.000">	en  scriptPayPalPagoCuotaElegidaAhora.php
				//pero no devuelve $_POST['tax_rate']
				
				if (!isset($_POST['tax']) || empty($_POST['tax']))
				{ $datosPagoPayPal['IVA'] = 0.00;					
				}
    else
    { $datosPagoPayPal['IVA'] = $_POST['tax'];	      
				}	
				$datosPagoPayPal['IVA'] = $datosPagoPayPal['IVA']." ".$_POST['mc_currency'];	 
				
				$datosPagoPayPal['importe'] = $_POST['mc_gross'] - $_POST['tax']." ".$_POST['mc_currency'];//importe sin IVA con EUR al final			

				$datosPagoPayPal['totalPagos'] = $_POST['mc_gross']." ".$_POST['mc_currency'];
				
				$datosPagoPayPal['gastosPayPal'] = $_POST['mc_fee']." ".$_POST['mc_currency'];//Comisión PayPal
	   //--- FIN datos que proceden del POST que envía PayPal, Despues de aceptar un pago -------------

					
				$datosPagoPayPal['observaciones'] = 'Este pago con PayPal, se ha realizado en el proceso incial de alta del socio/a, y en este momento aún no ha confirmado su alta, 
				por lo que no se le podrá encontrar en el menú de -Cuotas socios/as, pero sí estará en -Confirmación de soci@s. 
				Hasta que confirme su alta por el mismo/a o por un gestor no se podrá anotar el pago de la cuota. 							
				Fecha pago de PayPal: '.$datosPagoPayPal['fechaPago']." Id. de transación: ".$datosPagoPayPal['IdentificadorTransaccion'];				
				
				//echo"<br><br>3b cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:datosPagoPayPal: ";print_r($datosPagoPayPal);

					
				require_once './modelos/modeloSocios.php';	  
		  $resDatosAgrupacion = buscarDatosAgrupacion($codAgrupacion);
	   //echo"<br><br>5-a cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:resDatosAgrupacion: ";print_r($resDatosAgrupacion);			
		 
	   if ($resDatosAgrupacion['codError'] !== '00000')
		  {$resDatosAgrupacion['textoCabecera'] ='Alta de socios y socias. Pago con PayPal';
				 vMensajeCabInicialInc($tituloSeccion,$resDatosAgrupacion,'');
			  $resEmailErrorWMaster = emailErrorWMaster($resDatosAgrupacion['codError'].": ".$resDatosAgrupacion['textoComentarios']);
		  }
				else//f ($resDatosAgrupacion['codError'] == '00000')
		  {
	   //OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL Tesorería
	   //****************************************************************************************************************
			  $datosEmailCoSeTe = $resDatosAgrupacion['resultadoFilas'][0];
	    //echo"<br><br>5-b cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:datosEmailCoSeTe: ";print_r($datosEmailCoSeTe);
	
			  $reEnviarEmailCoSeTe = emailPagoPayPalTesoria($datosEmailCoSeTe,$datosPagoPayPal);	  
	
	   //FIN COMENTAR *********************************************************************************					
		   //echo"<br><br>6  cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:reEnviarEmailCoSeTe:";print_r($reEnviarEmailCoSeTe);
		  }	
	   //-- Fin Preparar datos para email y enviar a tesorero y coordinador	confirmando pago PayPal  --
				
	   //----- Inicio Preparar datos para mensaje en pantalla a socio confirmando pago con PayPal -----	
					
				$cadenaDatosAnotadosPorPaypal =
						"Pago de la cuota del socio/a: <strong>".$datosPagoPayPal['apeNomSocio']."</strong><br /><br />".
						"DATOS DE LA PERSONA PAGADORA:<br /><br />".
					 "Nombre: <strong>".$datosPagoPayPal['nombre']." ".$datosPagoPayPal['apellidos']."</strong><br />".
		    "Email: ".$datosPagoPayPal['emailPagadorPayPal']."<br /><br />".
			   "Concepto: ".$datosPagoPayPal['producto'].": ".$datosPagoPayPal['identificadorProducto']."<br />".
						"Fecha anotación pago: ".$datosPagoPayPal['fechaPago']."<br />".
						"Identificador de transacción: ".$datosPagoPayPal['IdentificadorTransaccion']."<br /><br />".
				  "Importe: <strong>".$datosPagoPayPal['importe']."</strong><br />".
				  "IVA&nbsp;: ".$datosPagoPayPal['IVA']."<br />".
						"---------------------------<br />".	
				  "Total: <strong>".$datosPagoPayPal['totalPagos']."</strong><br /><br /><br /><br />". 
				  "Nota: comisión que pagará Europa Laica a PayPal: ".$datosPagoPayPal['gastosPayPal']. 
						"<br /><br />Si quieres, para evitar estos costes, puedes domiciliar el pago de la cuota en la opción del menú: - Actualizar datos socio/a ";
												
		  //echo"<br><br>4 cPayPal:confirmarPagoPayPal:cadenaDatosAnotadosPorPaypal:";print_r($cadenaDatosAnotadosPorPaypal);
					
			 $datosMensaje['textoCabecera'] = 'Registrar nuevo socio/a. Confirmación de pago con PayPal';		
			 $datosMensaje['textoComentarios'] =
					"Te llegará un correo electrónico, para que termines de confirmar tu intención de asociarte a Europa Laica.						
					 <br /><br />La orden de pago de tu cuota anual mediante PayPal, se ha realizado de modo correcto. 
						<br /><br />PayPal verificará que los datos del pagador y posteriormente lo ingresará en la cuenta de Europa Laica<br /><br />".
						$cadenaDatosAnotadosPorPaypal.
				  "<br /><br /><br />
						La tesorería de Europa Laica, cuando revise los extractos bancarios, lo anotará en la 
						aplicación de gestión de socios/as como cuota pagada. Puede pasar algún tiempo antes de que se anote el pago.
						<br /><br /> 						
				  Por si hubiese algún problema, nos puedes confirmar tu pago envíandonos un correo electrónico a
						<strong>".$datosEmailCoSeTe['EMAILTESORERO']."</strong> 
						 indicando asunto: Cuota con PayPal, NIF, nombre y apellidos, cantidad y fecha pago.";
						
				//$datosMensaje['textoBoton'] = 'Salir de la aplicación';
			 //$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	
	
		  //echo "<br><br>7 cPayPal:confirmadoPagoAltaSocioPayPal_Registrarse:datosMensaje: ";print_r($datosMensaje);	 		 		 
	 
		  require_once './vistas/PayPal/vMensajeVolverPayPalInc.php';	
	  	vMensajeVolverPayPalInc($tituloSeccion,$datosMensaje,$navegacion );
	   //----- Fin Preparar datos para mensaje en pantalla a socio confirmando pago con PayPal --------
	
		 }	// ($reDatosSocio['codError'] == '00000')			 
  }////isset($_POST[payment_status] && $_POST[payment_status]== 'Completed')
 }
}	
//--------------------------- Fin confirmadoPagoAltaSocioPayPal_Registrarse ------------------------


/*--------------------------- Inicio  confirmarPagoPayPal() -----------------------------------
Agustin 2018-01-18: Cambios de las líneas 394 a 461

En esta función se muesta un formulario al socio, indicando que el pago con paypal ha 
sido correcto, anota el pago en la tabla  "CUOTAANIOSOCIO" y envía un email a tesoreria@europalaica
informándole del pago por PayPal. 
Ojo esto solo se produce después de que pulse en PayPal el link volver a Europa Laica después de pagar
(o que por defecto deje a PayPal volver). En caso de que cierre rápidamente la ventana, no se ejecutará esta función. 
Ver OBSERVACIONES:  
LLAMADO: Desde el link del texto que se pone en PayPal, después de aceptar un pago por PayPal 						
										
RECIBE: un POST desde PayPal, con datos que se pueden utilizar para informar al socio, 
        en la pantalla y/o enviando un email a socio, tesorero, etc.
								Los datos que no vienen en el POST desde PayPal habría que buscarlos con 
								la función modeloSocios.php:buscarDatosSocio()

LLAMA A: modeloSocios.php:buscarDatosSocio(),buscarDatosAgrupacion(),actualizCuotaAnioSocio()
         modeloEmail.php:emailPagoPayPalTesoria()
				     ./vistas/PayPal/ vMensajeVolverPayPalSinLinkDonarInc.php
									
OBSERVACIONES: 

- Incluye: **** ACTUALIZAR LA TABLA DE "CUOTAANIOSOCIO"		AL VOLVER DE PAYPAL *****
		
- Pega: El problema de salir el socio cerrando la ventana de PayPal, en lugar de pulsar el link, 
  es que no se volverá a esta función de confirmación y no se enviará el mensaje de confirmación.
  ni se actualizrá la tabla CUATAANIOSOCIOS, por lo que el tesorero deberá revisar siempre la pago de PayPal
  
		Existe otro sistema de confirmar, que es el IPN, que trabaja de forma asincróna,
  necesita un listener, en Nodo50, para escuchar los sucesivos envíos desde PayPal,
		hasta que se contesta que ha sido recibido.	
--------------------------------------------------------------------------------------------------*/
function confirmarPagoPayPal()
{	
 //echo "<br><br>0-a cPayPal:confirmarPagoPayPal:SESSION:";print_r($_SESSION);	
 //echo "<br><br>0-b cPayPal:confirmarPagoPayPal:_POST:";print_r($_POST);	

 //------------ inicio navegación para socios gestores CODROL >2 -----------------------------------	
 if (isset($_SESSION['vs_autentificadoGestor']))
 {	
	 $pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio" ||
		    $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=cTesorero&accion=mostrarIngresosCuotas")			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=controladorSocios&accion=pagarCuotaSocio";	
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Pagar cuota anual";
	 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
		require_once './controladores/libs/cNavegaHistoria.php';
	 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior");
	}
	else
	{
		$navegacion = '';
	} 
	//echo "<br><br>1-a cPayPal:confirmarPagoPayPal:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);
 //echo "<br><br>1-b cPayPal:confirmarPagoPayPal:SESSION: ";print_r($_SESSION);

	require_once './modelos/modeloSocios.php';
	require_once './vistas/mensajes/vMensajeCabInicialInc.php';		
	require_once './modelos/modeloEmail.php';
 
	$datosMensaje['textoCabecera'] = 'Confirmado pago con PayPal';		
 $datosMensaje['textoBoton'] = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	
			
 $tituloSeccion  = "Socios/as";		
	
	if (!isset($_POST['payment_status']) || $_POST['payment_status']!== 'Completed')//siempre entrara por aqui, si no intento directo 
	{ //($reAltaSocioConfirmar['codError'] == '00000')
	  $datosMensaje['textoComentarios'] = 'Error al efectuar el pago con PayPal';
		 vMensajeCabInicialInc($tituloSeccion,$datosMensaje,'');
	}	
 else //isset($_POST[payment_status] && $_POST[payment_status]== 'Completed')
 {	
		if (isset($_POST['custom']) && !empty($_POST['custom']))//codUser enviado en formulario script './vistas/PayPal/scriptPayPalPagoCuotaElegidaAhora.php';
		{
	  //--Inicio Preparar datos socio para email y enviar a tesorero-coordinador	confirmando pago PayPal  ---
		
		 $codUser = $_POST['custom'];
		
		 $reDatosSocio = buscarDatosSocio($codUser,date('Y'));//PARA ENVIAR EMAILS A TESORERO Y MENSAJE A SOCIO

   //echo"<br><br>2-a1 cPayPal:confirmarPagoPayPal:reDatosSocio:";print_r($reDatosSocio);	echo"<br><br>";		
			
			if ($reDatosSocio['codError'] !== '00000')
	  {$reDatosSocio['textoCabecera'] ='Pago con PayPal. Confirmado pago con PayPal';
			 vMensajeCabInicialInc($tituloSeccion,$reDatosSocio,'');
		  $resEmailErrorWMaster = emailErrorWMaster($reDatosSocio['codError'].": ".$reDatosSocio['textoComentarios']);
	  }	
			else	//($reDatosSocio['codError'] == '00000')
			{	
			 //--Inicio datos del socio, obtenidos de BBDD EL que pueden ser distintos de persona que paga en paypal ---
		 	$apeNomSocio = $reDatosSocio['valoresCampos']['datosFormMiembro']['APE1']['valorCampo']." ".
			                $reDatosSocio['valoresCampos']['datosFormMiembro']['APE2']['valorCampo'].", ".
			                $reDatosSocio['valoresCampos']['datosFormMiembro']['NOM']['valorCampo'];
    $datosPagoPayPal['apeNomSocio'] =	$apeNomSocio;	
				
				$codAgrupacion = $reDatosSocio['valoresCampos']['datosFormSocio']['CODAGRUPACION']['valorCampo'];
				//echo"<br><br>2-a2 cPayPal:confirmarPagoPayPal:codAgrupacion:";print_r($codAgrupacion);		
				
				$datosPagoPayPal['tipoDocumento']   = $reDatosSocio['valoresCampos']['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']['valorCampo'];//NIF, pasaporte, otros											
			 $datosPagoPayPal['numeroDocumento'] = $reDatosSocio['valoresCampos']['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'];//12692547C			 
			 $datosPagoPayPal['codPaisDocumento']= $reDatosSocio['valoresCampos']['datosFormMiembro']['CODPAISDOC']['valorCampo'];// => ES			   
		
    //echo"<br><br>3-a cPayPal:confirmarPagoAltaPayPalEmail:datosPagoPayPal:";print_r($datosPagoPayPal);				
			 //----- Fin Datos del socio que pueden ser distintos de la persona que paga en PayPal ----------
			
		  //---- Inicio datos que proceden del POST que envía PayPal, Despues de aceptar un pago ---------				
			 //--- LOS DATOS DE NOMBRE DE PAGADOR QUE ENVÍA PAYPAL EN $_POST PUEDE SER DISTINTO DEL SOCIO ---
				
				$datosPagoPayPal['nombre'] = $_POST['first_name'];	
				$datosPagoPayPal['apellidos'] = $_POST['last_name'];		
		  $datosPagoPayPal['emailPagadorPayPal']  = $_POST['payer_email'];
				
				//$datosPagoPayPal['numReciboFactura'] = $_POST['receipt_id'];//Su número de recibo para este pago es: 3519-6435-6087-9186.	no le he puesto valor valor		y daría un warning
				$datosPagoPayPal['IdentificadorTransaccion'] = $_POST['txn_id'];//Id. de transacción exclusivo	tipo: 7D454324S40097843	 
						 		
				$strFecha = $_POST['payment_date']; //formato PayPal //11:38:53 Jan 03, 2015 PST    
				$date = new DateTime($strFecha);
				$date->setTimezone(new DateTimezone('UTC'));//El tiempo universal coordinado, o UTC
				//$payment_date = $date->format('Y-m-d\T H:i:s\ Z');
		  //$datosPagoPayPal['fechaPago'] = $date->format('Y-m-d: H:i:s');//formato 2015-03-01: 11:38:53
				$datosPagoPayPal['fechaPago'] = $date->format('Y-m-d');//formato 2015-03-01		
						
			 $datosPagoPayPal['producto'] = $_POST['item_name'];//'PAGAR CUOTA ANUAL A EUROPA LAICA' 
				$datosPagoPayPal['identificadorProducto'] = $_POST['item_number'];	//CUOTA 2018	
				
				//Total pagado por el socio = $_POST['mc_gross']
				//$datosPagoPayPal['importe'] = $_POST['mc_gross']." ".$_POST['mc_currency'];//con EUR al final
				//devuelve $_POST['tax'] si hay <input type="hidden" name="tax_rate" value="10.000">	en  scriptPayPalPagoCuotaElegidaAhora.php
				//pero no devuelve $_POST['tax_rate']

				if (!isset($_POST['tax']) || empty($_POST['tax']))
				{ $datosPagoPayPal['IVA'] = 0.00;					
				}
    else
    { $datosPagoPayPal['IVA'] = $_POST['tax'];	      
				}	
				$datosPagoPayPal['IVA'] = $datosPagoPayPal['IVA']." ".$_POST['mc_currency'];	 
				
				$datosPagoPayPal['importe'] = $_POST['mc_gross'] - $_POST['tax']." ".$_POST['mc_currency'];//importe restando IVA con EUR al final			

				$datosPagoPayPal['totalPagos'] = $_POST['mc_gross']." ".$_POST['mc_currency'];
				
				$datosPagoPayPal['gastosPayPal'] = $_POST['mc_fee']." ".$_POST['mc_currency'];//Comisión PayPal
				
	   //---- FIN datos que proceden del POST que envía PayPal, Despues de aceptar un pago ------------
				

				$datosPagoPayPal['observaciones'] = "Fecha pago de PayPal: ".$datosPagoPayPal['fechaPago']." Id. de transación: ".$datosPagoPayPal['IdentificadorTransaccion'];							
		
				//$datosPagoPayPal['emailTesoreria'] = $_POST['receiver_email'];	//pero mejor el que se busca en la BBDD	
					
				//echo"<br><br>3a cPayPal:confirmarPagoPayPal:datosPagoPayPal: ";print_r($datosPagoPayPal);

				$codSocio = $reDatosSocio['valoresCampos']['datosFormSocio']['CODSOCIO']['valorCampo'];
				
				$arrayDatosAct['ANIOCUOTA']['valorCampo'] = date('Y');
				
 	  //$arrayDatosAct['CODCUOTA']['valorCampo'] = $reDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['CODCUOTA']['valorCampo'];				

	   //--- Inicio Nuevo por si un socio modifica la cantidad y paga menos que IMPORTECUOTAANIOEL 
			 // o ya hay algo pagado, se añade esta cantidad a la ya pagada o a los gastos                     
			 //----------------------------------------------------------------------------------------------
 	 	if($reDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['IMPORTECUOTAANIOEL']['valorCampo'] <= $reDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['IMPORTECUOTAANIOPAGADA']['valorCampo']
				                                                                                                           + $_POST['mc_gross'] 
						)
				{$arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'ABONADA';
     $arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'NO';				
			 }
				else
				{ $arrayDatosAct['ESTADOCUOTA']['valorCampo'] = 'ABONADA-PARTE';
      $arrayDatosAct['ORDENARCOBROBANCO']['valorCampo'] = 'SI';				
				}
				
    if ($reDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['IMPORTECUOTAANIOPAGADA']['valorCampo'] >0)	
    {					
				 $arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] = $reDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['IMPORTECUOTAANIOPAGADA']['valorCampo'] + 
					                                                          $_POST['mc_gross']; 
				}
				else
				{ $arrayDatosAct['IMPORTECUOTAANIOPAGADA']['valorCampo'] = $_POST['mc_gross']; 
				}			
				
    if ($reDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['IMPORTEGASTOSABONOCUOTA']['valorCampo'] >0)	
    {					
				 $arrayDatosAct['IMPORTEGASTOSABONOCUOTA']['valorCampo'] = $reDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['IMPORTEGASTOSABONOCUOTA']['valorCampo'] + 
					                                                          $datosPagoPayPal['gastosPayPal'];	
				}
				else
				{ $arrayDatosAct['IMPORTEGASTOSABONOCUOTA']['valorCampo'] = $datosPagoPayPal['gastosPayPal'];
				}	
    //--- Fin  Nuevo por si un socio modifica la cantidad y paga menos que IMPORTECUOTAANIOEL ------
	
 			$arrayDatosAct['MODOINGRESO']['valorCampo'] = 'PAYPAL';			
 			$arrayDatosAct['FECHAPAGO']['valorCampo'] = $datosPagoPayPal['fechaPago'];				
 			$arrayDatosAct['FECHAANOTACION']['valorCampo'] = $datosPagoPayPal['fechaPago'];				
 			$arrayDatosAct['OBSERVACIONES']['valorCampo'] = $reDatosSocio['valoresCampos']['datosFormCuotaSocio'][date('Y')]['OBSERVACIONES']['valorCampo']."Fecha pago PayPal: ".$datosPagoPayPal['fechaPago'].
				                                                " Id. transación: ".$datosPagoPayPal['IdentificadorTransaccion']. 
				                                                '. Anotación automática pago PayPal, realizada por la aplicación informática. Comprobar que no se devuelve';	
				
				//echo"<br><br>3b cPayPal:confirmarPagoPayPal:arrayDatosAct: ";print_r($arrayDatosAct);				
    
				require_once './modelos/modeloSocios.php';					
				$resActualizCuotaAnioSocio = actualizCuotaAnioSocio('CUOTAANIOSOCIO',$codSocio,$arrayDatosAct);
				
			 //echo"<br><br>3c cPayPal:confirmarPagoPayPal:resActualizCuotaAnioSocio: ";print_r($resActualizCuotaAnioSocio);
			
	   if ($resActualizCuotaAnioSocio['codError'] !== '00000')
		  {$resActualizCuotaAnioSocio['textoCabecera'] ='Pago con PayPal. Confirmado pago con PayPal';
				 // vMensajeCabInicialInc($tituloSeccion,$resDatosAgrupacion,'');
				 $textoError = ". No se ha podido actualizar automáticamente la tabla 'CUOTAANIOSOCIO' después de Pagar con PayPal, para este socio/a CODUSER:".$codUser;
				 $resActualizCuotaAnioSocio['textoComentarios'] .= $textoError;
 																																																								
			  $resEmailErrorWMaster = emailErrorWMaster($resActualizCuotaAnioSocio['codError'].": ".$resActualizCuotaAnioSocio['textoComentarios']);
					
     $datosPagoPayPal['observaciones'] = $textoError;
				}						

				require_once './modelos/modeloSocios.php';	  
		  $resDatosAgrupacion = buscarDatosAgrupacion($codAgrupacion);
				
	   //echo"<br><br>4 cPayPal:confirmarPagoPayPal:$resDatosAgrupacion:";print_r($resDatosAgrupacion);
			
	   if ($resDatosAgrupacion['codError'] !== '00000')
		  {$resDatosAgrupacion['textoCabecera'] ='Pago con PayPal. Confirmado pago con PayPal';
				 vMensajeCabInicialInc($tituloSeccion,$resDatosAgrupacion,'');
			  $resEmailErrorWMaster = emailErrorWMaster($resDatosAgrupacion['codError'].": ".$resDatosAgrupacion['textoComentarios']);
		  
				}
				else//($resDatosAgrupacion['codError'] == '00000')
		  {
	   //OJO	*** cuando se quiera hacer pruebas comentar aquí o en modeloEmail.php, PARA QUE NO LLEGUEN EMAIL Tesorería
	   //****************************************************************************************************************
			  $datosEmailCoSeTe = $resDatosAgrupacion['resultadoFilas'][0];
	    //echo"<br><br>5 cPayPal:confirmarPagoPayPal:datosEmailCoSeTe:";print_r($datosEmailCoSeTe);
	
			  $reEnviarEmailCoSeTe = emailPagoPayPalTesoria($datosEmailCoSeTe,$datosPagoPayPal);
	
	   //FIN COMENTAR *********************************************************************************
				
		   //echo"<br><br>6  cPayPal:confirmarPagoPayPal:reEnviarEmailCoSeTe:";print_r($reEnviarEmailCoSeTe);
				}//else($resDatosAgrupacion['codError'] == '00000')	
	   //-- Fin Preparar datos email y enviar a tesorero y coordinador	confirmando pago con PayPal-----	
				
	   //----- Inicio Preparar datos para mensaje en pantalla a socio confirmando pago con PayPal -----
				
				$cadenaDatosAnotadosPorPaypal =
				  "Pago de la cuota del socio/a: <strong>".$datosPagoPayPal['apeNomSocio']."</strong><br /><br />".
						"DATOS DE LA PERSONA PAGADORA:<br /><br />".
					 "Nombre: <strong>".$datosPagoPayPal['nombre']." ".$datosPagoPayPal['apellidos']."</strong><br />".
		    "Email: ".$datosPagoPayPal['emailPagadorPayPal']."<br /><br />".
			   "Concepto: ".$datosPagoPayPal['producto'].": ".$datosPagoPayPal['identificadorProducto']."<br />".
						"Fecha anotación pago: ".$datosPagoPayPal['fechaPago']."<br />".
						"Identificador de transacción: ".$datosPagoPayPal['IdentificadorTransaccion']."<br /><br />".
				  "Importe: <strong>".$datosPagoPayPal['importe']."</strong><br />".
				  "IVA&nbsp;: ".$datosPagoPayPal['IVA']."<br />".
						"---------------------------<br />".	
				  "Total: <strong>".$datosPagoPayPal['totalPagos']."</strong><br /><br /><br /><br />". 
				  "Nota: comisión que pagará Europa Laica a PayPal: ".$datosPagoPayPal['gastosPayPal']. 
						"<br /><br />Si quieres, para evitar estos costes, puedes domiciliar el pago de la cuota en la opción del menú: - Actualizar datos socio/a ";
												
		  //echo"<br><br>7 cPayPal:confirmarPagoPayPal:cadenaDatosAnotadosPorPaypal:";print_r($cadenaDatosAnotadosPorPaypal);				
	
			 $datosMensaje['textoCabecera'] = 'Pago con PayPal. Confirmado pago con PayPal';
			 $datosMensaje['textoComentarios'] =
					"La orden de pago de tu cuota anual mediante PayPal, se ha realizado de modo correcto.
					 PayPal verificará que los datos del pagador y posteriormente lo ingresará en la cuenta de Europa Laica<br /><br />".
						$cadenaDatosAnotadosPorPaypal.
				  "<br /><br /><br />
						La tesorería de Europa Laica, cuando revise los extractos bancarios, lo anotará en la 
						aplicación de gestión de socios/as como cuota pagada. Puede pasar algún tiempo antes de que se anote el pago.
						<br /><br /> 
				  Por si hubiese algún problema, nos puedes confirmar tu pago enviándonos un correo electrónico a
						<strong>".$datosEmailCoSeTe['EMAILTESORERO']."</strong> 
						indicando asunto: Cuota con PayPal, NIF, nombre y apellidos, cantidad y fecha pago.";
						
				//$datosMensaje['textoBoton'] = 'Salir de la aplicación';
			 //$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	
	
		  //echo "<br><br>8 cPayPal:confirmarPagoPayPal:datosMensaje: "; print_r($datosMensaje);	 		 		 
	 
		  require_once './vistas/PayPal/vMensajeVolverPayPalInc.php';	
	  	vMensajeVolverPayPalInc($tituloSeccion,$datosMensaje,$navegacion );
				
	  //----- Fin Preparar datos para mensaje en pantalla a socio confirmando pago con PayPal ---------
	
	  }//($reDatosSocio['codError'] == '00000')		
		}//if (isset($_POST['custom']) && !empty($_POST['custom']))	 
 }//else isset($_POST[payment_status] && $_POST[payment_status]== 'Completed')
}
/*--------------------------- Fin confirmarPagoPayPal------------------------------------------


/*--------------------------- Inicio  cancelacionPagoPayPal ----------------------------------------
Se muesta un formulario que indicando que el  pago de la CUOTA con paypal ha 
sido cancelado desde PayPal, incluido cuando el proceso de alta de socio o confirmacion de alta del socio

LLAMADO: desde el link del texto que se pone en PayPal 

LLAMA A: require_once './vistas/PayPal/vMensajeVolverPayPalInc.php'
--------------------------------------------------------------------------------------------------*/
function cancelacionPagoPayPal()
{ $tituloSeccion  = "Socios/as";

	//echo "<br><br>0 cPayPal:cancelacionPagoPayPal:_GET: "; print_r($_GET);
	//echo "<br><br>0 cPayPal:cancelacionPagoPayPal:_REQUEST: "; print_r($_REQUEST);
 //echo "<br><br>0 cPayPal:cancelacionPagoPayPal:_POST: ";print_r($_POST);	 
 //echo "<br><br>0 cPayPal:cancelacionPagoPayPal:SESSION: ";print_r($_SESSION);

 //------------ inicio navegación para socios gestores CODROL >2 -----------------------------------	
	if (isset($_SESSION['vs_autentificadoGestor']))
 {	
	 $pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio" )			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=controladorSocios&accion=pagarCuotaSocio";	
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Pagar cuota Socio/a";
	 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
		require_once './controladores/libs/cNavegaHistoria.php';
	 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior"); 
 }
	else
	{
		$navegacion = '';
	}
	//echo "<br><br>1 cPayPal:cancelacionPagoPayPal:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
	
	//------------ fin navegación ---------------------------------------------------------------------		
	
 $datosMensaje['textoCabecera'] = '<strong>Cancelación del pago con PayPal de la cuota anual</strong>';		
 $datosMensaje['textoComentarios'] =
	'<br /><br />A petición tuya se ha cancelado el pago de tu cuota anual mediante PayPal
  <br /><br /><br /><br /> 	 	  
  Si tienes algún problema puedes enviar un correo electrónico a <strong>tesoreria@europalaica.org</strong> indicándolo
		<br />';
		
			
	//$datosMensaje['textoBoton'] = 'Salir de la aplicación';
 //$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		
 //echo "<br><br>2 cPayPal:cancelacionPagoPayPal:datosMensaje";print_r($datosMensaje);
	
 $tituloSeccion  = "Socios /as";	
	 	
	require_once './vistas/PayPal/vMensajeVolverPayPalInc.php';	
	vMensajeVolverPayPalInc($tituloSeccion,$datosMensaje,$navegacion );	
}
//--------------------------- Fin  cancelacionPagoPayPal  ------------------------------------------

/*--------------------------- Inicio  confirmarDonacionPayPal --------------------------------------
Agustin 2018-01-23: Añado lineas 656 a 663 para informar de la cantidad pagada

Se muesta un formulario que indicando que el  pago de una DONACION con paypal ha 
sido aceptado en PayPal

LLAMADO: desde el link del texto que se pone en PayPal 

LLAMA A: require_once './vistas/PayPal/vMensajeVolverPayPalInc.php'
--------------------------------------------------------------------------------------------------*/
function confirmarDonacionPayPal()
{
 $tituloSeccion  = "Socios/as";
/*
	echo "<br><br>0 cPayPal:confirmarDonacionPayPal:_GET: "; print_r($_GET);
	echo "<br><br>0 cPayPal:confirmarDonacionPayPal:_REQUEST: "; print_r($_REQUEST);
 echo "<br><br>0 cPayPal:confirmarDonacionPayPal:_POST: ";print_r($_POST);	 
 echo "<br><br>0 cPayPal:confirmarDonacionPayPal:SESSION: ";print_r($_SESSION);
*/

 //------------ inicio navegación para socios gestores CODROL >2 -----------------------------------	
	if (isset($_SESSION['vs_autentificadoGestor']))
 {	
	 $pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio" )			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=controladorSocios&accion=donarSocio";	
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Hacer una donación";
	 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
		require_once './controladores/libs/cNavegaHistoria.php';
	 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior"); 
 }
	else
	{
		$navegacion = '';
	}
	//echo "<br><br>3 controladorSocios:pagarCuotaSocio:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			
	
	//------------ fin navegación ---------------------------------------------------------------------	
	
 $datosMensaje['textoCabecera'] = '<strong>Confirmación de pago con PayPal de una donación</strong>';	

	if (isset($_POST['mc_gross']) && !empty($_POST['mc_gross']))
	{ $importe = " de ".$_POST['mc_gross']." ".$_POST['mc_currency'];//importe con EUR al final		
	}
 else
 { $importe = "";		
	}	
	
 $datosMensaje['textoComentarios'] =
	'A petición tuya SE HA EFECTUADO EL PAGO de una donación mediante PayPal '.$importe.'<br /><br />'.
		'La tesorería de Europa Laica, cuando revise los extractos bancarios, lo anotará en la 
		aplicación de gestión de donaciones. 
		<br />	<br />		 	  
  Si tienes algún problema puedes enviar un correo electrónico a <strong>tesoreria@europalaica.org</strong>  
		explicando la incidencia';
		/*	
	$datosMensaje['textoBoton'] = 'Salir de la aplicación';
 $datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	
 	*/
		 		
	require_once './vistas/PayPal/vMensajeVolverPayPalInc.php';	
	vMensajeVolverPayPalInc($tituloSeccion,$datosMensaje,$navegacion );

}
//--------------------------- Fin  confirmarDonacionPayPal  ----------------------------------------

/*--------------------------- Inicio  cancelacionDonacionPayPal ------------------------------------
Se muesta un formulario que indicando que el  pago de una DONACION con paypal ha 
sido cancelado desde PayPal

LLAMADO: desde el link del texto que se pone en PayPal 

LLAMA A: require_once './vistas/PayPal/vMensajeVolverPayPalInc.php'
--------------------------------------------------------------------------------------------------*/
function cancelacionDonacionPayPal()
{
 $tituloSeccion  = "Socios/as";
/*
	echo "<br><br>0 cPayPal:cancelacionDonacionPayPal:_GET: "; print_r($_GET);
	echo "<br><br>0 cPayPal:cancelacionDonacionPayPal:_REQUEST: "; print_r($_REQUEST);
 echo "<br><br>0 cPayPal:cancelacionDonacionPayPal:_POST: ";print_r($_POST);	 
 echo "<br><br>0 cPayPal:cancelacionDonacionPayPal:_SESSION: ";print_r($_SESSION);
*/
 //------------ inicio navegación para socios gestores CODROL >2 -----------------------------------	
	if (isset($_SESSION['vs_autentificadoGestor']))
 {	
	 $pagActual = $_SESSION['vs_HISTORIA']['pagActual'];
		
		if ($_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']=="index.php?controlador=controladorSocios&accion=menuGralSocio" )			
		{	$pagActual = $_SESSION['vs_HISTORIA']['pagActual'] + 1;
		}
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['link']="index.php?controlador=controladorSocios&accion=donarSocio";	
	 $_SESSION['vs_HISTORIA']['enlaces'][$pagActual]['textoEnlace'] = "Hacer una donación";
	 $_SESSION['vs_HISTORIA']['pagActual'] = $pagActual;	
		
		require_once './controladores/libs/cNavegaHistoria.php';
	 $navegacion = cNavegaHistoria($_SESSION['vs_HISTORIA'],"<< Pag. anterior"); 
 }
	else
	{
		$navegacion = '';
	}
	//echo "<br><br>3 controladorSocios:pagarCuotaSocio:_SESSION['vs_HISTORIA']:";print_r($_SESSION['vs_HISTORIA']);			

	//------------ fin navegación ---------------------------------------------------------------------	
	
 $datosMensaje['textoCabecera'] = '<strong>Cancelación de pago con PayPal de una donación</strong>';		
 $datosMensaje['textoComentarios'] =
	'A petición tuya <strong>SE HA CANCELADO EL PAGO</strong> de una donación mediante PayPal
  <br /><br />  <br /><br /> 		  
  Si tienes algún problema puedes enviar un correo electrónico a <strong>tesoreria@europalaica.org</strong>  
		explicando la incidencia';
			
	//$datosMensaje['textoBoton'] = 'Salir de la aplicación';
 //$datosMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	

	require_once './vistas/PayPal/vMensajeVolverPayPalInc.php';	
	vMensajeVolverPayPalInc($tituloSeccion,$datosMensaje,$navegacion );

}
//--------------------------- Fin  cancelacionDonacionPayPal  --------------------------------------

?>