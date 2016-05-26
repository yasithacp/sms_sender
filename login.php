<?php
session_start();

unset($_SESSION['user_id']);

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if($username == SYSTEM_USER && $password == SYSTEM_PASS) {
    $_SESSION['user_id'] = 1;
    header('Location: index.php');
    die;
  } else {
    $message['messageType'] = 'danger';
    $message['message'] = "Login Failed";
  }
}

?>
<html lang="en">
<head>
  <title>RC Parent Information System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="favicon.ico" />
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/custom.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/jquery.validate.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/custom.js"></script>
</head>
<body>
  <div class="login-panel">
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <?php if(isset($message)) { ?>
        <div id="message-baloon">
          <div class="<?php echo 'alert alert-' . $message['messageType']; ?>">
            <a class="close" data-dismiss="alert" href="#">×</a>
            <?php echo $message['message']; ?>
          </div>
        </div>
        <?php } ?>
        <div class="login-panel panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Please Sign In</h3>
          </div>
          <div class="panel-body">
            <form role="form" action="login.php" method="post">
              <fieldset>
                <div class="form-group">
                  <input type="text" autofocus="" name="username" placeholder="Username" class="form-control">
                </div>
                <div class="form-group">
                  <input type="password" value="" name="password" placeholder="Password" class="form-control">
                </div>
                <button type="submit" class="btn btn-md btn-primary" href="index.html">Login</button>
              </fieldset>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>