"use strict"
let recieved_status=$('#received_status').val();

var t="";
t = $("#received_items_status").DataTable({
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
        url: recieved_status,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "receiving_date", width: "15%" },
        { data: "Supplier", width: "15%" },
        { data: "checked_by", width: "20%" },
        { data: "reviewed_by", width: "20%" },
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
function showItemDetails(id){
    let details=$('#view_received_status_details').val();

 $.ajax({
     method: "GET",

     url: details,
     data: {
         id: id,
     },

     success: function (data) {
         $("#receive_item").html(data);
         $("#inforg").modal("show"); // show bootstrap modal
         $(".modal-title").text(" Goods Received  Status Checklist");
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + errorThrown);
     },
 });
    
}