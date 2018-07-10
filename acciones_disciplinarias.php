<?php
	session_start();
	include "header.php";
	//include conexion_sql.php();
	

	//Conecxión Sql Server
	//Acceso a SQL_Server
	ini_set('mssql.charset', 'WINDOWS-1252');
	$dbhost_sql = "217.116.2.232";//80.35.196.106
	$dbusuario_sql = "hermes";
	$dbpassword_sql = "talislanta";
	//$db_sql = "GestionAula";
	//require "GestionAula.php";
	$dbg = GestionAula::getDBCentroProfesorActualizacion($conexion_sql);
	
		//casteo la variable a entero para guardarla en la variable php
		$desc = (int)$_GET[ob];
		$odb = $_GET[orderby];
		switch($odb){
			case "fecha":
				switch($desc){
					case 0:
					$orderBy = "ad_fecha desc";
					break;
					
					case 1:
					$orderBy = "ad_fecha asc";
					break;
				}
			break;

			case "alumno":
				switch($desc){
					case 0:
					$orderBy = "ad_alumno desc";
					break;
					
					case 1:
					$orderBy = "ad_alumno asc";
					break;
				}
			break;

			case "grupo":
				switch($desc){
					case 0:
					$orderBy = "ad_grupo desc";
					break;
					
					case 1:
					$orderBy = "ad_grupo asc";
					break;
				}
			break;
		
			case "observacion":
				switch($desc){
					case 0:
					$orderBy = "ad_observacion desc";
					break;
					
					case 1:
					$orderBy = "ad_observacion asc";
					break;
				}
			break;
			
			default:
				switch($desc){
					case 0:
					$orderBy = "ad_fecha desc";
					break;
					
					case 1:
					$orderBy = "ad_fecha asc";
					break;
				}
			break;
		}
	
	
	
	
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
								<h2 class="breadcrumbs-title"><span style="font-size: 30px; display: inline"><strong>Acciones disciplinarias</strong></span></h2>
							</div>
						</div>
						
						<div align="center" class="portlet-body">						
							<table>
								<tr>
									 <th> Fecha Inicio </th>
									 <th> Fecha Fin </th>
								</tr>
								<tr>
									  <td>&nbsp;&nbsp;<input value="<?php echo $_GET[fecha_inicial]; ?>" type="date" class="form-control" id="fini" value="" > </td>
									  
									  <td>&nbsp;&nbsp;<input value="<?php echo $_GET[fecha_final]; ?>"  type="date" class="form-control" id="ffin" value="" > </td>
										<!-- BOTÓN AÑADIR NO HACE FALTA -->
									  <!-- <td>&nbsp;&nbsp;<button class="btn btn-default" id="establecer_fecha" type="submit" >Añadir</button></td>-->
								</tr>
							</table>
						</div>
						
						
					</div>
				</div>
			</div>
			<div class="breadcrumbs">
			  <div class="col-md-12 col-sm-12">
                <div class="container-fluid">
				 <div class="portlet light bordered">
				  <div class="portlet-title">
					<div class="portlet-body">
													
						<!-- EN ESTA TABLA PINTO EL RESULTADO DE LAS CONSULTAS POR FECHAS | NOMBRE | GRUPO | OBSERVACIÓN |-->
						   <table class="table table-striped table-hover table-bordered tablesorter" id="sample_3">
						    <div class="portlet light bordered">
								<thead id="table-head">
								<?php
									
										if(isset($_GET[fecha_inicial]) && isset($_GET[fecha_final])){
											$fecha_inicial = $_GET[fecha_inicial];
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
										<th id="sorter" value="fecha" class="header"  width="6%">Fecha</th>
										<th id="sorter" value="alumno" class="header">Alumno</th>
										<th id="sorter" value="grupo" class="header">Grupo</th>
										<th id="sorter" value="observacion" class="header">Observación</th>
										<th id="sorter" value="observacion" class="header">Tipo de falta</th>
										<th id="sorter" value="imprimir" class="header">Imprimir</th>
										
									<!-- PINTO LA FECHA POR RANGO DE DÍAS -->
									<?php
										$periodo = GetDays($fecha_inicial, $fecha_fin);
									?>
									</tr>
								</thead>
							   <tbody id="table-body">
							   <?php
									//PINTAR LOS GRUPOS

							
								$fecha = $dia;
								$fecha = date("D", strtotime($dia));
									switch($fecha){
										case "Mon":

										case "Tue":
		
										case "Wed":
									
										case "Thu":
										
										case "Fri":
									$obtenerAC = "SELECT AD_Observacion, ad_nombrealumno,  idAccionDisciplinaria, ad_grupo, CONVERT(char(10), ad_fecha, 120) FROM $dbg.T_AccionesDisciplinarias where CONVERT(char(10), ad_fecha, 120) >='$_GET[fecha_inicial]' AND CONVERT(char(10), ad_fecha, 120) <='$_GET[fecha_final]' order by $orderBy";
									
									//$obtenerAC = "SELECT AD_Observacion, ad_nombrealumno, idAccionDisciplinaria, ad_grupo, CONVERT(char(10), ad_fecha, 120) FROM $dbg.T_AccionesDisciplinarias order by ad_fecha desc";
									$resultAC = mssql_query($obtenerAC, $conexion_sql);
										while ($filaAC = mssql_fetch_row($resultAC)){
											$nombreAlumno = mb_convert_encoding($filaAC[1],  "UTF-8");
											$fechaAC = mb_convert_encoding($filaAC[4], "UTF-8");
											$fechaIncidencia = mb_convert_encoding($fechaAC,"UTF-8");
											$fecha = date("d-M-Y", strtotime($fechaIncidencia));
											$observacion = mb_convert_encoding($filaAC[0],  "UTF-8");
											$grupoActual = mb_convert_encoding($filaAC[3],  "UTF-8");
											echo "<tr><td>$fecha</td><td>$nombreAlumno</td><td>$grupoActual</td><td>$observacion</td>
											<td>
											<radiogroup>
											  <input class='tipo' type='checkbox' value='CONTRARIA' checked>Contraria<br>
											  <input class='tipo' type='checkbox' value='GRAVE'>Grave
											 </radiogroup> 
											</td>
											<td><button type='button' data-id-ac='$filaAC[2]' class='btn red btn-outline'>Imprimir PDF</button></td></tr>";
										
										
									}
										break;
									}
										}
							?>
					</tr>
										
								</tbody>

							</table>
						</div>
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
	//ORDENAR TABLA POR COLUMNAS
		$("#sample_3").find("thead").find("tr").each(function(){
			$(this).find("th").on("click", function(){
				var idCabecera = $(this).attr("value");
				  //alert("Ha clickado en: "+idCabecera);
				  if($_GET("ob") != undefined && $_GET("ob") != ""){
					  switch (parseInt($_GET("ob"))){
						  case 0:
							if($_GET("orderby") == $(this).attr("value")){
								location.href = "acciones_disciplinarias.php?fecha_inicial="+$_GET("fecha_inicial")+"&fecha_final="+$_GET("fecha_final")+"&orderby="+idCabecera+"&ob=0"; 
							}else{
								location.href = "acciones_disciplinarias.php?fecha_inicial="+$_GET("fecha_inicial")+"&fecha_final="+$_GET("fecha_final")+"&orderby="+idCabecera+"&ob=1"; 
							}
						  break;
						  
						  case 1:
							if($_GET("orderby") == $(this).attr("value")){
								location.href = "acciones_disciplinarias.php?fecha_inicial="+$_GET("fecha_inicial")+"&fecha_final="+$_GET("fecha_final")+"&orderby="+idCabecera+"&ob=0"; 
							}else{
								location.href = "acciones_disciplinarias.php?fecha_inicial="+$_GET("fecha_inicial")+"&fecha_final="+$_GET("fecha_final")+"&orderby="+idCabecera+"&ob=1"; 
							}
						  break;
					  }
				  }else{
					  location.href = "acciones_disciplinarias.php?fecha_inicial="+$_GET("fecha_inicial")+"&fecha_final="+$_GET("fecha_final")+"&orderby="+idCabecera+"&ob=1"; 
				  }
				
							
			});
		});
			
			
		$("#fini").on("change", function(){
			var fecha_inicio = $(this).val();
			location.href = "acciones_disciplinarias.php?fecha_inicial="+fecha_inicio;
		});
		$("#ffin").on("change", function(){
			var fecha_fin = $(this).val();
			var fecha_inicio = $_GET("fecha_inicial");
			location.href = "acciones_disciplinarias.php?fecha_inicial="+fecha_inicio+"&fecha_final="+fecha_fin;
		});
		
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
					location.href = "acciones_disciplinarias.php?fecha_inicial="+fecha_inicio+"&fecha_final="+fecha_fin;
					alert("Orden de fechas correctas");
				}else{
					
					location.href = "acciones_disciplinarias.php?fecha_inicial="+fecha_inicio+"&fecha_final="+fecha_fin;
					
					alert("No puede seleccionar una fecha de inicio menor a la fecha final");
				}
				
				
		});
		
		//tabla_informacion_ac
			$("#sample_3").find("tbody").find("tr").each(function(){
				$(this).find("td:eq(5)").find("button").on("click", function(){
					var idAccionDisciplinaria = $(this).attr("data-id-ac");
					var tipoFalta = document.querySelector('.tipo:checked').value;
					//alert(tipoFalta);
					  window.open('./tcpdf/examples/generar_acciones_disciplinarias.php?id='+idAccionDisciplinaria+'&tipoF='+tipoFalta, '_blank');
				//});
				});
			});
		
			
	</script>
</body>
</html>