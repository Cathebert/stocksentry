"use strict";
var y;
var received_items=$('#inventory_received').val()

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
y = $("#received_items_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy:true,
    info: true,
    lengthMenu: [5, 50, 100],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: received_items,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "receiving_date", width: "15%" },
        { data: "Supplier", width: "15%" },
        { data: "Received_by", width: "20%" },
        { data: "action", width: "10%" },
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
function generatePDF(id,name){
    var generate=$('#generate_pdf').val()
    $.ajax({
        url:generate,
        data:{
            id:id,
            action:name
        }
    })
}
function  showItemDetails(id){
    var showGRNDetails=$('#showgrndetails').val()
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
function getDates(){
    const start_date =$('#start_date').val();
    if(!start_date){
          $.alert({
              icon: "fa fa-warning",
              title: "Missing information!",
              type: "orange",
              content: "Please enter Start date!",
          });
          return;
    }
    loadFilteredData()
}
function getSupplier() {
 loadFilteredData();
}
function loadFilteredData() {

      const all_receipts = $("#form_id").serialize();
      const filtered = $("#received_filters").val();
      
      $.ajaxSetup({
          headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
      });
      y = $("#received_items_table").DataTable({
          processing: true,
          serverSide: true,
          paging: true,
          destroy: true,
          info: true,
          lengthMenu: [10, 20, 50],
          responsive: true,
          order: [[0, "desc"]],
          oLanguage: {
              sProcessing:
                  "<div class='loader-container'><div id='loader'></div></div>",
          },
          ajax: {
              url: filtered,
              dataType: "json",
              type: "GET",
              data: {
                  all_receipts,
              },
          },

          AutoWidth: false,
          columns: [
              { data: "id", width: "3%" },
              { data: "receiving_date", width: "35%" },
              { data: "Supplier", width: "40%" },
              { data: "Received_by", width: "20%" },
              { data: "action", width: "20%" },
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