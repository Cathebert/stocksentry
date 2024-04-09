"use strict";
let expiry_report = $("#expiry_report").val();
let filtered_report = $("#expiry_report").val();

var t;

t = $("#expiry_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    scrollCollapse: true,

    info: true,

    lengthMenu: [10, 20, 50],
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
        { data: "name", width: "15%" },
        { data: "item" },
        { data: "brand", width: "15%" },
        { data: "batch_number" },
        { data: "lab"},
        { data: "date"},
        { data: "quantity"},
        { data: "remark" },
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
function getSelected(value) {
    t.destroy();
    // getTableData();

        getTableData();
    
}

function getSelectedRange(value) {
    t.destroy();
    if (value == -1) {
        getTableData();
        return
    } 
    if(value==10){
             document.getElementById('custom').hidden=false;
        $('#start').focus();
       
    }
    else{
         document.getElementById("custom").hidden = true;


     getTableData();
}
}
function getTableData() {
    var expiry_form = $("#expiry_form").serialize();
  var expired_by_range = $("#filter_by_range").val();
    t = $("#expiry_table").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        scrollCollapse: true,

        info: true,

        lengthMenu: [10, 20, 50],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: expired_by_range,
            dataType: "json",
            type: "GET",
            data: {
                expiry_form,
            },
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
            { data: "name", width: "15%" },
            { data: "item" },
            { data: "brand", width: "15%" },
            { data: "batch_number" },
            { data: "lab" },
            { data: "date" },
            { data: "quantity" },
            { data: "remark" },
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
function getSelectedByLab(value) {
    var expired_by_lab = $("#filter_by_lab").val();

    t = $("#expiry_table").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        scrollCollapse: true,

        info: true,

        lengthMenu: [10, 20, 50],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: expired_by_lab,
            dataType: "json",
            type: "GET",
            data: { lab: value },
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
            { data: "name", width: "15%" },
            { data: "item" },
            { data: "brand", width: "15%" },
            { data: "batch_number" },
            { data: "lab" },
            { data: "date" },
            { data: "quantity" },
            { data: "remark" },
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
function getSelectedByRange(value) {
    var expired_by_range = $("#filter_by_range").val();

    t = $("#expiry_table").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        scrollCollapse: true,

        info: true,

        lengthMenu: [10, 20, 50],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: expired_by_range,
            dataType: "json",
            type: "GET",
            data: { range: value },
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
            { data: "name", width: "15%" },
            { data: "item" },
            { data: "brand", width: "15%" },
            { data: "batch_number" },
            { data: "lab" },
            { data: "date" },
            { data: "quantity" },
            { data: "remark" },
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
 function getLastDate() {
     let period = 10;
     let start = $("#start").val();
     let end = $("#end").val();
     if (!start) {
         $("#start").focus();
         return;
     }
 getTableData()
 }