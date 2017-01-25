<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Login</title>
	<link rel="icon" type="image/png" href="../../assets/img/ico.png" />
	<!-- Bootstrap core CSS -->
	<link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="http://getbootstrap.com/examples/signin/signin.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body style="background-color: #19191b">
<div class="container" style="color: #fff">

<?php
	unlink('install.php');
	unlink('step1.php');
	unlink('step2.php');
	unlink('step3.php');
	unlink('step4.php');
?>
<h1>The steps are successfully completed !</h1>
<p>You can now access to the <a href="../../dash.php">Foxiadmin dashboard</a> !</p>

</div> <!-- /container -->

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>