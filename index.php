<?php 

	session_start(); 

	if($_GET["usuario"] || $_GET["password"]){

		$m = new MongoDB\Driver\Manager("mongodb://192.168.1.24:27017");
		$filter = ['nickname' => $_GET["usuario"], 'password' => $_GET["password"]];
		$options = [];
		$query = new MongoDB\Driver\Query($filter,$options);
		$rows = $m->executeQuery('Proyecto.usuarios', $query);

		if($rows->isDead()){ echo "El usuario no existe! Ingrese nuevamente."; }
		else{
			foreach($rows as $r){
				//echo "Usuario: " . $r->nickname;			
				$_SESSION['login'] = $r->nickname;
			}
			header("Location: menu.php");
		}

		//echo "Usuario: " . $rows . PHP_EOL;

	}else { //echo "Ingrese los datos necesarios para ingresar!"; }

?>

<html>

<body>
	<form action="<?php $_PHP_SELF?>" method="GET">

		Usuario: <input type="text" name="usuario"/><br/>
		Password: <input type="password" name="password"/><br/>
		</br>
		<input type="submit" value="Ingresar" />

	</form>
</body>

</html>
