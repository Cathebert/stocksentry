"use strict";
var url = $("#post_url").val();
var user_url = $("#supplier_list_url").val();

var t = "";

function loadLab() {
    location.reload();
}
t = $("#suppliersTable").DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [10, 50, 100],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: user_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "name", width: "10%" },
        { data: "address", width: "10%" },
        { data: "email", width: "10%" },
        { data: "phone_number", width: "10%" },
        { data: "expiry", width: "10%" },
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
function editSupplier(id) {
    var load_edit_modal_url = $("#edit_modal").val();
    $.ajax({
        method: "GET",
        url: load_edit_modal_url,
        data: {
            id: id,
        },

        success: function (data) {
            //console.log(data)
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}

$("#inputsection").select2({
    allowClear: true,
    placeholder: "Select Lab Sections",
    multiple: true,
});
function hasChanged(value) {
    if (value == "yes") {
        $("#sections").show();
    } else {
        $("#sections").hide();
    }
}

function deleteSupplier(id) {
    var delete_user = $("#delete_supplier").val();
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to delete this supplier. Any transaction associated with this supplier will be removed?!",
        buttons: {
            Oky: {
                btnClass: "btn-danger",
                action: function () {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });

                    $.ajax({
                        method: "POST",
                        url: delete_user,
                        dataType: "JSON",
                        data: {
                            id: id,
                        },
                        success: function (data) {
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
                            loadLab();
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

    ///
}

function resetPassword(id) {
    var reset_user = $("#reset_user").val();
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to reset  this user's Password?!",
        buttons: {
            Oky: {
                btnClass: "btn-info",
                action: function () {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });

                    $.ajax({
                        method: "POST",
                        url: reset_user,
                        dataType: "JSON",
                        data: {
                            id: id,
                        },
                        success: function (data) {
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
                            loadLab();
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

    ///
}
function getLabName(value) {
    const receive_from = $("#lab_id").val();
    const get_sections = $("#get_sections").val();
    if (value == 99) {
        document.getElementById("not_coldroom").hidden = true;
        document.getElementById("coldroom").hidden = false;
        document.getElementById("my_lab").hidden = true;
        $("#check").val(1);
    }

    if (value > 0 && value < 99) {
        document.getElementById("my_lab").hidden = false;
        document.getElementById("not_coldroom").hidden = true;
        document.getElementById("coldroom").hidden = true;
        $("#check").val(2);
    }

    if (value == 0) {
        document.getElementById("not_coldroom").hidden = false;
        document.getElementById("my_lab").hidden = true;
        document.getElementById("coldroom").hidden = true;
        $("#check").val(0);
    }
}