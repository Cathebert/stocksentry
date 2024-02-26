var inventory = $("#load_inventory").val();
var checked = [];
var quantities = [];
var table;
var item_search = $("#item_search").val();
var search = $("#search_item").val();
let item_id = "";
//--->save whole row entery > end

$("#search_item").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: item_search,
            type: "GET",
            dataType: "json",
            data: {
                search: request.term,
            },
            success: function (data) {
                response(data);
            },
        });
    },
    select: function (event, ui) {
        $("#search_item").val(ui.item.label);
        console.log(ui.item);
        item_id = ui.item.id;
          
        LoadTable(ui.item.id);
        return false;
    },
});
function incrementValue() {
    var value = $("#adjusted").val();
    var converted = Number(value);
    var adjusted = converted + 1;
    $("#adjusted").val(adjusted);
    console.log(adjusted);
}
function decrementValue() {
    var value = $("#adjusted").val();
    var converted = Number(value);
    if (converted == 0) {
    } else {
        var adjusted = converted - 1;
        $("#adjusted").val(adjusted);
        console.log(adjusted);
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
function saveAdjustment(id) {
    // console.log(consumed_form)

    var quantity = $("#c_" + id).val();
    if (!quantity) {
        $("#c_" + id).focus();
        return;
    }

    saveAdjustedItem(id, quantity);
}

function saveAdjustedItem(id, quantity) {
    var update_selected = $("#update_selected").val();

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
            id: id,
            consumed: quantity,
        },

        success: function (data) {
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
                $("#sel_" + id).prop("checked", true);
                $("#fa_" + id).removeClass("fa-save");
                $("#fa_" + id).addClass("fa-check");
               table.destroy();
               $("#search_item").val("");
                //LoadTable();
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
            { data: "code" },
            { data: "brand" },
            { data: "batch_number" },
            { data: "name" },
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
$("#adjust").on("click", function (e) {
    e.preventDefault();
    var type = $("#type").val();
    var adjustment = $("#adjusted").val();
    var notes = $("#notes").val();

    var update_selected = $("#update_selected").val();

    if (!adjustment) {
        $.alert({
            icon: "fa fa-danger",
            title: "Missing information!",
            type: "red",
            content: "Please search for item you want to adjust!",
        });
        return;
    }
    if (!notes) {
        document.getElementById("notes").focus();
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
            notes: notes,
            item: item_id,
        },

        success: function (data) {
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
                   $("#search_item").val("");
                table.destroy();
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
});

$("#view_adjustment").on("click", function (e) {
    const adjustments = $("#view_adjustment_url").val();
    // e.preventDefault();
    $.ajax({
        method: "GET",
        url: adjustments,
        success: function (data) {
            // $('#boot').click();
            $("#view_item_datails").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text("Adjustments");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + textStatus);
        },
    });
});
function ApproveAdjustment(id) {
    let approve_selected = $("#approve_selected").val();
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

        success: function (data) {
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
                   $("#search_item").val("");
                reloadTable();
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
 
    var load_forecast = $("#reload").val();
   table = $("#item_adjust").DataTable({
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
        },

        AutoWidth: false,
        columns: [
            { data: "code" },
            { data: "item" },
            { data: "available" },
            { data: "adjusted" },
            { data: "type" },
            { data: "remarks" },
            { data: "status" },
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
