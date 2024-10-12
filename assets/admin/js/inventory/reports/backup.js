"use strict";
var scheduled_backup_url = $("#scheduled_backup_url").val();

var t = "";

t = $("#back_ups").DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [10, 50, 100],
    responsive: true,
    order: [[0, "desc"]],

    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    language: {
        emptyTable: "There are no any scheduled reports.",
    },
    ajax: {
        url:scheduled_backup_url,
        dataType: "json",
        type: "GET",
    },

         
    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
 
        { data: "frequency", width: "10%" },
        { data: "scheduled_by", width: "10%" },
        { data: "receipient", width: "10%" },
     
        { data: "status", width: "10%" },
        { data: "action", width: "10%" },
        { data: "delete", width: "10%" },
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
 function changeText(value){
    console.log(value);
    switch (value) {
    case '1':
     
    
$('#report_infor').text("This will back up the database and a copy will be sent on a daily basis to the specified recipient");
        break;
        case '2':

$("#report_infor").text("This will back up the database and a copy will be sent on a weekly basis to the specified recipient");
        break;
    case '3':
$("#report_infor").text("This will back up the database and a copy will be sent on a monthly basis to the specified recipient")

    break;
    
   case '4':
 
        $("#report_infor").text("Report will be generated and sent on a yearly basis" );
            break;
    }
}
function DeactivateBackup(id,name){
let deactivate=$('#deactivate').val();
 $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });
 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: deactivate,
     data: {
        id: id,
        name:name
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
         window.location.reload();
     },
     error: function (error) {},
 });
}

function DeleteBackup(id){
let delete_schedule=$('#delete').val();
 $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });
 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: delete_schedule,
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
         window.location.reload();
     },
     error: function (error) {},
 });
}

$('#backup_list').on('click',function(){
let backup_list=$('#view_backups').val();
     $.ajax({
         method: "GET",

         url: backup_list,
         

         success: function (data) {
             $("#receive_item").html(data);
             $("#inforg").modal("show"); // show bootstrap modal
             $(".modal-title").text(" Backups Generated");
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
})

$('#run_backup').on('click',function(){
let generate_backup=$('#generate_backup').val();
   $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });
 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: generate_backup,
    
     success: function (data) {
        
         window.location=data.url;
     },
     error: function (error) {},
 });
})


function downloadBackup(id){
let download_url=$('#download_url').val();

 $.ajax({
     method: "GET",
     dataType: "JSON",
     url: download_url,
    data:{
    id:id
    },
     success: function (data) {
        
         window.location=data.url;
     },
     error: function (error) {},
 });

}

function deleteBackup(id){
let delete_url=$('#delete_url').val();
   $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });
 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: delete_url,
     data:{
     id:id
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
         reloadBackedUp()
     },
     error: function (error) {},
 });


}
function reloadBackedUp(){
let reload_url = $("#load_backups").val();
 p = $("#created_backups").DataTable({
    processing: true,
    serverSide: true,
    destroy:true,
    paging: true,
    info: true,
    lengthMenu: [10, 20, 50],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url:reload_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
       
        { data: "id", width: "3%" },
        { data: "name", width: "15%" },
        { data: "type", width: "10%" },
        { data: "backup_by", width: "20%" },
        { data: "scheduled_by", width: "10%" },
         { data: "created_at", width: "10%" },
        { data: "action", width: "30%" },
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
function clearAllBackups(){
let clear_all=$('#delete_all').val()
  $.confirm({
        title: "Confirm!",
        content: "Do you really  want to clear all the backups?!",
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
                        url: clear_all,
                        dataType: "JSON",
                        beforeSend: function () {
                    ajaxindicatorstart("cleaning data... please wait...");
                },
                        success: function (data) {
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
                            toastr["success"](data.message);
                             reloadBackedUp()
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
}


function uploadDocument(){
    let upload_url = $('#upload_backup').val()
   

     Swal.fire({
        title: "Upload Backup File",
         showCancelButton: true,
         confirmButtonText: "Upload Backup",
         confirmButtonColor: "#3085d6",
         reverseButtons: true,
         input: "file",
         inputAttributes: {
            
             "aria-label": "Upload Backup File",
         },
         onBeforeOpen: () => {
             $(".swal2-file").change(function () {
                 var reader = new FileReader();
                 reader.readAsDataURL(this.files[0]);
             });
         },
     }).then((file) => {
         if (file.value) {
             var formData = new FormData();
             var file = $(".swal2-file")[0].files[0];
             formData.append("fileToUpload", file);
            
             $.ajax({
                 headers: {
                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                         "content"
                     ),
                 },
                 method: "post",
                 url: upload_url,
                 data: formData,
                 processData: false,
                 contentType: false,
                     beforeSend: function () {
                    ajaxindicatorstart("restoring data... please wait...");
                },
                 success: function (resp) {
                   ajaxindicatorstop();
                 if(resp.error==false){
                  Swal.fire("Restored", resp.message, "success");
                 }
                 else{
                 Swal.fire({
                         type: "error",
                         title: "Oops...",
                         text: resp.message,
                     });
                 }
                    
                 },
                 error: function (error) {
                   ajaxindicatorstop();
                     Swal.fire({
                         type: "error",
                         title: "Oops...",
                         text: error.message,
                     });
                 },
             });
         }
     });
}

function RestoreBackup(id){
  let restore_url = $('#upload_backup').val()
  $.confirm({
        title: "Confirm!",
        content: "Do you really  want to restore your data to this backup?!",
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
                        url:  restore_url,
                        dataType: "JSON",
                        data:{
                        id:id
                        },
                        beforeSend: function () {
                    ajaxindicatorstart("restoring data... please wait...");
                },
                        success: function (data) {
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
                            toastr["success"](data.message);
                             reloadBackedUp()
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


}