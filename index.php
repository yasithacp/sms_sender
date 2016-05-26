<?php
session_start();

if(!isset($_SESSION['user_id']))
{
    header('Location: login.php');
    die;
}

require_once realpath( dirname( __FILE__ ) ) . '/classes/parent_dao.php';
require_once realpath( dirname( __FILE__ ) ) . '/classes/sms_service.php';
require_once realpath( dirname( __FILE__ ) ) . '/config.php';

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

  try {
    $send_to = $_POST['send_to'];
    $message_body = $_POST['message'];

    if($send_to == 'all') {
      $dao = new ParentDao();
      $bomb = $dao->getParentsMobileNumbers();
    } else if ($send_to == 'admin') {
      $bomb = array('773667501', '719109916', '778463291', '712272727', '711739906');
    } else if ($send_to == 'custom'){
      $custom_no = $_POST['custom_no'];
      $bomb = explode(",", $custom_no);
    }

    $chunks = array_chunk($bomb, 300);

    $gateway = new SmsService();
    $session = $gateway->createSession('',SMS_GATEWAY_USER,SMS_GATEWAY_PASS,'');

    $alias = "RCIN";

    foreach($chunks as $chunk) {
      $response = $gateway->sendMessages($session,$alias,$message_body,$chunk);
    }

    if ($response == '200') {
      $message['messageType'] = 'success';
      $message['message'] = "Successfully Sent";
    } else if ($response == '169'){
      $message['messageType'] = 'warning';
      $message['message'] = "Invalid Alias";
    } else if ($response == '151'){
      $message['messageType'] = 'warning';
      $message['message'] = "Invalid Session";
    }

    $gateway->closeSession($session);
  } catch (Exception $e) {
    print_r($e->getMessage());
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
  <div class="container">
    <div id="header">
      <div id="crest"></div>
      <div id="logo">
        <h1>Royal College</h1>
        <p id="tagline">School Development Society</p>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <span class="glyphicon glyphicon-envelope"></span>
          Royal College - SMS Gateway
        </h3>
      </div>
      <div class="panel-body">
        <?php if(isset($message)) { ?>
        <div id="message-baloon">
          <div class="<?php echo 'alert alert-' . $message['messageType']; ?>">
            <a class="close" data-dismiss="alert" href="#">×</a>
            <?php echo $message['message']; ?>
          </div>
        </div>
        <?php } ?>
        <form id="form" action="index.php" method="post" role="form" >
          <div class="form-group">
            <label for="send_to">Send to:</label>
            <select id="send_to" name="send_to" class="form-control">
              <option value="all">All Parents</option>
              <option value="admin">Only Admins</option>
              <option value="custom">Custom Numbers</option>
            </select> 
          </div>
          <div class="form-group">
            <input id="custom_no" class="form-control" name="custom_no" placeholder="Enter comma seperated numbers in 7XXXXXXXX format"></textarea>
          </div>
          <div class="form-group">
            <label for="message">Message:</label>
            <textarea class="form-control" rows="4" name="message"></textarea>
          </div>
          <button id="send" type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
    <a href="login.php">Log Out</a>
  </div>

</body>
</html>
