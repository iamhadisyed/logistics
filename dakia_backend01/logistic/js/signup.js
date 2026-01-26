  $(document).ready(function () {
      
 
            
            
        ////// country drop down bs select added /////                     
        $('.bs-select').selectpicker();
        
        ////// signu form validation ///// 
        $("#signUp").validate({
            focusInvalid: !1,
            rules: {
                company: "required",
                contact: "required",
                telephone: "required",
                email: {
                        required: true,
                        email: true
                },
                country_id: "required"
            },
    
            invalidHandler: function(r, e) {
               // $(".alert-danger", $("#signUp")).show()
            },
            highlight: function(r) {
                $(r).closest(".form-group").addClass("has-error")
            },
            success: function(r) {
                r.closest(".form-group").removeClass("has-error"), r.remove()
            },
            errorPlacement: function(r, e) {
               return false;
            },
            submitHandler: function(r) {
                 var form_data = $("#signUp").serializeArray();
                    form_data.push({name: "action", value: 'account_signup'});
                    $.ajax({
                                        url: 'account_signup.php',
                                        type: 'POST',
                                        data:form_data,
                                        dataType: "json",
                                        success: function (data) {
                                         if(data.status=='error'){
 
                                                    swal({                                
                                                    title: "Sorry!",
                                                    text:  data.message ,
                                                    html: true,
                                                    type: "error",
                                                    showCancelButton: true
                                                });
                                         }else  if(data.status=='success'){
                                               
                                                
                                                 swal({                                
                                                    title: "Success!",
                                                    text:  data.message ,
                                                    html: true,
                                                    type: "success",
                                                    showCancelButton: true
                                                });
                                                  $('#register-popup').modal('hide');
                                         }
                                                
                                        },
                                        error: function (xhr, status, error) {
                                          
                                        }
                                    });

             
            }
        });
           });
         