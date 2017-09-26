/* User authencation [/view/auth/login] */
if($("#frmAuthencation").length > 0){
    $("#frmAuthencation").validate({
        rules: {
            txtEmail: {
                required: true,
                email:true
            },
            txtPassword: {
        		required: true,
        		minlength: 1
        	}
        },
        //For custom messages
        messages: {
            txtEmail: "Invalid email address.",
            txtPassword: "Invalid password",
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
          var placement = $(element).data('error');
          if (placement) {
            $(placement).append(error)
          } else {
            error.insertAfter(element);
          }
        }
    });
}

/* Company registration [/view/company/register] */
if($("#frmCompanyRegistration").length > 0){
    $("#frmCompanyRegistration").validate({
        rules: {
            txtCompanyName: {
                required: true,
            },
            txtAdminName: {
                required: true,
            },
            txtEmail: {
                required: true,
                email:true
            },
            txtPassword: {
                required: true,
                minlength: 6
            }
        },
        //For custom messages
        messages: {
            txtCompanyName: "Invalid company name.",
            txtAdminName: "Invalid name.",
            txtEmail: "Invalid email address.",
            txtPassword: {
                required:"Invalid password",
                minlength:"Enter at least 6 characters",
            },
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
          var placement = $(element).data('error');
          if (placement) {
            $(placement).append(error)
          } else {
            error.insertAfter(element);
          }
        }
    });
}



/* Company registration [/view/company/register] */
if($("#frmStatus").length > 0){
    $("#frmStatus").validate({
        rules: {
            txtStatusName: {
                required: true,
            },
            cboStatusType: {
                required: true,
            }
        },
        //For custom messages
        messages: {
            txtStatusName: "Please enter valid status description.",
            cboStatusType: "Select Status type.",
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
          var placement = $(element).data('error');
          if (placement) {
            $(placement).append(error)
          } else {
            error.insertAfter(element);
          }
        }
    });
}