"use strict"
let received=$('#received_orders').val()
let table=''

table = $("#received_orders_table").DataTable({
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
        url: received,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id" },
        { data: "order", width: "5%" },
        { data: "lab", width: "10%" },
        { data: "delivery", width: "10%" },
        { data: "ordered_by", width: "10%" },
          { data: "received_by", width: "10%" },
        { data: "approved_by", width: "10%" },
      
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
function viewOrder(id) {
    let show_details = $("#show_orders_details").val();
    $.ajax({
        method: "GET",

        url: show_details,
        data: {
            id: id,
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