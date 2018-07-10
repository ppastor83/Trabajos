<?php
	session_start();
	include "header.php";
	//include conexion_sql.php();

	//$_SESSION[k_email]
	/*
	$dbhost_sql = "217.116.2.233";
	$dbusuario_sql = "safa";
	$dbpassword_sql = "SFJ_SIL_40";
	//CONSULTA
	$consultaJefeEstudio = "select grupo from $db.usuarios where email ='".$_SESSION[k_email]"'";
	$resulConsulta = mysql_query($consultaJefeEstudio,$conexion);
	if($resulConsulta == 'Jestudios'){
		echo "Jefe de Estudios";
	}else{
		echo "Profesor";
	}*/
						
	//echo $_SESSION[centro_id];
	//Conecxión Sql Server
	//Acceso a SQL_Server
	ini_set('mssql.charset', 'WINDOWS-1252');
	$dbhost_sql = "217.116.2.232";//80.35.196.106
	$dbusuario_sql = "hermes";
	$dbpassword_sql = "talislanta";
	//$db_sql = "GestionAula";
	//require "GestionAula.php";
	$dbg = GestionAula::getDBCentroProfesorActualizacion($conexion_sql);
	
	
$ConsultaCentro = "select ip from dbo.CENTROS where codigojunta like '".$_SESSION[centro_id]."'"; //11.001.555
$resulCentro = mssql_query($ConsultaCentro,$conexion_sql);
$filaCentro = mssql_fetch_row($resulCentro);
//echo $resulCentro[IP];

	//Conecxión Sql Server
	//Acceso a SQL_Server con la ip del usuario segun centro al que pertenezca
	ini_set('mssql.charset', 'WINDOWS-1252');
	$dbhost_sql = $filaCentro[0];
	$dbusuario_sql = "hermes";
	$dbpassword_sql = "talislanta";
	//$db_sql = "GestionAula";

	$conexion_centro = mssql_connect($filaCentro[0], $dbusuario_sql, $dbpassword_sql);
	
$consultaFaltas = "SELECT inDescripcion as[FALTAS], inNombrealumno as[ALUMNO], inFecha as[Fecha], inHora as [Hora] FROM $dbg.T_IncidenciaRegistro order by FECHA DESC";
$result_faltas = mssql_query($consultaFaltas,$conexion_sql);
$fila_faltas = mssql_fetch_row($result_faltas);

//CONSULTO GRUPOS SEGUN EL CENTRO
$consultaGrupo = "select gr_abrev from $dbg.T_GRUPO"; //"gr_abrev" hace referencia a la abreviatura del grupo/curso

//hacer innerjoin a la tabla tgrupoasignatura; el campo gr_abrev que es = a ag_grupo en la tabla tgrupoasignatura. 
//$consultaGrupo = "select gr_abrev, dbo.T_GRUPO.GR_ABREV from dbo.T_GRUPO inner join dbo.T_GRUPOASIGNATURA on dbo.T_GRUPOASIGNATURA.AG_GRUPO = dbo.T_GRUPO.GR_ABREV where gr_abrev = 'EP1'";
//dentro de tg_rupo_asignatura esta el id_grupoasig 
//hace referencia al resultado de la consulta $resulGrupo
//echo $consultaGrupo;
$resulGrupo = mssql_query($consultaGrupo,$conexion_centro);

$alumnos = explode(",",$_GET[alumnoselegidos]);

function GetDays($sStartDate, $sEndDate){  
     $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));  
     $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));  
 
     $aDays[] = $sStartDate;  

     $sCurrentDate = $sStartDate;   
	 
     while($sCurrentDate < $sEndDate){  
       $sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));  
       $aDays[] = $sCurrentDate;  
     }  

     return $aDays;  
   }
   
   function diferenciaDias($inicio, $fin)
{
    $inicio = strtotime($inicio);
    $fin = strtotime($fin);
    $dif = $fin - $inicio;
    $diasFalt = (( ( $dif / 60 ) / 60 ) / 24);
    return ceil($diasFalt);
}

?>
		<body class="">
        <!-- addCols.php BEGIN HEADER -->
        <header class="page-header">
        </header>
        <!-- END HEADER -->
        <!-- BEGIN CONTAINER -->
		<div class="page-container page-content-inner page-container-bg-solid">
            <!-- BEGIN BREADCRUMBS -->
			<div class ="row">
				<div class="col-md-12">
					<div class="portlet light bordered">
						<div class="portlet-title">
							<div class="caption">
								<h2 class="breadcrumbs-title"><span style="font-size: 30px; display: inline" align="center"> Informes de falta Séneca</span></h2>
							</div>
						</div>
						<?php
						 $_SESSION[k_email];
						
						$dbhost_sql = "217.116.2.233";
						$dbusuario_sql = "safa";
						$dbpassword_sql = "SFJ_SIL_40";
						//CONSULTA
						$consultaJefeEstudio = "select grupo from $dbp.usuarios where email ='".$_SESSION[k_email]."'";
						$resulJE = mysql_query($consultaJefeEstudio,$conexion);
						$filaJE = mysql_fetch_row($resulJE);
						//echo "Soy: ".$filaJE[0];
						if($filaJE[0] == "Jestudios"){
							//TABLA PARA JEFES DE ESTUDIOS
						?>
							<div class="portlet-body">
						<!--
						HACER CONSULTA A LA 233 PARA VER SI EL PROFESOR ES JEFE DE ESTUDIOS.
						AÑADIR IFELSE QUE MUESTRE UNA TABLA DISTINTA SEGÚN RESULTADO
						-->
						
						
							<table align="center">
								<tr>
									 <th> Fecha Inicio </th>
									 <th> Fecha Fin </th>
									 <th> Grupo/s </th>
								</tr>
								<tr>
									  <td>&nbsp;&nbsp;<input value="<?php echo $_GET[fecha_inicial]; ?>" type="date" class="form-control" id="fini" value="" > </td>
									  
									  <td>&nbsp;&nbsp;<input value="<?php echo $_GET[fecha_final]; ?>"  type="date" class="form-control" id="ffin" value="" > </td>
									  
									  <td>&nbsp;&nbsp;<!--<input type="list" id="grupo" value="" >-->
										<select class="form-control selectpicker" id="GRUPOS" name="GRUPOS" form="GRUPOS">
											<option value=" ">Grupo/s</option>
										
										<?php
										/*if(isset($_GET[grupo])){
										
											echo '<option value="'.mb_convert_encoding($gru[0], "UTF-8").'">'.mb_convert_encoding($valoresGrupo[0], "UTF-8").'</option>';
											
										}else{
											
											echo '<option value=" ">Grupo/s</option>';
											
										}*/
											while($valoresGrupo = mssql_fetch_row($resulGrupo)) {
												$obtener_idgrupoasig = "SELECT IDGRUPOASIG FROM $dbg.T_GRUPOASIGNATURA WHERE AG_GRUPO = '$valoresGrupo[0]';";
												$result_idgrupoasig = mssql_query($obtener_idgrupoasig, $conexion_centro);
												$fila_id_grupoasig = mssql_fetch_row($result_idgrupoasig);
												echo '<option value="'.mb_convert_encoding($fila_id_grupoasig[0], "UTF-8").'">'.mb_convert_encoding($valoresGrupo[0], "UTF-8").'</option>';
											}
											
											
										?>
										  
										</select>
									  </td>
									  <!-- <td>&nbsp;&nbsp;<button class="btn btn-default" id="establecer_fecha" type="submit" >Añadir</button></td> -->
								</tr>
							</table>
						</div>
						<?php
						}else{
							//TABLA PARA PROFESORES  
						?>
						<div class="portlet-body">
						
						
							<table>
								<tr>
									 <th> Fecha Inicio </th>
									 <th> Fecha Fin </th>
									 <th> Grupo/s </th>
									 <th> Alumno/s </th>
								</tr>
								<tr>
									  <td>&nbsp;&nbsp;<input value="<?php echo $_GET[fecha_inicial]; ?>" type="date" class="form-control" id="fini" value="" > </td>
									  
									  <td>&nbsp;&nbsp;<input value="<?php echo $_GET[fecha_final]; ?>"  type="date" class="form-control" id="ffin" value="" > </td>
									  
									  <td>&nbsp;&nbsp;<!--<input type="list" id="grupo" value="" >-->
										<select class="form-control"id="GRUPOS" name="GRUPOS" form="GRUPOS">
											<option value=" ">Grupo/s</option>
										
										<?php
										/*if(isset($_GET[grupo])){
										
											echo '<option value="'.mb_convert_encoding($gru[0], "UTF-8").'">'.mb_convert_encoding($valoresGrupo[0], "UTF-8").'</option>';
											
										}else{
											
											echo '<option value=" ">Grupo/s</option>';
											
										}*/
											while($valoresGrupo = mssql_fetch_row($resulGrupo)) {
												$obtener_idgrupoasig = "SELECT IDGRUPOASIG FROM $dbg.T_GRUPOASIGNATURA WHERE AG_GRUPO = '$valoresGrupo[0]';";
												$result_idgrupoasig = mssql_query($obtener_idgrupoasig, $conexion_centro);
												$fila_id_grupoasig = mssql_fetch_row($result_idgrupoasig);
												echo '<option value="'.mb_convert_encoding($fila_id_grupoasig[0], "UTF-8").'">'.mb_convert_encoding($valoresGrupo[0], "UTF-8").'</option>';
											}
										?>
										  
										</select>
									  </td>
									  <td>&nbsp;&nbsp;<!--<input type="list" id="alumno" value="" >-->
										<select multiple class="form-control selectpicker" id="Alumnos" name="Alumnos" form="alumnos">
											<option value=" "></option>
											<?php
													ini_set('mssql.charset', 'WINDOWS-1252');
													$dbhost_sql = "217.116.2.232";
													$dbusuario_sql = "hermes";
													$dbpassword_sql = "talislanta";
													//$db_sql = "GestionAula";
													

												$ConsultaCentro = "select ip from [safa.MDF].[dbo].CENTROS where codigojunta = ".$_SESSION[centro_id].";";
												//echo $ConsultaCentro;
												$resulCentro = mssql_query($ConsultaCentro,$conexion_sql);
												$filaCentro = mssql_fetch_row($resulCentro);
												//echo $resulCentro[IP];
													//Conecxión Sql Server
													//Acceso a SQL_Server con la ip del usuario segun centro al que pertenezca
													ini_set('mssql.charset', 'WINDOWS-1252');
													$dbhost_sql = $filaCentro[0];
													$dbusuario_sql = "hermes";
													$dbpassword_sql = "talislanta";
													//$db_sql = "GestionAula";

													$conexion_centro = mssql_connect($filaCentro[0], $dbusuario_sql, $dbpassword_sql);

														$grupo = $_GET[grupoelegido];
														$obtener_ag_grupo = "SELECT AG_GRUPO FROM $dbg.T_GRUPOASIGNATURA WHERE IDGRUPOASIG = '$grupo';";
														$result_ag_grupo = mssql_query($obtener_ag_grupo, $conexion_centro);
														$fila_ag_grupo = mssql_fetch_row($result_ag_grupo);
														$SqlAlumnos = "SELECT AL_Nombre +' '+AL_apellido1 +' '+AL_apellido2, AL_Nexpediente FROM $dbg.T_ALUMNO WHERE AL_Grupo = '$fila_ag_grupo[0]';";
														//echo $SqlAlumnos;
														
														$resulAlumnos = mssql_query($SqlAlumnos, $conexion_centro);
														
														echo mssql_num_rows($resulAlumnos);
														while ($valoresAlumnos = mssql_fetch_row($resulAlumnos)){ 											
															echo '<option value="'.mb_convert_encoding($valoresAlumnos[1], "UTF-8").'">'.mb_convert_encoding($valoresAlumnos[0], "UTF-8").'</option>';
														}
											?>
										</select>
									  </td>
									  <td>&nbsp;&nbsp;<button class="btn btn-default" id="establecer_fecha" type="submit" >Añadir</button></td>
								</tr>
							</table>
						</div>
<?php						

						}
			?>
						
						
					</div>
				</div>
			</div>
			<div class="breadcrumbs">
			  <div class="col-md-12 col-sm-12">
                <div class="container-fluid">
				
					  <div class="portlet light bordered">
				 <div class="portlet-title">
	 
					</div>
						<div class="portlet-body">
						<!-- EN ESTA TABLA PINTO EL RESULTADO DE LAS CONSULTAS POR NOMBRE | GRUPO | FECHAS |-->
						   <table class="table table-striped table-hover" id="sample_3">
								<thead id="table-head">
									<?php
									
										if(isset($_GET[fecha_inicial]) && isset($_GET[fecha_final]) && isset($_GET[grupoelegido]) && (  isset($_GET[alumnoselegidos]) || isset($_GET[JE]))){
											$fecha_inicial = $_GET[fecha_inicial];
											if(  isset($_GET[alumnoselegidos])) {
												$alumnos = explode(",", $_GET[alumnoselegidos]);
											}else if(isset($_GET[JE])){ 
												$alumnos = array();
												$obtenerAlumnos = "SELECT DISTINCT AA_Alumno FROM $dbg.T_ALUMNOASIGNATURA INNER JOIN $dbg.T_GRUPOASIGNATURA ON $dbg.T_GRUPOASIGNATURA.AG_GRUPO = $dbg.T_ALUMNOASIGNATURA.AA_GRUPO WHERE $dbg.T_GRUPOASIGNATURA.IDGRUPOASIG = '$_GET[grupoelegido]';";
												
												//$obtenerAlumnos = "SELECT DISTINCT AA_Alumno FROM $dbg.T_GRUPOASIGNATURA INNER JOIN $dbg.T_GRUPO ON $dbg.T_GRUPO.GR_NOMBRECOTO = $dbg.T_GRUPOASIGNATURA.AG_GRUPO WHERE $dbg.T_GRUPO.IDGRUPOASIG = '$_GET[grupoelegido]';";
												//echo $obtenerAlumnos;
												$resulAlumnos = mssql_query($obtenerAlumnos, $conexion_sql);
												while($fila_alumnos = mssql_fetch_row($resulAlumnos)){
													array_push($alumnos,$fila_alumnos[0]);
												}
											}
											
											$fecha_ini = jddayofweek($fecha_inicial,1);
											$fecha_fin = $_GET[fecha_final];
											$dias_diff = diferenciaDias($fecha_inicial, $fecha_final);
											$dias_diff++;
											//echo " | ".$dias_diff."  |   ";
											$dias = $_GET[diff];
											$dia_inicio = $_GET[diaIni];
											$mes_inicio =$_GET[mesIni];
											$anio_inicio = $_GET[anioIni];
											$nueva_fecha = $dia_inicio."/".$mes_inicio."/".$anio_inicio;
											$dia_semana = jddayofweek($nueva_fecha,0);
									?>
	<!--</tr>-->
	<tr>
		<th>Nombre</th>
		<th>Grupo</th>
		
	<!-- PINTO LA FECHA POR RANGO DE DÍAS -->	
	<?php
	//$i=1;
		$periodo = GetDays($fecha_inicial, $fecha_fin);
		foreach($periodo as $dia){
			$fecha = $dia;
			$fecha = date("D", strtotime($dia));
				switch($fecha){
					case "Mon":
						echo "<th>".$dia."</th>";
					break;
					case "Tue":
						echo "<th>".$dia."</th>";
					break;
					case "Wed":
						echo "<th>".$dia."</th>";
					break;
					case "Thu":
						echo "<th>".$dia."</th>";
					break;	
					case "Fri":
						echo "<th>".$dia."</th>";
					break;
				}
		}
	}
	?>
	
	</tr>
	
								  <!-- <tr >
									   <th> # </th>
										<th> Nombre  </th>
										<th> Lunes </th>
										<th> Martes </th>
										<th> Miércoles </th>
										<th> Jueves </th>
										<th> Viernes </th> 
									</tr> -->
									
								</thead>
							   <tbody id="table-body">
							   <?php
							   $dbhost_sql = "217.116.2.232";
							   $dbusuario_sql = "hermes";
							   $dbpassword_sql = "talislanta";
								//$db_sql = "GestionAula";
			
		$ConsultaCentro = "select ip from [safa.MDF].[dbo].CENTROS where codigojunta = ".$_SESSION[centro_id].";";
		//echo $ConsultaCentro;
		$resulCentro = mssql_query($ConsultaCentro,$conexion_sql);
		$filaCentro = mssql_fetch_row($resulCentro);
		//echo $resulCentro[IP];
			//Conecxión Sql Server
			//Acceso a SQL_Server con la ip del usuario segun centro al que pertenezca
			ini_set('mssql.charset', 'WINDOWS-1252');
			$dbhost_sql = $filaCentro[0];
			$dbusuario_sql = "hermes";
			$dbpassword_sql = "talislanta";
			//$db_sql = "GestionAula";

			$conexion_centro = mssql_connect($filaCentro[0], $dbusuario_sql, $dbpassword_sql);
			//PINTAR LOS GRUPOS
			
		$grupo = $_GET[grupoelegido];
		//echo "Grupo elegido ".$grupo;
		$obtener_idgrupoasig = "SELECT AG_GRUPO FROM $dbg.T_GRUPOASIGNATURA WHERE IDGRUPOASIG = '$grupo';";
		$result_idgrupoasig = mssql_query($obtener_idgrupoasig, $conexion_sql);
		$fila_id_grupoasig = mssql_fetch_row($result_idgrupoasig);	
		
			
		foreach($alumnos as $alumno){
			$obtener_alumnos = "SELECT AL_Nombre +' '+AL_apellido1 +' '+AL_apellido2 FROM $dbg.T_ALUMNO WHERE AL_Nexpediente = '$alumno';";
			$result_alumnos = mssql_query($obtener_alumnos, $conexion_sql);
			//CONSULTO LAS FALTAS			
			while($fila_alumnos = mssql_fetch_row($result_alumnos)){
				?>
					<tr>
						<td><?php echo mb_convert_encoding($fila_alumnos[0], "UTF-8"); ?></td>
						
						<td><?php echo mb_convert_encoding($fila_id_grupoasig[0], "UTF-8"); ?></td>

						<!-- HACER CONSULTA PARA QUE ME DEVUELVA LAS FALTAS DE LOS ALUMNOS Y PONERLOS EN EL <td> CORRESCPONDIENTE </td> -->
						
						
						<?php
						
							foreach($periodo as $dia){
								$fecha = $dia;
								$fecha = date("D", strtotime($dia));
									switch($fecha){
										case "Mon":
											//echo "<th>".$result_faltas[0]."</th>";

										case "Tue":
											//echo "<th>".$result_faltas[0]."</th>";
		
										case "Wed":
											//echo "<th>".$result_faltas[0]."</th>";
									
										case "Thu":
											//echo "<th>".$result_faltas[0]."</th>";
										
										case "Fri":
											//echo "<th>".$result_faltas[0]."</th>";
											$obtener_faltas = "SELECT inDescripcion as[FALTAS], inNombreAsignatura, inNombrealumno as[ALUMNO], inFecha as[Fecha_Falta], inHora as [Hora], id as [ID] FROM $dbg.T_IncidenciaRegistro  WHERE inAlumno = '".$alumno."' and CONVERT(char(10), $dbg.T_IncidenciaRegistro.inFecha, 120) = '$dia';";
											$result_faltas = mssql_query($obtener_faltas, $conexion_centro);
											?><td>  <?php //echo "Faltas: ".$obtener_faltas;
											while($fila_faltas = mssql_fetch_row($result_faltas)){
												echo "[".$fila_faltas[0]."] | [" .  mb_convert_encoding($fila_faltas[1],"utf-8") ."]<br>";
											}
											?> </td><?php
										break;
									}
							}
						?>
					</tr>
				<?php
			}
		}
							?>   
								</tbody>
							</table>
						</div>
					</div>
				</div>
              </div>
					</div>
                </div>
				
            </div>


<?php	
	include "footer.php";
	?>
	
	<script>
		
		
		$("#fini").on("change", function(){
			var fecha_inicio = $(this).val();
			location.href = "informes_faltas_seneca.php?fecha_inicial="+fecha_inicio;
		});
		$("#ffin").on("change", function(){
			var fecha_fin = $(this).val();
			var fecha_inicio = $_GET("fecha_inicial");
			location.href = "informes_faltas_seneca.php?fecha_inicial="+fecha_inicio+"&fecha_final="+fecha_fin;
		});
		<?php
		$consultaJefeEstudio = "select grupo from $dbp.usuarios where email ='".$_SESSION[k_email]."'";
						$resulJE = mysql_query($consultaJefeEstudio,$conexion);
						$filaJE = mysql_fetch_row($resulJE);
						if($filaJE[0] == "Jestudios"){
							
						
		?>
		$("#GRUPOS").on("change", function(){
			 var grupo = $(this).val();
			 var fecha_inicio = $_GET("fecha_inicial");
			 var fecha_fin = $_GET("fecha_final");
			 location.href = "informes_faltas_seneca.php?fecha_inicial="+fecha_inicio+"&fecha_final="+fecha_fin+"&grupoelegido="+grupo+"&JE=1";
			 
		});
		<?php
						}else{
		?>
		$("#GRUPOS").on("change", function(){
			 var grupo = $(this).val();
			 var fecha_inicio = $_GET("fecha_inicial");
			 var fecha_fin = $_GET("fecha_final");
			 location.href = "informes_faltas_seneca.php?fecha_inicial="+fecha_inicio+"&fecha_final="+fecha_fin+"&grupoelegido="+grupo;
			 
		});
		<?php
						}
		?>
		$("#establecer_fecha").on("click", function(){
			var establecer = $(this).val();
			var fecha_inicio = $_GET("fecha_inicial");
			var fecha_fin = $_GET("fecha_final");
			var grupo_elegido = $_GET("grupoelegido");
			var alumnos = $("#Alumnos").val();
			
			var array_fecha_inicio = fecha_inicio.split("-");
			var array_fecha_fin = fecha_fin.split("-");
			
			if(array_fecha_inicio[0] <= array_fecha_fin[0]){
					comprobaranio = true;
				}else{
					comprobar_fecha = true;
				}
				
				if(array_fecha_inicio[1] <= array_fecha_fin[1] && comprobaranio){
					comprobarmes = true;
				}else{
					comprobar_fecha = true;
				}
				
				if(array_fecha_inicio[2] <= array_fecha_fin[2] && comprobarmes && comprobaranio){
					comprobardia = true;
				}else{
					if (array_fecha_inicio[1] < array_fecha_fin[1]){
						
						comprobardia = true;
					}
				}
				if(comprobaranio && comprobarmes && comprobardia){
					location.href = "informes_faltas_seneca.php?fecha_inicial="+fecha_inicio+"&fecha_final="+fecha_fin+"&grupoelegido="+grupo_elegido+"&alumnoselegidos="+alumnos;
					//alert("Orden de fechas correctas");
				}else{
					
					location.href = "informes_faltas_seneca.php?fecha_inicial="+fecha_inicio+"&fecha_final="+fecha_fin+"&grupoelegido="+grupo_elegido+"&alumnoselegidos="+alumnos;
					
					alert("No puede seleccionar una fecha de inicio menor a la fecha final");
				}
				
				
		});
		
	</script>
</body>
</html>

