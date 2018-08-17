<!DOCTYPE html>
<html>
<head>
	<title>r/proceduralgeneration Logo Generator</title>
	<style type="text/css">
		body{
			text-align: center;
			width: 100%;
			padding: 0;
			margin: 0;
			background: #eeeeee;
			color: #333333;
		}
		a{
			color: #808bff;
		}
		a:hover{
			color: #ff808b;
		}
		a:visited{
			color: #b580ff;
		}
		input{
			width: 300px;
		}
		.list{
			position: relative;
			text-align: center;
			width: 100%;
			padding: 0;
			margin: 0;
		}
		.list .logo{
			width: 380px;
			padding: 15px;
			position: relative;
			display: inline-block;
		}
	</style>
</head>
<body>
<br>
<h1>r/proceduralgeneration Logo Generator</h1>
<small>by ciaccodavide (u/forkafork)</small>
<br><br><br>
	<div>
		<img src="./logo.php"><br>
		<input type="text" value="https://ciaccodavi.de/procgen/reddit/logo/logo.php">
		<br>
		<small><b>Source code</b>: <a href="https://github.com/CiaccoDavide/procgen-reddit-logo">GitHub</a></small><br>
		<small><b>Options</b>: ps (pixel size), id (seed)</small><br>
		<small><b>Usage</b>: logo.php to get a random logo,<br>logo.php?ps=2 to multiply the default pixel size (120x40),<br>logo.php?ps=1&id=1337 use a seed to load always the same logo</small><br>
		<br>
		<br>
	</div>
	<div class="list">
	<?php
		$n = isset($_GET['n']) ? $_GET['n'] : 10;

		for (; $n > 0; $n--) {
			$rand = mt_rand(0, 10000000);
			echo '<div class="logo">
			<img src="./logo.php?ps=3&id='.$rand.'"><br>Seed: '.$rand.' Resolution: <a href="./logo.php?id='.$rand.'" target="_blank">120x40</a>
			 | <a href="./logo.php?ps=2&id='.$rand.'" target="_blank">x2</a>
	 		 | <a href="./logo.php?ps=3&id='.$rand.'" target="_blank">x3</a>
	 		 | <a href="./logo.php?ps=4&id='.$rand.'" target="_blank">x4</a>
	 		 | <a href="./logo.php?ps=5&id='.$rand.'" target="_blank">x5</a>
	 		 | <a href="./logo.php?ps=6&id='.$rand.'" target="_blank">x6</a>
	 		 </div>
			 <br><br><br>';
		}
	?>
	</div>
</body>
</html>