"use strict"

let disposal_url=$('#disposal_url').val();
var disposals='';
var quantities=[];
var ids=[];
var reasons=[]
$('#add_new').on('click',function(){
     $.ajax({
         method: "GET",

         url: disposal_url,
         

         success: function (data) {
             $("#view_item_datails").html(data);
             $("#inforg").modal("show"); // show bootstrap modal
             $(".modal-title").text(" Select Items to  Dispose");
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
})
function RunDisposal(){
        $(".btn-close").click();
    if(selected.length==0){
        $.alert({
            icon: "fa fa-danger",
            title: "Missing information!",
            type: "red",
            content: "Please select  items  you want to dispose!",
        });
        return;
   
}
    let disposal_selected = $("#disposal_selected").val();

    disposals = $("#update_disposals").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        paging: false,
        select: true,
        info: false,
        sDom: "lrtip",
        lengthMenu: [10, 20, 50],
        responsive: true,

        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: disposal_selected,
            dataType: "json",
            type: "GET",
            data: {
                selected: selected,
            },
        },
        initComplete: function (settings, json) {
            document.getElementById('dispose').hidden=false
        },
        AutoWidth: false,
     
  
        columns: [
            { data: "id" },
            { data: "item" },
            { data: "code" },
            { data: "batch" },
            { data: "catalog" },
            { data: "unit" },
            { data: "available" },
             { data: "reason" },
            { data: "quantity" },
          
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
function getQuantity(id ,value){
  
    var reason =$("#q_"+id).val();
   let check_quantity = $("#check_quantity").val();
    if(!reason){
      $("#q_"+id).focus();  
    }
    else{
        var id_value = id + "_" + value+"_"+reason;
      

        $.ajax({
            method: "GET",
            dataType: "JSON",
            url: check_quantity,
            data: {
                id: id,
                quantity: value,
            },

            success: function (data) {
                if (data.error == false) {
                     //quantities.push(id_value);
                       let startsWithBan = quantities.find((item) => item.startsWith(id+"_"));
console.log(startsWithBan);
    
        if (startsWithBan) {
           quantities = arrayRemove(quantities, startsWithBan);
             quantities.push(id_value);
        } else {
            quantities.push(id_value);
        }
                    console.log(quantities);
                } else {
                    $.alert({
                        icon: "fa fa-danger",
                        title: "Error",
                        type: "red",
                        content: data.message,
                    });
                    $("#"+id).val('')
                     $("#"+id).focus();  
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // console.log(get_case_next_modal)
                alert("Error " + errorThrown);
            },
        });
 
}
  //console.log(quantities)
 
}
function arrayRemove(arr, value) {
    return arr.filter(function (item) {
        return item != value;
    });
}

function getReason(id,value){
   var reason_value=id+'_'+value
   reasons.push(reason_value);
   
    console.log(reasons)
}
function RunItemsDisposal(){
   
    var run_disposal = $("#run_disposal").val();
     $.ajaxSetup({
         headers: {
             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
         },
     });

      if(quantities.length==0){
        $.alert({
            icon: "fa fa-danger",
            title: "Missing information!",
            type: "red",
            content: "enter  quantities   you want to dispose!",
        });
        return;
    }
     $.ajax({
         method: "POST",
         dataType: "JSON",
         url: run_disposal,
         data: {
             quantity: quantities,
             reasons: reasons,
            
            
         },
           beforeSend: function () {
                    ajaxindicatorstart("loading data... please wait...");
                },
         success: function (data) {
             if (data.error == false) {
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
 reasons.length=0
                 quantities.length=0;
                 disposals.destroy();
                 ajaxindicatorstop();
                 location.reload();
                
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
             // Welcome notification
             // Welcome notification
         },
     });
}
function showDisposedListModal(){
 
    let disposal_modal_url=$('#modal_dispose_url').val(); 
    $.ajax({
        method: "GET",

        url: disposal_modal_url,

        success: function (data) {
            $("#view_item_datails").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" List of Disposals");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });  
}


function ViewDisposal(id) {
    let view_disposal_modal_url = $("#modal_view_dispose_url").val();
    $.ajax({
        method: "GET",
        url: view_disposal_modal_url,
        data:{
         id:id
        },

        success: function (data) {
            $("#view_item_datails").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Items Disposed");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
function ApproveDisposal(id){
 var approve_disposal = $("#approve_disposal").val();
 $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });

 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: approve_disposal,
     data: {
         id: id,
         
     },
       beforeSend: function () {
                    ajaxindicatorstart("loading data... please wait...");
                },
     success: function (data) {
         if (data.error == false) {
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
ajaxindicatorstop();
            reloadTable();
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
         // Welcome notification
         // Welcome notification
     },
 });   
}

function DenyDisposal(id){
 var deny_disposal = $("#deny_disposal").val();
 $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });

 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: deny_disposal,
     data: {
         id: id,
         
     },
       beforeSend: function () {
                    ajaxindicatorstart("loading data... please wait...");
                },
     success: function (data) {
         if (data.error == false) {
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
ajaxindicatorstop();
            reloadTable();
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
         // Welcome notification
         // Welcome notification
     },
     error:function(error){
     ajaxindicatorstop();
     }
 });  
}
function reloadTable(){
    y = $("#disposal_list").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        destroy: true,
        info: true,
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: get_approved,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "dispose_date", width: "10%" },
            { data: "disposed_by", width: "15%" },
            { data: "approved_by", width: "15%" },
            { data: "items", width: "15%" },
            { data: "action", width: "40%" },
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