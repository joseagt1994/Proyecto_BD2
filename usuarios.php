<?php
	session_start();
	
	if(!isset($_SESSION['login'])){
		$_SESSION['Redirect'] = $_SERVER['REQUEST_URL'];
		header('Location: index.php');
	}else{
		$m = new MongoDB\Driver\Manager("mongodb://192.168.1.24:27017");
		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		
		// Listar todos los usuarios para mostrar
		$filter = [];
		$options = [];

		// atributos
		$codigo = $_GET['mcodigo'];
		$nombre = "";
		$apellido = "";
		$edad = "";
		$usuario = "";
		$pass = "";

		if(isset($_GET['tipo'])){
			switch($_GET['tipo']){
				case 1: // crear usuario
					$command = new MongoDB\Driver\Command(array('eval' => "siguienteUsuario(\"usuario\")"));
					$cursor = $m->executeCommand('Proyecto', $command);
					$cod = $cursor->toArray()[0]->retval;
					$bulk->insert(['codigo' => $cod,'nombre' => $_GET['nombre'],
						'apellido' => $_GET['apellido'],'edad' => $_GET['edad'],'nickname' => $_GET['usuario'],
						'password' => $_GET['password']]);
					$m->executeBulkWrite('Proyecto.usuarios', $bulk, $writeConcern);
					break;
				case 2: // buscar usuario
					$query = new MongoDB\Driver\Query($filter,$options);
					$rows = $m->executeQuery('Proyecto.usuarios', $query);
					foreach($rows as $row){
						if($row->codigo == $_GET['mcodigo']){
							$nombre = $row->nombre;
							$apellido = $row->apellido;
							$edad = $row->edad;
							$usuario = $row->nickname;
							$pass = $row->password;
							break;
						}
					}
					break;
				case 3: // modificar usuario
					$c = "db.usuarios.update({\"codigo\":".$_GET['mcodigo']."},";
					$c = $c . "{\"codigo\":\"".$_GET['mcodigo']."\"";
					if(isset($_GET['mnombre'])){ $c = $c . ",\"nombre\":\"".$_GET['mnombre']."\""; }
					if(isset($_GET['mapellido'])){ $c = $c . ",\"apellido\":\"".$_GET['mapellido']."\""; }
					if(isset($_GET['medad'])){ $c = $c . ",\"edad\":\"".$_GET['medad']."\""; }
					if(isset($_GET['musuario'])){ $c = $c . ",\"nickname\":\"".$_GET['musuario']."\""; }
					if(isset($_GET['mpassword'])){ $c = $c . ",\"password\":\"".$_GET['mpassword']."\""; }
					$c = $c . "});";
					$command = new MongoDB\Driver\Command(array('eval' => $c);
					$cursor = $m->executeCommand('Proyecto', $command);
					break;
				case 4: // borrar usuario
					$command = new MongoDB\Driver\Command(array('eval' => "db.usuarios.remove({\"codigo\": ".$_GET['bcodigo']."})"));
					$cursor = $m->executeCommand('Proyecto', $command);
					break;
				default: // listar usuario
					$filter = ['nombre' => $_GET['lnombre'],'apellido' => $_GET['lapellido']];	
					break;
			}	
		}
		$query = new MongoDB\Driver\Query($filter,$options);
		$usuarios = $m->executeQuery('Proyecto.usuarios', $query);

	}	

?>

<html>

<body>
	<h1> Usuarios </h1>
	<h2> Crear, editar, ver o eliminar usuarios </h2>
	
	<h3>Crear un nuevo usuario</h3>
	<form>
		<input type="hidden" name="tipo" id="tipo"/>
		Nombre: <input type="text" name="nombre"/> Apellido: <input type="text" name="apellido"/><br/>
		Edad: <input type="text" name="edad"/> Usuario: <input type="text" name="usuario"/> Password: <input type="text" name="password"/>
		<input type="submit" value="Crear usuario" onclick="cambiar(1)"/>


	<h3>Modificar usuario</h3>

		Codigo: <input type="text" name="mcodigo" value="<?php echo $codigo ?>"/> <input type="submit" value="Buscar" onclick="cambiar(2)"/> <br/><br/>
		Nombre: <input type="text" name="mnombre" value="<?php echo $nombre ?>"/> Apellido: <input type="text" name="mapellido" value="<?php echo $apellido ?>"/><br/>
		Edad: <input type="text" name="medad" value="<?php echo $edad ?>"/> Usuario: <input type="text" name="musuario" value="<?php echo $usuario ?>"/> Password: <input type="text" name="mpassword" value="<?php echo $pass ?>"/>
		<input type="submit" value="Modificar usuario" onclick="cambiar(3)"/>


	<h3>Borrar usuario</h3>

		Codigo: <input type="text" name="bcodigo"/> <input type="submit" value="Eliminar" onclick="cambiar(4)"/> <br/><br/>


	<h3>Listado de usuarios</h3>

		Nombre: <input type="text" name="lnombre"/> Apellido: <input type="text" name="lapellido"/> <input type="submit" value="Buscar" onclick="cambiar(5)"/> <br/><br/>
		<table style="width:100%">
			<tr>
				<th>Codigo</th>	
				<th>Nombre</th>
				<th>Apellido</th>
				<th>Edad</th>
				<th>Usuario</th>
				<th>Password</th>
			</tr>
			<?php
				foreach($usuarios as $u){
					echo "<tr>".PHP_EOL."<td>".$u->codigo."</td>".PHP_EOL;
					echo "<td>".$u->nombre."</td>".PHP_EOL;
					echo "<td>".$u->apellido."</td>".PHP_EOL;
					echo "<td>".$u->edad."</td>".PHP_EOL;
					echo "<td>".$u->nickname."</td>".PHP_EOL;
					echo "<td>".$u->password."</td>".PHP_EOL."</tr>";
				} 
			?>
			
		</table>
	</form>

</body>

<script>
	function cambiar(valor){
		document.getElementById("tipo").value = valor;
	}
</script>

</html>
