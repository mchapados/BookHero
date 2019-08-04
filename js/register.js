/* ____________________________________________________________
    FILE: register.js

    DESCRIPTION: Book Hero REGISTRATION Form Validation
    Also used for EDIT ACCOUNT page.

    Other Methods: Custom security question, check username
    availability, email address validation, display terms and
    conditions
   ____________________________________________________________

*/

/* ____________________________________________________________

     REGISTRATION FORM VALIDATION
   ____________________________________________________________ */
    $("button[name='register']").on("click", function(ev){
      let errorMsg = [];
      let user = $("input[name='user']").val();
      let email = $("input[name='email']").val();
      let password = $("input[name='pass']").val();
      let confirmPass = $("input[name='confirmPass']").val();
      let $terms = $("input[name='terms']");
      let answer = $("input[name='answer']").val();
      let custom = false;
      validateForm(errorMsg);

      if ($("select[name='securityQuestion']").val() == 'custom') {
        custom = true;
        customQuestion = $("input[name='customQuestion']").val();
      }

      // check for blank fields
      if (user.length <= 0 || password.length <= 0 || answer.length <= 0 || (custom == true && customQuestion.length <= 0))
        errorMsg.push("ERROR -- all fields must be complete");

      // check terms
      if($terms.is(":not(:checked)"))
        errorMsg.push("ERROR -- you must accept the terms and conditions");

      // if error, stop processing and display messages
      if (errorMsg.length > 0) {
        ev.preventDefault();
        errorMsg.forEach(function (value, index, array) {
          $("button[name='register']").parent().after("<span class='error'>" + value + "</span>");
        });
      }
    });

/* ____________________________________________________________

     EDIT ACCOUNT FORM VALIDATION
   ____________________________________________________________ */
    $("button[name='update']").on("click", function(ev){
      let errorMsg = [];
      validateForm(errorMsg);

      // if error, stop processing and display messages
      if (errorMsg.length > 0) {
        ev.preventDefault();
        errorMsg.forEach(function (value, index, array) {
          $("button[name='update']").parent().after("<span class='error'>" + value + "</span>");
        });
      }
    });

/* ____________________________________________________________

     COMMON FORM VALIDATOR (REGISTRATION / EDIT ACCOUNT)
   ____________________________________________________________ */
    function validateForm(errorMsg){
      // reset errors
      $(".error").remove();

      // set values
      let email = $("input[name='email']").val();
      let password = $("input[name='pass']").val();
      let confirmPass = $("input[name='confirmPass']").val();

      // check email address
      if (!emailIsValid(email))
        errorMsg.push("ERROR -- invalid email address");
      // check that passwords match
      if (password != confirmPass)
        errorMsg.push("ERROR -- passwords do not match");
    }

/* ____________________________________________________________

     OTHER FUNCTIONS
   ____________________________________________________________ */

    // display field for custom security question
    $("select[name='securityQuestion']").blur(function(ev){
      $("input[name='customQuestion']").parent().remove();
      let $questSelect = $("select[name='securityQuestion']");
      if ($questSelect.val() == 'custom')
        $questSelect.parent().after("<div><input type='text' name='customQuestion' value='' /></div>");
    });

    // check that username is unique
    $("input[name='user']").blur(function(ev){
      $("#userError").remove();
      let $regUsername = $("input[name='user']");
      $.get("checkusername.php", {username: $regUsername.val()})
      .done(function(data) {
        if (data)
          $regUsername.after("<span id='userError' class='error'>not available</span>");
        });
    });

    // regular expression check for valid email address
    function emailIsValid(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
    }

    // terms and conditions
    $("#showTerms").on("click", function(ev){
      ev.preventDefault();
      alert("TERMS AND CONDITIONS: Book Hero is a student project and not a real" +
      " service. By creating an account, you agree that your data may be used and/or" +
      " deleted according to said student's whims.");
    });
