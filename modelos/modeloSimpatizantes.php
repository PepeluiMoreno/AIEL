<?php
/*-----------------------------------------------------------------------------
FICHERO: modeloSimpatizantes.php
VERSION: PHP 5.2.3
DESCRIPCION: Este "Modelo" busca, inserta, y actualiza en (BBDD), pedida por
						 controladorSimpatizantes
LLAMADO: desde "controladorSimpatizantes						 
OBSERVACIONES:Necesita modeloUsuarios.php, ya que hay funciones compartidas 
              modeloSocios											 
------------------------------------------------------------------------------*/
require_once "BBDD/MySQL/conexionMySQL.php";
require_once "BBDD/MySQL/modeloMySQL.php";
require_once './modelos/modeloUsuarios.php';//se activará cuando se leiminen las que hay aquí
//-----------------------------------------------------------------------------

//---------------------------- Inicio buscarDatosSocio ---------------------
//Llamada desde:controladorSimpatizantes.php:
//              actualizarSimpatizante(),mostrarDatosSimpatizante()
//              ,eliminarSimpatizante() y simpatizanteAsocio()
//Vale solo para simpatizantes 
//--------------------------------------------------------------------------
function buscarDatosSimp($codUsuario)
{$resBuscarDatosSimp['codError']='00000';
 $resBuscarDatosSimp['errorMensaje']='';
 $resBuscarDatosSimp['nomScript']='modeloSimpatizantes.php';
 $resBuscarDatosSimp['nomFuncion']='buscarDatosSimp';//se van a repetir
	$arrMensaje['textoCabecera']='Buscar datos del simpatizante';	
	//$arrMensaje['textoBoton']='Salir de la aplicación';
	$arrMensaje['textoComentarios']="Error del sistema al buscar los datos del simpatizante,
	 vuelva a intentarlo pasado un tiempo ";	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
	
	//require "BBDD/MySQL/configMySQL.php";	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	if ($conexionUsuariosDB['codError']!=='00000')	
	{ $resBuscarDatosSimp=$conexionUsuariosDB;
   $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionUsuariosDB['codError']=='00000'
	{$tablasBusqueda='USUARIO';
		$camposBuscados='*';
		$cadCondicionesBuscar=" WHERE USUARIO.CODUSER=\"".$codUsuario."\"";
    
		$resDatosUsuario = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
			                     			  					  $camposBuscados,$conexionUsuariosDB['conexionLink']);  
		if ($resDatosUsuario['codError']!=='00000')
		{ $resBuscarDatosSimp['codError']=$resDatosUsuario['codError'];
    $resBuscarDatosSimp['errorMensaje']=$resDatosUsuario['errorMensaje'];
		  $resBuscarDatosSimp['nomScript']=$resDatosUsuario['nomScript'];//se van a repetir
		  $resBuscarDatosSimp['nomFuncion']=$resDatosUsuario['nomFuncion'];//se van a repetir
		}	
		elseif ($resDatosUsuario['numFilas']==0)
		{$resBuscarDatosSimp['codError']='80001'; //no encontrado
			$resBuscarDatosSimp['errorMensaje']="No existe ese simpatizante";
		}	
	 else //$resDatosUsuario['codError']=='00000')
		{ 
			foreach ($resDatosUsuario['resultadoFilas'][0] as $indice => $contenido)                         
   {//$$resBuscarDatosSimp['valoresCampos']['datosFormUsuario'][$indice]['valorCampo'] = $contenido;		    
     $resBuscarDatosSimp['valoresCampos']['datosFormUsuario'][$indice]['valorCampo'] = 
				 $resDatosUsuario['resultadoFilas'][0][$indice]; 	        
		 } 			
			//$$resBuscarDatosSimp['valoresCampos']['USUARIO']=$resDatosUsuario['resultadoFilas'];
	
	  $tablasBusqueda='PAIS, MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV';
			$camposBuscados ='MIEMBRO.*, PAIS.NOMBREPAIS, PROVINCIA.NOMPROVINCIA';
			$cadCondicionesBuscar =" WHERE MIEMBRO.CODPAISDOM=PAIS.CODPAIS1
			                         AND MIEMBRO.CODUSER=\"".$codUsuario."\"";			
		 /*
			$tablasBusqueda ='MIEMBRO,PAIS';
			$camposBuscados ='*';
			$cadCondicionesBuscar =" WHERE MIEMBRO.CODPAISDOM=PAIS.CODPAIS1
			                         AND MIEMBRO.CODUSER=\"".$codUsuario."\"";																													
			*/																									
			$resDatosMiembro = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
				                     								    $camposBuscados,$conexionUsuariosDB['conexionLink']);
			if ($resDatosMiembro['codError']!=='00000')
			{ $resBuscarDatosSimp['codError']=$resDatosMiembro['codError'];
	    $resBuscarDatosSimp['errorMensaje']=$resDatosMiembro['errorMensaje'];
			  $resBuscarDatosSimp['nomScript']=$resDatosMiembro['nomScript'];//se van a repetir
			  $resBuscarDatosSimp['nomFuncion']=$resDatosMiembro['nomFuncion'];//se van a repetir
			}	
			elseif ($resDatosMiembro['numFilas']==0)
			{$resBuscarDatosSimp['codError']='80001'; //no encontrado
				$resBuscarDatosSimp['errorMensaje']="No existe ese socio";
			}			
			else	//$resDatosMiembro['codError']=='00000'		
			{ 
				foreach ($resDatosMiembro['resultadoFilas'][0] as $indice => $contenido)                         
			 {//$resBuscarDatosSimp['valoresCampos']['datosFormMiembro'][$indice]['valorCampo'] = $contenido;		    
	    $resBuscarDatosSimp['valoresCampos']['datosFormMiembro'][$indice]['valorCampo'] = 
					                    $resDatosMiembro['resultadoFilas'][0][$indice]; 	        
			 }			
			 //$resBuscarDatosSimp['valoresCampos']['MIEMBRO']=$resDatosMiembro['resultadoFilas'];
				
				$fechaNacAux=$resDatosMiembro['resultadoFilas'][0]['FECHANAC'];
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['FECHANAC']['dia']['valorCampo']=substr($fechaNacAux,8,2);
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['FECHANAC']['mes']['valorCampo']=substr($fechaNacAux,5,2);
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['FECHANAC']['anio']['valorCampo']=substr($fechaNacAux,0,4); 

				$resBuscarDatosSimp['valoresCampos']['datosFormDomicilio']['CODPAISDOM']['valorCampo']=
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['CODPAISDOM']['valorCampo'];	
				$resBuscarDatosSimp['valoresCampos']['datosFormDomicilio']['NOMBREPAIS']['valorCampo']=
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['NOMBREPAIS']['valorCampo'];	
				$resBuscarDatosSimp['valoresCampos']['datosFormDomicilio']['DIRECCION']['valorCampo']=
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['DIRECCION']['valorCampo'];	

				$resBuscarDatosSimp['valoresCampos']['datosFormDomicilio']['LOCALIDAD']['valorCampo']=
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['LOCALIDAD']['valorCampo'];					
				$resBuscarDatosSimp['valoresCampos']['datosFormDomicilio']['NOMPROVINCIA']['valorCampo']=
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['NOMPROVINCIA']['valorCampo'];
				$resBuscarDatosSimp['valoresCampos']['datosFormDomicilio']['CP']['valorCampo']=
				$resBuscarDatosSimp['valoresCampos']['datosFormMiembro']['CP']['valorCampo'];					
				
			 $tablasBusqueda='SIMPATIZANTE';
			 $camposBuscados='*';
				$cadCondicionesBuscar=" WHERE SIMPATIZANTE.CODUSER=\"".$codUsuario."\"";
		    
				$resDatosSimp = buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
					                          				$camposBuscados,$conexionUsuariosDB['conexionLink']);	
				if ($resDatosSimp['codError']!=='00000')
				{ $resBuscarDatosSimp['codError']=$resDatosSimp['codError'];
		    $resBuscarDatosSimp['errorMensaje']=$resDatosSimp['errorMensaje'];
				  $resBuscarDatosSimp['nomScript']=$resDatosSimp['nomScript'];//se van a repetir
				  $resBuscarDatosSimp['nomFuncion']=$resDatosSimp['nomFuncion'];//se van a repetir
				}	
				elseif ($resDatosSimp['numFilas']==0)
				{$resBuscarDatosSimp['codError']='80001'; //no encontrado
					$resBuscarDatosSimp['errorMensaje']="No existe ese simpatizante";
				}
				else
				{	//$resBuscarDatosSimp	= $resDatosSimp[];//FECHAALTA 	FECHABAJA
				  //echo "<br><br>1-6-1modeloSimpatizantes:buscarDatosSimp:";print_r($resBuscarDatosSimp);
				}
			}	//$resDatosMiembro['codError']=='00000'		
		} //$resDatosUsuario['codError']=='00000')
  //echo "<br><br>1-6-2modeloSimpatizantes:buscarDatosSimp:";print_r($resBuscarDatosSimp);
		
		if ($resBuscarDatosSimp['codError']!=='00000')
		{ $arrMensaje['textoComentarios']="Error del sistema al buscar datos del simpatizante, vuelva a intentarlo pasado un tiempo ";
		
			require_once './modelos/modeloErrores.php'; //si es un error de la tabla error, insertar errores 
			$resInsertarErrores=insertarError($resBuscarDatosSimp);			
			if ($resInsertarErrores['codError']!=='00000')
	  { $resBuscarDatosSimp['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'].="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}
		}//if $resBuscarDatosSimp['codError']!=='00000'
	}	//$conexionUsuariosDB['codError']=='00000'
	//echo "<br><br>1-8 modeloSimpatizantes:buscarDatosSimp:resBuscarDatosSimp:";print_r($resBuscarDatosSimp);
	$resBuscarDatosSimp['arrMensaje']=$arrMensaje;	
 return 	$resBuscarDatosSimp; 	
}
//------------------------------ Fin buscarDatosSimpActualizar --------------------

//---------------------------- Inicio cadBuscarDatosSimpsEmail ------------------
//Descripción: Forma la cadena select sql para busca los datos de un simps a partir
//             del email
//Llamada desde: cGestorSimps.php:mostrarSimps()
//--------------------------------------------------------------------------
function cadBuscarDatosSimpsEmail($codEmail)  
{									
 $tablasBusqueda="USUARIO,SIMPATIZANTE,PAIS,MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV";

 $camposBuscados="USUARIO.CODUSER,USUARIO.USUARIO,
                CONCAT(IFNULL(APE1,''),' ',IFNULL(APE2,''),', ',NOM) as apeNom,
                MIEMBRO.DIRECCION,MIEMBRO.LOCALIDAD,MIEMBRO.CODPROV,MIEMBRO.CODPAISDOM,TIPOMIEMBRO,SEXO,
							         MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,INFORMACIONEMAIL,INFORMACIONCARTAS,
																PAIS.NOMBREPAIS,IFNULL(PROVINCIA.NOMPROVINCIA,'-') as NOMPROVINCIA,COLABORA,
																SIMPATIZANTE.FECHAALTA,SIMPATIZANTE.FECHABAJA";
																
 $cadCondicionesBuscar=" WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
                        AND USUARIO.CODUSER=SIMPATIZANTE.CODUSER 
																								AND MIEMBRO.CODPAISDOM=PAIS.CODPAIS1 
																								AND MIEMBRO.EMAIL = '$codEmail'
                        AND (SIMPATIZANTE.FECHABAJA IS NULL OR SIMPATIZANTE.FECHABAJA ='0000-00-00')																											
										              AND USUARIO.ESTADO='alta'";
				
	$cadBuscarDatosSimps="SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
																																														
	//echo '<br>modeloSimpatizantes:cadBuscarDatosSimps:cadBuscarDatosSimps ';print_r($cadBuscarDatosSimps);
	return $cadBuscarDatosSimps;
}
//------------------------------ Fin cadBuscarDatosSimpsEmail ------------------

//---------------------------- Inicio cadBuscarDatosSimps ------------------
//Descripción: Forma la cadena select sql para busca los datos de todos los 
//             simps de un determinado país y provincia
//Llamada desde: cGestorSimps.php:mostrarSimps()
//--------------------------------------------------------------------------
function cadBuscarDatosSimps($codPaisDom, $codProv)  
{
		if ( $codPaisDom == 'ES')
		{ $tablasBusqueda = "USUARIO,SIMPATIZANTE,PAIS,MIEMBRO,PROVINCIA";
		
			 $camposBuscados = "DISTINCT USUARIO.ESTADO,USUARIO.CODUSER,USUARIO.USUARIO,
		                 CONCAT(IFNULL(APE1,''),' ',IFNULL(APE2,''),', ',NOM) as apeNom,
		                 MIEMBRO.DIRECCION,MIEMBRO.LOCALIDAD,MIEMBRO.CODPROV,MIEMBRO.CODPAISDOM,TIPOMIEMBRO,SEXO,
										         MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,INFORMACIONEMAIL,INFORMACIONCARTAS,
																			PAIS.NOMBREPAIS,PROVINCIA.NOMPROVINCIA,COLABORA, SIMPATIZANTE.FECHAALTA,SIMPATIZANTE.FECHABAJA";
																			
		  $cadCondicionesBuscar = " WHERE USUARIO.CODUSER = MIEMBRO.CODUSER
                           AND USUARIO.CODUSER = SIMPATIZANTE.CODUSER 
																											AND MIEMBRO.CODPAISDOM = PAIS.CODPAIS1 
																											AND MIEMBRO.CODPROV=PROVINCIA.CODPROV
                           AND (SIMPATIZANTE.FECHABAJA IS NULL OR SIMPATIZANTE.FECHABAJA ='0000-00-00')																											
																											AND MIEMBRO.CODPAISDOM ='ES'
																											AND MIEMBRO.CODPROV LIKE '$codProv'
													              AND USUARIO.ESTADO ='alta' ORDER BY PROVINCIA.NOMPROVINCIA, MIEMBRO.EMAIL";
		}
		elseif ($codPaisDom =='%')
		{ $tablasBusqueda="USUARIO,SIMPATIZANTE,PAIS,MIEMBRO LEFT JOIN PROVINCIA ON MIEMBRO.CODPROV=PROVINCIA.CODPROV";
		
			 $camposBuscados = "DISTINCT USUARIO.ESTADO,USUARIO.CODUSER,USUARIO.USUARIO,
		                 CONCAT(IFNULL(APE1,''),' ',IFNULL(APE2,''),', ',NOM) as apeNom,
		                 MIEMBRO.DIRECCION,MIEMBRO.LOCALIDAD,MIEMBRO.CODPROV,MIEMBRO.CODPAISDOM,TIPOMIEMBRO,SEXO,
										         MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,INFORMACIONEMAIL,INFORMACIONCARTAS,
																			PAIS.NOMBREPAIS,IFNULL(PROVINCIA.NOMPROVINCIA,'-') as NOMPROVINCIA,COLABORA,
																			SIMPATIZANTE.FECHAALTA,SIMPATIZANTE.FECHABAJA";
																			
		  $cadCondicionesBuscar = " WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
                           AND USUARIO.CODUSER=SIMPATIZANTE.CODUSER 
																											AND MIEMBRO.CODPAISDOM=PAIS.CODPAIS1 
                           AND (SIMPATIZANTE.FECHABAJA IS NULL OR SIMPATIZANTE.FECHABAJA ='0000-00-00')																											
																											AND MIEMBRO.CODPAISDOM LIKE '%'
													              AND USUARIO.ESTADO='alta' ORDER BY MIEMBRO.CODPAISDOM, MIEMBRO.EMAIL";
		}
		elseif ($codPaisDom =='extranjero')
		{$tablasBusqueda="USUARIO,SIMPATIZANTE,PAIS,MIEMBRO";
		  
			 $camposBuscados="DISTINCT USUARIO.ESTADO,USUARIO.CODUSER,USUARIO.USUARIO,
		                 CONCAT(IFNULL(APE1,''),' ',IFNULL(APE2,''),', ',NOM) as apeNom,
		                 MIEMBRO.CODPAISDOM,MIEMBRO.DIRECCION,TIPOMIEMBRO,SEXO,
										         MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,INFORMACIONEMAIL,INFORMACIONCARTAS,
																			PAIS.NOMBREPAIS,COLABORA, SIMPATIZANTE.FECHAALTA,SIMPATIZANTE.FECHABAJA";
		
			 $cadCondicionesBuscar =" WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
                           AND USUARIO.CODUSER=SIMPATIZANTE.CODUSER 
																											AND MIEMBRO.CODPAISDOM=PAIS.CODPAIS1 
                           AND (SIMPATIZANTE.FECHABAJA IS NULL OR SIMPATIZANTE.FECHABAJA ='0000-00-00')																											
																											AND MIEMBRO.CODPAISDOM != 'ES'
 												              AND USUARIO.ESTADO='alta' ORDER BY MIEMBRO.CODPAISDOM, MIEMBRO.EMAIL";
		}
		else
		{ $tablasBusqueda="USUARIO,SIMPATIZANTE,PAIS,MIEMBRO";
		  
			 $camposBuscados="DISTINCT USUARIO.ESTADO,USUARIO.CODUSER,USUARIO.USUARIO,
		                 CONCAT(IFNULL(APE1,''),' ',IFNULL(APE2,''),', ',NOM) as apeNom,
		                 MIEMBRO.CODPAISDOM,MIEMBRO.DIRECCION,TIPOMIEMBRO,SEXO,
										         MIEMBRO.TELFIJOCASA,MIEMBRO.TELMOVIL,MIEMBRO.EMAIL,INFORMACIONEMAIL,INFORMACIONCARTAS,
																			PAIS.NOMBREPAIS,COLABORA, SIMPATIZANTE.FECHAALTA,SIMPATIZANTE.FECHABAJA";
		
			 $cadCondicionesBuscar=" WHERE USUARIO.CODUSER=MIEMBRO.CODUSER
                           AND USUARIO.CODUSER=SIMPATIZANTE.CODUSER 
																											AND MIEMBRO.CODPAISDOM=PAIS.CODPAIS1 
                           AND (SIMPATIZANTE.FECHABAJA IS NULL OR SIMPATIZANTE.FECHABAJA ='0000-00-00')																											
																											AND MIEMBRO.CODPAISDOM LIKE '$codPaisDom'
 												              AND USUARIO.ESTADO='alta' ORDER BY MIEMBRO.CODPAISDOM, MIEMBRO.EMAIL";
		}
																														
	$cadBuscarDatosSimps="SELECT $camposBuscados FROM $tablasBusqueda $cadCondicionesBuscar";	
																																														
	//echo '<br>modeloSimpatizantes:cadBuscarDatosSimps:cadBuscarDatosSimps ';print_r($cadBuscarDatosSimps);
	return $cadBuscarDatosSimps;
}
//------------------------------ Fin cadBuscarDatosSimps ------------------

/*------------------------- Inicio altaSimp----------------------------------
Acción: En esta función se dan de alta los simpatizantes, controlando transacciones
Recibe: un array con los campos de los formularios ya validados
Devuelve: un array con los controles de errores, y los valores de deshacerTrans
Llamada: desde controladorSimpatizantes: altaSimpatizante(),
Llama: a varias funciones dentro de "modeloSimpatizantes.php" y otras en 
       modeloUsuarios.php 
OBSERVACIONES: Se graban los errores del sistema en ERRORES
Vale solo para simpatizantes debido a la agrupacionTerritorial, CC solo para socios
------------------------------------------------------------------------------*/
function altaSimp($resValidarCampos)
{//echo "<br><br>0altaSimp: resValidarCampos: "; print_r($resValidarCampos);
 $arrMensaje['textoCabecera']='Registrar nuevo simpatizante';
	//$arrMensaje['textoBoton']='Salir de la aplicación';
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	
 $resInsertar=$resValidarCampos;
	
	//echo "<br><br>1-0altaSimp: resInsertar: "; print_r($resInsertar);//
 //require "BBDD/MySQL/configMySQL.php";		
 require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionUsuariosDB['codError']!=="00000")
	{ $resEliminarSocio=$conexionUsuariosDB;
	  $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 
	}	
 else //$conexionUsuariosDB['codError']=="00000"
	{$iniTrans = "START TRANSACTION";
  $resIniTrans = mysql_query($iniTrans,$conexionUsuariosDB['conexionLink']);
		if (!$resIniTrans)
	 {$resIniTrans['codError']='70501';   
			$resIniTrans['errno']=mysql_errno(); 
	  $resIniTrans['errorMensaje']='Error en el sistema, no se ha podido iniciar la transación. '.
				           'Error mysql_query '.mysql_error();
	  $resIniTrans['numFilas']=0;	
			
			$resInsertar=$resIniTrans;	
			//echo "<br><br>modeloSimpatizantes:altaSimp1-0:resIniTrans: ";print_r($resIniTrans);echo "<br>";
	 }	
		else //$resIniTrans
  {
		 $passEncriptada = $resInsertar['datosFormUsuario']['PASSUSUARIO']['valorCampo'];		
   $resInsertar['datosFormUsuario']['PASSUSUARIO']['valorCampo'] = sha1($passEncriptada);		
		 unset($resInsertar['datosFormUsuario']['RPASSUSUARIO']);
		 unset($resInsertar['datosFormUsuario']['privacidad']); 
							
	  $resInsertarUsuario = insertarUsuario($resInsertar['datosFormUsuario']);			

   //echo "<br><br>altaSimp1-2: resInsertarUsuario"; print_r($resInsertarUsuario);//
		
   if ($resInsertarUsuario['codError']!=='00000')			
   {$resInsertar=$resInsertarUsuario;
   }
   else //($resInsertarUsuario['codError']=='00000')
   {$resInsertar['datosFormUsuario']['CODUSER']['valorCampo']=	$resInsertarUsuario['CODUSER'];//devuelve siguiente a máx 			
			 $resInsertar['datosFormUsuario']['CODROL']['valorCampo']='2';			
		  $resInsertarUsuarioRol=insertarUsuarioRol($resInsertar['datosFormUsuario']);	//todos se dan de alta con rol simp codigo '2'
	   //echo "<br><br>1-3 altaSimp:insertarUsuarioRol ";print_r($resInsertarUsuarioRol);echo "<br><br>";    
		
    if ($resInsertarUsuarioRol['codError']!=='00000')			
    {$resInsertar=$resInsertarUsuarioRol;
    }
	   else //$resInsertarUsuarioRol['codError']=='00000'
    {$resInsertar['datosFormMiembro']['CODUSER']['valorCampo']=$resInsertarUsuario['CODUSER'];				
				 $resInsertar['datosFormMiembro']['TIPOMIEMBRO']['valorCampo'] = "simpatizante";
				 /*
					if ($resInsertar['datosFormDomicilio']['CODPAISDOM']['valorCampo'] == 'ES')		
					{$resInsertar['datosFormDomicilio']['DIREXTRANJERO']['valorCampo']= NULL;
					}
					*/
					//echo "<br><br>1-4-1 altaSimp:resInsertarMiembro['datosFormMiembro']:";print_r($resInsertarMiembro['datosFormMiembro']);
					/*if ($resInsertar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']['valorCampo'] == '')//ahora ya no se pide		
					{unset($resInsertar['datosFormMiembro']['NUMDOCUMENTOMIEMBRO']);
					 unset($resInsertar['datosFormMiembro']['TIPODOCUMENTOMIEMBRO']);
						unset($resInsertar['datosFormMiembro']['CODPAISDOC']);
					}
					*/		
     unset($resInsertar['datosFormMiembro']['REMAIL']);																													
	    //unset($resInsertar['datosFormDomicilio']['controlDuplicidad']);
										
					//las dos siguientes están para no cambiar en el formulario "datosFormDomicilio-->datosFormMiembro"						
		   $resInsertar['datosFormMiembro']=array_merge($resInsertar['datosFormMiembro'],$resInsertar['datosFormDomicilio']);
			  unset ($resInsertar['datosFormDomicilio']); 							
				 //echo "<br><br>1-4-2 altaSimp:resInsertar['datosFormMiembro'] ";print_r($resInsertar['datosFormMiembro']);
		   $resInsertarMiembro=insertarMiembro($resInsertar['datosFormMiembro']);	//se necesitan datos de DOMICILIO el codigo
	    //echo "<br><br>1-4-3 altaSimp:resInsertarMiembro ";print_r($resInsertarMiembro);
    
	    if ($resInsertarMiembro['codError']!=='00000')			
	    {$resInsertar=$resInsertarMiembro;
	    }
		   else //$resInsertarMiembro['codError']=='00000'
	    {$resInsertar['datosFormSimp']['CODUSER']['valorCampo'] = $resInsertarUsuario['CODUSER']; 
					  
			   $resInsertarSimp = insertarSimp($resInsertar['datosFormSimp']);
			   //echo "<br><br>1-5 altaSimp:resInsertarSimp ";print_r($resInsertarSimp);echo "<br><br>";
		
		    if ($resInsertarSimp['codError']!=='00000')			
		    {$resInsertar=$resInsertarSimp;
		    }
			   else //$resInsertarMiembro['codError']=='00000'
		    {	//-----------------------
					  $finTrans = "COMMIT";
		     $resFinTrans = mysql_query($finTrans,$conexionUsuariosDB['conexionLink']);
					  if (!$resFinTrans)
				   {$resFinTrans['codError']='70502';   
						  $resFinTrans['errno']=mysql_errno(); 
				    $resFinTrans['errorMensaje']='Error en el sistema, no se ha podido finalizar transación. '.
							           'Error mysql_query '.mysql_error();
				    $resFinTrans['numFilas']=0;	
						
						  $resInsertar=$resFinTrans;	
						//echo "<br><br>modeloSimp:altaSimp1-6:$resInsertar: ";print_r($resInsertar);echo "<br>";
				   }
							else
							{$resInsertar['CODUSER']= $resInsertarUsuario['CODUSER'];	
							 $arrMensaje['textoComentarios']="<br /><br />Te acabas de registrar como simpatizante en la base de datos de Europa Laica
								<br /><br /><br /><br />
						  Para entrar en la aplicación de gestión de socios, debes utilizar el nombre de usuario y contraseña
								 que acabas de introducir<br /><br /><br /><br /> 
						  Desde el menú lateral podrás modificar tus datos y eliminar tus datos (según la ley de protección de datos)";
							}
				   //------------------------
				  }
	    }//else $resInsertarMiembro['codError']=='00000'
    }//else $resInsertarUsuarioRol['codError']=='00000'				
   }//else($resInsertarUsuario['codError']=='00000')	
			//echo "<br><br>1-6 altaSocios:resInsertar: ";print_r($resInsertar);echo "<br>";
				
		if ($resInsertar['codError']!=='00000')
		{ $arrMensaje['textoComentarios']="Error del sistema al insertar simpatizante, vuelva a intentarlo pasado un tiempo ";
			
			$deshacerTrans = "ROLLBACK"; //$sql = "ROLLBACK TO SAVEPOINT transEliminarSocio";
		 $resDeshacerTrans = mysql_query($deshacerTrans,$conexionUsuariosDB['conexionLink']);	
			if (!$resDeshacerTrans)
		 {$resDeshacerTrans['codError']='70503';   
				$resDeshacerTrans['errno']=mysql_errno(); 
		  $resDeshacerTrans['errorMensaje']='Error en el sistema, no se ha podido deshacer la transación. '.
					                                'Error mysql_query '.mysql_error();
		  $resDeshacerTrans['numFilas']=0;	
				
				$resInsertar=$resDeshacerTrans;
				//echo "<br><br>modeloSimpatizante:altasSimp1-6-4:resInsertar: ";print_r($resInsertar);echo "<br>";	
		 }	
			require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
			$resInsertarErrores=insertarError($resInsertar);
			
			if ($resInsertarErrores['codError']!=='00000')
	  {$resInsertar['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'].="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}	
	 }//if ($resInsertar['codError']!=='00000')
	}//$resIniTrans
 }//$conexionUsuariosDB['codError']=="00000"
	$resAltaSimp=$resInsertar;
	$resAltaSimp['arrMensaje']=$arrMensaje;	
	//echo "<br><br>1-8 altaSimp:resAltaSimp: ";print_r($resAltaSimp);echo "<br>";	
 return 	$resAltaSimp; 	
}
//------------------------------ Fin altaSimp ----------------------------------------

//----------------------------- Inicio insertarSimp ---------
function insertarSimp($resulValidar)//se pasa el array :$resulValidar['datosFormSocio']
{	  
 //require "BBDD/MySQL/configMySQL.php";
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);		
	if ($conexionUsuariosDB['codError']!=="00000")
	{ $resulInserSocio=$conexionUsuariosDB;
	}
	else
	{$tablasBusqueda='SIMPATIZANTE';
		$camposBuscados='CODSIMPATIZANTE';
  require_once './modelos/libs/buscarCodMax.php';
	 $resulBuscarCodMax=buscarCodMax($tablasBusqueda,$camposBuscados,$conexionUsuariosDB['conexionLink']);
		
		if ($resulBuscarCodMax['codError']!=='00000')
		{ $resulInserSimp=$resulBuscarCodMax;
		}
		else 
		{//echo ,$_POST['datosFormUsuario']['USUARIO'];
			$arrValoresInser['CODSIMPATIZANTE'] = $resulBuscarCodMax['valorCampo'];
			//$arrValoresInser['USUARIO'] = $resulValidar['datosFormUsuario']['USUARIO']['valorCampo'];
			$arrValoresInser['CODUSER'] = $resulValidar['CODUSER']['valorCampo'];
			$arrValoresInser['FECHAALTA'] = date('Y-m-d');//mysql:(CURRENT_DATE());
			$arrValoresInser['FECHABAJA'] = '0000-00-00';	

	  $resulInserSimp = insertarUnaFila('SIMPATIZANTE',$arrValoresInser,$conexionUsuariosDB['conexionLink']);
			if ($resulInserSimp)
			{	$resulInserSimp['valorCampo']=$arrValoresInser['CODSIMPATIZANTE'];
			}
		}
	}	
 //echo "<br><br>4 insertarSimp:resulInserSimp:"; print_r($resulInserSimp); 
 return $resulInserSimp;
}
//------------------------------ Fin insertarSimp ----------------------------


/*------------------------------ Inicio eliminarDatosSimp ----------------------
Acción: En esta función se eliminan los datos de los simpatizantes, controlando transacciones
        con start transation y rollback.
Recibe: var de sesión $_SESSION['vs_NOMUSUARIO'], y $_SESSION['vs_CODDOMICILIO']
Devuelve: un array con los controles de errores, y mensajes 
Llamada: desde controladorsimpatizantes: eliminarsimpatizantes(),
Llama: a varias funciones de modeloUsuarios y también dentro de "modeloSimpatizantes.php" 
OBSERVACIONES: Se graban los errores del sistema en ERRORES
------------------------------------------------------------------------------*/
function eliminarDatosSimp($usuarioBuscado)//
{ 
	$resEliminarSimp['nomFuncion']="eliminarDatosSimp";
	$resEliminarSimp['nomScript']="modeloSimpatizantes.php";	
 $resEliminarSimp['codError']='00000';
 $resEliminarSimp['errorMensaje'] = '';
	
 $arrMensaje['textoCabecera']='Eliminar simpatizante';
	//$arrMensaje['textoBoton']='Salir de la aplicación';	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	
 //require "BBDD/MySQL/configMySQL.php";
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";		
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionUsuariosDB['codError']!=="00000")
	{ $resEliminarSimp=$conexionUsuariosDB;
	  $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 
	}
	else //$conexionUsuariosDB['codError']=="00000"
	{$iniTrans = "START TRANSACTION";
  $resIniTrans = mysql_query($iniTrans,$conexionUsuariosDB['conexionLink']);
		if (!$resIniTrans)
	 {$resIniTrans['codError']='70501';   
			$resIniTrans['errno']=mysql_errno(); 
	  $resIniTrans['errorMensaje']='Error en el sistema, no se ha podido iniciar la transación. '.
				           'Error mysql_query '.mysql_error();
	  $resIniTrans['numFilas']=0;	
			
			$resEliminarSimp=$resIniTrans;	
			//echo "<br><br>modeloSimpatizantes:eliminarDatosSimp1-0:resEliminarSocio: ";print_r($resEliminarSocio);echo "<br>";
	 }	
		else //$resIniTrans
		{$reActUsuarioEliminar=actUsuarioEliminar('USUARIO',$usuarioBuscado); 
	    		
		 //echo "<br><br>modeloSimpatizantes:eliminarDatosSimp1-1: $reActUsuarioEliminar: "; print_r($reActUsuarioEliminar);
			
		 if ($reActUsuarioEliminar['codError']!=='00000')			
	  {$resEliminarSimp=$reActUsuarioEliminar;
   }		
			else //$reActUsuarioEliminar['codError']=='00000')
			{$reActSimpEliminar=actSimpEliminar('SIMPATIZANTE',$usuarioBuscado);	   		
		  //echo "<br><br>modeloSimpatizantes:eliminarSocio1-2: reActSimpEliminar: "; print_r($reActSimpEliminar);
			
			 if ($reActSimpEliminar['codError']!=='00000')			
	   {$resEliminarSimp=$reActSimpEliminar;
		  }	
				else //$reActSimpEliminar['codError']=='00000')
				{ 
				 $reActMiembroEliminar=actMiembroEliminar('MIEMBRO',$usuarioBuscado); 
			  //echo "<br><br>modeloSimpatizantes:eliminarDatosSimp1-3: reActMiembroEliminar: "; print_r($reActMiembroEliminar);
									
				 if ($reActMiembroEliminar['codError']!=='00000')			
	   	{$resEliminarSimp=$reActMiembroEliminar;
			  }	
					else //$reActMiembroEliminar['codError']=='00000')
					{
					 $finTrans = "COMMIT";
			   $resFinTrans = mysql_query($finTrans,$conexionUsuariosDB['conexionLink']);
						if (!$resFinTrans)
					 {$resFinTrans['codError']='70502';   
							$resFinTrans['errno']=mysql_errno(); 
					  $resFinTrans['errorMensaje']='Error en el sistema, no se ha podido finalizar transación. '.
								           'Error mysql_query '.mysql_error();
					  $resFinTrans['numFilas']=0;	
							
							$resEliminarSimp=$resFinTrans;	
							//echo "<br><br>modeloSimpatizantes:eliminarDatosSimp1-6-1:resEliminarSimp:";print_r($resEliminarSimp);
					 }
						else
						{	$arrMensaje['textoComentarios']="Se ha eliminado el simpatizante de la base de datos de Europa Laica<br /><br />";
						}										
					}	//$reActMiembroEliminar['codError']=='00000')		
				}//$reActSimpEliminar['codError']=='00000')				
		 }//$reActUsuarioEliminar['codError']=='00000')																																							
		}//$resIniTrans
	  //echo "<br><br>modeloSimpatizantes:eliminarDatosSimp1-6-2:resEliminarSimp: ";print_r($resEliminarSimp);
	
	 if ($resEliminarSimp['codError']!=='00000')
		{//echo "<br><br>modeloSimpatizantes:eliminarDatosSimp1-6-3:resEliminarSimp: ";print_r($resEliminarSimp);echo "<br>";
		
		 $deshacerTrans = "ROLLBACK"; //$sql = "ROLLBACK TO SAVEPOINT transEliminarSocio";
		 $resDeshacerTrans = mysql_query($deshacerTrans,$conexionUsuariosDB['conexionLink']);	
			if (!$resDeshacerTrans)
		 {$resDeshacerTrans['codError']='70503';   
				$resDeshacerTrans['errno']=mysql_errno(); 
		  $resDeshacerTrans['errorMensaje']='Error en el sistema, no se ha podido deshacer la transación. '.
					                                'Error mysql_query '.mysql_error();
		  $resDeshacerTrans['numFilas']=0;	
				
				$resEliminarSimp=$resDeshacerTrans;
				//echo "<br><br>modeloSimpatizantes:eliminarDatosSimp1-6-4:resEliminarSimp: ";print_r($resEliminarSimp);echo "<br>";	
		 }	 
			$arrMensaje['textoComentarios']="Error del sistema al eliminar simpatizante, vuelva a intentarlo pasado un tiempo ";
			
			require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
			$resInsertarErrores=insertarError($resEliminarSimp);	
				
			if ($resInsertarErrores['codError']!=='00000')
	  { $resEliminarSimp['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'].="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}		
		}//if ($resEliminarSimp['codError']!=='00000')	
		//echo "<br><br>modeloSimpatizantes:eliminarDatosSimp1-7:resEliminarSimp: ";print_r($resEliminarSimp);echo "<br>";
	}//$conexionUsuariosDB['codError']=="00000"		
	$resEliminarSimp['arrMensaje']=$arrMensaje;	
 return 	$resEliminarSimp; 	
}
//---------------------------- Fin eliminarDatosSimp --------------------------

//----------------------------- Inicio actSimpEliminar ------------------------
//Vale solo para simp    
function actSimpEliminar($tablaAct,$codUser)   	   
{
 $arrayCondiciones['CODUSER']['valorCampo']= $codUser;
 $arrayCondiciones['CODUSER']['operador']= '=';
 $arrayCondiciones['CODUSER']['opUnir']= ' ';

	$arrayDatosAct['FECHABAJA']=date('Y-m-d');//mysql:(CURRENT_DATE());
		
	$resActSimpEliminar = actualizarTabla($tablaAct,$arrayCondiciones,$arrayDatosAct); 																					
	//echo '<br><br>resActSimpEliminar:';print_r($resActSimpEliminar);
	
	return $resActSimpEliminar;
 } 
//----------------------------- Fin actSimpEliminar -----------------------
//Llamada desde controladorSimpatizantes.php-->eliminarsimpatizantes()
function buscarDatosSimpEliminar($codUser)
{$resDatosSocioEliminar['codError']='00000';
 $resDatosSocioEliminar['errorMensaje']='';
 $resDatosSocioEliminar['nomScript']='modeloSimpatizantes.php';
 $resDatosSocioEliminar['nomFuncion']='buscarDatosSimpEliminar';//se van a repetir
	$arrMensaje['textoCabecera']='Eliminar simpatizante';		
	$arrMensaje['textoBoton']='Salir de la aplicación';
	//$arrMensaje['textoComentarios']="??Se han eliminado los datos identificativos del socio en la base de datos de 
	//Europa Laica<br /><br />	(según la ley de protección de datos)";
	$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';	
	
	//require "BBDD/MySQL/configMySQL.php";	
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);
	if ($conexionUsuariosDB['codError']!=='00000')	
	{ $resDatosSocioEliminar=$conexionUsuariosDB;
   $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 	
	}
	else	//$conexionUsuariosDB['codError']=='00000'
	{$tablasBusqueda='USUARIO';
		$camposBuscados='*';
		$cadCondicionesBuscar=" WHERE USUARIO.CODUSER=\"".$codUser."\"";
    
		$resDatosUsuario=buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
			                     								$camposBuscados,$conexionUsuariosDB['conexionLink']);  
		if ($resDatosUsuario['codError']!=='00000')
		{$resDatosSocioEliminar['codError']=$resDatosUsuario['codError'];
   $resDatosSocioEliminar['errorMensaje']=$resDatosUsuario['errorMensaje'];
		 $resDatosSocioEliminar['nomScript']=$resDatosUsuario['nomScript'];//se van a repetir
		 $resDatosSocioEliminar['nomFuncion']=$resDatosUsuario['nomFuncion'];//se van a repetir
		}	
		elseif ($resDatosUsuario['numFilas']==0)
		{$resDatosSocioEliminar['codError']='80001'; //no encontrado
			$resDatosSocioEliminar['errorMensaje']="No existe ese miembro";
		}	
	  else //$resDatosUsuario['codError']=='00000')
		{$resDatosSocioEliminar['valoresCampos']['USUARIO']=$resDatosUsuario['resultadoFilas'];
		 $tablasBusqueda='MIEMBRO';
			$camposBuscados='*';
			$cadCondicionesBuscar=" WHERE MIEMBRO.CODUSER=\"".$codUser."\"";
	    
			$resDatosMiembro=buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
				                     								$camposBuscados,$conexionUsuariosDB['conexionLink']);
			if ($resDatosMiembro['codError']!=='00000')
			{$resDatosSocioEliminar['codError']=$resDatosMiembro['codError'];
	   $resDatosSocioEliminar['errorMensaje']=$resDatosMiembro['errorMensaje'];
			 $resDatosSocioEliminar['nomScript']=$resDatosMiembro['nomScript'];//se van a repetir
			 $resDatosSocioEliminar['nomFuncion']=$resDatosMiembro['nomFuncion'];//se van a repetir
			}	
			elseif ($resDatosMiembro['numFilas']==0)
			{$resDatosSocioEliminar['codError']='80001'; //no encontrado
				$resDatosSocioEliminar['errorMensaje']="No existe ese miembro";
			}			
			else	//$resDatosMiembro['codError']=='00000'		
			{/*//$resDatosSocioEliminar['valoresCampos']['MIEMBRO']=$resDatosMiembro['resultadoFilas'];
			 //$tablasBusqueda='DOMICILIO';
				//$camposBuscados='*';
				
				//$cadCondicionesBuscar=" WHERE DOMICILIO.CODDOMICILIO=".$resDatosMiembro['resultadoFilas'][0]['CODDOMICILIO'];
		    
				//$resDatosDomicilio=buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
			 //		                     								$camposBuscados,$conexionUsuariosDB['conexionLink']);
				if ($resDatosDomicilio['codError']!=='00000')
				{$resDatosSocioEliminar['codError']=$resDatosDomicilio['codError'];
		   $resDatosSocioEliminar['errorMensaje']=$resDatosDomicilio['errorMensaje'];
				 $resDatosSocioEliminar['nomScript']=$resDatosDomicilio['nomScript'];//se van a repetir
				 $resDatosSocioEliminar['nomFuncion']=$resDatosDomicilio['nomFuncion'];//se van a repetir
				}	
				elseif ($resDatosDomicilio['numFilas']==0)
				{$resDatosSocioEliminar['codError']='80001'; //no encontrado
					$resDatosSocioEliminar['errorMensaje']="No existe ese miembro";
				}	
				else //$resDatosDomicilio['codError']=='00000'
				*/
				{//$resDatosSocioEliminar['valoresCampos']['DOMICILIO']=$resDatosDomicilio['resultadoFilas'];
				 $tablasBusqueda='SIMPATIZANTE';
				 $camposBuscados='*';
					$cadCondicionesBuscar=" WHERE SIMPATIZANTE.CODUSER=\"".$codUser."\"";
			    
					$resDatosSocio=buscarEnTablas($tablasBusqueda,$cadCondicionesBuscar,
						                     				$camposBuscados,$conexionUsuariosDB['conexionLink']);	
					if ($resDatosSocio['codError']!=='00000')
					{ $resDatosSocioEliminar['codError']=$resDatosSocio['codError'];
			    $resDatosSocioEliminar['errorMensaje']=$resDatosSocio['errorMensaje'];
					  $resDatosSocioEliminar['nomScript']=$resDatosSocio['nomScript'];//se van a repetir
					  $resDatosSocioEliminar['nomFuncion']=$resDatosSocio['nomFuncion'];//se van a repetir
					}	
					elseif ($resDatosSocio['numFilas']==0)
					{$resDatosSocioEliminar['codError']='80001'; //no encontrado
						$resDatosSocioEliminar['errorMensaje']="No existe ese miembro";
					}
					else
					{$resDatosSocioEliminar['valoresCampos']['SIMPATIZANTE']=$resDatosSocio['resultadoFilas'];
						//echo "<br><br>1-5 eliminarSocios:buscarDatosSocioEliminar:resDatosSocioEliminar: ";
						//print_r($resDatosSocioEliminar);echo "<br>";						
					}															
				}	//$resDatosDomicilio['codError']=='00000'		
			}	//$resDatosMiembro['codError']=='00000'		
		} //$resDatosUsuario['codError']=='00000')
    //echo "<br><br>1-6eliminarSocios:buscarDatosSocioEliminar:resDatosSocioEliminar: ";print_r($resDatosSocioEliminar);echo "<br>";
		
		if ($resDatosSocioEliminar['codError']!=='00000')
		{ $arrMensaje['textoComentarios']="Error del sistema al eliminar simpatizante, vuelva a intentarlo pasado un tiempo ";
		
			require_once './modelos/modeloErrores.php'; //si es un error de conexión a la tabla error, insertar errores 
			$resInsertarErrores=insertarError($resDatosSocioEliminar);			
			if ($resInsertarErrores['codError']!=='00000')
	    { $resDatosSocioEliminar['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'].="Error del sistema al tratar ERRORES, vuelva a intentarlo pasado un tiempo ";
			}
		}//if $resDatosSocioEliminar['codError']!=='00000'
	}	//$conexionUsuariosDB['codError']=='00000'
	//echo "<br><br>1-8 altaSocios:resAltaSocios: ";print_r($resAltaSocios);echo "<br>";		
	$resDatosSocioEliminar['arrMensaje']=$arrMensaje;	
 return 	$resDatosSocioEliminar; 	
}
//------------------------------ Fin buscarDatosSimpEliminar --------------------

/*-----------------------------------------------------------------------------
Acción: En esta función se actualizan los datos de los simpatizantes, controlando transacciones
        con start transation y rollback.
Recibe: var de sesión $_SESSION['vs_NOMUSUARIO'], y $_SESSION['vs_CODDOMICILIO']
Devuelve: un array con los controles de errores, y mensajes 
Llamada: desde controladorUsuarios: actualizarSimpatizantes(),
Llama: a varias funciones modeloUsuarios y también dentro de "modeloSimpatizantes.php" 
OBSERVACIONES: Se graban los errores del sistema en ERRORES
------------------------------------------------------------------------------*/
function actualizarDatosSimp($resValidarCamposForm)
{//echo "<br><br>1-0 modeloSimpatizantes:actualizarDatosSimp:resValidarCamposForm:"; print_r($resValidarCamposForm);// 
	$resActDatosSocio['nomFuncion']="actualizarDatosSocio";
	$resActDatosSocio['nomScript']="modeloUsuarios.php";	
 $resActDatosSocio['codError']='00000';
 $resActDatosSocio['errorMensaje'] = '';
	
 $arrMensaje['textoCabecera']='Actualizar datos del socio';
	//$arrMensaje['textoBoton']='Salir de la aplicación';	
	//$arrMensaje['enlaceBoton']='./index.php?controlador=controladorLogin&amp;accion=logOut';
	
 //require "BBDD/MySQL/configMySQL.php";		
	require "../../usuariosConfig/BBDD/MySQL/configMySQL.php";
	$conexionUsuariosDB=conexionDB($serverDB,$usernameDB,$passwordDB,$esquemaDB);	

	if ($conexionUsuariosDB['codError']!=="00000")
	{ $resActDatosSocio=$conexionUsuariosDB;
	  $arrMensaje['textoComentarios'].="Error del sistema al conectarse a la base de datos"; 
	}
	else //$conexionUsuariosDB['codError']=="00000"
	{$iniTrans = "START TRANSACTION";
  $resIniTrans = mysql_query($iniTrans,$conexionUsuariosDB['conexionLink']);
		if (!$resIniTrans)
	 {$resIniTrans['codError']='70501';   
			$resIniTrans['errno']=mysql_errno(); 
	  $resIniTrans['errorMensaje']='Error en el sistema, no se ha podido iniciar la transación. '.
				           'Error mysql_query '.mysql_error();
	  $resIniTrans['numFilas']=0;	
			
			$resActDatosSocio=$resIniTrans;	
			//echo "<br><br>1-1modeloSimpatizantes:actualizarDatosSimp:resActDatosSocio: ";
			//print_r($resActDatosSocio);
	 }	
		else //$resIniTrans
		{//$reActUsuario=actualizUsuario('USUARIO',$_SESSION['vs_NOMUSUARIO'],$resValidarCamposForm['datosFormUsuario']); 
   $usuarioBuscado=$resValidarCamposForm['datosFormUsuario']['CODUSER']['valorCampo'];	 
 	
		 $reActUsuario=actualizUsuario('USUARIO',$usuarioBuscado,$resValidarCamposForm['datosFormUsuario']);			
					
		 //$reActUsuario=actualizUsuario('USUARIO',$_SESSION['vs_CODUSER'],$resValidarCamposForm['datosFormUsuario']); 
	    	
		 //echo "<br><br>2modeloSimpatizantes:actualizarDatosSimp: reActUsuario: ";print_r($reActUsuario);//
			
		 if ($reActUsuario['codError']!=='00000')			
	  {$resActDatosSocio=$reActUsuario;
   }		
			else //$reActUsuario['codError']=='00000')
			{ //$_SESSION['vs_NOMUSUARIO']=$resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo'];
			  
					//------------  las siguientes preparan datos de domicilio para insertar en tabla MIEMBRO ---------------
			  /*if ($resValidarCamposForm['datosFormDomicilio']['CODPAISDOM']['valorCampo'] == 'ES')		
					{$resValidarCamposForm['datosFormDomicilio']['DIREXTRANJERO']['valorCampo'] = NULL;}
					unset($resValidarCamposForm['datosFormDomicilio']['controlDuplicidad']);
					*/	
				 unset($resValidarCamposForm['datosFormMiembro']['REMAIL']);
					//las dos siguientes están para no cambiar en el formulario "datosFormDomicilio-->datosFormMiembro"						
				 $resValidarCamposForm['datosFormMiembro']=array_merge($resValidarCamposForm['datosFormMiembro'],
																																																											$resValidarCamposForm['datosFormDomicilio']);
					unset($resValidarCamposForm['datosFormDomicilio']); 
						
			  //$reActMiembro=actualizMiembro('MIEMBRO',$_SESSION['vs_CODUSER'],$resValidarCamposForm['datosFormMiembro']); 
					$reActMiembro=actualizMiembro('MIEMBRO',$usuarioBuscado,$resValidarCamposForm['datosFormMiembro']); 
		    
			 //echo "<br><br>3modeloSimpatizantes:actualizarDatosSimp:reActMiembro: ";
			 // print_r($reActMiembro);
								
			 if ($reActMiembro['codError']!=='00000')			
   	{$resActDatosSocio=$reActMiembro;
		  }	
				else //$reActMiembro['codError']=='00000')
				{ //echo "<br><br>4modeloSimpatizantes:actualizarDatosSimp: _SESSION['vs_CODDOMICILIO'] ";
				  //print_r($_SESSION['vs_CODDOMICILIO']);
				  //$reActDomicilio=actualizDomicilio('DOMICILIO',$_SESSION['vs_CODDOMICILIO'],
					 //                     $resValidarCamposForm['datosFormDomicilio']); 
			   	
				  //echo "<br><br>5modeloSimpatizantes:actualizarDatosSimp: reActDomicilio: ";
					 //print_r($reActDomicilio);
									
				  //if ($reActDomicilio['codError']!=='00000')			
	   	 //  {$resActDatosSocio=$reActDomicilio;
			   //}	
					//else //$reActDomicilio['codError']=='00000')
					{ //echo "<br><br>6modeloSimpatizantes:actualizarDatosSimp:reActDomicilio: ";
					  //print_r($reActDomicilio);echo "<br>";	
							
					 $finTrans = "COMMIT";
			   $resFinTrans = mysql_query($finTrans,$conexionUsuariosDB['conexionLink']);
						if (!$resFinTrans)
					 {$resFinTrans['codError']='70502';   
							$resFinTrans['errno']=mysql_errno(); 
					  $resFinTrans['errorMensaje']='Error en el sistema, no se ha podido finalizar transación. '.
								           'Error mysql_query '.mysql_error();
					  $resFinTrans['numFilas']=0;	
							
							$resActDatosSocio=$resFinTrans;	
							//echo "<br><br>modeloUsuarios:actualizarDatosSocio1-6-1:resActDatosSocio: ";
							//print_r($resActDatosSocio);echo "<br>";
					 }
						else
						{	$arrMensaje['textoComentarios']="Se han actualizado los datos del simpatizante en la base de datos de Europa Laica";
					   //	.$resValidarCamposForm['datosFormUsuario']['USUARIO']['valorCampo']." <br /><br />";
						}
					}	//$reActDomicilio['codError']=='00000')										
				}	//$reActMiembro['codError']=='00000')
		  }//$reActUsuario['codError']=='00000')																																							
		}//$resIniTrans
	  //echo "<br><br>7modeloSimpatizantes:actualizarDatosSimp:resActDatosSocio: ";
		//print_r($resActDatosSocio);echo "<br>";
	
	 if ($resActDatosSocio['codError']!=='00000')
		{//echo "<br><br>8modeloSimpatizantes:actualizarDatosSimp:resActDatosSocio: ";
		 //print_r($resActDatosSocio);echo "<br>";
		
		 $deshacerTrans = "ROLLBACK"; //$sql = "ROLLBACK TO SAVEPOINT transEliminarSocio";
		 $resDeshacerTrans = mysql_query($deshacerTrans,$conexionUsuariosDB['conexionLink']);	
			if (!$resDeshacerTrans)
		 {$resDeshacerTrans['codError']='70503';   
				$resDeshacerTrans['errno']=mysql_errno(); 
		  $resDeshacerTrans['errorMensaje']='Error en el sistema, no se ha podido deshacer la transación. '.
					                                'Error mysql_query '.mysql_error();
		  $resDeshacerTrans['numFilas']=0;	
				
				$resActDatosSocio=$resDeshacerTrans;
				//echo "<br><br>9modeloSimpatizantes:actualizarDatosSimp:resActDatosSocio: ";
				//print_r($resActDatosSocio);echo "<br>";	
		  }	 
			$arrMensaje['textoComentarios']="Error del sistema al actualizar los datos del simpatizante, 
			vuelva a intentarlo pasado un tiempo ";
			
			require_once './modelos/modeloErrores.php';
			$resInsertarErrores=insertarError($resActDatosSocio);	
				
			if ($resInsertarErrores['codError']!=='00000')
	    { $resActDatosSocio['errorMensaje'] .= $resInsertarErrores['errorMensaje'];
				$arrMensaje['textoComentarios'].="Error del sistema al tratar ERRORES,
				 vuelva a intentarlo pasado un tiempo ";
			}		
		}//if ($resActDatosSocio['codError']!=='00000')	
		//echo "<br><br>10modeloSimpatizantes:actualizarDatosSimp:resActDatosSocio: ";
		//print_r($resActDatosSocio);echo "<br>";
	}//$conexionUsuariosDB['codError']=="00000"		
	$resActDatosSocio['arrMensaje']=$arrMensaje;	
return 	$resActDatosSocio; 	
}
//----------------------------- Fin actualizarDatosSimp -------------- 
?>