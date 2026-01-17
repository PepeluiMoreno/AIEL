<?php
session_start();
/*-----------------------------------------------------------------------------
FICHERO: controladorSimpatizantes.php
PROYECTO: EL
VERSION: PHP 5.2.3
DESCRIPCION: En este fichero se encuentran las funciones relacionadas con las 
             acciones que pueden realizar los propios simpatizantes 
LLAMA: modeloSimpatizantes, modeloUsuarios, modeloEmail (para errores al webmaster)
       varias libs y formularios en vistas/simpatizantes/	y /vistas/mensajes/					 
OBSERVACIONES:
------------------------------------------------------------------------------*/
//------------------------------ Inicio menuGralSimp -------------------------
function menuGralSimp()
{
	if ($_SESSION['vs_autentificado'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }
	else
	{
	 $tituloSeccion  = "Simpatizantes";

  //echo "<br><br>1controladorSimpatizantes:menuGralSimp:_SESSION:";print_r($_SESSION); 			
  require_once './modelos/modeloUsuarios.php';
  //$validarUsuario=buscarRolFuncionUsuario($validarUsuario['resultadoFilas'][0]['USUARIO']);
		//$resRolFuncion=buscarRolFuncion($_SESSION['vs_CODROL'])
		$resFuncionRol = buscarRolFuncion("2");//SIMPATIZANTE CODROL=2// mejor sería leer rol de tabla
		//echo "<br><br>2controladorSimp:menuGralSimp:resFuncionRol:";print_r($resFuncionRol);	
				 
  if ($resFuncionRol['codError'] == '00000')
	 {$_SESSION['vs_autentificado'] = 'SI';//ya esta en controladorLogin pero acoso este mejor aqui
			$_SESSION['vs_enlacesSeccIzda'] = $resFuncionRol['resultadoFilas'];//Se puede acceder desde cualquier sitio

   //echo "<br><br>3controladorSimp:menuGralSimp:_SESSION:";print_r($_SESSION);
	  require_once './vistas/login/vFuncionRolInc.php';
		 vFuncionRolInc($tituloSeccion,$resFuncionRol['resultadoFilas'],"");
  }		 
  else //($validarUsuario['codError'] !== '00000')
  {$arrayParamMensaje['textoCabecera']='Identificación como usuario';
	 	$arrayParamMensaje['textoComentarios']='Error del sistema al identificarse, vuelva a intentarlo pasado un tiempo ';
		 $arrayParamMensaje['textoBoton']='Salir de la aplicación';
		 $arrayParamMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
   require_once './vistas/mensajes/vMensajeCabSalirInc.php';
			vMensajeCabSalirInc($tituloSecc,$arrayParamMensaje,$resFuncionRol['resultadoFilas']);
			
   require_once './modelos/modeloEmail.php';	
		 $resEmailErrorWMaster=emailErrorWMaster($arrayParamMensaje['textoComentarios']);			
  }			
 } 
}
//------------------------------ Fin menuGralSimp ---------------------------- 

//--------------- Inicio altaSimpatizante  -----------------------------------
function altaSimpatizante()
{
	require_once './modelos/modeloSimpatizantes.php';
	require_once './vistas/simpatizantes/vAltaSimpInc.php';	
	require_once './vistas/mensajes/vMensajeCabInicialInc.php';	
	require_once './vistas/mensajes/vMensajeCabSalirInc.php';	
	require_once './modelos/modeloEmail.php';
 $tituloSeccion = "Simpatizantes";	//se puede incluir en el requiere
 
 if (!$_POST) 
 {//echo "<br><br>1controladorSimpatizantes:altaSimpatizante:POST1:";print_r($_POST);
  require_once './controladores/libs/inicializaCamposAltaSimp.php';
	 //$parValorCombo=parValoresRegistrarUsuario("","",1);//parValorPais()
		//$parValorCombo['domicilioPais']=parValorPais("");
		$parValorCombo['domicilioPais']=parValorPais("ES");

  //echo "<br><br>2-1controladorSimpatizantes:altaSimpatizante:parValorCombo:";print_r($parValorCombo); 
	
	 if ($parValorCombo['domicilioPais']['codError']!=='00000')//errores al preparar las listas $parValorCombo;
		{//echo "<br><br>2-2controladorSimpatizantes:altaSimpatizante:SESSION['vs_enlacesSeccIzda']:";
		 //print_r($_SESSION['vs_enlacesSeccIzda']);
		 vMensajeCabInicialInc($tituloSeccion,$parValorCombo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
			$resEmailErrorWMaster=emailErrorWMaster($parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);								
		}	
		else //no hay errores al preparar las listas $parValorCombo; 
		{ //echo "<br><br>2-3controladorSimpatizantes:altaSimpatizante:resInsertar:";print_r($resInsertar); 
		  vAltaSimpInc("",$resInsertar,$parValorCombo);		//sustituir por arrayParValor.php de pais domicilio		
		}
	}
	else //POST
	{if (isset($_POST['noGuardarDatosSimp']))//ha pulsado el botón "noGuardarDatosSimp"
		{//echo "<br><br>3controladorSimpatizantes:altaSimpatizante:POST:";print_r($_POST);
	  $noGuardarDatosSimp['arrMensaje']['textoCabecera']="Altas de socios"; 
   $noGuardarDatosSimp['arrMensaje']['textoComentarios']="Ha salido sin dar de alta al simpatizante";				
		 vMensajeCabInicialInc($tituloSeccion,$noGuardarDatosSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);				 
	 }	
		else //==(isset($_POST['siGuardarDatosSimp']))Pulsado el botón "siGuardarDatosSimp"
		{//echo "<br><br>5controladorSimpatizantes:altaSimpatizante:POST:";print_r($_POST);
		 require_once './modelos/libs/validarCamposSimp.php';			
			$resValidarCamposForm=validarCamposAltaSimp($_POST);

		 //echo "<br><br>6controladorSimpatizantes:altaSimpatizante:$resValidarCamposForm:";print_r($resValidarCamposForm);

			if ($resValidarCamposForm['codError']!=='00000')
			{if ($resValidarCamposForm['codError'] >= '80000')//Error lógico		
				{ $parValorCombo['domicilioPais']=parValorPais($resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo']);
		
     if ($parValorCombo['domicilioPais']['codError']!=='00000') //$resInsertar = $parValorCombo;
				 {vMensajeCabInicialInc($tituloSeccion,$parValorCombo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
					 $resEmailErrorWMaster=emailErrorWMaster($parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);					
				 }	
				 else
				 { //echo "<br><br>7-2-2controladorSimpatizantes:altaSimpatizante:SESSION['vs_enlacesSeccIzda']:";
					  vAltaSimpInc("",$resValidarCamposForm,$parValorCombo);		
				 }
				}			
				else //$resValidarCamposForm['codError']< '80000')
				{	vMensajeCabInicialInc($tituloSeccion,$resValidarCamposForm['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
					$resEmailErrorWMaster=emailErrorWMaster($resValidarCamposForm['codError'].": ".$resValidarCamposForm['errorMensaje']);
				}
			}	
			else //$resValidarCamposForm['codError']=='00000'
			{
				 $resInsertar= altaSimp($resValidarCamposForm);

				if ($resInsertar['codError']!=='00000') //siempres será ($resInsertar['codError'] < '80000'))
				{//echo "<br><br>8controladorSimpatizantes:altaSimpatizante:SESSION['vs_enlacesSeccIzda']:";
				 //print_r($_SESSION['vs_enlacesSeccIzda']);						
				 vMensajeCabInicialInc($tituloSeccion,$resInsertar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
					$resEmailErrorWMaster=emailErrorWMaster($resInsertar['codError'].": ".$resInsertar['errorMensaje']);
				}	
				else // ($resInsertar['codError']=='00000') 
				{//$codRol="2";//simpatizante
     $_SESSION['vs_autentificado'] = 'SI';				 					
					$_SESSION['vs_CODUSER'] = $resInsertar['CODUSER'];
					$_SESSION['vs_CODROL']="2";					
				 $resFuncionesRol=buscarRolFuncion($_SESSION['vs_CODROL']);		 

				 //echo "<br><br>9controladorSimpatizantes:altaSimpatizante:resFuncionesRol";print_r( $resFuncionesRol);
				 
     if ($resFuncionesRol['codError']!=='00000')
		   {
				  $resFuncionesRol['arrMensaje']['textoComentarios'].='. '.$resInsertar['arrMensaje']['textoComentarios'];					
			   vMensajeCabInicialInc($tituloSeccion,$resFuncionesRol['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
						
				  $resEmailErrorWMaster=emailErrorWMaster($resFuncionesRol['codError'].": ".$resFuncionesRol['errorMensaje']);	
				 }
				 else //$resFuncionesRol['codError']=='00000' bien
				 {$_SESSION['vs_enlacesSeccIzda']=$resFuncionesRol['resultadoFilas'];
	       
      require_once './controladores/libs/datosEmailSimpAlta.php';	 
				  
						$resultEnviarEmail=emailAltaUsuario($datosEnvioEmail);
				  
						vMensajeCabSalirInc($tituloSeccion,$resInsertar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
				 }
				}	//else // NO ($resInsertar['codError']!=='00000') && ($resInsertar['codError'] <= '80000')
	  } //$resValidarCamposForm['codError']=='00000'		
	 }//else //if (!isset($_POST['vaciarCampos']))
 }//else post 
}
//------------------------------- Fin altaSimpatizante -----------------------

//------------------------------- Inicio mostrarDatosSimpatizante ------------
function mostrarDatosSimpatizante()
{ 
	if ($_SESSION['vs_autentificado'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 

	require_once './modelos/modeloSimpatizantes.php';
	require_once './vistas/simpatizantes/vMostrarDatosSimpInc.php';	
	require_once './vistas/mensajes/vMensajeCabSalirInc.php'; 	
	require_once './modelos/modeloEmail.php';
	
	$tituloSeccion  = "Simpatizantes";
	//echo "<br><br>1controladorSimpatizantes:mostrarDatosSimpatizante:POST:";print_r($_POST);
	//echo "<br><br>2controladorSimpatizantes:mostrarDatosSimpatizante:_SESSION:";print_r($_SESSION); 			 
 
	$usuarioBuscado = $_SESSION['vs_CODUSER']; 
	
 $resDatosSimpMostrar = buscarDatosSimp($usuarioBuscado);	
	
 //echo "<br><br>3controladorSimpatizantes:mostrarDatosSimpatizante:resDatosSimpMostrar:";print_r($resDatosSimpMostrar); 
 
 if ($resDatosSimpMostrar['codError']!=='00000')
 {$resEmailErrorWMaster = emailErrorWMaster(date("Y-m-d:H:i:s").
			".Código Error: ".$resDatosSimpMostrar['codError'].": ".$resDatosSimpMostrar['errorMensaje']);		
  $resDatosSimpMostrar['arrMensaje']['textoComentarios']=
	  "Error del sistema al mostrar un simpatizante, vuelva a intentarlo pasado un tiempo ";
		$resDatosSimpMostrar['arrMensaje']['textoBoton']='Salir de la aplicación';
		$resDatosSimpMostrar['arrMensaje']['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
					
	 vMensajeCabSalirInc($tituloSeccion,$resDatosSimpMostrar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
 }      
 else //$resDatosSimpMostrar['codError']=='00000' 
 {	vMostrarDatosSimpInc($tituloSeccion,$resDatosSimpMostrar['valoresCampos']);								
		
 } //$resDatosSimpMostrar['codError']=='00000' 
}
//--------------------------- Fin mostrarDatosSimpatizante -------------------------


//--------------------- Inicio actualizarSimpatizante con rollback en modelo -------
function actualizarSimpatizante()
{
	if ($_SESSION['vs_autentificado'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 }	
	require_once './modelos/modeloSimpatizantes.php';
	require_once './vistas/simpatizantes/vActualizarSimpInc.php';	
	require_once './vistas/mensajes/vMensajeCabSalirInc.php'; 	
	require_once './modelos/modeloEmail.php';	
	
	//echo "<br><br>1controladorSimpatizantes:actualizarSimpatizante:_SESSION:";print_r($_SESSION); 			 	
	//echo "<br><br>2controladorSimpatizantes:actualizarSimpatizante:POST";print_r($_POST)
	
 $tituloSeccion  = "Simpatizantes";

	$usuarioBuscado = $_SESSION['vs_CODUSER'];  

 if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
 {//echo "<br><br>3controladorSimpatizantes:actualizarSimpatizante:SESSION[vs_USUARIO]";print_r($_SESSION['vs_USUARIO']);
  //echo "<br><br>4controladorSimpatizantes:actualizarSimpatizante:POST:";print_r($_POST);
	 if (isset($_POST['salirSinActualizar']))
  {$salirSinActualizar['arrMensaje']['textoCabecera']="Actualizar datos del simpatizante"; 
	  $salirSinActualizar['arrMensaje']['textoComentarios']="No se han modificado los datos del simpatizante";	
						
			vMensajeCabSalirInc($tituloSeccion,$salirSinActualizar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);			
		}
		else //(isset($_POST['comprobarYactualizar']))
	 {require_once './modelos/libs/validarCamposSimp.php';	
			//echo "<br><br>5controladorSimpatizantes:actualizarSimpatizante:_POST:";print_r($_POST);
			$resValidarCamposForm = validarCamposActualizarSimp($_POST);
			//echo "<br><br>6controladorSimpatizantes:actualizarSimpatizante:resValidarCamposForm:";print_r($resValidarCamposForm);
			if (($resValidarCamposForm['codError']!=='00000') && ($resValidarCamposForm['codError']>'80000'))
			{
				$parValorCombo['domicilioPais']=parValorPais($resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo']);				
	 		if ($parValorCombo['domicilioPais']['codError']!=='00000') 
				{vMensajeCabSalirInc($tituloSeccion,$parValorCombo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
				 	
					$resEmailErrorWMaster=emailErrorWMaster($parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);					
				}	
				else
				{//vActualizarSimpInc($tituloSeccion,$resValidarCamposForm,"",$parValorCombo['domicilioPais']);
				 vActualizarSimpInc($tituloSeccion,$resValidarCamposForm,$parValorCombo['domicilioPais']);
				}			
		 }													
			else //$resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
			{$resActDatosSimp= actualizarDatosSimp($resValidarCamposForm); 			
				//echo "<br><br>7controladorSimpatizantes:actualizarSimpatizante:resActDatosSocio:";print_r($resActDatosSocio);			  
				if ($resActDatosSimp['codError']!=="00000")
				{vMensajeCabSalirInc($tituloSeccion,$resActDatosSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);	
						
			  $resEmailErrorWMaster=emailErrorWMaster($resActDatosSimp['codError'].": ".$resActDatosSimp['errorMensaje']);
			 }		
	   else //($resActDatosSimp['codError'] == '00000')
		  { //actualizar $_SESSION['vs_NOMUSUARIO'];    por si ha cambiado usuario
				  vMensajeCabSalirInc($tituloSeccion,$resActDatosSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);						
 		 }	        
	  }
		}	//(isset($_POST['comprobarYactualizar']))	 				  
 }//if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
 else //!if (isset($_POST['comprobarYactualizar']) || isset($_POST['salirSinActualizar']))  
 {//echo "<br><br>8-1controladorSimpatizantes:actualizarSimpatizante:SESSION";print_r($_SESSION);

		$resDatosSimpActualizar = buscarDatosSimp($usuarioBuscado);
		//echo "<br><br>8-2controladorSimpatizantes:actualizarSimpatizante:resDatosSimpActualizar:";print_r($resDatosSimpActualizar);
		
  if ($resDatosSimpActualizar['codError']!=='00000')
  {$resEmailErrorWMaster=emailErrorWMaster($resDatosSimpActualizar['codError'].": ".$resDatosSimpActualizar['errorMensaje']);	
		
   $resDatosSimpActualizar['arrMensaje']['textoComentarios']=
			 "Error del sistema al actualizar datos del simpatizante, vuelva a intentarlo pasado un tiempo ";			
			vMensajeCabSalirInc($tituloSeccion,$resDatosSimpActualizar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);			
		}      
		else //$resDatosSocioActualizar['codError']=='00000'
		{//echo "<br><br>9controladorSimpatizantes:actualizarSimpatizante:numFilas:",$resDatosSimpActualizar['numFilas'];
			
			$parValorCombo['domicilioPais']=
				       parValorPais($resDatosSimpActualizar['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo']);				
		 if ($parValorCombo['domicilioPais']['codError']!=='00000')				
			{vMensajeCabSalirInc($tituloSeccion,$parValorCombo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
			
			 $resEmailErrorWMaster=emailErrorWMaster($parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);					
			}	
		 else //$parValorCombo['codError']=='00000'
			{$_SESSION['vs_USUARIO']=$resDatosSimpActualizar['valoresCampos']['datosFormUsuario']['USUARIO']['valorCampo'];				
				$_SESSION['vs_EMAIL']=$resDatosSimpActualizar['valoresCampos']['datosFormMiembro']['EMAIL']['valorCampo'];	
					
				//echo "<br><br>10controladorSimpatizantes:actualizarSimpatizante:resDatosSimpActualizar['valoresCampos']";
				//print_r($resDatosSimpActualizar['valoresCampos']);														
   
				vActualizarSimpInc($tituloSeccion,$resDatosSimpActualizar['valoresCampos'],$parValorCombo['domicilioPais']);	
			}	
  }   
 }//!(isset($_POST['ConfirmarEliminacion']) || isset($_POST['NoConfirmarEliminacion']))    
}
//------------------ Fin actualizarSimpatizante ------------------------------

//--------------------- Inicio eliminarSimpatizante con rollback -------------
function eliminarSimpatizante()
{ 
	if ($_SESSION['vs_autentificado'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 		
	require_once './modelos/modeloSimpatizantes.php';
	require_once './vistas/simpatizantes/vEliminarSimpInc.php';			
	require_once './vistas/mensajes/vMensajeCabSalirInc.php'; 	
	require_once './modelos/modeloEmail.php';

	//echo "<br><br>1controladorSimpatizantes:eliminarSimpatizante: POST";print_r($_POST);
 //echo "<br><br>2controladorSimpatizantes:eliminarSimpatizante:_SESSION:";print_r($_SESSION);
	
	$tituloSeccion = "Simpatizantes";
 			  
	if (isset($_POST['SiEliminar']) || isset($_POST['NoEliminar'])) 
 {//echo "<br><br>3controladorSimpatizantes:eliminarSimpatizante: SESSION[vs_NOMUSUARIO]";print_r($_SESSION['vs_NOMUSUARIO']);
	 if (isset($_POST['SiEliminar']))
  {$datosSimp = $_POST;				
			$reEliminarSimp = eliminarDatosSimp($_SESSION['vs_CODUSER']);
	
			//echo "<br><br>4controladorSimpatizantes:eliminarSimpatizante:reEliminarSimp:"; print_r($reEliminarSimp);			
			  
			if ($reEliminarSimp['codError']!=="00000")
			{//echo "<br><br>5controladorSimpatizantes: eliminarSimpatizante:reEliminarSimp "; print_r($reEliminarSimp);	 
			 	vMensajeCabSalirInc($tituloSeccion,$reEliminarSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);		
					//vMensajeCabSalirNavInc($tituloSeccion,$reEliminarSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda'],$navegacion);
				 $resEmailErrorWMaster=emailErrorWMaster($reEliminarSimp['codError'].": ".$reEliminarSimp['errorMensaje']);								 
			}
   else //($reEliminarSimp['codError'] == '00000')
   { 	
	   $resultEnviarEmail = emailBajaUsuario($datosSimp['datosFormMiembro']);		
				 unset($_SESSION);//para que ya no esté como autorizado
					vMensajeCabSalirInc($tituloSeccion,$reEliminarSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);	
	  }        
  }     
  else //!(isset($_POST['ConfirmarEliminacion']))
  {$reEliminarSimp['arrMensaje']['textoCabecera']="Eliminar simpatizante"; 
   //$reEliminarSimp['arrMensaje']['textoBoton']="Salir de de la aplicación";						
 	 //$reEliminarSimp['arrMensaje']['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';				
	  $reEliminarSimp['arrMensaje']['textoComentarios']="Has salido sin eliminar el simpatizante";				
  
			vMensajeCabSalirInc($tituloSeccion,$reEliminarSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
  }    
 }//!(isset($_POST['ConfirmarEliminacion']) || isset($_POST['NoConfirmarEliminacion'])) 
 else //!(isset($_POST['ConfirmarEliminacion']) || isset($_POST['NoConfirmarEliminacion'])) 
 {//echo "<br><br>7controladorSimpatizantes: eliminarSimpatizante:SESSION";print_r($_SESSION);
  //echo "<br><br>8controladorSocios:eliminarSocio:usuarioBuscado:";print_r($usuarioBuscado);

		$usuarioBuscado = $_SESSION['vs_CODUSER'];   
  $datSimpEliminar=buscarDatosSimp($usuarioBuscado);	
  
  //echo "<br><br>9controladorSimpatizantes:eliminarSimpatizante:datSimpEliminar: ";print_r($datSimpEliminar); 
 		 		 
  if ($datSimpEliminar['codError']!=='00000')
  {$resEmailErrorWMaster=emailErrorWMaster($datSimpEliminar['codError'].": ".$datSimpEliminar['errorMensaje']);		
   $datSimpEliminar['arrMensaje']['textoComentarios']=
   "Error del sistema al eliminar simpatizante, vuelva a intentarlo pasado un tiempo ";		
				
   vMensajeCabSalirInc($tituloSeccion,$datSimpEliminar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
  }      
  else //$datSimpEliminar['codError']=='00000'
  {									
	  vEliminarSimpInc($tituloSeccion,$datSimpEliminar['valoresCampos']);			  	 
  }   
 }//!(isset($_POST['ConfirmarEliminacion']) || isset($_POST['NoConfirmarEliminacion']))    
}
//------------------- Fin eliminarSimpatizante -------------------------------- 

//-------------------- Inicio simpatizanteAsocio con rollback -------------------
function simpatizanteAsocio()
{if ($_SESSION['vs_autentificado'] !== 'SI')
 { header ("Location:./index.php?controlador=controladorLogin&amp;accion=validarLogin");
 } 
	$tituloSeccion = "Simpatizantes";		
	require_once './modelos/modeloSimpatizantes.php';
	require_once './vistas/simpatizantes/vSimpAsocioInc.php';	
	require_once './vistas/socios/vMensajeAltaSocioAceptadaInc.php';
	require_once './vistas/mensajes/vMensajeCabSalirInc.php'; 
	require_once './modelos/modeloEmail.php';

	//echo "<br><br>1controladorSimpatizantes:simpatizanteAsocio:POST";print_r($_POST);
 //echo "<br><br>2controladorSimpatizantes:simpatizanteAsocio:SESSION:";print_r($_SESSION);
			
 if (isset($_POST['siSimpAsocio']) || isset($_POST['noSimpAsocio'])) 
	{if (isset($_POST['noSimpAsocio']))
  { $salirSinCambiar['arrMensaje']['textoCabecera'] = "Simpatizante se hace socio"; 
	   $salirSinCambiar['arrMensaje']['textoComentarios']="No se ha cambiado el simpatizante a socio";				
			 vMensajeCabSalirInc($tituloSeccion,$salirSinCambiar['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);			
		}
		else //(isset($_POST['siSimpAsocio'])) 		
	 {require_once './modelos/libs/validarCamposSocio.php';	
		 $resValidarCamposForm = validarCamposSimpAsocio($_POST);		
		
		 //echo "<br><br>3controladorSimpatizantes:simpatizanteAsocio:resValidarCamposForm:";print_r($resValidarCamposForm);
			if (($resValidarCamposForm['codError']!=='00000') && ($resValidarCamposForm['codError'] > '80000'))
			{$parValorCombo=parValoresRegistrarUsuario($resValidarCamposForm['datosFormMiembro']['CODPAISDOC']['valorCampo'],
																																											   $resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo'],
																																											   $resValidarCamposForm['datosFormSocio']['CODAGRUPACION']['valorCampo']);
																																														
				//echo "<br><br>4controladorSimpatizantes:simpatizanteAsocio:parValorCombo:";print_r($parValorCombo);		
		  if ($parValorCombo['codError']!=='00000') 
				{vMensajeCabSalirInc($tituloSeccion,$parValorCombo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
				
					$resEmailErrorWMaster=emailErrorWMaster($parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);					
				}	
				else
				{vSimpAsocioInc($tituloSeccion,$resValidarCamposForm,$parValorCombo);		
				}			
			}
			else //$resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')
			{require_once './modelos/modeloSocios.php';
		  $resAltaSocioSimp = cambioSimpSocio($resValidarCamposForm); 
				
			 //echo "<br><br>5-1controladorSimpatizantes:simpatizanteAsocio:resAltaSocioSimp:";print_r($resAltaSocioSimp);			
		  //echo "<br><br>5-2controladorSimpatizantes:simpatizanteAsocio:SESSION:";print_r($_SESSION);
				if ($resAltaSocioSimp['codError']!=="00000")
				{ vMensajeCabSalirInc($tituloSeccion,$resAltaSocioSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);	
									
			   $resEmailErrorWMaster=emailErrorWMaster($resAltaSocioSimp['codError'].": ".$resAltaSocioSimp['errorMensaje']);
				}
	   else //($resAltaSocioSimp['codError'] == '00000')//AQUI DEBE ESTAR EN MODELO SOCIOS $_SESSION['vs_enlacesSeccIzda']
    {$codRol="1";//socio
	    $_SESSION['vs_autentificado'] = 'SI';
							
			  $resFuncionesRol = buscarRolFuncion($codRol);		 
	
			  //echo "<br><br>6controladorSimpatizantes:simpatizanteAsocio:resFuncionesRol";print_r( $resFuncionesRol);
	    if ($resFuncionesRol['codError']!=='00000')
		   {
				  $resFuncionesRol['arrMensaje']['textoComentarios'].='. '.$resInsertar['arrMensaje']['textoComentarios'];
		    vMensajeCabSalirInc($tituloSeccion,$resFuncionesRol['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
						
			   $resEmailErrorWMaster=emailErrorWMaster($resFuncionesRol['codError'].": ".$resFuncionesRol['errorMensaje']);	
			  }
			  else //$resFuncionesRol['codError']=='00000' bien
			  {$_SESSION['vs_enlacesSeccIzda']=$resFuncionesRol['resultadoFilas'];

	
      require_once './controladores/libs/datosEmailSimpASocioAlta.php';	 
						$resultEnviarEmail=emailAltaUsuario($datosEnvioEmail);						
			   //echo "<br><br>7controladorSimpatizantes:simpatizanteAsocio:resultEnviarEmail";print_r($resultEnviarEmail);
					
				   $tituloSeccion="Socios";
					
						vMensajeAltaSocioAceptadaInc($tituloSeccion,$resAltaSocioSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);	
			  }//else $resFuncionesRol['codError']=='00000'	            
		  }//($resAltaSocioSimp['codError'] == '00000')        
	  }//$resValidarCamposForm['codError']=='00000' || $resValidarCamposForm['codError'] =< '80000')			  
	 }//(isset($_POST['siSimpAsocio'])
 }//(isset($_POST['siSimpAsocio']) || isset($_POST['noSimpAsocio'])) 	 
 else //!(isset($_POST['siSimpAsocio']) || isset($_POST['noSimpAsocio'])) 
 {//echo "<br><br>8controladorSimpatizantes:simpatizanteAsocio:SESSION";print_r($_SESSION);       
  $usuariosBuscado=$_SESSION['vs_CODUSER'];       

  $resDatosAltaSocioSimp = buscarDatosSimp($usuariosBuscado);		
  //echo "<br><br>9controladorSimpatizantes:simpatizanteAsocio:resDatosAltaSocioSimp:";print_r($resDatosAltaSocioSimp); 
	 		 
  if ($resDatosAltaSocioSimp['codError']!=='00000')
  { $resEmailErrorWMaster=emailErrorWMaster($resDatosAltaSocioSimp['codError'].": ".$resDatosAltaSocioSimp['errorMensaje']);		
    $resDatosAltaSocioSimp['arrMensaje']['textoComentarios']=
	   "Error del sistema al cambiar el simpatizante a socio, vuelva a intentarlo pasado un tiempo ";		
					
	   vMensajeCabSalirInc($tituloSeccion,$resDatosAltaSocioSimp['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
  }      
  else //$resDatosAltaSocioSimp['codError']=='00000'
  {
	  $parValorCombo=parValoresRegistrarUsuario("ES",
			$resDatosAltaSocioSimp['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo'],'00000000');//AGRUP:'00000000':Estatal
	  //echo "<br><br>10ontroladorSimpatizantes:simpatizanteAsocio:parValorCombo:";print_r($parValorCombo);
		
   if ($parValorCombo['codError']!=='00000') 
	  {vMensajeCabSalirInc($tituloSeccion,$parValorCombo['arrMensaje'],$_SESSION['vs_enlacesSeccIzda']);
			
		  $resEmailErrorWMaster=emailErrorWMaster($parValorCombo['codError'].": ".$parValorCombo['errorMensaje']);					
	  }	
	  else
	  {require_once './modelos/modeloSocios.php';
				//$resCuotasAniosEL = buscarCuotasAnioEL(date('Y'),"1");		//antiguo	
				$resCuotasAniosEL = buscarCuotasAnioEL(date('Y'),'%');		//nuevo
			 //falta if error
				//echo "<br><br>11controladorSimpatizantes:simpatizanteAsocio:resCuotasAniosEL:";print_r($resCuotasAniosEL);		
				
	
				require_once './controladores/libs/inicializaCamposSimpAsocio.php';
				
    $datCuotaAnioEL=$resCuotasAniosEL['datosFormCuotaSocio']['resultadoFilas']['ANIOCUOTA'][date('Y')];
				
			 //echo "<br><br>12controladorSimpatizantes:simpatizanteAsocio:resDatosAltaSocioSimp:";print_r($resDatosAltaSocioSimp); 
				
	   $resDatosAltaSocioSimp['datosFormCuotaSocio']['ANIOCUOTA']=	$datCuotaAnioEL['General']['ANIOCUOTA'];
    $resDatosAltaSocioSimp['datosFormCuotaSocio']['CODCUOTAGeneral']=	$datCuotaAnioEL['General']['CODCUOTA'];	
    $resDatosAltaSocioSimp['datosFormCuotaSocio']['IMPORTECUOTAANIOELGeneral']=	$datCuotaAnioEL['General']['IMPORTECUOTAANIOEL'];
    $resDatosAltaSocioSimp['datosFormCuotaSocio']['IMPORTECUOTAANIOELJoven']=	$datCuotaAnioEL['Joven']['IMPORTECUOTAANIOEL'];
    $resDatosAltaSocioSimp['datosFormCuotaSocio']['IMPORTECUOTAANIOELParado']=	$datCuotaAnioEL['Parado']['IMPORTECUOTAANIOEL'];
					
			 //echo "<br><br>13controladorSimpatizantes:simpatizanteAsocio:resDatosAltaSocioSimp['valoresCampos']:";
				//print_r($resDatosAltaSocioSimp['valoresCampos']);				
				$resDatosAltaSocioSimp['valoresCampos']['datosFormCuotaSocio']=$resDatosAltaSocioSimp['datosFormCuotaSocio'];		
							
    $_SESSION['vs_EMAIL'] = $resDatosAltaSocioSimp['valoresCampos']['datosFormMiembro']['EMAIL']['valorCampo'];	  									

			 //echo "<br><br>14controladorSimpatizantes:simpatizanteAsocio:resDatosAltaSocioSimp['valoresCampos']:";
				//print_r($resDatosAltaSocioSimp['valoresCampos']);		
    
	   vSimpAsocioInc($tituloSeccion,$resDatosAltaSocioSimp['valoresCampos'],$parValorCombo);
	  }	
  }   
 }//!(isset($_POST['siSimpAsocio']) || isset($_POST['noSimpAsocio']))  
}
//--------------------------- Fin simpatizanteAsocio -------------------------------------------- 
?>