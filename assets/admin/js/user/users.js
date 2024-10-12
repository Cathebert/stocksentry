"use strict";
var url = $("#post_url").val();
var user_url = $("#user_list_url").val();

var t = "";

function loadLab() {
    location.reload();
}
t = $("#user_list").DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [10, 50, 100],
    destroy:true,
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: user_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "username", width: "10%" },
        { data: "name", width: "10%" },
        { data: "last_name", width: "10%" },
        { data: "email", width: "10%" },
        { data: "phone", width: "10%" },
        { data: "lab", width: "10%" },
        { data: "location", width: "10%" },
        { data: "options", width: "40%" },
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
function editUser(id) {
    var load_edit_modal_url = $("#edit_modal").val();
    $.ajax({
        method: "GET",
        url: load_edit_modal_url,
        data: {
            id: id,
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
function hasChanged(value) {
    if (value == "yes") {
        $("#sections").show();
    } else {
        $("#sections").hide();
    }
}

function deleteUser(id) {
    var delete_user = $("#delete_user").val();
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to remove this user?!",
        buttons: {
            Oky: {
                btnClass: "btn-warning",
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
                        url: delete_user,
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

function resetPassword(id) {
    var reset_user = $("#reset_user").val();
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to reset  this user's Password?!",
        buttons: {
            Oky: {
                btnClass: "btn-info",
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
                        url: reset_user,
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
 function getLabName(value) {
     const receive_from = $("#lab_id").val();
     const get_sections = $("#get_sections").val();
     if (value == 99) {
         document.getElementById("not_coldroom").hidden = true;
         document.getElementById("coldroom").hidden = false;
         document.getElementById("my_lab").hidden = true;
         $("#check").val(1);
     }

     if (value > 0 && value < 99) {
         document.getElementById("my_lab").hidden = false;
         document.getElementById("not_coldroom").hidden = true;
         document.getElementById("coldroom").hidden = true;
         $("#check").val(2);
     }

     if (value == 0) {
         document.getElementById("not_coldroom").hidden = false;
         document.getElementById("my_lab").hidden = true;
         document.getElementById("coldroom").hidden = true;
         $("#check").val(0);
     }


 }



  $("#edit_user_form").on("click", "#submit", function (e) {

      var update_url = $("#update_url").val();

      var name = $("#first_name").val();
      var last_name = $("#last_name").val();
      var email = $("#email").val();
      var user_position = $("#user_position").val();
      var user_role = $("#user_role").val();

      if (!name) {
          $.alert({
              icon: "fa fa-warning",
              title: "Missing information!",
              type: "orange",
              content: "Please provide name!",
          });
          $("#first_name").focus();
          e.preventDefault();
          return;
      }
      if (!last_name) {
          $.alert({
              icon: "fa fa-warning",
              title: "Missing information!",
              type: "orange",
              content: "Please provide Last name!",
          });
          $("#last_name").focus();
          e.preventDefault();
          return;
      }
      if (!email) {
          $.alert({
              icon: "fa fa-warning",
              title: "Missing information!",
              type: "orange",
              content: "Please provide email address!",
          });
          $("#email").focus();
          e.preventDefault();
          return;
      }
      if (!user_position) {
          $.alert({
              icon: "fa fa-warning",
              title: "Missing information!",
              type: "orange",
              content: "Please provide position of user!",
          });
          $("#user_position").focus();
          e.preventDefault();
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
          url: update_url,
          data: $("#edit_user_form").serialize(),
          success: function (data) {
              if (data.error == false) {
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
              } else {
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
              // $("#form_id")[0].reset();
          },
      });
      return false;
  });

  $("#user_pdf").on("click", function (e) {
    e.preventDefault();
    var download_url = $("#download_url").val();
    var expiry_form = $("#users_form").serialize();
    console.log(expiry_form);
    $.ajax({
        method: "GET",

        url: download_url,
        data: {
            expiry_form,
            type: "pdf",
        },

        success: function (data) {
        //alert( data.url);
            window.location = data.url;
            // show bootstrap modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
});

$("#user_excel").on("click", function (e) {
    e.preventDefault();
    var download_url = $("#download_url").val();
    var expiry_form = $("#users_form").serialize();
    console.log(expiry_form);
    $.ajax({
        method: "GET",

        url: download_url,
        data: {
            expiry_form,
            type: "excel",
        },

        success: function (data) {
            window.location = data.url;
            // show bootstrap modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
});

function filterUser(id){
let user_filter=$('#filter_user').val();

t = $("#user_list").DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [10, 50, 100],
    destroy:true,
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: user_filter,
        dataType: "json",
        type: "GET",
        data:{
        id:id
        }
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "username", width: "10%" },
        { data: "name", width: "10%" },
        { data: "last_name", width: "10%" },
        { data: "email", width: "10%" },
        { data: "phone", width: "10%" },
        { data: "lab", width: "10%" },
        { data: "location", width: "10%" },
        { data: "options", width: "40%" },
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


}


function deletedUsers(){
let deleted_users=$('#deleted_users').val();
   // alert(id);
    $.ajax({
        method:'GET',

        url:  deleted_users,
        success: function (data) {


             $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
             $('.modal-title').text('Deleted Users');


        },
        error: function (jqXHR, textStatus, errorThrown) {
           // console.log(get_case_next_modal)
            alert('Error '+errorThrown);
        }
    });

}

function restoreUser(id){
let restore=$('#restore_user').val()
    Swal.fire({
  title: 'Are you sure?',
  text: "Restore User!",
  icon: 'success',
  showCancelButton: true,
  confirmButtonColor: '#16a61d',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Yes, restore it!'
}).then((result) => {
  if (result.isConfirmed) {
    //delete

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method:'POST',
            dataType:'JSON',
            url: restore,
        data:{
            id:id,
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
   getRestored();
    getUsers();
        },
        error:function(error){

        }
        })

  }
})
}

function getRestored(){
  var get_restored=$("#load_deleted_users").val();
 y = $("#users_deleted").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy:true,
    info: true,
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url:get_restored,
        dataType: "json",
        type: "GET",
    },



    AutoWidth: false,
    columns: [
           { data: "id", width: "3%" },
        { data: "username", width: "10%" },
        { data: "name", width: "10%" },
        { data: "last_name", width: "10%" },
        { data: "email", width: "10%" },
        { data: "phone", width: "10%" },
        { data: "lab", width: "10%" },
        { data: "location", width: "10%" },
        { data: "options", width: "40%" },
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

}

function  getUsers(){

t = $("#user_list").DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [10, 50, 100],
    destroy:true,
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: user_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "username", width: "10%" },
        { data: "name", width: "10%" },
        { data: "last_name", width: "10%" },
        { data: "email", width: "10%" },
        { data: "phone", width: "10%" },
        { data: "lab", width: "10%" },
        { data: "location", width: "10%" },
        { data: "options", width: "40%" },
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



}
