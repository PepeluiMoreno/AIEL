<?php
/*-----------------------------------------------------------------------------
FICHERO: validarCamposUsuarios.php
PROYECTO: Europa Laica
VERSION: PHP 5.2.3
DESCRIPCION: Valida los campos recibidos desde los formularios de relacionados 
             con controlador login
Llamado: desde  controladorLogin, ....
Llama: ./modelos/libs/validarCampos.php (una librería de validaciones generales)
 './modelos/modeloUsuarios/validarPass.php';
------------------------------------------------------------------------------*/
	require_once './modelos/libs/validarCampos.php';	
//------------------- validarCampoUsuarioLogin -------------------------------
//Validación al introducir el usuario, servirá para evitar injection,
//no llegarán caracteres no permitidos a la consulta de la BBDD 
//---------------------------------------------------------------------------
function validarCampoUsuarioLogin($name,$longMin,$longMax, $textoErrorCampo)
{ 
	$resulValidarCampo = validarCampoNoVacio($name, $textoErrorCampo);

	if ($resulValidarCampo['codError']=='00000')
	{
		$name = trim($name);//Se quitan espacios

		if((mb_strlen($name) < $longMin) || (mb_strlen($name) >$longMax))//mb_strlen()para long unicode
		{
			$resulValidarCampo['codError']='80202';
			$resulValidarCampo['errorMensaje']=$textoErrorCampo;
		}
		elseif(!preg_match("/^[-0-9a-zA-Z._@]+$/",$name))
		{ //Si guiones -, no espacio, si numeros si . _ @
			//echo "No valido nombre:", $name,"<br>";
			$resulValidarCampo['codError']='80220';
			$resulValidarCampo['errorMensaje']=$textoErrorCampo;
		}
		else
		{//echo "valido: ",$name,"<br>";
			$resulValidarCampo['codError']='00000';
			$resulValidarCampo['errorMensaje']='';
		}
		$resulValidarCampo['valorCampo']=$name;		
	}
	return $resulValidarCampo;
} 
//------------------- Fin validarCampoUsuarioLogin -------------------------------

//------------------- Inicio validarCampoUsuarioLogin ---------------------------
//Validación al introducir el password, servirá para evitar injection,
//no llegarán caracteres no permitidos a la consulta de la BBDD 
//no se eliminan los espacios en blanco al princio y final
//---------------------------------------------------------------------------
function validarCampoPassLogin($clave,$longMin,$longMax, $textoErrorCampo)
{ 
	$resulValidarCampo = validarCampoNoVacio($clave, $textoErrorCampo);
	if ($resulValidarCampo['codError']=='00000')
	{
	 if(mb_strlen($clave) < $longMin || mb_strlen($clave) > $longMax)
		{
		 	$resulValidarCampo['codError']='80202';
		  $resulValidarCampo['errorMensaje']=$textoErrorCampo;
		}
	 else if(!preg_match("/^[-0-9a-zA-Z._@!?%&()]+$/", $clave))
		{//echo "No valido nombre:", $clave,"<br>";
		 $resulValidarCampo['codError']='80220';
	  $resulValidarCampo['errorMensaje']=$textoErrorCampo;
		}
	 else
		{//echo "valido: ",$clave,"<br>";
		 $resulValidarCampo['codError']='00000';
	  $resulValidarCampo['errorMensaje']='';
		}
		$resulValidarCampo['valorCampo']=$clave;
	}
	return $resulValidarCampo;
}
//------------------- Fin validarCampoUsuarioLogin ---------------------------

//-------- Inicio validarCambiarPass (llamada desde controladorLogin) ----------
function validarCambiarPass($codUser,$arrCamposForm)
{
	//echo "<br><br>1validarCambiarPass:arrCamposForm:";print_r($arrCamposForm);
	
	require_once './modelos/libs/validarCampos.php';	
	//$validarVacio = validarCampoNoVacio($arrCamposForm['actPASSUSUARIO'],"Contraseña: ");
	$validarCampoPassActual = validarCampoPassLogin($arrCamposForm['actPASSUSUARIO'],6,30,"Error en contraseña" );	

	//if ($validarVacio['codError'] !=='00000')//'80201'
	if ($validarCampoPassActual['codError'] !=='00000')
 {$resulValidar['datosFormUsuario']['actPASSUSUARIO'] = $validarCampoPassActual;
	}
	else
	{require_once './modelos/modeloUsuarios.php';//Necesario para validarPass()
	 $resulValidarPassActual = validarPass($codUser,$arrCamposForm['actPASSUSUARIO']);
	 $resulValidar['datosFormUsuario']['actPASSUSUARIO']=$resulValidarPassActual;
		$resulValidar['datosFormUsuario']['actPASSUSUARIO']['valorCampo']=$arrCamposForm['actPASSUSUARIO'];    
		//echo "<br><br>2validarCambiarPass:resulValidarPassActual:";print_r($resulValidarPassActual);
	 if ($resulValidarPassActual['codError']=='00000')
		{ 
			$resulValidar['datosFormUsuario']['PASSUSUARIO'] = validarCampoPass($arrCamposForm['PASSUSUARIO'],6,30,"" );	
   $resulValidar['datosFormUsuario']['RPASSUSUARIO'] = validarCampoPass($arrCamposForm['RPASSUSUARIO'],6,30,"" );	
	
			//echo "<br><br>3validarCambiarPass:resulValidar:";print_r($resulValidar);
			if ($arrCamposForm['PASSUSUARIO']!==$arrCamposForm['RPASSUSUARIO'])
			{$resulValidar['datosFormUsuario']['PASSUSUARIO']['codError'] = '80430';
				$resulValidar['datosFormUsuario']['PASSUSUARIO']['errorMensaje']='Las dos contraseñas introducidas no eran iguales';
				$resulValidar['datosFormUsuario']['RPASSUSUARIO']['codError'] = '80430';
				$resulValidar['datosFormUsuario']['RPASSUSUARIO']['errorMensaje']='Las dos contraseñas introducidas no eran iguales';
			}	
			elseif ($arrCamposForm['PASSUSUARIO']==$arrCamposForm['actPASSUSUARIO'])
			{
				$resulValidar['datosFormUsuario']['PASSUSUARIO']['codError'] = '80440';
				$resulValidar['datosFormUsuario']['PASSUSUARIO']['errorMensaje']='La nueva contraseña es igual a la antigua';
			}		
		}	
	}
	//echo "<br><br>4validarCambiarPass:$resulValidar:";print_r($resulValidar);		
	
	if ($resulValidar['datosFormUsuario']['actPASSUSUARIO']['codError']!=='00000' ||
	    $resulValidar['datosFormUsuario']['PASSUSUARIO']['codError']   !=='00000' || 
	    $resulValidar['datosFormUsuario']['RPASSUSUARIO']['codError']  !=='00000' )
	{$resulValidar['codError']='80200';}
	else
	{$resulValidar['codError']='00000';}
 	
	//echo "<br><br>5validarCambiarPass:arrCamposForm:";print_r($resulValidar);
	return $resulValidar;
}
//----------- fin validarCambiarPass (llamada desde controladorLogin) ----------

//-------- Inicio validarRestaurarPass (llamada desde controladorLogin) ----------
function validarRestaurarPass($arrCamposForm,$codUser)
{//echo "<br><br>1validarRestaurarPass:arrCamposForm:";print_r($arrCamposForm);
	
	require_once './modelos/libs/validarCampos.php';	

	$resulValidar['datosFormUsuario']['PASSUSUARIO']  = validarCampoPass($arrCamposForm['PASSUSUARIO'],6,30,"" );	
 $resulValidar['datosFormUsuario']['RPASSUSUARIO'] = validarCampoPass($arrCamposForm['RPASSUSUARIO'],6,30,"" );	

	//echo "<br><br>2validarRestaurarPass:resulValidar:";print_r($resulValidar);
	if ($arrCamposForm['PASSUSUARIO']!==$arrCamposForm['RPASSUSUARIO'])
	{$resulValidar['datosFormUsuario']['PASSUSUARIO']['codError'] = '80430';
		$resulValidar['datosFormUsuario']['PASSUSUARIO']['errorMensaje']='Las dos contraseñas introducidas no eran iguales';
		$resulValidar['datosFormUsuario']['RPASSUSUARIO']['codError'] = '80430';
		$resulValidar['datosFormUsuario']['RPASSUSUARIO']['errorMensaje']='Las dos contraseñas introducidas no eran iguales';
	}
	//echo "<br><br>3validarRestaurarPass:$resulValidar:";print_r($resulValidar);		
	
	if ($resulValidar['datosFormUsuario']['PASSUSUARIO']['codError'] !=='00000' ||
	    $resulValidar['datosFormUsuario']['RPASSUSUARIO']['codError']!=='00000' )
	{$resulValidar['codError'] = '80200';
	}
	else
	{if (!is_numeric($codUser))//evitar injection
		{ $resulValidar['codError'] = '70100';	//error en select
				//$resulValidar['errorMensaje'] = "Error codUser";			:no interesa que se vea mensaje por seguridad	
		}
		else
		{require_once './modelos/modeloUsuarios.php';	
		 $resValidarCodUser = validarCodUser($codUser);//busca en tablas
		 //echo "<br><br>4validarRestaurarPass:resValidarCodUser:";print_r($resValidarCodUser);
			
			if ($resValidarCodUser['codError'] !== '00000') //error de conexion, no encontrado, duplicado,
		 { 
			  if ($resValidarCodUser['codError'] >= '80000')
					{ $resulValidar['codError'] = '70100';	//error en select//
					  $resulValidar['errorMensaje'] = $resValidarCodUser['errorMensaje'];						
					}
					else //$resValidarCodUser['codError'] < '80000' // error de conexión
					{ $resulValidar = $resValidarCodUser;					
					}	 							 
		 }
		 else //$resValidarCodUser['codError'] == '00000'
			{ $resulValidar['codError'] = '00000';			
			}
		}		
	} 	
	//echo "<br><br>5validarRestaurarPass:arrCamposForm:";print_r($resulValidar);
	return $resulValidar;
}
//----------- fin validarRestaurarPass (llamada desde controladorLogin) ----------

//-------- Inicio emailemailRestablecerPass (llamada desde controladorLogin) ----------
function validarFormRecordarLogin($recordarLogin)
{//echo "<br><br>1validarFormRecordarLogin:recordarLogin:";print_r($recordarLogin); 
 require_once './modelos/libs/validarCampos.php';
	
	$reValidarRecordarLogin['codError']='00000';
	$reValidarRecordarLogin['errorMensaje']='';

	$reValidarRecordarLogin['EMAIL'] = validarEmail($recordarLogin['EMAIL'],"Email del usuario: ");

	if (isset($recordarLogin['opcionPassUser']) && !empty($recordarLogin['opcionPassUser']))//Para evitar notice
 {$reValidarRecordarLogin['opcionPassUser'] = validarCampoRadio($recordarLogin['opcionPassUser'],"");
 }
	else
	{$reValidarRecordarLogin['opcionPassUser']['codError'] = '80201';
	 $reValidarRecordarLogin['opcionPassUser']['errorMensaje'] = 'debes elegir una opción';
	}	

	//echo "<br /><br />validarFormRecordarLogin: reValidarRecordarLogin: ";print_r($reValidarRecordarLogin);
	if ($reValidarRecordarLogin['EMAIL']['codError']!=='00000')
 {$reValidarRecordarLogin['codError']=$reValidarRecordarLogin['EMAIL']['codError'];
	 $reValidarRecordarLogin['errorMensaje']=$reValidarRecordarLogin['EMAIL']['errorMensaje'];
		//$resulValidarUsuario['resultadoFilas']['NOMUSUARIO']=$errorLogico['NOMUSUARIO'];
	}
	if ($reValidarRecordarLogin['opcionPassUser']['codError']!=='00000')
 {$reValidarRecordarLogin['codError']=$reValidarRecordarLogin['opcionPassUser']['codError'];
	 $reValidarRecordarLogin['errorMensaje'].=". ".$reValidarRecordarLogin['opcionPassUser']['errorMensaje'];
		//$resulValidarUsuario['resultadoFilas']['NOMUSUARIO']=$errorLogico['NOMUSUARIO'];
	}
	//echo "<br><br>2validarFormRecordarLogin:reValidarRecordarLogin:";print_r($reValidarRecordarLogin); 
 return $reValidarRecordarLogin;	
}		
//----------- fin emailemailRestablecerPass (llamada desde controladorLogin) ----------
?>