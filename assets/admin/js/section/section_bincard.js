"use strict";

var y = "";
var bin_url = $("#get_bincard").val();
var item_id;
function onlyOne(checkbox) {}
function CheckBoxSelect(id) {
    $(".list-group-item-info").removeClass("list-group-item-info");
    $("#" + id).addClass("list-group-item-info");
    item_id = id;
    y.destroy();
    getItemDetails(id);
}
y = $("#bin_card").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy: true,
    scrollCollapse: true,
    paging: false,
    info: false,
    dom: "lrtip",

    lengthMenu: [10, 15, 20],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: bin_url,
        dataType: "json",
        type: "GET",
    },
    initComplete: function (settings, json) {
        var total = json.total;
            console.log(json);
            if (json.stock_taken == 1) {
                $("#out").text(json.out);
            } else {
                $("#out").text("");
            }
        $("#balance").val(total);
        $("#item_name").html(
            "<i><strong>" + json.item_name + "</strong></i> | Bin Card"
        );
        $("#open").text(total);
        $("#consumed").text(json.consumed);
        var countj = json.count;
        if (json.out.length > 0) {
            for (var i = 0; i < json.out.length; i++) {
                $("#out").text(json.out[i]["quantity"]);
            }
        } else {
            $("#out").text(0);
        }
    },
    AutoWidth: true,
    columns: [
        { data: "id" },
        { data: "date" },
        { data: "quantity_in" },
        { data: "batch_number" },
       
        { data: "supplier" },
        { data: "cost" },
        { data: "expiry_date" },
        { data: "quantity_out" },
        { data: "balance" },
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
function getItemDetails(id) {
    var url = $("#url").val();
    y = $("#bin_card").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        scrollCollapse: true,
        paging: false,
        info: false,
        dom: "lrtip",

        lengthMenu: [10, 15, 20],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: bin_url,
            dataType: "json",
            type: "GET",
            data: {
                id: id,
            },
        },
        initComplete: function (settings, json) {
            var total = json.total;

            if (json.out.length > 0) {
                for (var i = 0; i < json.out.length; i++) {
                    $("#out").text(json.out[i]["quantity"]);
                }
            } else {
                $("#out").text(0);
            }
            console.log(json.image);
            console.log(url);
            $("#balance").val(total);
            $("#item_name").html(
                "<i><strong>" + json.item_name + "</strong></i> | Bin Card"
            );
            $("#open").text(total);
            $("#consumed").text(json.consumed);

            if (json.image) {
                var img = url + "/public/upload/items/" + json.image;
                console.log(img);
                $("#img_card").attr("src", img);
            } else {
                var img = url + "/assets/icon/not_available.jpg";
                console.log(img);
                $("#img_card").attr("src", img);
            }
        },
        AutoWidth: true,
        columns: [
            { data: "id" },
            { data: "date" },
            { data: "quantity_in" },
            { data: "batch_number" },
              { data: "description", width: "20%" },
            { data: "supplier" },
            { data: "cost" },
            { data: "expiry_date" },
            { data: "quantity_out" },
            { data: "balance" },
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
function searchTerm() {
    document
        .getElementById("search_form")
        .addEventListener("submit", function (event) {
            event.preventDefault();
            var search_term = $("#search_term").val();
            if (!search_term) {
                $("#search_term").focus();
                return;
            }
            var is_reload = document.getElementById("search_button").innerHTML;
            if (is_reload == "Reload") {
                reloadForm();
            } else {
                submitForm(search_term);
            }
        });
}
function submitForm(params) {
    var search_result = $("#search_result").val();
    //Salert(search_result)
    $.ajax({
        method: "GET",
        url: search_result,
        dataType: "JSON",
        data: {
            search: params,
        },

        success: function (data) {
            console.log(data.data);
            $("#search").html("");
            var html = "";
            console.log(data.data.length);
            var num = 1;
            if (data.data.length > 0) {
                for (var x = 0; x < data.data.length; x++) {
                    html +=
                        '<div class="input-group-prepend"><span class="input-group-text">' +
                        num +
                        '</span><li class="list-group-item list-group-item-action" style="text-align:left"  id=' +
                        data.data[x]["id"] +
                        ' onclick="CheckBoxSelect(this.id)"><a>' +
                        data.data[x]["item_name"] +
                        "</a></li></div>";
                    num++;
                }
                $("#search").html(html);
                $("#foot").hide();
            } else {
                $("#search").html("<i>data not found</i>");
                $("#search_button").text("Reload");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}

function reloadForm(id) {
    var search_result = $("#search_result").val();
    //Salert(search_result)
    $.ajax({
        method: "GET",
        url: search_result,
        dataType: "JSON",
        data: {
            search: "reload",
        },

        success: function (data) {
            console.log(data.data);
            $("#search").html("");
            var html = "";
            console.log(data.length);
            var num = 1;
            if (data.data.length > 0) {
                for (var x = 0; x < data.data.length; x++) {
                    html +=
                        '<div class="input-group-prepend"><span class="input-group-text">' +
                        num +
                        '</span><li class="list-group-item list-group-item-action" style="text-align:left"  id=' +
                        data.data[x]["id"] +
                        ' onclick="CheckBoxSelect(this.id)"><a>' +
                        data.data[x]["item_name"] +
                        "</a></li></div>";
                    num++;
                }
                $("#search").html(html);
                $("#foot").show();
                $("#search_button").text("Search");
                $("#search_term").val("");
            } else {
                $("#search").html("<i>data not found</i>");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}

function filterDate() {
    var end = $("#end_date").val();
    var start = $("#start_date").val();

    var start_date = new Date(start);
    var end_date = new Date(end);
    if (!start) {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please Select start date !",
        });
        return;
    }
    if (start_date > end_date) {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Start date should be less than end date",
        });
        return;
    }
    if (!item_id) {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please Select Item  ",
        });
        return;
    }
    y.destroy();
    filterByDate(start, end);
}
function filterByDate(start, end) {
    var filter_by_date = $("#filter_by_date").val();
    y = $("#bin_card").DataTable({
        processing: true,
        serverSide: true,
         destroy: true,
        paging: true,
        scrollCollapse: true,
        info: false,
        dom: "lrtip",

        lengthMenu: [10, 15, 20],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: filter_by_date,
            dataType: "json",
            type: "GET",
            data: {
                id: item_id,
                start: start,
                end: end,
            },
        },
        initComplete: function (settings, json) {
            var total = json.total;
  $("#out").text(json.out);
            if (json.out.length > 0) {
                for (var i = 0; i < json.out.length; i++) {
                    $("#out").text(json.out[i]["quantity"]);
                }
            } else {
                $("#out").text(0);
            }
            console.log(json.image);
            console.log(url);
            $("#balance").val(total);
            $("#item_name").html(
                "<i><strong>" + json.item_name + "</strong></i> | Bin Card"
            );
            $("#open").text(total);
            $("#consumed").text(json.consumed);

            /*    if (json.image) {
            var img = url + "/public/upload/items/" + json.image;
            console.log(img);
            $("#img_card").attr("src", img);
        } else {
            var img = url + "/assets/icon/not_available.jpg";
            console.log(img);
            $("#img_card").attr("src", img);
        } */
        },
        AutoWidth: true,
        columns: [
            { data: "id" },
            { data: "date" },
            { data: "quantity_in" },
            { data: "batch_number" },
             { data: "description", width: "20%" },
            { data: "supplier" },
            { data: "cost" },
            { data: "expiry_date" },
            { data: "quantity_out" },
            { data: "balance" },
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
