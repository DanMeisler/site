<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(!empty($_POST["username"]) && !empty($_POST["password"])) {
		$username = $_POST["username"];
		$password = $_POST["password"];
		// connect
		try{
			$m = new MongoClient();
		} catch(MongoConnectionException $e)
		{
			die();
		};
		// select a database
		$db = $m->gpsDB;
		
		// select a collection (analogous to a relational database's table)
		$collection = $db->users;
		$user = $collection->findOne(array('username' => $username, 'password' => $password));
		$m->close();
		if($user) {
			session_start();
			$_SESSION["authenticated"] = 'true';
			if($user["isAdmin"])
			{
				$_SESSION["isAdmin"] = 'true';
			}
			else
			{
				$_SESSION["isAdmin"] = 'false';
			}
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