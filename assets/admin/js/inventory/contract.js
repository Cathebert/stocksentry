"use strict"
let contract_url=$('#contract_url').val();

var t = "";
t = $("#contract").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    lengthMenu: [10, 50, 100],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: contract_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: true,

    columns: [
        { data: "id" },
        { data: "contract_number" },
        { data: "contract_name" },
        { data: "contract_desc" },
        { data: "contract_start" },
        { data: "contract_end" },
         { data: "sub_type" },
          { data: "supplier" },
           { data: "status" },
            { data: "action",width:"30%" },
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
function showContractAdd(){
    let showAddModal=$('#shoW_modal').val();
    $.ajax({
        method: "GET",

        url: showAddModal,
      

        success: function (data) {
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Contract Add ");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
function viewContract(id){
    let viewContract = $("#view_contract").val();
    $.ajax({
        method: "GET",

        url: viewContract,
        data: {
            id: id,
        },

        success: function (data) {
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Contract Add ");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
function updateContract(id) {
    let updateContract = $("#update_contract").val();
    $.ajax({
        method: "GET",

        url: updateContract,
        data: {
            id: id,
        },

        success: function (data) {
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Contract Add ");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
function filterByNumber(val){
let value=val
let type='number';
search(value,type)

}

function filterByName(val){
    let value=val
    let type='name';
    search(value,type)
}
function search(value,type){
    let filter = $("#filter").val();
    t = $("#contract").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        lengthMenu: [10, 50, 100],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: filter,
            dataType: "json",
            type: "GET",
            data: {
                value: value,
                type: type,
            },
        },

        AutoWidth: true,

        columns: [
            { data: "id" },
            { data: "contract_number" },
            { data: "contract_name" },
            { data: "contract_desc" },
            { data: "contract_start" },
            { data: "contract_end" },
            { data: "sub_type" },
            { data: "supplier" },
            { data: "status" },
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