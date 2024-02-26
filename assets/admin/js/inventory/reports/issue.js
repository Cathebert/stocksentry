"use strict";
var issue_report = $("#issue_report").val();

var t;

t = $("#issue_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    scrollCollapse: true,

    info: true,

    lengthMenu: [10, 15, 20],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url:issue_report,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id" },
        { data: "siv" },
        { data: "to_lab" },
        { data: "issued_by" },
        { data: "approved_by" },
        { data: "received_by" },
        { data: "issue_date" },
        { data: "status" },
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
function getSelected() {
    t.destroy();
    getTableData();
}

function getSelectedRange() {
    t.destroy();
    getTableData();
}

function getTableData() {
    var expiry_form = $("#issue_form").serialize();

    t = $("#expiry_table").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        scrollCollapse: true,

        info: true,

        lengthMenu: [10, 15, 20],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: expiry_report,
            dataType: "json",
            type: "GET",
            data: { expiry_form, selected: "selected" },
        },
        initComplete: function (settings, json) {
            var total = parseFloat(json.total).toFixed(2);
            var formated = new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "MKW",
            }).format(total);

            $("#value").html(formated);
            $("#quantity").html(json.quantity);
        },
        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "item", width: "15%" },
            { data: "brand", width: "15%" },
            { data: "batch_number" },
            { data: "name" },
            { data: "location" },
            { data: "expire_date" },
            { data: "quantity" },
            { data: "cost" },
            { data: "est_loss" },
            { data: "status" },
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
function changeFrequency(value) {
    var print_item = $("#frequency_change").val();

    $.ajax({
        method: "GET",

        url: print_item,
        data: {
            id: value,
        },

        success: function (data) {
            $("#start_date").val(data.date);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}