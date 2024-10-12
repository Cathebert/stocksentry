"use strict"
var forecast = $("#forecast").val();
var selected = [];
var entered=0;
 var quantities = [];
  let place_purchase=''
function LoadInventory(e){
      $("#form_forecast").on("submit", function (e) {
          e.preventDefault();
           $.ajax({
               method: "GET",
               url: forecast,
               success: function (data) {
                   // $('#boot').click();
                   $("#view_item_datails").html(data);
                   $("#inforg").modal("show"); // show bootstrap modal
                   $(".modal-title").text("Select Items");
               },
               error: function (jqXHR, textStatus, errorThrown) {
                   // console.log(get_case_next_modal)
                   alert("Error " + textStatus);
               },
           });
      });
}
function selectItem(id){
    
    if (document.getElementById(id).checked==true){

    document.getElementById(id).checked = false; 
          if (selected.includes(id)) {
          } else {
              selected.push(id);
          }
    console.log(selected)
}
else{
   document.getElementById(id).checked = true; 
}
}


function RunForecast() {
    const url_selected=$('#run_forecast').val()
    const lead=$('#lead').val();
    const order=$('#order').val();
    
    console.log(url_selected);
    if (!selected) {
         $.alert({
             icon: "fa fa-warning",
             title: "Missing information!",
             type: "orange",
             content: "Select items to run forecast on!",
         });
       return

    }
    $(".btn-close").click();
   p = $("#forecast_table").DataTable({
       processing: true,
       serverSide: true,
       paging: true,
       destroy: true,
       info: true,

       lengthMenu: [10, 50, 100],
       responsive: true,
       order: [[0, "desc"]],
       oLanguage: {
           sProcessing:
               "<div class='loader-container'><div id='loader'></div></div>",
       },
       ajax: {
           url: url_selected,
           dataType: "json",
           type: "GET",
           data: {
               lead: lead,
               order: order,
               items: selected,
           },
       },

       initComplete: function (settings, json) {
         let elements = document.getElementsByName('ordered');
         console.log(elements.length);
         for (var i = 0; i < elements.length; i++) {
            quantities.push(elements[i].id+'_'+elements[i].value);
         }
         console.log(quantities);
       },

       AutoWidth: false,
       columns: [
             { data: "id" },
           { data: "item", width: "30%" },
           { data: "code" },
           { data: "supplier" },
           { data: "unit" },
           { data: "on_hand" },
           { data: "average" },
          
           { data: "forecasted" },
           { data: "order" },
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
selected.splice(0,selected.length)
document.getElementById('time_lead').hidden=false
}

function getOrdered(id_item,item_n){
   quantities.length=0;
  let elements = document.getElementsByName("ordered");
  console.log(elements.length);
  for (var i = 0; i < elements.length; i++) {
      quantities.push(elements[i].id + "_" + elements[i].value);
  }
  console.log(quantities);

   
}
function PlaceOrder(){
    let order_url=$('#order_url').val();
    let order_number = $("#order_number").val();
     const lead=$('#lead').val();
    const order=$('#order').val();
     $.ajaxSetup({
         headers: {
             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
         },
     });
     $.ajax({
         method: "POST",
         dataType: "JSON",
         url: order_url,
         data: {
             quantity: quantities,
             order_number:order_number,
             lead:lead,
            
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

                 $("#form_forecast")[0].reset();
                 ajaxindicatorstop();
            window.location.reload()
                p.destroy();
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
             // Welcome notification
             // Welcome notification
         },
     });
}
function getPlaceOfPurchase(value){

    let place_of_purchase_url=$('#purchase_place').val();
    place_purchase=value;
    if(place_purchase=='local'){
    $('#lead').val(1)
    $('#lead_time').val(1)
}
else{
    $("#lead").val(2);
     $("#lead_time").val(2);
}
    //alert(place_of_purchase_url)
    p = $("#item_forcast").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        select: true,
        info: true,
        lengthMenu: [10, 20, 50],
        responsive: true,

        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: place_of_purchase_url,
            dataType: "json",
            type: "GET",
            data:{
value:value
            },
        },

        AutoWidth: false,
        columns: [
            { data: "check" },
            { data: "item" },
            { data: "code" },
            { data: "unit" },
            { data: "available" },
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

    p.on("click", "tbody tr", function () {
        document.getElementById("select_all").checked = false;
        let data = p.row(this).data();
        let checkbox = document.getElementById(data["id"]);
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            if (selected.includes(data["id"])) {
            } else {
                selected.push(data["id"]);
            }
        } else {
            if (selected.includes(data["id"])) {
                selected = selected.filter(function (item) {
                    return item !== data["id"];
                });
            }
        }
        console.log("Spliced: " + selected);
    });

}
$("#approve_orders").on('click',function(){
    let load_approve=$('#load_approve').val();
   $.ajax({
       method: "GET",
       url: load_approve,
       success: function (data) {
           // $('#boot').click();
           $("#view_item_datails").html(data);
           $("#inforg").modal("show"); // show bootstrap modal
           $(".modal-title").text("Approve Orders");
       },
       error: function (jqXHR, textStatus, errorThrown) {
           // console.log(get_case_next_modal)
           alert("Error " + textStatus);
       },
   });
      });  
function approveOrder(id){
 const order_approved = $("#mark_approved").val();
 $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });
 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: order_approved,
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
           reloadOrders();

           
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

function DeclineOrder(id){
 const order_deny = $("#mark_deny").val();
 $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });
 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: order_deny ,
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
           reloadOrders();

           
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
function reloadOrders(){
    var p;

    var approval_issues = $("#orders_approvals").val();
    p = $("#orders_approvals_items").DataTable({
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
            url: approval_issues,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "order_no", width: "15%" },
            { data: "order_date", width: "10%" },
            { data: "order_by", width: "20%" },
            { data: "status", width: "10%" },
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

function viewOrder(id){
    let vieworder=$('#vieworder').val();
 $.ajax({
     method: "GET",
     url: vieworder,
     data: { id: id },
     success: function (data) {
         // $('#boot').click();
         $("#view_item_datails").html(data);
         $("#inforg").modal("show"); // show bootstrap modal
         $(".modal-title").text(" Order Details");
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + textStatus);
     },
 });
}