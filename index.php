<?php
  if(file_exists('core/setup/install.php')){
    header('Location: core/setup/install.php?d='.__DIR__);
    exit;
  }
session_start();
if(isset($_SESSION['connected'])){
  header('Location: dash.php');
  exit;
}
  if(isset($_POST['email']) && isset($_POST['pass'])){
    require_once('config/config.php');
    require_once('core/lib/dbManager.php');
    $sql = 'SELECT * FROM admin_users WHERE email="'.$_POST['email'].'" AND password="'.md5($_POST['pass']).'";';
    $data = execQueryWithResult($db, $sql);
    if($data)
    {
      $user = explode('@',$data[0]['email']);
      $_SESSION['connected'] = $user[0];
      header('Location: dash.php');
    exit;
    }
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>
    <link rel="icon" type="image/png" href="assets/img/ico.png" />
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

    <div class="container">

      <form class="form-signin" method="post" action="">
        <img src="assets/img/logo.png" style="margin-left: -5px" /><br /><br />
        <label for="inputEmail" class="sr-only">Email</label>
        <input name="email" type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="pass" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
      </form>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>