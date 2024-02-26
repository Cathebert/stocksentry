"use strict"
var p;
var y;
var approval_issues = $("#issue_approvals").val();
var issue_approved_items = $("#items_approved_take").val();


function ApproveItem(id){
   var approve_issued_item = $("#save_approve_issued_item").val();
   $.confirm({
       title: "Confirm!",
       content: "Do you really  want to approve this issue!",
       buttons: {
           Oky: {
               btnClass: "btn-success",
               action: function () {

                 $.ajaxSetup({
                     headers: {
                         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                             "content"
                         ),
                     },
                 });

                 $.ajax({
                     method: "POST",
                     url: approve_issued_item,
                     dataType: "JSON",
                     data: {
                         id: id,
                     },
                     success: function (data) {
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
                     },
                     error: function (error) {
                        console.log()
                     },
                 });
               },
           },
          

           cancel: function () {},
       },
   });
   
   ///
  
}
function ViewItem(id){

    const view_issue = $("#view_issue_siv").val();
   
       $.ajax({
           method: "GET",
           url: view_issue,
           data: {
               id: id,
           },

           success: function (data) {
           // $('#boot').click();
               $("#view_item_datails").html(data);
               $("#inforg").modal("show"); // show bootstrap modal
               $(".modal-title").text("View Details");
             
           },
           error: function (jqXHR, textStatus, errorThrown) {
               // console.log(get_case_next_modal)
               alert("Error " + textStatus);
           },
       });
}

function VoidItem(id){
    console.log(id)
    var void_url=$('#void_url').val();
      $.ajaxSetup({
          headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
      });
        $.ajax({
        method:"POST",
        url:void_url,
        dataType:"JSON",
        data:{
           id: id,
        },
        success:function(data){
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
        },
        error:function(error){
           
        }

        })
    
}






$("#show_approved").on("click", function () {
    var showapprovedrequest = $("#view_approved").val();
    $.ajax({
        method: "GET",

        url: showapprovedrequest,

        success: function (data) {
            $("#view_item_datails").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text("Approved Issues");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
});
