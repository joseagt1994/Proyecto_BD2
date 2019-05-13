<?php 
	session_start();

	if(!isset($_SESSION['login'])){
		$_SESSION['Redirect'] = $_SERVER['REQUEST_URL'];
		header('Location: index.php');
	}else{
		if(isset($_GET['tipo'])){
			if($_GET['tipo'] == 1){ header('Location: usuarios.php'); }
			else if($_GET['tipo'] == 2){ header('Location: productos.php'); }
			else{ unset($_SESSION['login']); header('Location: index.php'); }
		}
	}
	
	//print_r($_SESSION);

?>
<html>

<body>
	<h1> Menu Principal </h1>
	<p> Bienvenido <?php echo $_SESSION['login']?>!</p>
	
	<form action="<?php $_PHP_SELF?>" method="GET">
		<input type="hidden" id="tipo" name="tipo"/>
		<input type="submit" value="Usuarios" onclick="cambiar(1)"/>
		<input type="submit" value="Productos" onclick="cambiar(2)"/>
		<input type="submit" value="Cerrar sesion" onclick="cambiar(3)"/>	
	</form>	
	
</body>

<script>
	function cambiar(valor){
		document.getElementById("tipo").value = valor;
	}
</script>

</html>
