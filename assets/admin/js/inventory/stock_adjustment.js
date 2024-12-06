var inventory = $("#load_inventory").val();
var checked = [];
var quantities = [];
var table;
   var select_adjust = $("#adjust_url").val();
	var search = $("#search_item").val();
let item_id="";
    //--->save whole row entery > end

   $("#add_new").on("click", function () {
       $.ajax({
           method: "GET",

           url: select_adjust,

           success: function (data) {
               $("#view_item_datails").html(data);
               $("#inforg").modal("show"); // show bootstrap modal
               $(".modal-title").text(" Select Items to  Adjust");
           },
           error: function (jqXHR, textStatus, errorThrown) {
               // console.log(get_case_next_modal)
               alert("Error " + errorThrown);
           },
       });
   });
function RunAdjustment(){
        $(".btn-close").click();
    if(selected.length==0){
        $.alert({
            icon: "fa fa-danger",
            title: "Missing information!",
            type: "red",
            content: "Please select  items  you want to adjust!",
        });
        return;

}

   let adjust_selected = $("#adjust_selected").val();

    disposals = $('#update_disposals').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      paging: false,
      select: true,
      info: false,
      sDom: 'lrtip',
      lengthMenu: [10, 20, 50],
      responsive: true,

      order: [[0, 'desc']],
      oLanguage: {
        sProcessing:
          "<div class='loader-container'><div id='loader'></div></div>",
      },
      ajax: {
        url: adjust_selected,
        dataType: 'json',
        type: 'GET',
        data: {
          selected: selected,
        },
      },
      initComplete: function (settings, json) {
        document.getElementById('dispose').hidden = false;
      },
      AutoWidth: false,

      columns: [
        { data: 'id' },
        { data: 'item' },
        { data: 'code' },
        { data: 'batch' },
        { data: 'catalog' },
        { data: 'unit' },
        { data: 'available' },
        { data: 'quantity' },
        { data: 'type' },
        { data: 'reason' },
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

function incrementValue(id){
   var myvalue=$('#adjusted_'+id).val()
   var converted=Number(myvalue);
   var adjusted=converted+1


$('#adjusted_'+id).val(adjusted)
 let note = $("#q_" + id).val();
 let type=$('#type_'+id).val();
   console.log(adjusted);
   addQuantityToAdjustment(id, adjusted,type, note);
}
function decrementValue(id) {
    var my_value= $("#adjusted_"+id).val();

    var converted = Number(my_value);
    if(converted==0){

    }
   else{
     var adjusted = converted - 1;
      let note= $("#q_" + id).val();
       let type = $('#type_' + id).val();
     $("#adjusted_"+id).val(adjusted);
     console.log(adjusted);
     addQuantityToAdjustment(id,adjusted,type, note)
   }
}
function getNumber(id, name) {
    var quantity = $("#c_" + name).val();
    console.log(quantity);
    document.getElementById("sel_" + name).checked = true;
    if (quantity) {
        AddIdToArray(name);
    }
}

function AddIdToArray(id) {
    if (document.getElementById("sel_" + id).checked == true) {
        if (checked.includes(id)) {
        } else {
            var quantity = $("#c_" + id).val();
            checked.push(id);
            quantities.push(quantity);
        }
    } else {
        if (checked.includes(id)) {
            checked = checked.filter(function (item) {
                return item !== id;
            });
        }
    }
    console.log(checked);
    console.log(quantities);
}



        //saveAdjustedItem(id, quantity);


function LoadTable(id) {
table = $("#adjust_inventories").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    paging: false,
    searching: false,
    info: false,
    lengthMenu: [10, 50, 100],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: inventory,
        dataType: "json",
        type: "GET",
        data: {
            id: id,
        },
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
         { data: "name",width:"30%"},
        { data: "code" },
          { data: "batch_number" },
        { data: "catalog" },
        { data: "unit" },
        { data: "available" },
        { data: "consumed", width: "15%" },
        { data: "status", width: "10%" },
        { data: "action", width: "5%" },
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
$("#adjust").on('click',function(e){
    e.preventDefault();
    var type=$('#type').val();
    var adjustment=$('#adjusted').val();
    var notes=$('#notes').val();

    var update_selected = $("#update_selected").val();

    if(!adjustment){
        $.alert({
            icon: "fa fa-danger",
            title: "Missing information!",
            type: "red",
            content: "Please search for item you want to adjust!",
        });
        return;
    }
  if(!notes){

document.getElementById('notes').focus();
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
        url: update_selected,
        data: {
            type: type,
            adjustment: adjustment,
            notes:notes,
            item:item_id,
        },
  beforeSend: function () {
                    ajaxindicatorstart("loading data... please wait...");
                },
        success: function (data) {
            //console.log(data)
             ajaxindicatorstop();
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
                $("#search_item").val("");

                table.destroy();

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
            // show bootstrap modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
})

$("#view_adjustment").on("click", function (e) {
    const adjustments=$('#view_adjustment_url').val();
         // e.preventDefault();
           $.ajax({
               method: "GET",
               url: adjustments,
               success: function (data) {
                   // $('#boot').click();
                   $('#view_item_datails').html(data);
                   $("#inforg").modal("show"); // show bootstrap modal

                   $(".modal-title").text("Adjustments");
               },
               error: function (jqXHR, textStatus, errorThrown) {
                   // console.log(get_case_next_modal)
                   alert("Error " + textStatus);
               },
           });
        })
function ApproveAdjustment(id) {


  let approve_selected= $('#approve_selected').val();
 $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });
 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: approve_selected,
     data: {

         id: id,
     },
 beforeSend: function () {
                    ajaxindicatorstart("approving adjustment... please wait...");
                },
     success: function (data) {
         //console.log(data)
          ajaxindicatorstop();
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
             reloadTable()
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
         // show bootstrap modal
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + errorThrown);
     },
 });

        }

function cancelAdjustment(id) {

  let cancel= $('#cancel').val();

 $.ajaxSetup({
     headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
     },
 });
 $.ajax({
     method: "POST",
     dataType: "JSON",
     url: cancel,
     data: {

         id: id,
     },
 beforeSend: function () {
                    ajaxindicatorstart("cancelling adjustment... please wait...");
                },
     success: function (data) {
       ajaxindicatorstop();
         //console.log(data)
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
             reloadTable()
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
         // show bootstrap modal
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + errorThrown);
     },
 });

        }
 function reloadTable() {

     k = $("#item_adjust").DataTable({
         processing: true,
         serverSide: true,
         destroy: true,
         paging: false,
         select: true,
         info: false,
         lengthMenu: [10, 20, 50],
         responsive: true,

         order: [[0, "desc"]],
         oLanguage: {
             sProcessing:
                 "<div class='loader-container'><div id='loader'></div></div>",
         },
         ajax: {
             url: load_forecast,
             dataType: "json",
             type: "GET",
              data:{
id:adjustid
        },
         },

         AutoWidth: false,
         columns: [
        { data: "id" },
        { data: "item",width:"30%" },
        { data: "code" },
        { data: "batch_number" },
        {data:"date"},
        { data: "available" },
        { data: "adjusted" },
        { data: "adjusted_by"},
        { data: "type"},
        { data: "remarks",width:"20%"},
        { data: "status",width:"10%"},
        { data: "action"},
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
function getNote(id, name){
    let note=$('#q_'+name).val()

    var adjusted= $("#adjusted_" + name).val();
let type = $('#type_'+name).val();
   addQuantityToAdjustment(name, adjusted, type,note);
  console.log(adjusted);

}

function addQuantityToAdjustment(id,adjusted,type,note){
    var id_value_note=id+"_"+adjusted+'_'+type+'_'+note;
            let startsWithBan = quantities.find((item) =>
                item.startsWith(id + "_")
            );
            console.log(startsWithBan);

            if (startsWithBan) {
                quantities = arrayRemove(quantities, startsWithBan);
                quantities.push(id_value_note);
            } else {
                quantities.push(id_value_note);
            }
}
function getAdjustedValue(id){
    var ad=id.split('_');
    var splited_id=ad[1];
    var my_value = $("#"+id).val();
    var note=$('#q_'+splited_id).val();
     let type = $('#type_'+splited_id).val();
   addQuantityToAdjustment(splited_id,my_value,type,note)

}
function getType(id,value) {
     var ad = id.split('_');
     var splited_id = ad[1];
     var my_value = $('#' + splited_id).val();
     var note = $('#q_' + splited_id).val();
   let type=value;
   addQuantityToAdjustment(splited_id, my_value, type, note);
}
function arrayRemove(arr, value) {
    return arr.filter(function (item) {
        return item != value;
    });
}

function RunItemsAdjustment() {
    let run_adjustment = $("#run_adjustment").val();

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    if (quantities.length == 0) {
        $.alert({
            icon: "fa fa-danger",
            title: "Missing information!",
            type: "red",
            content: "enter  details  for items you  want to adjust!",
        });
        return;

    }
     if (selected.length > quantities.length) {
      $.confirm({
          title: "Confirm!",
          content:"Some details have not been enter.Do you really  want to continue?",
          buttons: {
              Oky: {
                  btnClass: "btn-warning",
                  action: function () {
                      continueWithAdjustement();
                      return;
                  },
              },
              cancel: function () {},
          },
      });
      return;
     }
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: run_adjustment,
        data: {
            quantity: quantities,

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

                quantities.length = 0;
                selected.length=0;
                 ajaxindicatorstop();
                //disposals.destroy();

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
        error:function(error){
             ajaxindicatorstop();
        }
    });
}
 function continueWithAdjustement(){
    let continue_adjustment = $("#run_adjustment").val();
     $.ajax({
         method: "POST",
         dataType: "JSON",
         url: continue_adjustment,
         data: {
             quantity: quantities,
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
                 reasons.length = 0;
                 quantities.length = 0;
                 selected.length = 0;
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
         error: function (error) {
             ajaxindicatorstop();
         },
     });
 }
function ViewAdjusted(id){
 let view_disposal_modal_url = $("#view_adjusted_items").val();
 $.ajax({
     method: "GET",
     url: view_disposal_modal_url,
     data: {
         id: id,
     },

     success: function (data) {
       $('#view_item_datails').html(data);
       $('#inforg').modal('show'); // show bootstrap modal

       $('.modal-title').text(' Adjusted Items test');
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + errorThrown);
     },
 });
}
function ApproveBulkAdjustment(id){
    let approve_bulk = $("#approve_bulk").val();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: approve_bulk,
        data: {
            id: id,
        },
        beforeSend: function () {
            ajaxindicatorstart("approving adjustment... please wait...");
        },
        success: function (data) {
            //console.log(data)
            ajaxindicatorstop();
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
                reloadBulkTable();
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
            // show bootstrap modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });

}

function reloadBulkTable(){
    var o;
    let bulk_adjustment_list = $("#load_adjusted").val();

    o = $("#item_adjusted").DataTable({
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
            url: bulk_adjustment_list,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "adjust_date", width: "10%" },
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

function cancelBulkAdjustment(id) {
    let cancel_bulk = $("#cancel_bulk").val();

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: cancel_bulk,
        data: {
            id: id,
        },
        beforeSend: function () {
            ajaxindicatorstart("cancelling adjustment... please wait...");
        },
        success: function (data) {
            ajaxindicatorstop();
            //console.log(data)
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
                reloadBulkTable();
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
            // show bootstrap modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
