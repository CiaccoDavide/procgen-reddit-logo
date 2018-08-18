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
			width: 100%;
			padding: 15px;
			position: relative;
			display: inline-block;
		}
	</style>
</head>
<body>
<br>
<h1>r/proceduralgeneration Logo Generator</h1>
<small>generic version</small>
<br>

<?php
    if(!isset($_GET['url']) || empty($_GET['url'])){
    	echo "<b>Usage</b>: https://ciaccodavi.de/procgen/reddit/logo/generic.php?url=https://i.imgur.com/xWmnzhC.png";
    	die();
    }
    $url = $_GET['url']; // 'https://i.imgur.com/xWmnzhC.png'
?>

<br><br>
	<div>
		<small>Original image:</small><br>
		<img src="<?php echo $_GET['url']; ?>"><br>
		<br>
		<br>
		<br>
	</div>
	<div class="list">
	<?php
		$n = isset($_GET['n']) ? $_GET['n'] : 10;

		for (; $n > 0; $n--) {
			$rand = mt_rand(0, 10000000);
			echo '<div class="logo">
			<img src="./fromurl.php?ps=2&id='.$rand.'&url='.$url.'"><br>Seed: '.$rand.' Resolution: <a href="./fromurl.php?id='.$rand.'&url='.$url.'" target="_blank">120x40</a>
			 | <a href="./fromurl.php?ps=2&id='.$rand.'&url='.$url.'" target="_blank">x2</a>
	 		 | <a href="./fromurl.php?ps=3&id='.$rand.'&url='.$url.'" target="_blank">x3</a>
	 		 | <a href="./fromurl.php?ps=4&id='.$rand.'&url='.$url.'" target="_blank">x4</a>
	 		 | <a href="./fromurl.php?ps=5&id='.$rand.'&url='.$url.'" target="_blank">x5</a>
	 		 | <a href="./fromurl.php?ps=6&id='.$rand.'&url='.$url.'" target="_blank">x6</a>
	 		 </div>
			 <br><br><br>';
		}
	?>
	</div>
</body>
</html>