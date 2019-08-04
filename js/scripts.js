/*  ____________________________________________________________

      FILE: scripts.js
      DESCRIPTION: Small and project-Wide scripts go here
    ____________________________________________________________ */

"use strict"


/* ____________________________________________________________

     BOOK & ACCOUNT DELETE CONFIRMATION SCRIPTS
   ____________________________________________________________ */

    // Confirm Book Deletion
    $("a.deleteBook").on("click", function(ev) {
      if (!confirm("Are you sure you want to delete this book?"))
        ev.preventDefault();
    });

    // Confirm Account Deletion
    $("#deleteAccount").on("click", function(ev) {
      if (!confirm("This will permanently remove your account and all books associated with it. Are you sure you want to proceed?"))
        ev.preventDefault();
    });

/* ____________________________________________________________

     PASSWORD RECOVERY VALIDATION
   ____________________________________________________________ */

    $("#getSecurityQ input[name='user']").blur(function(ev){
     $("#userError").remove();
     let $name = $("input[name='user']");
     $.get("checkusername.php", {username: $name.val()})
     .done(function(data) {
       if (!data)
         $("#getSecurityQ").after("<span id='userError' class='error'>Username not found</span>");
       });
    });


    $("#resetPass button").on("click", function(ev) {
      $(".error").remove();
      // check that passwords match
      let password = $("input[name='pass']").val();
      let confirmPass = $("input[name='confirmPass']").val();
      if (password != confirmPass) {
        ev.preventDefault();
        $("#resetPass").after("<span class='error'>Error -- passwords do not match</span>");
      }
    });

/* ____________________________________________________________

     PASSWORD STRENGTH METER

     Uses regular expression to determine password strength
     and displays below password fields (Registration, Edit
     Account, and Password Recovery pages)
   ____________________________________________________________ */
    $("input[name='pass']").blur(function(ev){
      $("meter").parent().remove();
      let pass = $("input[name='pass']").val();
      let passStrength = 0;
      if (/[a-z]/.test(pass))
        ++passStrength;
      if (/[A-Z]/.test(pass))
        ++passStrength;
      if (/[0-9]/.test(pass))
        ++passStrength;
      if (/[^a-z, A-Z, 0-9]/.test(pass))
        ++passStrength;
      if (pass.length >= 8)
        ++passStrength;
      $("input[name='pass']").parent().after("<div><span class='small'>Strength:</span> <meter value='" + passStrength + "' min='0' max='5'></meter></div>");
    });

/* ____________________________________________________________

     ADD / EDIT BOOK FORM VALIDATION
   ____________________________________________________________ */

    $("button[name='submitBook']").on("click", function(ev) {
      $(".error").remove();
      let error = false;
      // check that ISBN and year are digits only
      if (isNaN($("input[name='year']").val())) {
        $("input[name='year']").after("<span class='error'>Digits Only</span>");
        error = true;
      }
      if (isNaN($("input[name='isbn']").val())) {
        $("input[name='isbn']").after("<span class='error'>Digits Only</span>");
        error = true;
      }
      // stop processing if error
      if (error)
        ev.preventDefault();
    });
