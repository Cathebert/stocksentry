"use strict";
var mail_url = $("#mail_url").val();


var t = "";


t = $("#system_mail_list").DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [10, 50, 100],
    responsive: true,
    order: [[0, "desc"]],

    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    language: {
        emptyTable: "There are no system generated emails.",
    },
    ajax: {
        url: mail_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "date", width: "10%" },
        { data: "lab", width: "10%" },
        { data: "subject", width: "10%" },
        { data: "type", width: "10%" },
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
