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