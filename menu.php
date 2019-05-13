<?php 
	session_start();

	if(!Isset($_SESSION['login'])){
		$_SESSION['Redirect'] = $_SERVER['REQUEST_URL'];
		header('Location: index.php');
	}
	
?>
<html>

<body>
	<h1> Menu Principal </h1>
	<p> Bienvenido! </p><?php $_SESSION['login']?>
</body>

</html>
