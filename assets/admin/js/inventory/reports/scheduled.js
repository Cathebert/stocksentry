"use strict";
var scheduled_url = $("#scheduled_url").val();

var t = "";

t = $("#scheduled_list").DataTable({
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
        url: scheduled_url,
        dataType: "json",
        type: "GET",
    },

           
    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "name", width: "20%" },
        { data: "frequency", width: "10%" },
        { data: "scheduled_by", width: "10%" },
        { data: "date", width: "10%" },
        { data: "type", width: "10%" },
        { data: "receipient", width: "10%" },
        { data: "file", width: "10%" },
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
     
    
$('#report_infor').text("Report will be generated and sent on a weekly basis");
        break;
        case '2':

$("#report_infor").text("Report will be generated and sent on a monthly basis");
        break;
    case '3':
$("#report_infor").text("Report will be generated and sent on  a quarterly basis")

    break;
    
   case '4':
 
        $("#report_infor").text("Report will be generated and sent on a yearly basis" );
            break;
    }
}
function Deactivate(id,name){
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

function DeleteSchedule(id){
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