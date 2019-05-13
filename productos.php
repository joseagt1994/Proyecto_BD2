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
		$precio = 0.0;
		$stock = 0;

		if(isset($_GET['tipo'])){
			switch($_GET['tipo']){
				case 1: // crear usuario
					$command = new MongoDB\Driver\Command(array('eval' => "siguienteProducto(\"producto\")"));
					$cursor = $m->executeCommand('Proyecto', $command);
					$cod = $cursor->toArray()[0]->retval;
					$bulk->insert(['codigo' => $cod,'nombre' => $_GET['nombre'],
						'precio' => $_GET['precio'],'stock' => $_GET['stock']]);
					$m->executeBulkWrite('Proyecto.productos', $bulk, $writeConcern);
					break;
				case 2: // buscar usuario
					$query = new MongoDB\Driver\Query($filter,$options);
					$rows = $m->executeQuery('Proyecto.productos', $query);
					foreach($rows as $row){
						if($row->codigo == $_GET['mcodigo']){
							$nombre = $row->nombre;
							$precio = $row->precio;
							$stock = $row->stock;
							break;
						}
					}
					break;
				case 3: // modificar usuario
					$c = "db.productos.update({\"codigo\":".$_GET['mcodigo']."},";
					$c = $c . "{\"codigo\":\"".$_GET['mcodigo']."\"";
					if(isset($_GET['mnombre'])){ $c = $c . ",\"nombre\":\"".$_GET['mnombre']."\""; }
					if(isset($_GET['mprecio'])){ $c = $c . ",\"precio\":".$_GET['mprecio'].""; }
					if(isset($_GET['mstock'])){ $c = $c . ",\"stock\":".$_GET['mstock'].""; }
					$c = $c . "});";
					$command = new MongoDB\Driver\Command(array('eval' => $c));
					$cursor = $m->executeCommand('Proyecto', $command);
					break;
				case 4: // borrar usuario
					$command = new MongoDB\Driver\Command(array('eval' => "db.productos.remove({\"codigo\": ".$_GET['bcodigo']."})"));
					$cursor = $m->executeCommand('Proyecto', $command);
					break;
				default: // listar usuario
					$filter = ['nombre' => $_GET['lnombre']];	
					break;
			}	
		}
		$query = new MongoDB\Driver\Query($filter,$options);
		$usuarios = $m->executeQuery('Proyecto.productos', $query);

	}	

?>

<html>

<body>
	<h1> Productos </h1>
	<h2> Crear, editar, ver o eliminar productos </h2>
	
	<h3>Crear un nuevo producto</h3>
	<form>
		<input type="hidden" name="tipo" id="tipo"/>
		Nombre: <input type="text" name="nombre"/> Precio: <input type="text" name="precio"/><br/>
		Stock: <input type="text" name="stock"/> 
		<input type="submit" value="Crear producto" onclick="cambiar(1)"/>


	<h3>Modificar producto</h3>

		Codigo: <input type="text" name="mcodigo" value="<?php echo $codigo ?>"/> <input type="submit" value="Buscar" onclick="cambiar(2)"/> <br/><br/>
		Nombre: <input type="text" name="mnombre" value="<?php echo $nombre ?>"/> Precio: <input type="text" name="mprecio" value="<?php echo $precio ?>"/><br/>
		Stock: <input type="text" name="mstock" value="<?php echo $edad ?>"/>
		<input type="submit" value="Modificar usuario" onclick="cambiar(3)"/>


	<h3>Borrar producto</h3>

		Codigo: <input type="text" name="bcodigo"/> <input type="submit" value="Eliminar" onclick="cambiar(4)"/> <br/><br/>


	<h3>Listado de productos</h3>

		Nombre: <input type="text" name="lnombre"/> <input type="submit" value="Buscar" onclick="cambiar(5)"/> <br/><br/>
		<table style="width:100%">
			<tr>
				<th>Codigo</th>	
				<th>Nombre</th>
				<th>Precio</th>
				<th>Stock</th>
			</tr>
			<?php
				foreach($usuarios as $u){
					echo "<tr>".PHP_EOL."<td>".$u->codigo."</td>".PHP_EOL;
					echo "<td>".$u->nombre."</td>".PHP_EOL;
					echo "<td>".$u->precio."</td>".PHP_EOL;
					echo "<td>".$u->stock."</td>".PHP_EOL;
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
