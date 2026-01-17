<?php
/*--------------- formMostrarCuotasVigentesELTes.php  ----------------------------------------------
FICHERO: formMostrarCuotasVigentesELTes.php
VERSION: PHP PHP 7.3.21

DESCRIPCION:
Se muestran en unas tablas los datos de las cuotas vigentes para EL del año actual y el año siguiente,
a partir de la tabla "IMPORTEDESCUOTAANIO" y desde este formulario se puede ir a la función de 
cambiar los importes de las cuotas anuales vigentes en EL para el año siguiente. 

Con llamada a función: cTeserero.php:actualizarCuotasVigentesELTes()

LLAMADA: vistas/tesorero/vCuerpoMostrarCuotasVigentesELTes.php

OBSERVACIONES:
--------------------------------------------------------------------------------------------------*/
//require_once './modelos/libs/comboLista.php';

?>

<div id="registro">
		<span class="textoAzu112Left2">
		 Sólo se pueden cambiar las cuotas vigentes de la asoción EL del año <strong> 
			  <?php 	echo $cuotasAnioActualEL ['General']['ANIOCUOTA']['valorCampo']+1 //echo date('Y')+1	?>	</strong>
			  previa aprobación por la asamblea en el año anterior <strong><?php echo ($cuotasAnioActualEL['General']['ANIOCUOTA']['valorCampo'])//echo date('Y')?></strong>
					<br /><br />No se permite cambiar las cuotas vigentes del año actual
					<strong><?php echo ($cuotasAnioActualEL['General']['ANIOCUOTA']['valorCampo'])?></strong>, pues habrá socios/as que ya hayan pagado
		     y daría lugar a que quedasen como deudoras/es, si después se incrementasen las cuotas
					<br /><br />
					Al <strong>subir la cuota</strong> para el próximo año:
					<ul> 
					<li>Las socias/os actuales verán incrementada su cuota en el próximo año 
					(si en el año actual tienen elegida una cuota+donación que sea superior a la nueva cuota, mantendrán la cuota que tenían elegida)</li>
					</ul>
					Al <strong>bajar la cuota</strong> (caso poco probable) para el próximo año:
					<ul>
					<li>Las socias/os que previamente habían elegido una cuota+donación superior a la nueva cuota del próximo año, mantendrán la cuota que eligieron previamante para el nuevo año
					<li>Tesorería podría comunicar por email a las socias/os, al inicio del nuevo año, la posibilidad de que reduzcan individualmente las cuotas	</li>
					</ul>
	    <br />		
     <strong>NOTA: antes de efectuar el proceso de cambio de cuotas vigentes, es aconsejable hacer una copia de seguridad de la BBDD		</strong>			
	</span> 	

		<br /><br /><br />		
		
	<!-- ************** Inicio Laica vigentes para el año actual ******************************* -->
	<span class="textoAzu112Left2">
  <b>Cuotas de Europa Laica vigentes para el año actual <?php	echo $cuotasAnioActualEL['General']['ANIOCUOTA']['valorCampo']//echo date('Y')?> (ya no se pueden modificar)</b>
	</span> 
	<br /><br />		
	
		<table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
   <tr bgcolor="#CCCCCC">
    <th  class="textoAzul8L" width="20%"><strong>Tipo cuota</strong></th>						
				<th  class="textoAzul8R" width="15%"><strong>Cuota EL (euros)&nbsp;</strong></th>
    <th  class="textoAzul8L" width="65%"><strong>Descripción</strong></th>  		
   </tr>			
   <?php     

				foreach ($cuotasAnioActualEL as $fila)//original y tambien vale 
 	  { echo ("<tr height='25'>");
				
						echo ("<td class='textoAzul8L'>".$fila['CODCUOTA']['valorCampo']."</td>");
						echo ("<td class='textoAzul8R'>".$fila['IMPORTECUOTAANIOEL']['valorCampo']."&nbsp;&nbsp;&nbsp;"."</td>");   
						echo ("<td class='textoAzul8L'>"."&nbsp;&nbsp;&nbsp;".$fila['DESCRIPCIONCUOTA']['valorCampo']."</td>");						
						echo ("</td>");         						
						echo ("</tr>");
    }	
    ?>
		</table>
		<!-- ************** Fin Laica vigentes para el año actual ********************************** -->
			<br /><br />	
	
	
  <!-- ************** Inicio Laica para el próximo año *************************************** -->	 
		
  <span class="textoAzu112Left2">
		 <b>Cuotas de Europa Laica para el próximo año <?php	echo $cuotasAnioSiguienteEL['General']['ANIOCUOTA']['valorCampo']//date('Y')+?> 
		 (son provisionales y se pueden modificar antes del inicio del año	<?php	echo $cuotasAnioSiguienteEL['General']['ANIOCUOTA']['valorCampo']//date('Y')+1?>)</b>
	 </span> 
	 <br /><br />	
		
		<table width="100%" border="1" cellspacing="0" bordercolor="#99CCFF">
   <tr bgcolor="#CCCCCC">
    <th  class="textoAzul8L" width="20%"><strong>Tipo cuota</strong></th>						
				<th  class="textoAzul8R" width="15%"><strong>Cuota EL (euros)&nbsp;</strong></th>
    <th  class="textoAzul8L" width="57%"><strong>Descripción</strong></th>
				<th  class="textoAzul8L" width="8%"><strong>Modificar</strong></th>
   </tr>
   <?php 
			
			foreach ($cuotasAnioSiguienteEL as  $fila)
 	 {
     echo ("<tr height='25'>");
					
					echo ("<td class='textoAzul8L'>".$fila['CODCUOTA']['valorCampo']."</td>");			
     echo ("<td class='textoAzul8R'>".$fila['IMPORTECUOTAANIOEL']['valorCampo']."&nbsp;&nbsp;&nbsp;"."</td>");   
				 echo ("<td class='textoAzul8L'>"."&nbsp;&nbsp;&nbsp;".$fila['DESCRIPCIONCUOTA']['valorCampo']."</td>");					
     echo ("<td>");
    ?>
				<form method="post" action="./index.php?controlador=cTesorero&accion=actualizarCuotasVigentesELTes">
							
					<input type="image" src="./vistas/images/pluma.gif" value="actualizarCuotasVigentesELTes"
												alt="Actualizar tipo de cuota de EL" name="Actualizar"  
												title="Actualizar tipo de cuota de EL" />

					<input type='hidden' name="datosFormCuotasAnioSiguienteEL[ANIOCUOTA][valorCampo]"
												value='<?php echo $fila['ANIOCUOTA']['valorCampo'];?>' />													
					<input type="hidden"	name="datosFormCuotasAnioSiguienteEL[CODCUOTA][valorCampo]"
												value='<?php echo $fila['CODCUOTA']['valorCampo'];?>' />														
					<input type="hidden"	name="datosFormCuotasAnioSiguienteEL[IMPORTECUOTAANIOEL][valorCampo]"
												value='<?php echo $fila['IMPORTECUOTAANIOEL']['valorCampo'];?>' />	
					<input type="hidden"	name="datosFormCuotasAnioSiguienteEL[DESCRIPCIONCUOTA][valorCampo]"
												value='<?php echo $fila['DESCRIPCIONCUOTA']['valorCampo'];?>' />		
				</form>					
			 <?php
     echo ("</td>");         						
					echo ("</tr>");
    } 
    ?>
			</table>		
			
			<!-- ************** Fin Laica para el próximo año ****************************************** -->

</div>
