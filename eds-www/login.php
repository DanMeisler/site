<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(!empty($_POST["username"]) && !empty($_POST["password"])) {
		$username = $_POST["username"];
		$password = $_POST["password"];
		$python = 'C:\\Python\Python35\\python.exe';
		$pyscript = 'C:\\gpsServer\\login.py';
		$cmd = "$python $pyscript $username $password";
		exec("$cmd",$output,$retVal);
		if($retVal == 0) {
			session_start();
			$_SESSION["authenticated"] = 'true';
			header('Location: index.php');
		}
		else {
			header('Location: login.php');
		}
		
	} else {
		header('Location: login.php');
	}
} else {
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Login</title>
		<link rel="stylesheet" href="sources/css/style.css">
	</head>
	<body>
		<hgroup>
			<h1>Login</h1>
			<h3>gps server</h3>
		</hgroup>
		<form method="post">
			<div class="group">
				<input placeholder="Username" name="username" type="text">
				<span class="highlight"></span>
				<span class="bar"></span>
			</div>
			<div class="group">
				<input placeholder="Password" name="password" type="password">
				<span class="highlight"></span>
				<span class="bar"></span>
			</div>
			<button type="submit" class="button buttonBlue">login
				<div class="ripples buttonRipples">
					<span class="ripplesCircle"></span>
				</div>
			</button>
		</form>
		<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('/images/logo.png');">
		</div>
	</body>
</html>
<?php } ?>