"use strict";
var url = $("#supplier_url").val();

$("#supplier_form").on("click", "#submit", function (e) {
    e.preventDefault();

     var supplier_name = $("#supplier_name").val();
     var contact_person = $("#contact_person").val();
      var address = $("#address").val();
       var email = $("#email").val();
        var phone = $("#phone").val();
         var expiry = $("#contract_expiry").val();
     if (!supplier_name) {
         $("#supplier_name").focus();
         $.alert({
             icon: "fa fa-warning",
             title: "Missing information!",
             type: "orange",
             content: "Please provide Supplier name!",
         });

         return;
     }

 
      
         $.ajaxSetup({
             headers: {
                 "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
             },
         });
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: url,
        data: $("#supplier_form").serialize(),
          beforeSend: function () {
                    ajaxindicatorstart("saving data... please wait...");
                },
        success: function (data) {
            if (data.error == false) {
            ajaxindicatorstop();
                // Welcome notification
                // Welcome notification
                toastr.options = {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toast-top-right",
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                };
                toastr["success"](data.message);
                $("#supplier_form")[0].reset();
            } else {
             ajaxindicatorstop();
                toastr.options = {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toast-top-right",
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                };
                toastr["error"](data.message);
            }
        },
    });
    return false;
});