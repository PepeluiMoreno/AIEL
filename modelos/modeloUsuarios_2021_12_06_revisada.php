<?php
/*------------------------------------------------------------------------------
FICHERO: modeloUsuarios.php
VERSION: PHP 5.6.4

DESCRIPCION: Este "Modelo" busca, inserta, y actualiza en (BBDD), pedida por
						       controladores o modelos.
LLAMADO: desde "controladorUsuarios.php", contoladorLogin.php,
         modeloSocios.php, modelosSimpatizantes, ...

Incluye funciones: "buscarUsuarios","insertarUsuario","eliminarUsuario",
                   "actualizarUsuarios", etc.
OBSERVACIONES: Agustín
2020-02-10: Introduzco modificaciones para PHP-PDP MySQL
2018-11-15: añado parValoresProvinciaPresCoord(), para estadísticas 
por provincias en cPresidente.php:cExportarExcelEstadisticasAltasBajasProvPres()

Nota: existen funciones que pueden ser redundantes y se podrían simplificar en 
número.																							
------------------------------------------------------------------------------*/
require_once "BBDD/MySQL/conexionMySQL.php";
require_once "BBDD/MySQL/modeloMySQL.php";

/*------------------------------ Inicio buscarDatosUsuario ---------------------
Descripcion: Busca los datos en la tabla USUARIO, por el campo "CODUSER"

RECIBE: $codUser	
DEVUELVE: array $resulValidarUsuario con datos tabla USUARIO, 	y código error

LLAMADA:controladorSocios.php:confirmarAnularAltaSocio(),confirmarEmailPassAltaSocioPorGestor()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php
       modelos/BBDD/MySQL/conexionMySQL.php":conexionDB(),
       modeloMySQL.php/buscarEnTablas()
					
OBSERVACIONES:														
Agustín 2020-02-05: 
Modifico incluir buscarCadSql()	y para incluir PHP: PDOStatement::bindParamValue				
								
------------------------------------------------------------------------------*/
function buscarDatosUsuario($codUser)
{
	//echo "<br><br>0 modelosUsuarios.php:buscarDatosUsuario:codUser: ";print_r($codUser);	
	
	$arrMensaje ='';

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 
	
	if ($conexionDB['codError'] !== '00000')		
	{ $datosUsuario = $conexionDB;	
	}
	else
	{
		$tablasBusqueda = 'USUARIO';
		$camposBuscados = '*';  
		$cadCondicionesBuscar = " WHERE USUARIO.CODUSER = :codUser ";	
	
		$arrBind = array(':codUser' => $codUser); 

	 //echo "<br><br>1-1 modelosUsuarios.php:buscarDatosUsuario:arrBind: ";print_r($arrBind);		
			
  $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		
		//echo "<br><br>1-2 modelosUsuarios.php:buscarDatosUsuario:arrBind: ";print_r($arrBind);	
		
		$resBuscarUsuario = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);
		//echo "<br><br>1-3 modelosUsuarios.php:buscarDatosUsuario:resBuscarUsuario: ";print_r($resBuscarUsuario);	
		
  if ($resBuscarUsuario['codError'] !== '00000')
  {
			$datosUsuario =	$resBuscarUsuario;		 
	  $arrMensaje['textoComentarios'] = "Error del sistema al buscar datos usuario, vuelva a intentarlo pasado un tiempo ";			
			require_once './modelos/modeloErrores.php';
		 insertarError($resBuscarUsuario,$conexionDB['conexionLink']);		
  }
  elseif ($resBuscarUsuario['numFilas'] == 0)
	 {
			$datosUsuario['codError'] = '80001'; //no encontrado
	  $datosUsuario['errorMensaje'] = "Error sistema: No existe ese usuario";
			$datosUsuario['numFilas'] = $resBuscarUsuario['numFilas'];
  }
  else
  { $datosUsuario['codError'] = '00000';
		  $datosUsuario['numFilas'] = $resBuscarUsuario['numFilas'];
				
		  foreach ($resBuscarUsuario['resultadoFilas'][0] as $indice => $contenido)                         
    {      
      $datosUsuario['resultadoFilas'][$indice] = $contenido; 
    }
  }
	}
	$datosUsuario['arrMensaje'] = $arrMensaje;			

	//echo "<br>2 modelosUsuarios.php:buscarDatosUsuario:datosUsuario:";print_r($datosUsuario);
	
	return $datosUsuario;
}
//------------------------------ Fin buscarDatosUsuario ------------------------

/*------------------------------ Inicio validarUsuario -------------------------
Es la entrada de identificación de un usuario a aplicación de Gestión de Soci@s 
A partir de las variables $nomUsuario,$passUsuario se validan que los campos 
$_POST['USUARIO'], $_POST['CLAVE'], introducidos en el formulario cumplen las 
condiciones, y después con los campos "USUARIO,PASSUSUARIO" se busca en la tabla
USUARIO, si está dado de "alta" y despues en las tablas USUARIOTIENEROL,ROL los
roles de ese usuario.
Si en la tabla "USUARIO" el campo estado 'ESTADO' !== 'alta', se muestran los 
mensajes correspondientes a cada situación y ESTADO
se emite asigna "errorMensaje" = 'El usuario está dado de baja o bloqueado',etc.

RECIBE: $nomUsuario=$_POST['USUARIO'],$passUsuario=$_POST['CLAVE']	procedentes 
        del formulario de entrada	a la aplicación		
DEVUELVE: array $resulValidarUsuario con datos tablas USUARIO,USUARIOTIENEROL,ROL
          y  código error																	

LLAMADA desde:controladorLogin.php:validarLogin()
LLAMA: modelos/libs/validarCamposUsuarios.php:validarCampoUsuarioLogin(),
       validarCampoPassLogin()
       require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
       usuariosConfig/BBDD/MySQL/configMySQL.php:conexionDB(),
       modeloMySQL.php/buscarCadSql()
									
OBSERVACIONES:															
Agustín 2020-06-24: modifico para incluir PHP: PDOStatement::bindParamValue	y 
modifico consultas para más información en caso de no encontrado											
------------------------------------------------------------------------------*/
function validarUsuario($nomUsuario,$passUsuario)//Nuevo se llama solo desde contoladorLogin
{//echo "<br /><br />0-1 modeloUsuarios.php:validarUsuario:path: "; print_r($_SERVER['SCRIPT_FILENAME']);
 //echo "<br /><br />0-2 modeloUsuarios.php:validarUsuario:nomUsuario: "; print_r($nomUsuario);
 //echo "<br /><br />0-3 modeloUsuarios.php:validarUsuario:passUsuario: "; print_r($passUsuario);
 
 require_once './modelos/libs/validarCamposUsuarios.php';
	require_once './modelos/modeloErrores.php'; //si es un error en tabla error, insertar errores 
		
	$validarUsuario['nomScript'] = ' modeloUsuarios.php';
 $validarUsuario['nomFuncion'] = 'validarUsuario';
	$validarUsuario['codError'] = '00000';
 $validarUsuario['errorMensaje'] = '';
	
	$reValidarCampos['USUARIO'] = validarCampoUsuarioLogin($nomUsuario,6,30,'Error en usuario o contraseña, o no estás registrado');
 $reValidarCampos['CLAVE'] = validarCampoPassLogin($passUsuario,6,30,'Error en usuario o contraseña, o no estás registrado');
	
	//echo "<br><br>1-1 modeloUsuarios.php:validarUsuario:reValidarCampos: ";print_r($reValidarCampos);	

	if ($reValidarCampos['USUARIO']['codError'] !=='00000' || $reValidarCampos['CLAVE']['codError'] !=='00000')
 { $resulValidarUsuario['codError'] = '80101';
		 $resulValidarUsuario['resultadoFilas']['USUARIO'] = $reValidarCampos['USUARIO'];
		 $resulValidarUsuario['resultadoFilas']['CLAVE'] = $reValidarCampos['CLAVE'];
	}
	else //$reValidarCampos['USUARIO']['codError'] =='00000' && $reValidarCampos['CLAVE']['codError'] =='00000')
	{
		require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
		require_once "BBDD/MySQL/conexionMySQL.php";
		$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
		if ($conexionDB['codError'] !== '00000')		
		{ $resulValidarUsuario = $conexionDB;	//$errorSistema==$conexionDB;
		}
		else//$conexionDB['codError'] == '00000'
		{
			/*------------- Inicio buscar datos socio en tabla USUARIO ----------------*/
			$nomUsuario = $reValidarCampos['USUARIO']['valorCampo'];//ya estarán quitados posibles espacios introducidos en el form. por el usuario
		 $passUsuarioSinEncriptar = $reValidarCampos['CLAVE']['valorCampo'];//idem
	  $passUsuarioEncriptada = sha1($passUsuarioSinEncriptar);//encriptada 
			
	  //echo "<br><br>1-2 modeloUsuarios.php:validarUsuario:passUsuarioEncriptada: "; print_r($passUsuarioEncriptada);
	
		 $tablasBusqueda = 'USUARIO';
		 $camposBuscados = 'USUARIO.CODUSER,USUARIO.USUARIO,PASSUSUARIO,ESTADO';	
																				
   $cadCondicionesBuscar = " WHERE USUARIO.USUARIO = :nomUsuario".
																														" AND USUARIO.PASSUSUARIO = :passUsuarioEncriptada";
			
			$arrBind = array(':nomUsuario' => $nomUsuario, ':passUsuarioEncriptada' => $passUsuarioEncriptada);
			
   $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
   
			//echo "<br><br>2-1 modeloUsuarios.php:validarUsuario:cadSql: "; print_r($cadSql); 			
		
		 $resulValidarUsuario = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);

   //echo "<br><br>2-2 modeloUsuarios.php:validarUsuario:resulValidarUsuario: "; print_r($resulValidarUsuario);echo "<br><br>";
		
	  if ($resulValidarUsuario['codError'] !== '00000')//'error SELECT
	  { $resulValidarUsuario['textoComentarios'] = 'modeloUsuarios.php:validarUsuario(). Error al buscar en USUARIO';
			  $resInsertarErrores = insertarError($resulValidarUsuario,$conexionDB['conexionLink']);
	  }
	  elseif ($resulValidarUsuario['numFilas'] == 0)
		 {
				$resulValidarUsuario['codError'] = '80001';
	   $resulValidarUsuario['resultadoFilas']['USUARIO']['codError'] = '80001';
	   $resulValidarUsuario['resultadoFilas']['USUARIO']['errorMensaje'] = 'Usuario o contraseña incorrecta o no está registrado';
	  }	
	  elseif ($resulValidarUsuario['resultadoFilas'][0]['ESTADO'] !== 'alta')
	  {			
				$resulValidarUsuario['codError'] = '80101';//no es error pero lo trato como error lógico 
		  $resulValidarUsuario['resultadoFilas']['USUARIO']['codError'] = '80101';	  
				
				if ($resulValidarUsuario['resultadoFilas'][0]['ESTADO'] == 'PENDIENTE-CONFIRMAR')
				{
					$resulValidarUsuario['resultadoFilas']['USUARIO']['errorMensaje'] = "Por seguridad tienes bloqueado el acceso hasta que confirmes tu alta como socio/a. 
					<br /><br />Anteriomente iniciaste el proceso de alta como socio/a, y habrás recibido un email para confirmar el alta. 
					Si no lo has recibido, mira en la carpeta de correo no deseado -spam-.					
				 <br /><br />Por favor confirma tu alta o avisa a <strong>secretaria@europalaica.org</strong> indicando que quieres confirmar tu alta";			                        
				}			
				elseif ($resulValidarUsuario['resultadoFilas'][0]['ESTADO'] == 'alta-sin-password-excel')
				{
					$resulValidarUsuario['resultadoFilas']['USUARIO']['errorMensaje'] = "Por seguridad tienes bloqueado el acceso a la aplicación de Gestión de Soci@s. 
					<br /><br />Hace tiempo que estás registrado como socio/a de Europa Laica, envía ahora un email a <strong>secretaria@europalaica.org</strong> con tu nombre y apellidos, 
					para que te envíe un correo electrónico para desbloquear el acceso.";			                        														
				}	
				elseif ($resulValidarUsuario['resultadoFilas'][0]['ESTADO'] == 'alta-sin-password-gestor') 
				{
					$resulValidarUsuario['resultadoFilas']['USUARIO']['errorMensaje'] =  "Por seguridad tienes bloqueado el acceso a la aplicación de Gestión de Soci@s, 
					hasta que confirmes tu email. 
					<br /><br />Anteriormente, a petición tuya, un gestor de Europa Laica, te dio de alta como socio/a.
     <br /><br />Habrás recibido un email cuando el gestor realizó tu alta (si no, mira en la carpeta de correo no deseado -spam-), 
					<br /><br />Por favor confirma tu alta o avisa a <strong>secretaria@europalaica.org</strong> indicando que tienes bloqueado el acceso";			                        														
				}	
				//echo "<br><br>2-3 modeloUsuarios.php:validarUsuario:resulValidarUsuario: "; print_r($resulValidarUsuario);
	  }//elseif ($resulValidarUsuario['resultadoFilas'][0]['ESTADO'] !== 'alta')
   /*------------- Fin buscar datos socio en tabla USUARIO -------------------*/		
			
			else //elseif ($resulValidarUsuario['resultadoFilas'][0]['ESTADO'] == 'alta') //se buscan roles
			{ 
			  /*----- Inicio buscar datos roles socio en tabla USUARIOTIENEROL,ROL ----*/ 																						
					$tablasBusqueda = 'USUARIOTIENEROL,ROL';
					$camposBuscados = 'USUARIOTIENEROL.CODUSER,ROL.*';	
																						
					$cadCondicionesBuscar = " WHERE USUARIOTIENEROL.CODUSER = :codUser AND USUARIOTIENEROL.CODROL = ROL.CODROL ";																															

					$arrBind = array(':codUser' => $resulValidarUsuario['resultadoFilas'][0]['CODUSER']);
					
					$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
					
					//echo "<br><br>3-1 modeloUsuarios.php:validarUsuario:cadSql: "; print_r($cadSql); 			
				
					$resulValidarUsuario = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);
		   					
					if ($resulValidarUsuario['codError'] !== '00000')//'error SELECT
					{ $resulValidarUsuario['textoComentarios'] = 'modeloUsuarios.php:validarUsuario(). Error al buscar en USUARIOTIENEROL,ROL';					
       $resInsertarErrores = insertarError($resulValidarUsuario,$conexionDB['conexionLink']);		
					}
					else// $resulValidarUsuario['codError'] == '00000'
					{						  
							if ($resulValidarUsuario['numFilas'] == 0)//si es alta como usuario debe tener un rol o es un error del sistema
							{	
									$resulValidarUsuario['codError'] = '70100';									
									$resulValidarUsuario['errorMensaje'] = 'Error al buscar en USUARIOTIENEROL,ROL';
         $resulValidarUsuario['textoComentarios'] = 'modeloUsuarios.php:validarUsuario(). Error al buscar en USUARIOTIENEROL,ROL';					
         $resInsertarErrores = insertarError($resulValidarUsuario,$conexionDB['conexionLink']);											
							}							
							//echo "<br><br>3-2 modeloUsuarios.php:validarUsuario:resulValidarUsuario: "; print_r($resulValidarUsuario);
							
					}//else $resulValidarUsuario['codError'] == '00000'					
			}//else if ($resulValidarUsuario['resultadoFilas'][0]['ESTADO'] == 'alta')
   
		 /*-------- Fin buscar datos roles socio en tabla USUARIOTIENEROL,ROL ------*/			

		}//else $conexionDB['codError'] == '00000'			
 }//else $reValidarCampos['USUARIO']['codError'] =='00000' && $reValidarCampos['CLAVE']['codError'] =='00000')
	
	//echo "<br><br>4 modeloUsuarios.php:validarUsuario:resulValidarUsuario: "; print_r($resulValidarUsuario);
 
	return $resulValidarUsuario;
}
/*------------------------------ Fin validarUsuario --------------------------*/

/*------------------------------ Inicio validarCodUser -------------------------
Descripción: Se usa para validar la existencia del CODUSER y que no esté 
             duplicado en la tabla USUARIO

RECIBE: $codUsuario		
DEVUELVE: array $reValidarCodUser con datos tablas USUARIO y  código error			

Llamada: controladorLogin.php:restablecerPass():modelos/libs/validarCamposUsuarios.php:validarRestaurarPass()	
LLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
       BBDD/MySQL/conexionMySQL.php:conexionDB()
							modeloMySQL.php/buscarCadSql()
									
OBSERVACIONES: 															
Agustín 2020-02-05: modifico para incluir PHP: PDOStatement::bindParamValue
------------------------------------------------------------------------------*/
function validarCodUser($codUsuario)//se llama solo desde contoladorLogin
{
	//echo "<br><br>0 modelosUsuarios.php:validarCodUser:codUsuario: ";print_r($codUsuario);	
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError']!=='00000')		
	{ $reValidarCodUser = $conexionDB;	
	}
	else
	{$tablasBusqueda = 'USUARIO';
		$camposBuscados = '*'; 		
		$cadenaCondicionesBuscar = " WHERE USUARIO.CODUSER = :codUsuario ";	
		
		//echo "<br>1 modeloUsuarios.php:validarCodUser:cadenaCondicionesBuscar: "; print_r($cadenaCondicionesBuscar); 
			
		$arrBind = array(':codUsuario' => $codUsuario); 		
		  
  $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		$resBuscarUsuario = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		

  if ($resBuscarUsuario['codError'] !== '00000')//'error sintaxis, error conexion, 
  {$reValidarCodUser = $resBuscarUsuario; //no se usa
  }
  elseif ($resBuscarUsuario['numFilas'] == 0)
	 {$reValidarCodUser['codError'] = '80001';
   $reValidarCodUser['errorMensaje'] = 'Error: Usuario no encontrado';
  }	
		elseif ($resBuscarUsuario['numFilas'] > 1)
	 {$reValidarCodUser['codError'] = '80440';   
   $reValidarCodUser['errorMensaje'] = 'Error: Usuario duplicado';
  }
		else
		{$reValidarCodUser = $resBuscarUsuario;
		}
	}
	//echo "<br>2 ModeloUsuarios.php:validarCodUser:reValidarCodUser: "; print_r($reValidarCodUser);
	
	return $reValidarCodUser;
}
//------------------------------ Fin validarCodUser ----------------------------

/*------------------------------ Inicio validarPass ----------------------------
Descripción: Busca si la contraseña introducida por el usuario es la existente 
             en tabla USUARIO.
													
RECIBE: $codUser,$passUsuario	
DEVUELVE: array $resulValidarPass con datos tablas USUARIO, y  código error															

Llamada: solo desde controladorLogin.php:cambiarPassUser()->validarCamposUsuario.php:validarCambiarPass()
LLAMA: validarCamposUsuarios.php:validarCambiarPass(),validarCampoPassLogin()
       require usuariosConfig/BBDD/MySQL/configMySQL.php
							BBDD/MySQL/conexionMySQL.php:conexionDB(),
       modeloMySQL.php/buscarCadSql()
									
OBSERVACIONES:															
Agustín 2020-02-05: modifico para incluir PHP: PDOStatement::bindParamValue	PDO7												
------------------------------------------------------------------------------*/
function validarPass($codUser,$passUsuario)//se llama solo desde contoladorLogin
{ 
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')		
	{ $resulValidarPass = $conexionDB;	
	}
	else
	{$passUsuarioSinEncriptar = $passUsuario;
	 $passUsuarioEncriptada = sha1($passUsuarioSinEncriptar);//encriptada 
			
	 $tablasBusqueda = 'USUARIO';
		$camposBuscados = 'USUARIO.CODUSER,USUARIO.USUARIO,PASSUSUARIO,ESTADO';
		$cadenaCondicionesBuscar = " WHERE USUARIO.CODUSER = :codUser AND USUARIO.PASSUSUARIO = :passUsuarioEncriptada ";		
		
		//echo "<br>1 modeloUsuarios.php:validarPass:cadenaCondicionesBuscar: "; print_r($cadenaCondicionesBuscar); 
			
		$arrBind = array(':codUser' => $codUser,':passUsuarioEncriptada' => $passUsuarioEncriptada); 	
		
  $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		$resBuscarPass = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);				

  if ($resBuscarPass['codError'] !== '00000')
  {
		 $resulValidarPass = $resBuscarPass;
  }
  elseif ($resBuscarPass['numFilas'] == 0) //no encontrado: error lógico
	 {
			$resulValidarPass['codError'] = '80001';
   $resulValidarPass['errorMensaje'] = 'Contraseña incorrecta';				
  }
		else //$resBuscarPass['codError']=='00000'
		{ 
			$resulValidarPass = $resBuscarPass;							
		}
	}
	//echo "<br><br>2 modeloUsuarios.php:validarPass:resulValidarPass: "; print_r($resulValidarPass);
	return $resulValidarPass;
}
//------------------------------ Fin validarPass -------------------------------

/*------------------------------ Inicio validarPaisSEPA -------------------------
Se usa para validar que la cuenta bancaria IBAN pertenezca a un país con cuentas 
SEPA mediante la columna "SEPA" de la tabla "PAIS"
Se busca por los dos primeros digítos de las cuentas SEPA

RECIBE: $cuentaIBAN, cuenta bancaria IBAN
DEVUELVE: array $resValidarPaisSEPA con datos código error			

LLAMADA: desdefunciones de validación de cuentas bancarias de socios:
validarCamposSocio.php:validarCamposFormAltaSocio(),validarCamposFormActualizarSocio()
validarCamposSocioPorGestor.php:validarCamposFormAltaSocioPorGestor()

LLAMA: require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
       BBDD/MySQL/conexionMySQL.php:conexionDB()
							modeloMySQL.php/buscarCadSql()
							modeloErrores.php:insertarError()
									
OBSERVACIONES: 															
PHP 7.3.21 incluye PDOStatement::bindParamValue
------------------------------------------------------------------------------*/
function validarPaisSEPA($cuentaIBAN)//se llama solo desde contoladorLogin
{
	//echo "<br/ ><br />0-1 modelosUsuarios.php:validarPaisSEPA:cuentaIBAN: ";print_r($cuentaIBAN);	
						
	require_once './modelos/modeloErrores.php';
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	
	$resValidarPaisSEPA['nomScript'] = 'modelosUsuarios.php';		
	$resValidarPaisSEPA['nomFuncion'] = 'validarPaisSEPA';
	$resValidarPaisSEPA['codError'] = '00000'; 
	$resValidarPaisSEPA['errorMensaje'] = '';
	
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')		
	{ $resValidarPaisSEPA = $conexionDB;	
	}
	else //$conexionDB['codError'] == '00000'
	{	
		if ( isset($cuentaIBAN) && !empty ($cuentaIBAN) )
		{				
			 $CODPAIS1 = substr($cuentaIBAN,0,2);

				$tablasBusqueda = 'PAIS';
				$camposBuscados = '*'; 		
				$cadenaCondicionesBuscar = " WHERE SEPA ='SI' AND PAIS.CODPAIS1 = :CODPAIS1";	
				
				//$cadSql = "SELECT * FROM PAIS WHERE SEPA ='SI' AND CODPAIS1 = SUBSTRING('MA64011519000001205000534921',1,2)";
				
				//echo "<br />1 modeloUsuarios.php:validarPaisSEPA:cadenaCondicionesBuscar: "; print_r($cadenaCondicionesBuscar); 
					
				$arrBind = array(':CODPAIS1' => $CODPAIS1); 		
						
				$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
				
				$resBuscarSEPA = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
				
				//echo "<br />2 modeloUsuarios.php:validarPaisSEPA:resBuscarSEPA: "; print_r($resBuscarSEPA); 

				if ($resBuscarSEPA['codError'] !== '00000')//'error sintaxis, error conexion, 
				{$resValidarPaisSEPA = $resBuscarSEPA; //no se usa
				 insertarError($resValidarPaisSEPA,$conexionDB['conexionLink']);	
				}
				elseif ($resBuscarSEPA['numFilas'] == 0)
				{$resValidarPaisSEPA['codError'] = '80001';
					$resValidarPaisSEPA['errorMensaje'] = 'Error: cuenta banco, no permitida, no pertenece a un país SEPA';
				}	
				else
				{$resValidarPaisSEPA = $resBuscarSEPA;
				}
		}//else if ( isset($cuentaIBAN) && !empty ($cuentaIBAN) )
	}//else $conexionDB['codError'] == '00000'	
	
	//echo "<br />3 modeloUsuarios.php:validarPaisSEPA:resValidarPaisSEPA: "; print_r($resValidarPaisSEPA);
	
	return $resValidarPaisSEPA;
}
/*------------------------------ Fin validarPaisSEPA ----------------------------*/

/*------------------------------ Inicio buscarUnRolUsuario ---------------------
Descripción: Busca un rol concreto de un usuario en tabla USUARIOTIENEROL.

RECIBE: $codUser,$codRol
DEVUELVE: array $rolUsuario con datos tabla USUARIOTIENEROL, y  código error		

Llamada: modeloSocios.php:eliminarDatosSocios(),modeloPresCoord.php:bajaSocioFallecido(),
         cPresidente.php:asignarPresidenciaRolBuscar(),asignarTesoreriaRolBuscar()
LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       BBDD/MySQL/conexionMySQL.php:conexionDB(),
       modeloMySQL.php/buscarEnTablas()
									
OBSERVACIONES:															
Agustín 2020-02-05: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarUnRolUsuario($codUser, $codRol)
{
	//echo "<br><br>0-1 modelosUsuarios.php:buscarUnRolUsuario:codUser: $codUser, codRol: $codRol";	

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')		
	{ $rolUsuario = $conexionDB;	//$errorSistema==$conexionDB;
	}
	else
	{
		$tablasBusqueda = 'USUARIOTIENEROL';
		$camposBuscados = 'USUARIOTIENEROL.*';		
		$cadenaCondicionesBuscar = " WHERE USUARIOTIENEROL.CODUSER = :codUser AND USUARIOTIENEROL.CODROL = :codRol ";	

		//echo "<br>1 modeloUsuarios.php:buscarUnRolUsuario:cadenaCondicionesBuscar: "; print_r($cadenaCondicionesBuscar); 
			
		$arrBind = array(':codUser' => $codUser,':codRol' => $codRol);
	
  $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		$resBuscarUnRolUsuario = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);			

  if ($resBuscarUnRolUsuario['codError'] !== '00000')//'error sintaxis, error conexion, 
  {
			$rolUsuario = $resBuscarUnRolUsuario; //no se usa
  }
		else
		{
			$rolUsuario = $resBuscarUnRolUsuario;
		}
	}
	//echo "<br>2 modeloUsuarios.php:validarUsuario:buscarUnRolUsuario:rolUsuario: "; print_r($rolUsuario);
	
	return $rolUsuario;
}
//------------------------------ Fin buscarUnRolUsuario ------------------------


/*------------------------------ Inicio buscarRolesUsuario ---------------------
Descripción: Busca los roles de un usuario en tabla USUARIOTIENEROL, ROL.

RECIBE: $codUser
DEVUELVE: array $rolesUsuario con datos tablas USUARIOTIENEROL, ROL y código error						

Llamada: controladorLogin.php:menuRolesUsuario(),modeloPresCoord.php:buscarAreaGestionCoordRol()        
LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       BBDD/MySQL/conexionMySQL.php:conexionDB(),
       modeloMySQL.php/buscarCadSql()
									
OBSERVACIONES:															
Agustín 2020-02-07: modifico para incluir PHP7: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarRolesUsuario($codUser)//se llama solo desde contoladorLogin
{
	//echo "<br><br>0-1 modelosUsuarios.php:buscarRolesUsuario:codUser: ";print_r($codUser);
	
	require_once './modelos/modeloErrores.php';
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')		
	{ $rolesUsuario = $conexionDB;	
	}
	else
	{
		$tablasBusqueda = 'USUARIOTIENEROL,ROL';
		$camposBuscados = 'ROL.*';
  $cadenaCondicionesBuscar = " WHERE USUARIOTIENEROL.CODUSER = :codUser "." AND USUARIOTIENEROL.CODROL = ROL.CODROL";		
				
		$arrBind = array(':codUser' => $codUser);
		
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		//echo "<br>1-1 modeloUsuarios.php:buscarRolesUsuario:cadSql: "; print_r($cadSql); 
		
		$resBuscarRolesUsuario = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);			

  //echo "<br>1-2 modeloUsuarios.php:buscarRolesUsuario:resBuscarRolesUsuario: "; print_r($resBuscarRolesUsuario); 
		
  if ($resBuscarRolesUsuario['codError'] !== '00000')//'error sintaxis, error conexion, 
  {
			$rolesUsuario = $resBuscarRolUsuario;
			
			insertarError($resBuscarRolUsuario,$conexionDB['conexionLink']);	
  }
  elseif ($resBuscarRolesUsuario['numFilas'] == 0)
	 {
			$rolesUsuario['codError'] = '80001';
			$rolesUsuario['errorMensaje'] = 'Usuario no tiene rol';
   $rolesUsuario['resultadoFilas']['CODROL']['codError'] = '80001';
   $rolesUsuario['resultadoFilas']['CODROL']['errorMensaje'] = 'Usuario no tiene rol';
			
			insertarError($rolesUsuario,$conexionDB['conexionLink']);	
  }	
		else
		{$rolesUsuario = $resBuscarRolesUsuario;
		}
	}
	//echo "<br>2 ModeloUsuarios.php:buscarRolesUsuario:rolesUsuario: "; print_r($rolesUsuario);
	
	return $rolesUsuario;
}
//------------------------------ Fin buscarRolesUsuario ------------------------

/*---------------------------- Inicio buscarRolFuncion -------------------------
Descripción: Busca las datos y funciones de un rol en tablas FUNCION,ROLTIENEFUNCION

RECIBE: $codRol
DEVUELVE: array $rolesYfunciones con datos tablas FUNCION,ROLTIENEFUNCION 
          y código error			

Llamada: controladorSocios.php:menuGralSocio(),cCoordinador.php:menuGralSocio(),
         cPresidente.php:menuGralSocio(),cTesorero.php:menuGralSocio(),
									cAdmin.php:menuGralAdmin()
LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
       modeloMySQL.php/buscarEnTablas(),/modelos/modeloErrores.php:insertarError()
									
OBSERVACIONES: PHP 7.3.21. modifico para incluir PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarRolFuncion($codRol)
{	
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $rolesYfunciones = $conexionDB;
	}
	else	
	{
		$tablasBusqueda = 'FUNCION,ROLTIENEFUNCION';
		$camposBuscados = 'NOMFUNCION,CONTROLADOR,TEXTOMENU,DESCRIPCIONALT';
																														
		$cadenaCondicionesBuscar = " WHERE FUNCION.CODFUNCION = ROLTIENEFUNCION.CODFUNCION	AND ROLTIENEFUNCION.CODROL = :codRol".
														               " ORDER BY FUNCION.CODFUNCION";																														
																										
	 //echo "<br>1 modeloUsuarios.php:buscarRolFuncion:cadenaCondicionesBuscar: "; print_r($cadenaCondicionesBuscar); 
			
		$arrBind = array(':codRol' => $codRol); 
		
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		$rolesYfunciones = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);				
		
		if ($rolesYfunciones['codError'] !== '00000')
		{ 
		 $rolesYfunciones['arrMensaje']['textoCabecera'] = 'Gestión de usuarios';
			$rolesYfunciones['arrMensaje']['textoComentarios'] .= "<br /><br />Error del sistema al buscar el perfil del usuario, vuelva a intentarlo pasado un tiempo";
		 //$rolesYfunciones['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		 //$rolesYfunciones['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	
			require_once './modelos/modeloErrores.php';
			insertarError($rolesYfunciones,$conexionDB['conexionLink']);		
		}	
		elseif ($rolesYfunciones['numFilas'] === 0)
		{
			$rolesYfunciones['codError'] = '80004'; //no tiene roles
			$rolesYfunciones['errorMensaje'] = "No tiene ningún rol";
		}
	}
 //echo '<br><br>2 modeloUsuarios.php:buscarRolFuncion:rolesYfunciones: ';print_r($rolesYfunciones);
	
	return $rolesYfunciones;
}
//------------------------------ Fin buscarRolFuncion --------------------------

/*---------------------------- Inicio buscarRolFuncionUsuario ------------------
Descripción: Busca las dstos de un rol y funciones de un rol en tablas 
            USUARIOTIENEROL,FUNCION, ROLTIENEFUNCION, ROL a partir del campo "USUARIO"
												
RECIBE: $campoUsuario
DEVUELVE: array $rolesYfuncionesUser con datos tablas USUARIOTIENEROL,FUNCION,
          ROLTIENEFUNCION, ROL y código error																		

Llamada: controladorLogin.php-->validarLogin()//Ahora desde ahi no se llama, 
LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
       modeloMySQL.php/buscarEnTablas(),/modelos/modeloErrores.php:insertarError()
									
OBSERVACIONES:	Es probable que haya sido sustituido por los anteriores y ya no se use
													
Agustín 2020-02-09: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarRolFuncionUsuario($campoUsuario)
{
	//echo '<br>1 modeloUsuarios.php:buscarRolFuncionUsuario:campoUsuario: ';print_r($campoUsuario);
	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError']!=='00000')	
	{ $rolesYfuncionesUser = $conexionDB;
	}
	else	
	{
		$tablasBusqueda = ' USUARIOTIENEROL,FUNCION,ROLTIENEFUNCION, ROL ';
	 $camposBuscados = ' DISTINCT ROL.CODROL,ROL.NOMROL,FUNCION.NOMFUNCION,FUNCION.CONTROLADOR,FUNCION.TEXTOMENU,FUNCION.DESCRIPCIONALT ';

		$cadenaCondicionesBuscar = " WHERE FUNCION.CODFUNCION = ROLTIENEFUNCION.CODFUNCION
																												   	AND USUARIOTIENEROL.CODROL = ROL.CODROL
																												   	AND ROLTIENEFUNCION.CODROL = USUARIOTIENEROL.CODROL
																												   	AND USUARIOTIENEROL.USUARIO = :campoUsuario ".
																													    "	ORDER BY FUNCION.CODFUNCION DESC";																														
   
		$arrBind = array(':campoUsuario' => $campoUsuario);
		
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		$rolesYfuncionesUser = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
		
		if ($rolesYfuncionesUser['codError'] !== '00000')
		{ 
		 $rolesYfuncionesUser['arrMensaje']['textoCabecera'] = 'Gestión de usuarios';
			$rolesYfuncionesUser['arrMensaje']['textoComentarios'] .= ".Error del sistema, vuelva a intentarlo pasado un tiempo ";
		 $rolesYfuncionesUser['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		 $rolesYfuncionesUser['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	
			require_once './modelos/modeloErrores.php';
			insertarError($rolesYfuncionesUser,$conexionDB['conexionLink']);		
		}	
		elseif ($rolesYfuncionesUser['numFilas'] == 0)
		{$rolesYfuncionesUser['codError'] = '80004'; //no tiene roles
			$rolesYfuncionesUser['errorMensaje'] = "No tiene ningún rol";
		}
	}
 //echo '<br>2 modeloUsuarios.php:buscarRolFuncionUsuario:rolesYfuncionesUser: ';print_r($rolesYfuncionesUser);
	
	return $rolesYfuncionesUser;
}
//------------------------ Fin buscarRolFuncionUsuario -------------------------


/*---------------------------- Inicio buscarUsuario ----------------------------
Descripción: Busca los datos en tabla USUARIO a partir del campo "USUARIO"

RECIBE: $campoUsuario
DEVUELVE: array $resulBuscarUsuario con datos tablas USUARIO y código error		

Llamada: modelos/libs/validarCamposSocio.php, validarCamposSimp() 
LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
       modeloMySQL.php/buscarEnTablas(),/modelos/modeloErrores.php:insertarError()
									
OBSERVACIONES:													
Agustín 2020-02-12: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarUsuario($campoUsuario)
{
	//echo "<br>1- modeloUsuarios.php:buscarUsuario:campoUsuario "; print_r($campoUsuario); 
	
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')		
	{	 
			$resulBuscarUsuario = $conexionDB;
	}
	else
 {
		$tablasBusqueda = 'USUARIO';
		$camposBuscados = '*';
		//$cadCondicionesBuscar =" WHERE USUARIO.USUARIO=\"".$campoUsuario."\"";
		$cadenaCondicionesBuscar = " WHERE USUARIO.USUARIO = :campoUsuario ";

  $arrBind = array(':campoUsuario' => $campoUsuario);
		
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		$resulBuscarUsuario = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
	}
	//echo "<br>2 modeloUsuarios.php:buscarUsuario:resulBuscarUsuario: "; print_r($resulBuscarUsuario);  
				
	if ($resulBuscarUsuario['codError'] !=='00000')
	{ 
  $resulBuscarUsuario['arrMensaje']['textoCabecera'] = 'Gestión de usuarios';
		$resulBuscarUsuario['arrMensaje']['textoComentarios'] .= ".Error del sistema, vuelva a intentarlo pasado un tiempo ";
  $resulBuscarUsuario['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
  $resulBuscarUsuario['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';

		require_once './modelos/modeloErrores.php';
		insertarError($resulBuscarUsuario,$conexionDB['conexionLink']);		
	}
 //echo "<br>3- modeloUsuarios.php:buscarUsuario:resulBuscarUsuario: "; print_r($resulBuscarUsuario); 
	
	return $resulBuscarUsuario;
}
//------------------------------ Fin buscarUsuario  ----------------------------


/*---------------------------- Inicio buscarEmail ------------------------------
Descripción: Busca la existencia de un EMAIL en tablas MIEMBRO,SOCIOSCONFIRMAR,
             AGRUPACIONTERRITORIAL a partir del campo campo EMAIL que se está 
													validando para evitar repetición de email.
RECIBE: $campoEmail
DEVUELVE: array $resulBuscarEmail con datos de si se ha encontrado la búsqueda 
          de email y código error															

LLAMADA: modelos/libs/validarCamposSocio.php, validarCamposSimp(),
         validarCamposSocioPorGestor.php,validarCamposTesorero.php 
LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
       modeloMySQL.php/buscarCadSql(),/modelos/modeloErrores.php:insertarError()
									
OBSERVACIONES:	buscar en tabla AGRUPACIONTERRITORIAL
No inserta errores con insertarError()
													
Agustín 2020-02-12: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarEmail($campoEmail)
{
	//echo "<br><br>1- modeloUsuarios.php:buscarEmail:campoEmail: "; print_r($campoEmail); 

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')		
	{ 
	  $resulBuscarEmail = $conexionDB;
	}
	else
	{ /* Si ya está confirmada el alta o es alta por gestor hay que buscar en 'MIEMBRO' */		
			$tablasBusqueda = 'MIEMBRO';
			$camposBuscados = 'MIEMBRO.EMAIL';		
			$cadenaCondicionesBuscar = " WHERE MIEMBRO.EMAIL = :campoEmail ";
			
			$arrBind = array(':campoEmail' => $campoEmail);
			
			$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
				
			$resulBuscarEmailMiembro = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);					
			
			//echo "<br><br>2- modeloUsuarios.php:buscarEmail:resulBuscarEmailMiembro: "; print_r($resulBuscarEmailMiembro);		
							
			/* Si no está confirmada el alta del socio hay que buscar en 'SOCIOSCONFIRMAR' */		
			$tablasBusqueda = 'SOCIOSCONFIRMAR';
			$camposBuscados = '*';			
			$cadenaCondicionesBuscar = " WHERE SOCIOSCONFIRMAR.EMAIL = :campoEmail ";	
			
			$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		 $resulBuscarEmailSociosConfirmar = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);	
			
			//echo "<br><br>3- modeloUsuarios.php:buscarEmail:resulBuscarEmailSociosConfirmar: "; print_r($resulBuscarEmailSociosConfirmar);
						
			/* Se busca también en tabla 'AGRUPACIONTERRITORIAL'  */		
			$tablasBusqueda = 'AGRUPACIONTERRITORIAL';
			$camposBuscados = '*';
																												
			$cadenaCondicionesBuscar = " WHERE AGRUPACIONTERRITORIAL.EMAIL =  :campoEmail ".
																															" OR AGRUPACIONTERRITORIAL.EMAILCOORD =  :campoEmailCoord ".
																															" OR AGRUPACIONTERRITORIAL.EMAILTESORERO =  :campoEmailTes ".
																															" OR AGRUPACIONTERRITORIAL.EMAILSECRETARIO =  :campoEmailSecr ";
			
			/*Necesita una entrada única para cada BindParamValues para que no dé error. No sirve $arrBind=array(':campoEmail'=>$campoEmail) para todo;*/ 
			
			$arrBind = array(':campoEmail' => $campoEmail,':campoEmailCoord' => $campoEmail,':campoEmailTes' => $campoEmail,':campoEmailSecr' => $campoEmail);
			
			$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		 $resulBuscarEmailAgrupaTerritorial = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);	
			
			//echo "<br><br>4- modeloUsuarios.php:buscarEmail:resulBuscarEmailAgrupaTerritorial: "; print_r($resulBuscarEmailAgrupaTerritorial); 
	}
		
	if ($resulBuscarEmailMiembro['codError'] !== '00000' || $resulBuscarEmailSociosConfirmar['codError'] !== '00000' ||
	    $resulBuscarEmailAgrupaTerritorial['codError'] !== '00000' )
	{ 					
			$resulBuscarEmail['arrMensaje']['textoCabecera'] = 'Gestión de usuarios';
			$resulBuscarEmail['arrMensaje']['textoComentarios'] .= ".Error del sistema, vuelva a intentarlo pasado un tiempo ";
			$resulBuscarEmail['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
			$resulBuscarEmail['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
			
			//echo "<br><br>5-1 modeloUsuarios.php:buscarEmail:resulBuscarEmail: "; print_r($resulBuscarEmail);echo "<br>"; 

			require_once './modelos/modeloErrores.php';
			insertarError($resulBuscarEmail,$conexionDB['conexionLink']);		
	}
	elseif ($resulBuscarEmailMiembro['numFilas'] !== 0 || $resulBuscarEmailSociosConfirmar['numFilas'] !== 0 ||
	        $resulBuscarEmailAgrupaTerritorial['numFilas'] !== 0 )
	{ 			
			$resulBuscarEmail['numFilas'] = 1;	// le asigno el valor 1 porque aunque esté en más de una tabla, con que esté en una sola tabla, el socio ya no puede elegir ese email por estar repetido.
			$resulBuscarEmail['codError'] = '00000';		
   
			//echo "<br><br>5-2 modeloUsuarios.php:buscarEmail:resulBuscarEmail: "; print_r($resulBuscarEmail); 			
	}	
	else
	{ 
   $resulBuscarEmail['numFilas'] = 0;	//no existe ese email se podrá validar el campo EMAIL
	  $resulBuscarEmail['codError'] = '00000';			
			//echo "<br><br>5-3 modeloUsuarios.php:buscarEmail:resulBuscarEmail: "; print_r($resulBuscarEmail); 
	}		
	//echo "<br><br>6- modeloUsuarios.php:buscarEmail:resulBuscarEmail: "; print_r($resulBuscarEmail); 
	
	return $resulBuscarEmail;
}
//------------------------------ Fin buscarEmail -------------------------------

/*---------------------------- Inicio buscarNumDoc -----------------------------
Descripción: Busca la existencia de un NUMDOCUMENTOMIEMBRO en tablas:
             MIEMBRO, SOCIOSCONFIRMAR,	a partir de los campos CODPAISDOC,
													TIPODOCUMENTOMIEMBRO, NUMDOCUMENTOMIEMBRO,  
													que se está validando para evitar repetición de NUMDOCUMENTOMIEMBRO.
													Se utiliza al dar de alta o modificar datos de socio.

RECIBE: $codPais,$tipoDoc,$numDoc
DEVUELVE: array $resNumDoc con datos de si se ha encontrado la búsqueda de    
         NUMDOCUMENTOMIEMBRO y código error															

Llamada: modelos/libs/validarCamposSocio.php, validarCamposSimp(),
         validarCamposSocioPorGestor.php,validarCamposTesorero.php 
LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
       modeloMySQL.php/buscarCadSql()
									
OBSERVACIONES:	No inserta errores con insertarError()
													
Agustín 2020-02-12: modifico para incluir PHP: PDOStatement::bindParamValue													
------------------------------------------------------------------------------*/
function buscarNumDoc($codPais,$tipoDoc,$numDoc)
{
	//echo '<br><br>1- modeloUsuarios.php:buscarNumDoc:numDoc: ';print_r($numDoc);

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	

 require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);

	if ($conexionDB['codError'] !== '00000')	
	{ $resNumDoc = $conexionDB;
	}
	else	
	{
		$tablasBusqueda = 'MIEMBRO';
		$camposBuscados = 'CODPAISDOC,TIPODOCUMENTOMIEMBRO,NUMDOCUMENTOMIEMBRO';
																												
		$cadenaCondicionesBuscar = " WHERE MIEMBRO.CODPAISDOC = :codPais".
		                             " AND MIEMBRO.TIPODOCUMENTOMIEMBRO = :tipoDoc".
																									     	" AND MIEMBRO.NUMDOCUMENTOMIEMBRO = :numDoc";																											
																											
  $arrBind = array(':codPais' => $codPais,':tipoDoc' => $tipoDoc,':numDoc' => $numDoc);
		
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
	
		$resNumDocMiembro = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);			
		
		//echo '<br><br>2- modeloUsuarios.php:buscarNumDoc:resNumDocMiembro: ';print_r($resNumDocMiembro);																												
		
  $tablasBusqueda = 'SOCIOSCONFIRMAR';
		$camposBuscados = 'CODPAISDOC,TIPODOCUMENTOMIEMBRO,NUMDOCUMENTOMIEMBRO';
																										
		$cadenaCondicionesBuscar = " WHERE SOCIOSCONFIRMAR.CODPAISDOC = :codPais".
		                            " AND SOCIOSCONFIRMAR.TIPODOCUMENTOMIEMBRO = :tipoDoc".
																						    				" AND SOCIOSCONFIRMAR.NUMDOCUMENTOMIEMBRO = :numDoc";	
   																										
  //sobra ya está antes $arrBind = array(':codPais' => $codPais,':tipoDoc' => $tipoDoc,':numDoc' => $numDoc);
		
		$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
	
		$resNumDocSocioConfirmar = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);			
		
  //echo '<br><br>3-1 modeloUsuarios.php:buscarNumDoc:resNumDocSocioConfirmar: ';print_r($resNumDocSocioConfirmar);			
																																																											
		if ($resNumDocMiembro['codError'] !== '00000' || $resNumDocSocioConfirmar['codError'] !== '00000')
		{ 
		 $resNumDoc['arrMensaje']['textoCabecera'] = 'Gestión de usuarios';
			$resNumDoc['arrMensaje']['textoComentarios'] .= ".Error del sistema, vuelva a intentarlo pasado un tiempo ";
		 $resNumDoc['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		 $resNumDoc['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
			
	  //echo '<br><br>3-2 modeloUsuarios.php:buscarNumDoc:resNumDoc: ';print_r($resNumDoc);		
			
			require_once './modelos/modeloErrores.php';
			insertarError($resNumDoc,$conexionDB['conexionLink']);		
		}	
		elseif ($resNumDocMiembro['numFilas'] !== 0 || $resNumDocSocioConfirmar['numFilas'] !== 0 )
		{ $resNumDoc['numFilas'] = 1;	
		  $resNumDoc['codError'] = '00000';		
    //echo '<br><br>3-3 modeloUsuarios.php:buscarNumDoc:resNumDoc: ';print_r($resNumDoc);						
		}	
		else
		{ $resNumDoc['numFilas'] = 0;	
		  $resNumDoc['codError'] = '00000';		
    //echo '<br><br>3-4 modeloUsuarios.php:buscarNumDoc:resNumDoc: ';print_r($resNumDoc);				
		}		
	}
 //echo '<br><br>4- modeloUsuarios.php:buscarNumDoc:resNumDoc: ';print_r($resNumDoc);
	
	return $resNumDoc;
}
//---------------------------- Fin buscarEmailNumDoc ---------------------------

/*------------------------------ Inicio provinciaDatos -------------------------
Si el país es España, genera un array buscando todos los datos de una provincia,
 buscando en la tabla "PROVINCIA" a partir del CODPROV

RECIBE: $codProv, que son los dos primeros digitos del CPostal, y $codPais, que debe se "ES"
DEVUELVE: array $parValor con todos los campos de PROVINCIA

LLAMADA: modeloBancos.php:mPrepararDatosSocioPagoCuotaBancosPayForm
LLAMA: require usuariosConfig/BBDD/MySQL/configMySQL.php
       modelos/BBDD/MySQL/conexionMySQL.php:conexionDB()
       modeloMySQL.php/buscarCadSql()
OBSERVACIONES: 				
2020-03-06: modifico para incluir PHP: PDOStatement::bindParamValue								
------------------------------------------------------------------------------*/
function provinciaDatos($codProv,$codPais)
{//echo "<br><br>0-a modeloUsuarios.php:provinciaDatos:codProv: $codProv";
 //echo "<br><br>0-b modeloUsuarios.php:provinciaDatos:codPais: $codPais";

 $datosProvincia['nomScript'] = "modeloUsuarios.php";	
	$datosProvincia['nomFuncion'] = "provinciaNombre";
	$datosProvincia['codError'] = '00000';
	$datosProvincia['errorMensaje'] = '';

 if ( !isset($codPais) || empty($codPais) || $codPais !=='ES' )
 { 	
	  $datosProvincia['codError'] = '80001';
			$datosProvincia['errorMensaje'] = 'Error: Datos Provincia, el país debe ser España';
	}
	else 
	{require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
		$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
		if ($conexionDB['codError'] !== '00000')		
		{ $datosProvincia = $conexionDB;	
		}
		else
	 {
			$tablasBusqueda = 'PROVINCIA';
		 $camposBuscados = '*';					
			$cadenaCondicionesBuscar = " WHERE PROVINCIA.CODPROV = :codProv ";	
			
   $arrBind = array(':codProv' => $codProv);		
			
   $cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
		
		 $datosTabla = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);		
	
		 //echo "<br><br>1 modeloUsuarios.php:provinciaDatos:datosTabla:";print_r($datosTabla);
			
			if ($datosTabla['codError'] !== '00000')
			{ $datosProvincia['codError'] = '80001';
			  $datosProvincia['errorMensaje'] = 'Error: Datos Provincia, provincia no encontrada';
					
					require_once './modelos/modeloErrores.php';
				 insertarError($datosTabla,$conexionDB['conexionLink']);							
			}
			elseif ($datosTabla['numFilas'] == 0)
			{$datosProvincia['codError'] = '80001';
				$datosProvincia['errorMensaje'] = 'Error sistema: Datos Provincia, num. filas=0, provincia no encontrada';
			}
		 else
			{ 
				$datosProvincia = $datosTabla['resultadoFilas'][0]; 
	
				$datosProvincia['codError'] = '00000';
				$datosProvincia['errorMensaje'] = '';
			}
  }		
	}
	if ($datosProvincia['codError'] !=='00000')
	{ 
	 $errorProvincia['arrMensaje']['textoCabecera'] = "Administrar usuarios"; 
	 $errorProvincia['arrMensaje']['textoComentarios'] = "Error del sistema, vuelva a intentarlo pasado un tiempo";
		$errorProvincia['arrMensaje']['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
		$errorProvincia['arrMensaje']['textoBoton'] = 'Salir de la aplicación';
		
		$errorProvincia['codError'] = $datosProvincia['codError'];
		$errorProvincia['errorMensaje'] = $datosProvincia['errorMensaje'];
		
		require_once './modelos/modeloErrores.php';
		insertarError($errorProvincia,$conexionDB['conexionLink']);		
	}	
 //echo "<br><br>2 modeloUsuarios.php:provinciaDatos:datosProvincia:"; print_r($datosProvincia);
	
 return $datosProvincia;
}
//------------------------------ Fin provinciaDatos ----------------------------


/*------------------------------ Inicio insertarUsuario ------------------------
Inserta un usuario en la tabla 'USUARIO', previamente llama a la función 
buscarCodMax() para buscar el maximo valor 'CODUSER' en la tabla 'USUARIO' 

RECIBE: $resulValidarUsuario, que son los dos datos de ese usuario 
(validados previamente: no existe otro usuario idéntico) 
DEVUELVE: array $resulInserUsuario con el resultado de la inserción y código error

LLAMADO: modeloSocios.php:altaSocios(),modeloPresCoord.php:mAltaSocioPorGestor()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php	
	      BBDD/MySQL/conexionMySQL.php:conexionDB()
	      modelos/libs/buscarCodMax.php:buscarCodMax(),
       modeloMySQL.php:insertarUnaFila()

OBSERVACIONES: Valdría para socios y simpatizantes		
2020-03-06: no requiere modificaciones para PDO, ya que la función insertarUnaFila() 
ya incluye la transformación de los datos recibidos de "$arrValoresInser", 
para utilizar "PDOStatement::bindParamValue"					
------------------------------------------------------------------------------*/
function insertarUsuario($resulValidarUsuario)//recibe array:$resulValidar['datosFormUsuario']
{
	$resulInserUsuario['nomScript'] = "modeloUsuarios.php";	
	$resulInserUsuario['nomFuncion'] = "insertarUsuario";
	$resulInserUsuario['codError'] = '00000';
	$resulInserUsuario['errorMensaje'] = '';
	
	$resulValidar = $resulValidarUsuario; 
 //echo "<br><br>1 modeloUsuarios.php:insertarUsuario:resulValidar: ";print_r($resulValidar);//
 
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si			
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
	
	if ($conexionDB['codError'] !== "00000")
	{ $resulInserUsuario = $conexionDB;
	}
	else
	{
		$tablasBusqueda = 'USUARIO';
		$camposBuscados = 'CODUSER';
  
		require_once './modelos/libs/buscarCodMax.php';
	 $resulBuscarCodMax = buscarCodMax($tablasBusqueda,$camposBuscados,$conexionDB['conexionLink']);
		
		if ($resulBuscarCodMax['codError'] !== '00000')
		{ $resulInserUsuario = $resulBuscarCodMax;
		}
		else 
		{
			$arrValoresInser['CODUSER'] = $resulBuscarCodMax['valorCampo'];
			$arrValoresInser['ESTADO'] = 'alta';
			$arrValoresInser['OBSERVACIONES'] = '';

			foreach ($resulValidar as $nomCampo=>$valNomCampo)
			{
				$arrValoresInser[$nomCampo] = $valNomCampo['valorCampo'];
			}				
			
			//echo "<br>3 modeloUsuarios.php:insertarUsuario:arrValoresInser: ";print_r($arrValoresInser);
	  $resulInserUsuario = insertarUnaFila('USUARIO',$arrValoresInser,$conexionDB['conexionLink']);
			
			if ($resulInserUsuario)
			{	
			  $resulInserUsuario['CODUSER'] = $arrValoresInser['CODUSER'];
			}						
		}
	}
 //echo "<br>4 modeloUsuarios.php:insertarUsuario:resulInserUsuario:" ;print_r($resulInserUsuario); echo "<br>";
	
 return $resulInserUsuario;
}
//----------------------------- Fin insertarUsuario ----------------------------

/*----------------------------- Inicio insertarUsuarioRol ----------------------
Inserta una fila en la tabla 'USUARIOTIENEROL', con un rol de un usuario

RECIBE: $resulValidarUsuario, que son los dos datos del rol de ese usuario
DEVUELVE: array $resulInserUsuarioRol con el resultado de la inserción y código error

LLAMADO: modeloSocios.php:altaSocios(),altaSociosConfirmada(),
         modeloPresCoord.php:mAltaSocioPorGestor(),altaSocioPendienteConfirmadaPorGestor(),
									asignarCoordinadorArea()
									cPresidente:asignarPresidenciaRol(),asignarTesoreriaRol()									
									cAdmin.php:asignarAdministracionRol(),asignarMantenimientoRol()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php	
	      BBDD/MySQL/conexionMySQL.php:conexionDB()
       modeloMySQL.php:insertarUnaFila()

OBSERVACIONES: 
2020-03-06: no requiere modificaciones para PDO, ya que la función insertarUnaFila() 
ya incluye la transformación de los datos recibidos de "$arrValoresInser", 
para utilizar "PDOStatement::bindParamValue"					
------------------------------------------------------------------------------*/
function insertarUsuarioRol($resulValidarUsuario)//recibe array :$resulValidar['datosFormUsuario']
{
	$resulInserUsuarioRol['nomScript'] = "modeloUsuarios.php";	
	$resulInserUsuarioRol['nomFuncion'] = "insertarUsuarioRol";
	$resulInserUsuarioRol['codError'] = '00000';
	$resulInserUsuarioRol['errorMensaje'] = '';
	
	$resulValidar = $resulValidarUsuario;
	
 //ACASO HAYA QUE CONTROLAR DUPLICADOS USUARRIO+ROL
 
 //echo "<br><br>1 modeloUsuarios.php:insertarUsuarioRol:resulValidar: ";print_r($resulValidar);//
 
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si			
	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);			

	if ($conexionDB['codError'] !== "00000")
	{ $resulInserUsuarioRol = $conexionDB;
	}
	else
	{	  
		$arrValoresInser['CODUSER'] = $resulValidar['CODUSER']['valorCampo'];		
		$arrValoresInser['CODROL'] = $resulValidar['CODROL']['valorCampo'];
	
  //echo "<br><br>2 modeloUsuarios.php:insertarUsuarioRol:arrValoresInser: "; print_r($arrValoresInser); echo "<br>";//
	 $resulInserUsuarioRol = insertarUnaFila('USUARIOTIENEROL',$arrValoresInser,$conexionDB['conexionLink']);		
	}
  //echo "<br>3 modeloUsuarios.php:insertarUsuarioRol:resulInserUsuarioRol: "; print_r($resulInserUsuarioRol); echo "<br>";//
		
  return $resulInserUsuarioRol;
}
//----------------------------- Fin insertarUsuarioRol -------------------------

/*----------------------------- Inicio insertarMiembro -------------------------
Inserta una fila en la tabla 'MIEMBRO', con los datos personales de una persona, 
Si el pais es ES, previamente busca en la tabla PROVINCIAS el nombre de la 
provincia a partir de los dos primeros dígitos del CP 

RECIBE: $resulValidar, que son los datos de ese usuario
DEVUELVE: array $resulInserMiembro con el resultado de la inserción y código error

LLAMADO: modeloSocios.php:altaSocios(),modeloPresCoord.php:mAltaSocioPorGestor()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php	
	      BBDD/MySQL/conexionMySQL.php:conexionDB()
	      modeloMySQL.php:buscarCadSql(),insertarUnaFila()

OBSERVACIONES: 2020-03-06: 
Utiliza "PDOStatement::bindParamValue,  es necesario para buscarCadSql() 
pero no se requiere modificaciones para PDO en la función insertarUnaFila()
ya incluye la transformación de los datos recibidos de "$arrValoresInser"
------------------------------------------------------------------------------*/
function insertarMiembro($resulValidar)
{
	//echo "<br><br>0-1 modeloUsuarios.php:insertarMiembro: resulValidar: "; print_r($resulValidar);

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";//si	
		
	require_once "BBDD/MySQL/conexionMySQL.php";		
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
	
	if ($conexionDB['codError'] !== "00000")
	{ $resulInserMiembro = $conexionDB;
	}
	else
	{ 		
		foreach ($resulValidar as $nomCampo=>$valNomCampo)
		{
			$arrValoresInser[$nomCampo] = $valNomCampo['valorCampo'];
		}	
		//si no se ha introducido fecha se contiene 0000,00,00, y graba 0000-00-00	
  /*	$arrValoresInser['FECHANAC']=$resulValidar['FECHANAC']['anio']['valorCampo'].'-'.
		                                $resulValidar['FECHANAC']['mes']['valorCampo'].'-'.
					  									                  $resulValidar['FECHANAC']['dia']['valorCampo']; 	
		*/
		//echo "<br><br>1 modeloUsuarios.php:insertarMiembro: arrValoresInser['FECHANAC']: "; print_r($arrValoresInser['FECHANAC']);
		
		if (!isset($resulValidar['CODPAISDOM']['valorCampo']) || $resulValidar['CODPAISDOM']['valorCampo'] !== 'ES')
		{
			//$arrValoresInser['CODPROV']=NULL;
		 unset($arrValoresInser['CODPROV']);
			$nombreProvincia['codError'] ='00000';
		}
		else
		{	
		 $codProv = substr($resulValidar['CP']['valorCampo'],0,2);
				
			$tablasBusqueda = 'PROVINCIA';	
		 $camposBuscados = 'NOMPROVINCIA';				
			$cadCondicionesBuscar = " WHERE CODPROV = :codProv";
			
			$arrBind = array(':codProv' => $codProv);
			
			//$nombreProvincia = buscarEnTablas('PROVINCIA',$cadCondicionesBuscar,$camposBuscados,$conexionDB['conexionLink'],$arrBind);

   $cadSql = " SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";
		 
			//echo '<br><br>2 modeloUsuarios.php:insertarMiembro:cadSql: ';print_r($cadSql);
		
		 $nombreProvincia = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);
			
			//echo "<br><br>3 modeloUsuarios.php:insertarMiembro:nombreProvincia: ";print_r($nombreProvincia);    
			
   if ($nombreProvincia['codError']=='00000')			
  	{
				$arrValoresInser['CODPROV'] = $codProv;
				
			 $arrValoresInser['NOMPROVINCIA'] = $nombreProvincia['resultadoFilas'][0]['NOMPROVINCIA'];	
   }			
		}
		
		if ($nombreProvincia['codError'] !=='00000')
		{ $resulInserMiembro = $nombreProvincia;			
		}
		else
		{
		 //echo "<br><br>4 modeloUsuarios.php:insertarMiembro:arrValoresInser: "; print_r($arrValoresInser);//
   $resulInserMiembro = insertarUnaFila('MIEMBRO',$arrValoresInser,$conexionDB['conexionLink']);					
		}		
	}
 //echo "<br><br>5 modeloUsuarios.php:insertarMiembro:resulInserMiembro: "; print_r($resulInserMiembro); echo "<br>";//
 return $resulInserMiembro;
}
//----------------------------- Fin insertarMiembro ----------------------------


/*--------------------- Inicio insertarMiembroEliminado5Anios ------------------
Inserta una fila en la tabla 'MIEMBROELIMINADO5ANIOS', con unos pocos datos 
personales del socio que se da de baja para guardarlos durante 5 años por motivos 
fiscales, por si hubiese que hacer alguna auditoria. Al cumplirse el quinto año
en el cierre de año se eliminarán los datos personales.

RECIBE: $arrValoresInser, con los datos de ese socio
DEVUELVE: array $resInsEliminado5 con el resultado de la inserción y código error

LLAMADO: modeloSocios.php:eliminarDatosSocios(),modeloPresCoord.php:bajaSocioFallecido()
LLAMA: usuariosConfig/BBDD/MySQL/configMySQL.php	
	      BBDD/MySQL/conexionMySQL.php:conexionDB()
	      modeloMySQL.php:insertarUnaFila()

OBSERVACIONES: 2020-03-06: 
No es necesario utilizar "PDOStatement::bindParamValue para la función insertarUnaFila()
ya incluye la transformación para PDO de los datos recibidos de "$arrValoresInser"
para obtener los "bindParamValue"
------------------------------------------------------------------------------*/
function insertarMiembroEliminado5Anios($arrValoresInser)
{
	//echo "<br><br>1 modeloUsuarios.php:insertarMiembroEliminado5Anios:$arrValoresInser: ";print_r($resulValidar['datosFormMiembro']);
 
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";

	require_once "BBDD/MySQL/conexionMySQL.php";			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	
	if ($conexionDB['codError'] !== "00000")
	{ $resulInserMiembro = $conexionDB;
	}
	else
	{if ((!isset($arrValoresInser['FECHABAJA']) || empty($arrValoresInser['FECHABAJA']) || ($arrValoresInser['FECHABAJA']) == '0000-00-00'))	
		{
			 $arrValoresInser['FECHABAJA'] = date('Y-m-d');
	 	 $fechaMas5Anios = mktime(0, 0, 0, date("m"), date("d"), date("y")+5);		
		}
		else//caso de que tenga fecha de baja
		{
				$fechaBaja = $arrValoresInser['FECHABAJA'];
				$fechaBaja = explode("-",$fechaBaja);
				
				$fechaMas5Anios = mktime(0,0,0,$fechaBaja[1],$fechaBaja[2],$fechaBaja[0]+5);
		}	

		$arrValoresInser['FECHAELIMINAR5'] = date('Y-m-d',$fechaMas5Anios);		

		//echo "<br><br>2 modeloUsuarios.php:insertarMiembroEliminado5Anios:$arrValoresInser: "; print_r($arrValoresInser);//
  $resInsEliminado5 = insertarUnaFila('MIEMBROELIMINADO5ANIOS',$arrValoresInser,$conexionDB['conexionLink']);		
	}
  //echo "<br><br>3 modeloUsuarios.php:insertarMiembroEliminado5Anios:resInsEliminado5: "; print_r($resInsEliminado5);
		
  return $resInsEliminado5;
}
//----------------------------- Fin insertarMiembroEliminado5Anios -------------

/*--------------------- Inicio eliminarUsuarioTieneRol -------------------------
Elimina la/s fila/s en la tabla 'USUARIOTIENEROL', para la condición 
CODUSER = $codUser y CODROL = $codRol, podrá eliminar una fila o varias según 
el número de roles que tenga y valor CODROL = $codRol.

RECIBE: $tabla = 'USUARIOTIENEROL' y $codUser,$codRol, y $conexionLinkDB 
(En el caso de no recibir este parámetro se creará una conexion a BBDD conexión a BBDD )
DEVUELVE: array $resEliminarUsuarioTieneRol con número de filas borradas y código error

LLAMADO: modeloSocios.php:eliminarDatosSocios(),
         modeloPresCoord.php:bajaSocioFallecido(),eliminarAsignCoordinadorArea()
									cPresidente:eliminarAsignacionPresidenciaRol(),eliminarAsignacionTesoreriaRol()
LLAMA: modeloMySQL.php:borrarFilas()

OBSERVACIONES: 
Agustín 2020-03-13: modifico para incluir PHP: PDOStatement::bindParamValue	
Es necesaria conexión a BBDD previa
------------------------------------------------------------------------------*/
function eliminarUsuarioTieneRol($tabla,$codUser,$codRol,$conexionLinkDB=NULL)     
{
		//echo "<br><br>0-1 modeloUsuarios.php:eliminarUsuarioTieneRol:codUser: ";print_r($codUser);
		//echo "<br><br>0-22 modeloUsuarios.php:eliminarUsuarioTieneRol:codRol: ";print_r($codRol);
		
		if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
		{ require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
				require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
				
				$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
				
				//echo "<br><br>1-1 modeloUsuarios.php:eliminarUsuarioTieneRol:conexionDB: ";var_dump($conexionDB);	
		}		
		else
		{ $conexionDB['codError'] = "00000";
				$conexionDB['conexionLink'] = $conexionLinkDB;  			
				//echo "<br><br>1-2 modeloUsuarios.php:eliminarUsuarioTieneRol:conexionDB: ";var_dump($conexionDB);	
		}	
		
		if ($conexionDB['codError'] !== "00000")
		{ $resEliminarUsuarioTieneRol['codError'] = $conexionDB['codError'];
				$resEliminarUsuarioTieneRol['errorMensaje'] = $conexionDB['errorMensaje'];
		}
		else
		{ //echo "<br><br>2-1 modeloUsuarios.php:eliminarUsuarioTieneRol:conexionDB: ";var_dump($conexionDB);
				if ( !isset($codRol) || empty($codRol) || $codRol == '%') //Cuando se quieren eliminar todos los roles del usuario, por baja
				{ 
						$cadenaCondiciones = " CODUSER = :codUser ";	
						$arrBind = array(':codUser' => $codUser);
				}	   
				else //Cuando se quiere eliminar un solo rol del usuario, por ejmplo el rol de coordinador $codRol=6
				{ //echo "<br><br>2-2 modeloUsuarios.php:eliminarUsuarioTieneRol:conexionDB: ";var_dump($conexionDB);
						$cadenaCondiciones = " CODUSER = :codUser AND CODROL = :codRol ";
						$arrBind = array(':codUser' => $codUser, ':codRol' => $codRol); 
				}

				$resEliminarUsuarioTieneRol = borrarFilas($tabla,$cadenaCondiciones,$conexionDB['conexionLink'],$arrBind);
	
		}
		//echo "<br><br>3 modeloUsuarios.php:eliminarUsuarioTieneRol:resEliminarUsuarioTieneRol: ";print_r($resEliminarUsuarioTieneRol);
		
		return $resEliminarUsuarioTieneRol;
} 
//----------------------------- fin eliminarUsuarioTieneRol -------------------- 	

/*--------------------- Inicio actUsuarioEliminar ------------------------------
Al dar de baja a un socio, por él mismo o por gestor, se actualiza la fila 
correspondiente ['CODUSER'] = $codUser; en la tabla 'USUARIO', poniendo los 
siguientes valores:[ESTADO'] ='baja';['USUARIO'] =NULL;['OBSERVACIONES'] = NULL

RECIBE: $tablaAct='USUARIO', $codUser con los datos de ese usuario y $conexionLinkDB
DEVUELVE: $resActUsuarioEliminar con el resultado de la actualizacion y código error

LLAMADO: modeloSocios.php:eliminarDatosSocios(),
         modeloPresCoord.php:bajaSocioFallecido()
LLAMA: modeloMySQL.php:actualizarTabla()

OBSERVACIONES: 2020-03-13: 
No es necesario utilizar aquí "PDOStatement::bindParamValue para la función 
"actualizarTabla()" ya incluye la transformación para PDO de los datos recibidos
 de "$arrayDatosAct" y $"arrayCondiciones" para obtener los "bindParamValue"
------------------------------------------------------------------------------*/
function actUsuarioEliminar($tablaAct,$codUser,$conexionLinkDB)     
{
	//echo "<br><br>1 modeloUsuarios.php:actUsuarioEliminar:codUser: ";print_r($codUser);
 
	$arrayCondiciones['CODUSER']['valorCampo'] = $codUser;
 $arrayCondiciones['CODUSER']['operador'] = '=';
 $arrayCondiciones['CODUSER']['opUnir'] = ' '; 
	
	$arrayDatosAct['ESTADO'] = 'baja';
	$arrayDatosAct['USUARIO'] = NULL;
	//$arrayDatosAct['FECHABAJA'] = date('Y-m-d');//mysql:(CURRENT_DATE());
	$arrayDatosAct['OBSERVACIONES'] = NULL;		
		
	$resActUsuarioEliminar = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatosAct,$conexionLinkDB); 	
	
	//echo "<br><br>2 modeloUsuarios.php:actUsuarioEliminarl:resActUsuarioEliminar: ";print_r($resActUsuarioEliminar);
	
	return $resActUsuarioEliminar;
} 
//----------------------------- Fin actUsuarioEliminar -------------------------

/*----------------------------- Inicio actMiembroEliminar ----------------------  
Al dar de baja a un socio, por él mismo o por gestor, se actualiza la fila 
correspondiente ['CODUSER'] = $codUser; en la tabla 'MIEMBRO', poniendo los 
campos de datos personales a NULL, dejando sin modificar los demás para estadísticas

RECIBE: $tablaAct='MIEMBRO', $codUser con los datos de ese usuario, $conexionLinkDB
DEVUELVE: $resActMiembroEliminar con el resultado de la actualizacion y código error

LLAMADO: modeloSocios.php:eliminarDatosSocios(),
         modeloPresCoord.php:bajaSocioFallecido()
LLAMA: modeloMySQL.php:actualizarTabla()

OBSERVACIONES: 2020-03-26: 
No es necesario utilizar aquí "PDOStatement::bindParamValue para la función 
"actualizarTabla()" ya incluye la transformación para PDO de los datos recibidos
 de "$arrayDatosAct" y $"arrayCondiciones" para obtener los "bindParamValue"
	
2018-10-06: modificación para poner el campo ARCHIVOFIRMAPD = NULL 
(es el nombre del archivo con la firma del socio ARCHIVOFIRMAPD al dar de alta 
por un gestor como protección de datos)
------------------------------------------------------------------------------*/
function actMiembroEliminar($tablaAct,$codUser,$conexionLinkDB)     
{
	//echo '<br><br>0 modeloUsuarios.php:actMiembroEliminar:codUser: ';print_r($codUser);
	
 $arrayCondiciones['CODUSER']['valorCampo'] = $codUser;
 $arrayCondiciones['CODUSER']['operador'] = '=';
 $arrayCondiciones['CODUSER']['opUnir'] = ' ';  

	$arrayDatosAct['NUMDOCUMENTOMIEMBRO'] = NULL;	

	$arrayDatosAct['APE1'] = NULL;
	$arrayDatosAct['APE2'] = NULL;
	$arrayDatosAct['NOM'] = NULL;
	$arrayDatosAct['TELFIJOCASA'] = NULL;
	$arrayDatosAct['TELFIJOTRABAJO'] = NULL;
	$arrayDatosAct['TELMOVIL'] = NULL;
	$arrayDatosAct['EMAIL'] = NULL;		
 $arrayDatosAct['EMAILERROR'] = 'BAJA';		

	$arrayDatosAct['DIRECCION'] = NULL;
	//$arrayDatosAct['CP']=NULL;
	$arrayDatosAct['ARCHIVOFIRMAPD'] = NULL;	
	
	$arrayDatosAct['COMENTARIOSOCIO'] = NULL;
	$arrayDatosAct['OBSERVACIONES'] = NULL;		
	
	$resActMiembroEliminar = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatosAct,$conexionLinkDB); 																					
	
	//echo '<br><br>2 modeloUsuarios.php:resActMiembroEliminar: ';print_r($resActMiembroEliminar);
	
	return $resActMiembroEliminar;
} 
//----------------------------- Fin actMiembroEliminar -------------------------

/*----------------------------- Inicio actualizarPass() ------------------------  
Cambiar el campo PASSUSUARIO del usuario en la tabla USUARIO

RECIBE: $tablaAct='USUARIO', $codUser=$_SESSION['vs_CODUSER'] y  
$arrayDatosAct con los datos de la nueva contraseña ya validados
DEVUELVE: $resActualizarPass con el resultado de la actualizacion y código error

LLAMADO: controladorSocios.php:cambiarPassSocio(), y aunque ya no se usa desde
controladorLogin.php:cambiarPassUser()       
LLAMA: modeloMySQL.php:actualizarTabla(), insertarError()
usuariosConfig/BBDD/MySQL/configMySQL.php,BBDD/MySQL/conexionMySQL.php:conexionDB()

OBSERVACIONES: 2020-03-26: 
No es necesario utilizar aquí "PDOStatement::bindParamValue para la función 
"actualizarTabla()" ya incluye la transformación para PDO7 de los datos recibidos
 de "$arrayDatosAct" y $"arrayCondiciones" para obtener los "bindParamValue"
Está función, ahora podría ser sustitida por modelosUsuarios.php: 
actualizUsuario($tablaAct,$codUser,$arrayDatosAct,$conexionDB), pero al inicio
se obligaba a que l contraseña fuese distinta de la anterior. La dejo por si 
más adelante se quisiera activar de nuevo.
------------------------------------------------------------------------------*/
function actualizarPass($tablaAct,$codUser,$arrayDatosAct)     
{
	//echo '<br><br>0-1 modeloUsuarios.php:actualizarPass:arrayDatosAct: ';print_r($arrayDatosAct);
	
	require_once './modelos/modeloErrores.php'; 

	$resActualizarPass['nomScript'] = 'modeloUsuarios.php';
 $resActualizarPass['nomFuncion'] = 'actualizarPass';
 $resActualizarPass['codError'] = '00000';
 $resActualizarPass['errorMensaje'] = '';

	$arrMensaje['textoCabecera'] = 'Cambiar contraseña';	
	$arrMensaje['textoComentarios']	= '';
	//$arrMensaje['textoBoton'] = 'Salir';
	//$arrMensaje['enlaceBoton'] ='./index.php?controlador=controladorLogin&amp;accion=logOut';

	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "BBDD/MySQL/conexionMySQL.php";
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 

	if ($conexionDB['codError'] !== "00000")
	{ $resActualizarPass = $conexionDB;
   $arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos. Pruebe de nuevo pasado un rato. "; 
	}
	else	//$conexionDB['codError']=='00000'
	{		
		if (!isset($codUser) || empty($codUser) )
		{ $resActualizarPass['codError'] = '70601'; //no encontrado
				$resActualizarPass['errorMensaje'] = "Faltan variables-parámetros -codUser- necesarios para SQL en modeloUsuarios.php:actualizarPass()";
				
				$resInsertarErrores = insertarError($resActualizarPass,$conexionDB['conexionLink']);
				if ($resInsertarErrores['codError'] !== '00000')
				{$resActualizarPass['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
					$arrMensaje['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
				}					
		}
		else //if (isset($codUser) )
		{			
   $arrayCondiciones['CODUSER']['valorCampo'] = $codUser;
			$arrayCondiciones['CODUSER']['operador'] = '=';
			$arrayCondiciones['CODUSER']['opUnir'] = ' ';	
			
			$passNoEncriptada = $arrayDatosAct['datosFormUsuario']['PASSUSUARIO']['valorCampo'];
			$arrayDatos['PASSUSUARIO'] = sha1($passNoEncriptada);	//encripta contraseña	hab ría que sustituir por otra mas segura tipo password_hash()
			
			//echo '<br><br>2 modeloUsuarios.php:actualizarPass:arrayDatosAct: ';print_r($arrayDatosAct);

		 $resActualizarPass = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionDB['conexionLink']); 																					
   
			//echo '<br><br>3 modeloUsuarios.php:actualizarPass:resActualizarPass: ';print_r($resActualizarPass);echo '<br><br>';
		
			if ($resActualizarPass['codError'] !== '00000')
			{ $arrMensaje['textoComentarios'] = "Error del sistema al cambiar contraseña, vuelva a intentarlo pasado un tiempo ";

					$resInsertarErrores = insertarError($resActualizarPass,$conexionDB['conexionLink']);
					if ($resInsertarErrores['codError'] !== '00000')
					{$resActualizarPass['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
						$arrMensaje['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
					}	
			}
			else
			{ $arrMensaje['textoComentarios'] .= "Se ha grabado la nueva contraseña.<br /><br />Deberás utilizarla 
					la próxima vez que quieras entrar en la aplicación de Gestión de Soci@s de Europa Laica";
			}
			/*En MySQl al hacer UPDATE, si el contenido del campo antiguo es igual al que 
					se va a sustituir, aunque no da error, no contabiliza como fila actualizada,
					y devuelve ['numFilas'] = 0), si se quiere se puede tratar como un 
					error lógico para pedir que introduzca un nueva contraseña distinta de la antigua*/
			/* elseif ($resActualizarPass['numFilas'] !== 1)//En MySQl si el valor del campo es identico, al hacer UPDATE, aunque no da error
			{$resActualizarPass['codError'] ='80001';
				$resActualizarPass['errorMensaje'] = "No ha sido actualizada la contraseña";//Por ser igual a la antigua			
			}
			*/
		}//else if (isset($codUser) )	
	}//else $conexionDB['codError']=='00000'
	
	//echo '<br><br>4-1 modeloUsuarios.php:actualizarPass:resActualizarPass: ';print_r($resActualizarPass);
	
	$resActualizarPass['arrMensaje'] = $arrMensaje;	
	
	//echo '<br><br>4-2 modeloUsuarios.php:actualizarPass:resActualizarPass: ';print_r($resActualizarPass);
	
	return $resActualizarPass;
} 
/*----------------------------- Fin actualizarPass ---------------------------*/

/*----------------------------- Inicio actualizarUsuario -----------------------
Actualiza en la tabla "$tablaAct" USUARIO los datos de un usuario = $codUser, 
a partir de $arrayDatosAct 

RECIBE: $arrayDatosAct , que son los datos a actualizar
DEVUELVE: array $resActualizarUsuario con el resultado del UPDATE y código error

LLAMADO: controladorSocios.php:confirmarEmailPassAltaSocioPorGestor(),
modeloSocios.php:actualizarDatosSocio(),altaSociosConfirmada(),anularSocioPendienteConfirmar(),,
modeloPresCoord.php:altaSocioPendienteConfirmadaPorGestor(),anularSocioPendienteConfirmarPres(),

LLAMA: modeloMySQL.php:actualizarTabla()

OBSERVACIONES: 
2020-03-06: no requiere modificaciones para PDO, ya que la función insertarUnaFila() 
ya incluye la transformación de los datos recibidos de "$arrValoresInser", 
para utilizar "PDOStatement::bindParamValue"	
------------------------------------------------------------------------------*/
function actualizUsuario($tablaAct,$codUser,$arrayDatosAct,$conexionLinkDB)     
{
	//echo '<br><br>0-1 modeloUsuarios.php:actualizUsuario:codUser: ';print_r($codUser);
 //echo '<br><br>0-2 modeloUsuarios.php:actualizUsuario:arrayDatosAct: ';print_r($arrayDatosAct);
	//echo '<br><br>0-3 modeloUsuarios.php:actualizUsuario:conexionLinkDB: ';var_dump($conexionLinkDB);
	
 $arrayCondiciones['CODUSER']['valorCampo'] = $codUser;
 $arrayCondiciones['CODUSER']['operador'] = '=';
 $arrayCondiciones['CODUSER']['opUnir'] = ' ';
	
	foreach ($arrayDatosAct as $indice => $contenido)                         
 {      
    $arrayDatos[$indice] = $contenido['valorCampo']; 
 }	
	/* Podría ser algo como lo siguiente:		
				$arrayDatos['USUARIO']	= $arrayDatosAct['USUARIO']['valorCampo'];
				$arrayDatos['ESTADO']='alta';// creo que no es necesario
				$arrayDatos['OBSERVACIONES']['valorCampo'] = 'Alta inciada por usuario y finalizada por usuario';	
	*/

 //echo '<br><br>1 modeloUsuarios.php:actualizUsuario:arrayDatos: ';print_r($arrayDatos);
			
	$resActualizarUsuario = actualizarTabla_ParamPosicion($tablaAct,$arrayCondiciones,$arrayDatos,$conexionLinkDB);//no es necesario //unset($arrayDatosAct['CODUSER']);
	////puede que ['numFilas']=0 y no es error	
	
	//echo '<br><br>2 modeloUsuarios.php:actualizUsuario:resActualizUsuario: ';print_r($resActualizarUsuario);
	
	return $resActualizarUsuario;
} 
/*----------------------------- Fin actualizUsuario --------------------------*/

/*--------------------------  Inicio actualizarMiembro-PDO ---------------------
Se actualiza la fila correspondiente ['CODUSER'] = $codUser en la tabla 'MIEMBRO'
con los vaores contenidos en "$arrayDatosAct".
Primero bisca el nombre de Provincia en buscarEnTablas('PROVINCIA'...) a partir
de los dos primeros dígitos de CP 

RECIBE: $tablaAct='MIEMBRO', $campoCondiciones,$arrayDatosAct,$conexionLinkDB
DEVUELVE: $arrActualizarMiembro con el resultado de la actualizacion y código error

LLAMADO: modeloSocios.php:eliminarDatosSocios(),
         modeloPresCoord.php:bajaSocioFallecido(),eliminarAsignCoordinadorArea()
LLAMA: modeloMySQL.php:actualizarTabla()

OBSERVACIONES: 2020-03-13: 
Utilizar "PDOStatement::bindParamValue, es necesario para buscarEnTablas() 
pero no se requiere modificaciones para PDO en la función "actualizarTabla()"
ya incluye la transformación interna de los datos recibidos "$campoCondiciones" 
y "$arrayDatosAct" para obtener "bindParamValue"
2020-03-13: añado if (!isset($arrayDatosAct) || ...
------------------------------------------------------------------------------*/
function actualizMiembro($tablaAct,$campoCondiciones,$arrayDatosAct,$conexionLinkDB)     
{ 
  //echo '<br><br>0-1 modeloUsuarios.php:actualizMiembro:campoCondiciones: ';print_r($campoCondiciones); 	
  //echo '<br><br>0-2 modeloUsuarios.php:actualizMiembro:arrayDatosAct: ';print_r($arrayDatosAct); 	
  //echo '<br><br>0-3 modeloUsuarios.php:actualizMiembro:conexionLinkDB: ';var_dump($conexionLinkDB);	
		
 $arrActualizarMiembro['nomScript'] = 'modeloUsuarios.php';
 $arrActualizarMiembro['nomFuncion'] = 'actualizMiembro';		
 $arrActualizarMiembro['codError'] = '00000';
 $arrActualizarMiembro['errorMensaje'] = '';
	
 if (!isset($arrayDatosAct) || empty($arrayDatosAct) || $arrayDatosAct == NULL)//Si no hay valores para poner en SET, impedimos ejecutar UPDATE: podría poner valores a NULL
	{ $arrActualizarMiembro['codError'] = '70601';
   $arrActualizarMiembro['errorMensaje'] = 'Faltan variables-parámetros necesarios para actaulizar tablas '.$tablaAct;
	}
	else //if !!(!isset($arrayDatosAct) || empty($arrayDatosAct) || $arrayDatosAct == NULL) 
	{	
			if (!isset($conexionLinkDB) || empty($conexionLinkDB) || $conexionLinkDB == NULL) 
			{ require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
					require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
					
					$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
					
					//echo "<br><br>1-1 modeloUsuarios.php:actualizMiembro:conexionDB: ";var_dump($conexionDB);				
			}		
			else
			{ $conexionDB['codError'] = "00000";
					$conexionDB['conexionLink'] = $conexionLinkDB;  			
					//echo "<br><br>1-2 modeloUsuarios.php:actualizMiembro:conexionDB: ";var_dump($conexionDB);	 
			}	
			
			if ($conexionDB['codError'] !== "00000")
			{ $arrActualizarMiembro['codError'] = $conexionDB['codError'];
					$arrActualizarMiembro['errorMensaje'] = $conexionDB['errorMensaje'];
			}
			else //$conexionDB['codError'] == "00000"
			{					
					$arrayCondiciones['CODUSER']['valorCampo'] = $campoCondiciones;
					$arrayCondiciones['CODUSER']['operador'] = '=';
					$arrayCondiciones['CODUSER']['opUnir'] = ' ';
					//echo '<br><br>2-1 modeloUsuarios.php:actualizMiembro:arrayCondiciones: ';print_r($arrayCondiciones);
					
					foreach ($arrayDatosAct as $indice => $contenido)                         
					{ 
							if ($indice !== 'FECHANAC')
							{$arrayDatos[$indice] = $contenido['valorCampo']; 
						 }
							else
							{$arrayDatos[$indice] = $contenido;							
							}							
					}
					
					//echo '<br><br>2-2 modeloUsuarios.php:actualizMiembro:arrayDatos: ';print_r($arrayDatos); 	
					
					//si no se ha introducido fecha contiene 0000,00,00, y graba 0000-00-00 	
					if ((isset($arrayDatosAct['FECHANAC']['anio']['valorCampo']) && $arrayDatos['FECHANAC']['anio']['valorCampo'] !== '') && 
								(isset($arrayDatosAct['FECHANAC']['mes']['valorCampo']) && $arrayDatos['FECHANAC']['mes']['valorCampo'] !== '') &&
								(isset($arrayDatosAct['FECHANAC']['dia']['valorCampo']) && $arrayDatos['FECHANAC']['dia']['valorCampo'] !== '')
							)			
					{ $arrayDatos['FECHANAC'] = $arrayDatosAct['FECHANAC']['anio']['valorCampo']."-".
																																	$arrayDatosAct['FECHANAC']['mes']['valorCampo']."-".
																																	$arrayDatosAct['FECHANAC']['dia']['valorCampo'];		
					}		

					if (!isset($arrayDatosAct['CODPAISDOM']['valorCampo']) || $arrayDatosAct['CODPAISDOM']['valorCampo'] !== 'ES')
					{
							/*unset($arrayDatos['CODPROV']);*/
							$nombreProvincia['codError'] = '00000';
							$arrayDatos['CODPROV'] = NULL;				
							$arrayDatos['NOMPROVINCIA'] = NULL;			
					}		
					else
					{	$tablasBusqueda = 'PROVINCIA';								
							$camposBuscados = 'NOMPROVINCIA';
							$cadenaCondicionesBuscar = " WHERE CODPROV = :codProv";
							
							$codProv = substr($arrayDatosAct['CP']['valorCampo'],0,2);
							$arrBind = array(':codProv' => $codProv);					
							
							//echo '<br><br>2-3 modeloUsuarios.php:actualizMiembro:arrBind: ';print_r($arrBind);		

							$cadSql = "SELECT $camposBuscados FROM $tablasBusqueda $cadenaCondicionesBuscar";
				
							$nombreProvincia = buscarCadSql($cadSql,$conexionDB['conexionLink'],$arrBind);							
							
							//echo "<br><br>3 modeloUsuarios.php:actualizMiembro:nombreProvincia ";print_r($nombreProvincia);    
							
							if ($nombreProvincia['codError'] == '00000')			
							{$arrayDatos['CODPROV'] = $codProv;				
								$arrayDatos['NOMPROVINCIA'] = $nombreProvincia['resultadoFilas'][0]['NOMPROVINCIA'];	
							}			
					}
					if ($nombreProvincia['codError'] !=='00000')
					{ $arrActualizarMiembro = $nombreProvincia;			
					}
					else
					{	//echo "<br><br>4 modeloUsuarios.php:actualizMiembro:arrayDatos: "; print_r($arrayDatos);
							
							$arrActualizarMiembro = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionDB['conexionLink']);//puede que ['numFilas']=0 y no es error	
					}		
			}//else $conexionDB['codError'] == "00000"	
	}//else !!(!isset($arrayDatosAct) || empty($arrayDatosAct) || $arrayDatosAct == NULL) 
	//echo '<br><br>5 modeloUsuarios.php:actualizMiembro:arrActualizMiembro: ';print_r($arrActualizarMiembro);
	
	return $arrActualizarMiembro;
} 
//----------------------------- Fin actualizarMiembro-PDO ----------------------

//----------------------------- Inicio actualizarUsuarioRol --------------------
//actualizarUsuarioRol('USUARIOTIENEROL',$_SESSION['vs_NOMUSUARIO'],"2",1");//rol socio codigo '1
function actualizarUsuarioRol($tablaAct,$condUser,$condCodRol,$nuevoCodRol)//
{ 
 $resActUsuarioRol['nomScript'] = 'modeloUsuarios.php';
 $resActUsuarioRol['nomFuncion'] = 'actualizarUsuarioRol';
	$arrMensaje['textoCabecera'] = 'Actualizar rol de usuario';	
 $resActUsuarioRol['codError'] = '00000';
 $resActUsuarioRol['errorMensaje'] = '';	
	//$arrMensaje['textoBoton'] = 'Salir';
	//$arrMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';
	$arrMensaje['textoComentarios'] .= "Se ha actulizado el rol del usuario"; 		
	
	
 $arrayCondiciones['CODUSER']['valorCampo'] = $condUser;
 $arrayCondiciones['CODUSER']['operador'] = '=';
 $arrayCondiciones['CODUSER']['opUnir'] = 'AND';
	
	$arrayCondiciones['CODROL']['valorCampo'] = $condCodRol;
 $arrayCondiciones['CODROL']['operador'] = '=';
 $arrayCondiciones['CODROL']['opUnir'] = ' ';
	
 $arrayDatos['CODROL'] = $nuevoCodRol;
		
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";	
	require_once "./modelos/BBDD/MySQL/conexionMySQL.php";
			
	$conexionDB = conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB); 
	
	if ($conexionDB['codError'] !== '00000')	
	{ $resActUsuarioRol = $conexionDB;
   $arrMensaje['textoComentarios'] .= "Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionUsuariosDB['codError']=='00000'	
	{	//echo '<br><br>1 modeloUsuarios.php:actualizarUsuarioRol:resActUsuarioRol:';print_r( $arrayDatos);
		
		$resActUsuarioRol = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatos,$conexionDB['conexionLink']); 																					
		//echo '<br><br>2 modeloUsuarios.php:actualizarUsuarioRol:resActUsuarioRol:';print_r($resActUsuarioRol);
		
		if ($resActUsuarioRol['codError'] !== '00000')
		{ $arrMensaje['textoComentarios'] = "Error del sistema al actualizar rol usuario, vuelva a intentarlo pasado un tiempo ";
		
			require_once './modelos/modeloErrores.php'; 
			$resInsertarErrores = insertarError($resActUsuarioRol,$conexionDB['conexionLink']); 
			
			if ($resInsertarErrores['codError'] !== '00000')
	  {$resActUsuarioRol['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'] .= "Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}
		}//if $resActUsuarioRol['codError']!=='00000'
	}	//$conexionUsuariosDB['codError']=='00000'	
	
	$resActUsuarioRol['arrMensaje'] = $arrMensaje;	
	//echo "<br><br>3 modeloUsuarios.php:actualizarUsuarioRol:resActUsuarioRol: ";print_r($resActUsuarioRol);
	
	return $resActUsuarioRol;
} 
//----------------------------- Fin actualizarUsuarioRol -----------------------

?>