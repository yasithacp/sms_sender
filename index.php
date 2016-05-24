<?php

require_once realpath( dirname( __FILE__ ) ) . '/parent_dao.php';
require_once realpath( dirname( __FILE__ ) ) . '/sms_service.php';
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
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
  <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<style type="text/css">
body {
  background-color: #F1F1F1;
}
.form-group {
  width: 50%;
}
.error {
  margin-top: 2px;
  color: red;
}
#logo {
  font-family: Georgia,Times,"Times New Roman",serif;
  text-align: left;
  text-transform: uppercase;
}
#logo, #tagline {
  color: #0c5390;
  font-size: 23px;
}
#crest {
  height: 75px;
  width: 53px;
  background-image: url('crest.png');
  background-size: 53px 75px;
  background-repeat: no-repeat;
  float: left;
  margin-right: 10px;
}
</style>
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
        <form id="form"action="index.php" method="post" role="form" >
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
  </div>

</body>
<script type="text/javascript">
$( document ).ready(function() {
  $('#send').on('click', function(){
    if(isValidForm()){
      $('#form').submit();
    }
  });

  if($('#send_to').val() == 'custom'){
    $('#custom_no').show();
  } else {
    $('#custom_no').hide();
  }

  $('#send_to').on('change', function(){
    if($('#send_to').val() == 'custom'){
      $('#custom_no').show();
    } else {
      $('#custom_no').val("");
      $('#custom_no').hide();
    }
  });

  $.validator.addMethod("regex", function(value, element, regexp) {          
    var re = new RegExp(regexp);
    return this.optional(element) || re.test(value);
  }, "Please enter mobile numbers in a valid format");

});

function isValidForm(){
  $("#form").validate({
    rules: {
      message: {
        required: true,
        maxlength: 255
      },
      custom_no: {
        required: true,
        regex: '^[7][0-9]{8}(,[7][0-9]{8})*$'
      }
    },
    messages: {
      message: {
        required: "Message body is required",
        minlength: "Message should be less than 255 charators"
      },
      custom_no: {
        required: "Mobile Number(s) are required",
        regex: "Please enter mobile numbers in a valid format"
      }
    }
  });
  return true;
}
</script>
</html>
