"use strict";
var url=$('#post_url').val();
var lab_url = $("#lab_list_url").val();

var t='' 
        $('#form_id').on('click','#submit', function(e){
         
            e.preventDefault();
            var name=$('#lab_name').val()
            var location=$('#lab_location').val();
            var lab_code=$('#lab_code').val();
            if(!name){
                  $("#lab_name").focus();
                 $.alert({
                     icon: "fa fa-warning",
                     title: "Missing information!",
                     type: "orange",
                     content: "Please provide Laboratory name!",
                 });
               
                 return;
            }

                  if (!lab_code) {
                      $("#lab_code").focus();
                      $.alert({
                          icon: "fa fa-warning",
                          title: "Missing information!",
                          type: "orange",
                          content: "Please provide Laboratory code!",
                      });

                      return;
                  }
             if (!location) {
                 $.alert({
                     icon: "fa fa-warning",
                     title: "Missing information!",
                     type: "orange",
                     content: "Please provide Laboratory Location!",
                 });
                 $("#lab_location").focus();
                 return;
             }
            $.ajax({
                method: "POST",
                dataType:"JSON",
                url: url,
                data: $('#form_id').serialize(),
                  beforeSend: function () {
                    ajaxindicatorstart("saving data... please wait...");
                },
                success: function(data) {
               ajaxindicatorstop();
              // Welcome notification
               // Welcome notification
                toastr.options = {
                  "closeButton": true,
                  "debug": false,
                  "newestOnTop": false,
                  "progressBar": false,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "1000",
                  "timeOut": "5000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
                }
                toastr["success"](data.message);
                   loadLab();
                }
            });
            return false;
        });
   function loadLab(){
    
  
   location.reload();
   }
   t = $("#lab_list").DataTable({
       processing: true,
       serverSide: true,
       lengthMenu: [10, 50, 100],
       responsive: true,
       order: [[0, "desc"]],
       oLanguage: {
           sProcessing:
               "<div class='loader-container'><div id='loader'></div></div>",
       },
       ajax: {
           url: lab_url,
           dataType: "json",
           type: "GET",
       },

       AutoWidth: false,
       columns: [
           { data: "id", width: "3%" },
           { data: "name", width: "3%" },
           { data: "location", width: "20%" },
           { data: "email", width: "3%" },
           { data: "phone" },
           { data: "address" },
           { data: "action" },
       ],
       //Set column definition initialisation properties.
       columnDefs: [
           {
               targets: [-1], //last column
               orderable: false, //set not orderable
           },
           {
               targets: [-2], //last column
               orderable: false, //set not orderable
           },
           {
               targets: [-3], //last column
               orderable: false, //set not orderable
           },
       ],
   });
   function EditLab(id){
    var load_edit_modal_url = $("#edit_modal").val();
   $.ajax({
       method: "GET",
       url: load_edit_modal_url,
       data:{
        id:id,
       },

       success: function (data) {
           //console.log(data)
           $("#receive_item").html(data);
           $("#inforg").modal("show"); // show bootstrap modal
       },
       error: function (jqXHR, textStatus, errorThrown) {
           // console.log(get_case_next_modal)
           alert("Error " + errorThrown);
       },
   });
   }

    $("#inputsection").select2({
        allowClear: true,
        placeholder: "Select Lab Sections",
        multiple: true,
    });
  function hasChanged(value){
    if(value=="yes"){
        $('#sections').show();

    }
    else{
        $("#sections").hide();  
    }

  }
  
  
  function DeleteLab(id){
    var delete_lab= $("#delete_lab").val();
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to delete this lab and all its entries!",
        buttons: {
            Oky: {
                btnClass: "btn-danger",
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
                        url: delete_lab,
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
                          loadLab();
                        },
                        error: function (error) {
                            console.log();
                        },
                    });
                },
            },

            cancel: function () {},
        },
    });

    ///
}