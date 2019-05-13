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
					print_r("Usuario: ".$_GET['mcodigo']);
					$filter = ['codigo' => $_GET['mcodigo']];
					$query = new MongoDB\Driver\Query($filter,$options);
					$rows = $m->executeQuery('Proyecto.usuarios', $query);
					print_r($rows);
					foreach($rows as $row){
						$nombre = $row->nombre;
						$apellido = $row->apellido;
						$edad = $row->edad;
						$usuario = $row->nickname;
						$pass = $row->password;
						print_r( "Exito!" );
						break;
					}
					$filter = [];
					break;
				case 3: // modificar usuario
					$bulk->update(['codigo' => $_GET['mcodigo']], ['$set' => ['nombre' => $_GET['mnombre']]]);
					$bulk->update(['codigo' => $_GET['mcodigo']], ['$set' => ['apellido' => $_GET['mapellido']]]);
					$bulk->update(['codigo' => $_GET['mcodigo']], ['$set' => ['edad' => $_GET['medad']]]);
					$bulk->update(['codigo' => $_GET['mcodigo']], ['$set' => ['nickname' => $_GET['musuario']]]);
					$bulk->update(['codigo' => $_GET['mcodigo']], ['$set' => ['password' => $_GET['mpassword']]]);
					$m->executeBulkWrite('Proyecto.usuarios', $bulk, $writeConcern);
					break;
				case 4: // borrar usuario
					print_r("Eliminando usuario ".$_GET['bcodigo']);
					$bulk->delete(['codigo' => $_GET['bcodigo']]);
					$m->executeBulkWrite('Proyecto.usuarios', $bulk, $writeConcern);
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

		Codigo: <input type="text" name="mcodigo"/> <input type="submit" value="Buscar" onclick="cambiar(2)"/> <br/><br/>
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
