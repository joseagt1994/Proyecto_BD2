<?php
	session_start();
	
	if(!isset($_SESSION['login'])){
		$_SESSION['Redirect'] = $_SERVER['REQUEST_URL'];
		header('Location: index.php');
	}else{
		
	}	

?>

<html>

<body>
	<h1> Productos </h1>
	<h2> Crear, editar, ver o eliminar productos </h2>
</body>

</html>
