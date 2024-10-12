"use strict";
var consumption_update = $("#consumption_update").val();
var checked = [];
var quantities = [];
var p;
var inventory = $("#load_inventory").val();
$("#update_date").on("click", function (e) {
    e.preventDefault();

    $.ajax({
        method: "GET",
        url: consumption_update,

        success: function (data) {
            //console.log(data)
            $("#add_certi").html(data);
            $("#infor").modal("show"); // show bootstrap modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
});
function getText(id, name) {
    var quantity = $("#c_" + name).val();
    console.log(quantity);
    document.getElementById("sel_" + name).checked = true;
    if (quantity) {
        AddIdToArray(name);
    }
}
function saveConsumed(id) {
    // console.log(consumed_form)

    var period = $("#period").val();
    var custom_start = $("#start_date").val();
    var custom_end = $("#end_date").val();
    var quantity = $("#c_" + id).val();
    if (!quantity) {
        $("#c_" + id).focus();
        return;
    }

    if (period) {
        saveConsumedItem(id, quantity, period);
    } else if (custom_start && custom_end) {
        saveConsumedItem(id, quantity, 4);
    } else {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please Select Period or Set range !",
        });
    }
}
function saveConsumedItem(id, quantity, period) {
    var update_selected = $("#update_selected").val();
    var consumed_form = $("#consume_form").serialize();
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
            consumed_form,
            id: id,
            consumed: quantity,
            period: period,
        },
  beforeSend: function () {
                    ajaxindicatorstart("updating data... please wait...");
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
                   LoadTable();
                
             
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
}
function updateAll() {
    var period = $("#period").val();
    var custom_start = $("#start_date").val();
    var custom_end = $("#end_date").val();

    if (period) {
        if (checked.length == 0 || quantities.length == 0) {
            $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please enter quantities to update consumption !",
        });
            return;
        }
        doUpdate(period);
    } else if (custom_start && custom_end) {
        if (checked.length == 0 || quantities.length == 0) {
            return;
        }
        doUpdate(4);
    } else {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please Select Period or Set range !",
        });
        return;
    }
}
function doUpdate(period) {
    let update_all = $("#inventory_update_all").val();
    let consumed_form_data = $("#consume_form").serialize();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: update_all,
        data: {
            consumed_form_data,
            ids: checked,
            consumed: quantities,
            period: period,
        },
  beforeSend: function () {
                    ajaxindicatorstart("updating data... please wait...");
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
                 LoadTable();
               
               
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
}
function AddIdToArray(id) {
     let check_quantity = $("#check_quantity").val();
    if (document.getElementById("sel_" + id).checked == true) {
        if (checked.includes(id)) {
        } else {
            var quantity = $("#c_" + id).val();
            checked.push(id);
            
           
            
             $.ajax({
            method: "GET",
            dataType: "JSON",
            url: check_quantity,
            data: {
                id: id,
                quantity: quantity,
            },

            success: function (data) {
                if (data.error == false) {
                      quantities.push(quantity);
                    console.log(quantities);
                } else {
                    $.alert({
                        icon: "fa fa-danger",
                        title: "Error",
                        type: "red",
                        content: data.message,
                    });
                    $("#c_"+id).val('')
                     $("#c_"+id).focus();  
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // console.log(get_case_next_modal)
                alert("Error " + errorThrown);
            },
        });
 
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

function LoadTable() {
    p = $("#update_inventories").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        destroy:true,
        scrollCollapse: true,
      
        info: true,

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
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "name", width: "30%" },
            { data: "code", width: "15%" },
            { data: "batch_number" },
            { data: "catalog" },
      { data: "expiry" },
           { data: "last_update",width:"15%" },
        { data: "next_update",width:"15%" },
            { data: "unit" },
            { data: "available" },
            { data: "consumed" },
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

p = $("#update_inventories").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy:true,
    info: true,

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
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
            { data: "name", width: "30%" },
            { data: "code", width: "15%" },
            { data: "batch_number" },
            { data: "catalog" },
             { data: "expiry" },
           { data: "last_update",width:"15%" },
        { data: "next_update",width:"15%" },
            { data: "unit" },
            { data: "available" },
            { data: "consumed" },
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