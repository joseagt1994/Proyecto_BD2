<?php 
	session_start();

	if(!isset($_SESSION['login'])){
		$_SESSION['Redirect'] = $_SERVER['REQUEST_URL'];
		header('Location: index.php');
	}
	
	print_r($_SESSION);

?>
<html>

<body>
	<h1> Menu Principal </h1>
	<p> Bienvenido! </p><?php $_SESSION['login']?>

	<button>Usuarios</button>
	<button>Productos</button>	

</body>

</html>
