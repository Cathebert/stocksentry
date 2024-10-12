var t;
let variance_report=$('#variance_report')
t = $("#variance_table").DataTable({
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
        url: variance_report,
        dataType: "json",
        type: "GET",
    },
    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "stock_date", width: "30%" },
        { data: "lab_name" },
        { data: "supervised_by"},
        { data: "approved_by"},
      
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
