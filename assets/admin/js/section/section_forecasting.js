"use strict";
var forecast = $("#forecast").val();
var selected = [];
var entered = 0;
var quantities = [];
function LoadInventory(e) {
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
function selectItem(id) {
    if (document.getElementById(id).checked == true) {
        document.getElementById(id).checked = false;
        if (selected.includes(id)) {
        } else {
            selected.push(id);
        }
        console.log(selected);
    } else {
        document.getElementById(id).checked = true;
    }
}

function RunForecast() {
    const url_selected = $("#run_forecast").val();
    const lead = $("#lead").val();
    const order = $("#order").val();

    console.log(url_selected);
    if (!selected) {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Select items to run forecast on!",
        });
        return;
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
            let elements = document.getElementsByName("ordered");
            console.log(elements.length);
            for (var i = 0; i < elements.length; i++) {
                quantities.push(elements[i].id + "_" + elements[i].value);
            }
            console.log(quantities);
        },

        AutoWidth: false,
        columns: [
            { data: "code", width: "15%" },
            { data: "supplier", width: "25%" },
            { data: "item" },
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
    selected.splice(0, selected.length);
}

function getOrdered(id_item, item_n) {
    quantities.length = 0;
    let elements = document.getElementsByName("ordered");
    console.log(elements.length);
    for (var i = 0; i < elements.length; i++) {
        quantities.push(elements[i].id + "_" + elements[i].value);
    }
    console.log(quantities);
}
function PlaceOrder() {
    let order_url = $("#order_url").val();
    let order_number = $("#order_number").val();
    const lead = $("#lead").val();
    const order = $("#order").val();
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
            order_number: order_number,
            lead: lead,
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
                $.alert({
                    icon: "fa fa-success",
                    title: "Success!",
                    type: "orange",
                    content: data.message,
                });
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
