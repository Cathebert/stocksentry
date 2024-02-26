"use strict";
var y;
var order_url = $("#orders").val();
var consolidated=[]
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
y = $("#order_items_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    info: true,
    destroy: true,
    lengthMenu: [5, 50, 100],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: order_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "check" },
        { data: "order", width: "5%" },
        { data: "lab", width: "10%" },
        { data: "delivery", width: "10%" },
        { data: "ordered_by", width: "10%" },
        { data: "approved_by", width: "10%" },
        { data: "action", width: "20%" },
        { data: "cons", width: "20%" },
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
function generatePDF(id, name) {
    var generate = $("#generate_pdf").val();
    $.ajax({
        url: generate,
        data: {
            id: id,
            action: name,
        },
    });
}
function showItemDetails(id) {
    var showGRNDetails = $("#showgrndetails").val();
    $.ajax({
        method: "GET",

        url: showGRNDetails,
        data: {
            id: id,
        },

        success: function (data) {
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Goods Received Note ");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}



function viewOrder(id){
  
   let show_details=$('#show_orders_details').val();
   $.ajax({
       method: "GET",

       url: show_details,
       data:{
        id:id,
       },

       success: function (data) {
           $("#receive_item").html(data);
           $("#inforg").modal("show"); // show bootstrap modal
           $(".modal-title").text(" Order Details ");
       },
       error: function (jqXHR, textStatus, errorThrown) {
           // console.log(get_case_next_modal)
           alert("Error " + errorThrown);
       },
   });
}
function consolidateItem(id,name){
 // alert(name);
    if(document.getElementById('cons_'+id).checked==true){
       
     document.getElementById("cons_" + id).checked = false;
       $("#" + id).removeClass("btn-success");
           if (consolidated.includes(id)) {
               consolidated = consolidated.filter(function (item) {
                
                   return item !== id;
               });
           } else {
               //consolidated.push(id);
                  
           }
           console.log(consolidated);
}
else{
  document.getElementById("cons_" + id).checked = true; 
  $('#'+id).removeClass('btn-info')
    $("#" + id).addClass("btn-success");
      if (consolidated.includes(id)) {
        
consolidated = consolidated.filter(function (item) {
    
    return item !== id;
     
});
      } else {
          consolidated.push(id);
          
      }
     console.log(consolidated); 
}
  

}
function consolidateOrder() {
   
    if(consolidated.length==0){
          $.alert({
              icon: "fa fa-danger",
              title: "Missing information!",
              type: "red",
              content: "Please select orders you want to  consolidate!",
          });
        return;
    }
let consolidate = $("#order_consolidate").val();

   $.ajaxSetup({
       headers: {
           "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
       },
   });
   $.ajax({
       method: "POST",
       dataType: "JSON",
       url: consolidate,
       data: {

           item_ids:consolidated,
       },

       success: function (data) {

           //console.log(data)
           consolidated.length=0;
           window.location=data.return_url;
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
          
           resolveOrderTable();
       },
       error: function (jqXHR, textStatus, errorThrown) {
           // console.log(get_case_next_modal)
           alert("Error " + errorThrown);
       },
   });
}
function resolveOrderTable(){
    y = $("#order_items_table").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        destroy: true,
        info: true,
        lengthMenu: [5, 50, 100],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: order_url,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "check" },
            { data: "order", width: "10%" },
            { data: "lab", width: "15%" },
            { data: "delivery", width: "15%" },
            { data: "ordered_by", width: "20%" },
            { data: "approved_by", width: "20%" },
            { data: "action", width: "10%" },
            { data: "cons", width: "10%" },
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
function MarkAsReceived(id){
    let marked_url=$('#marked_delivered').val();

     $.ajaxSetup({
         headers: {
             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
         },
     });
     $.ajax({
         method: "POST",
         dataType: "JSON",
         url: marked_url,
         data: {
             id: id,
         },

         success: function (data) {
             //console.log(data)
             //consolidated.length = 0;
             // window.location = data.return_url;
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

             resolveOrderTable();
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });

}

function MarkForConsolidation(id,type) {
    let mark_order_consolidate = $("#mark_orderconsolidate").val();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        url: mark_order_consolidate,
        dataType: "JSON",
        data: {
            id: id,
            type:type
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
            resolveOrderTable();
        },
        error: function (error) {
            console.log(error);
        },
    });
}
$("#view_marked").on("click", function (e) {
    e.preventDefault();
    const marked_consolidate = $("#view_marked_for_consolidation").val();

    $.ajax({
        method: "GET",
        url: marked_consolidate,

        success: function (data) {
            // $('#boot').click();
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text("View Details");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + textStatus);
        },
    });
});


$("#show_consolidated_history").on('click',function(e){
e.preventDefault();
 const  orders_consolidated = $("#view_consolidated").val();

 $.ajax({
     method: "GET",
     url: orders_consolidated,

     success: function (data) {
         // $('#boot').click();
         $("#receive_item").html(data);
         $("#inforg").modal("show"); // show bootstrap modal
         $(".modal-title").text("Orders to Supplier Consolidated");
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + textStatus);
     },
 });
})
