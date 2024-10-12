"use strict";
let consumption_history = $("#consumption_taken_history_url").val();
var t = "";
t = $("#consumption_history").DataTable({
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
        url: consumption_history,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: true,

    columns: [
        { data: "id" },
        { data: "consumption_type" },
        { data: "range" },
        { data: "consumed_count" },
        { data: "updated_by" },
        { data: "action" },
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
function ViewConsumptionDetails(id) {
    var viewconsumption_taken_details = $("#viewconsumptionTakenDetails").val();
    $.ajax({
        method: "GET",

        url: viewconsumption_taken_details,
        data: {
            id: id,
        },

        success: function (data) {
            $("#view_item_datails").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Consumption Details ");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}

function ApproveStockTaken(id) {
    let approve = $("#approve_stock").val();

    $.confirm({
        title: "Warning!",
        content:
            "Do you really  want to approve this stock.The current Stock will be replaced",
        buttons: {
            Oky: {
                btnClass: "btn-warning",
                action: function () {
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
                    toastr["infor"](
                        "Stock update is in progress.We will notify you when it is done"
                    );
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });

                    $.ajax({
                        method: "POST",
                        url: approve,
                        dataType: "JSON",
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
                                LoadTable();
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
                                ajaxindicatorstop();
                            }
                        },
                        error: function (error) {
                            console.log();
                        },
                    });
                },
            },

            cancel: function () {},
        },
    });
}
function LoadTable() {
    t = $("#stock_history").DataTable({
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
            url: stock_history,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: true,

        columns: [
            { data: "id" },
            { data: "date" },
            { data: "supervisor" },
            { data: "view" },
            { data: "action" },
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