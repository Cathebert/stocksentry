"use strict"
var p=""
var t="";
var y="";
var consumption_report=$("#consumption_report").val();

p = $("#consumption_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    scrollCollapse: true,
    destroy: true,
    info: true,

    lengthMenu: [10, 15, 20],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: consumption_report,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id" },
        { data: "item_name" },

        { data: "catalog_number" },
        { data: "unit_issue" },
        { data: "consumed" },
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
 /*p.on("click", "tbody tr", function () {
     let data = p.row(this).data();
     console.log(data);
     var tr = $(this).closest("tr");
     var row = p.row(tr);

     if (row.child.isShown()) {
         row.child.hide();
         tr.removeClass("shown");
     } else {
         row.child(format(row.data())).show();
         tr.addClass("shown");
     }

     // alert("You clicked on " + data["available"] + "'s row");
 });

 $("#consumption_table tbody").on("click", "td.dt-control", function () {
     var tr = $(this).closest("tr");
     var row = p.row(tr);
     console.log(row);
     if (row.child.isShown()) {
         row.child.hide();
         tr.removeClass("shown");
     } else {
         row.child(format(row.data())).show();
         tr.addClass("shown");
     }
 });*/

 function format(rowData) {
     let more_details = $("#get_details").val();
     let period=$('#period').val();
     console.log(rowData);
     let start=$('#start').val();
     let end= $('#end').val();
     var div = $("<div/>").addClass("loading").text("Loading...");

     $.ajax({
         url: more_details,
         data: {
             id: rowData.item_id,
             period: period,
             start_date: start,
             end_date:end,
         },
         dataType: "json",
         success: function (json) {
             var info =
                 '<div class="table-responsive"><table class="table table-sm table-striped table-light"><thead> <tr>';
             info +=
                 '<th scope="col">#</th><th scope="col">Lab Name</th><th scope="col">Batch # </th><th scope="col">Consumed </th>';

             info += '</tr></thead> <tbody> <tr id="row1">';

             var x = 1;
             console.log(json.data.length);
             for (let index = 0; index < json.data.length; index++) {
                 info += "<td>" + json.data[index]["id"] + "</td>";
                 info += "<td>" + json.data[index]["lab_name"] + "</td>";
                 info += "<td>" + json.data[index]["batch_number"] + "</td>";
                 info += "<td>" + json.data[index]["consumed"] + "</td></tr>";

                 x++;
             }
             info += "</tbody></table></div>";
             console.log(info);
             div.html(info).removeClass("loading");
         },
     });

     return div;
 }
 function getSelected(value){
    if(value==99){
return;
    }
    if(value==10){
        document.getElementById('custom').hidden=false;
        $('#start').focus();
       
    }
    else{
         document.getElementById("custom").hidden = true;
getData(value)
 }
 }
 function getLastDate(){
    let period=10;
    let start=$('#start').val();
    let end=$('#end').val();
    if(!start){
       $("#start").focus(); 
       return;
    }
getCustom(period,start,end);
 }
function getData(id){
    var filter = $("#filter").val();
    
p = $("#consumption_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    scrollCollapse: true,
    destroy: true,
    info: true,

    lengthMenu: [10, 15, 20],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: filter,
        dataType: "json",
        type: "GET",
        data: {
            value: id,
        },
    },

    AutoWidth: false,
    columns: [
        { data: "id" },
        { data: "item_name" },
        { data: "catalog_number" },
        { data: "unit_issue" },
        { data: "consumed" },
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

function getCustom(period,start,end){
    var filtered = $("#filter").val();

    p = $("#consumption_table").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        scrollCollapse: true,
        destroy: true,
        info: true,

        lengthMenu: [10, 15, 20],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: filtered,
            dataType: "json",
            type: "GET",
            data: {
                value: period,
                start_date: start,
                end_date: end,
            },
        },

        AutoWidth: false,
        columns: [
            { data: "id" },
            { data: "item_name" },
            { data: "catalog_number" },
            { data: "unit_issue" },
            { data: "consumed" },
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
function changeFrequency(value){
     var print_item = $("#frequency_change").val();

     $.ajax({
         method: "GET",

         url: print_item,
         data: {
             id: value,
         },

         success: function (data) {
            $('#start_date').val(data.date)
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
}


    $('#report_download').on('click',function(e){
        e.preventDefault();
        let pe=$('#period').val();
      runReport('download',pe)
        
    })
function runReport(type,period){
      let download = $("#generate_report").val();
     $.ajax({
         method: "GET",
        dataType:"JSON",
         url: download,
         data: {
           
             type: type,
             period:period
         },

         success: function (data) {
            window.location=data.path;
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
       
}

function getDefaultData(){

 p = $("#consumption_table").DataTable({
     processing: true,
     serverSide: true,
     paging: true,
     scrollCollapse: true,
     destroy: true,
     info: true,

     lengthMenu: [10, 15, 20],
     responsive: true,
     order: [[0, "desc"]],
     oLanguage: {
         sProcessing:
             "<div class='loader-container'><div id='loader'></div></div>",
     },
     ajax: {
         url: consumption_report,
         dataType: "json",
         type: "GET",
     },

     AutoWidth: false,
     columns: [
         { data: "id" },
         { data: "item_name" },

         { data: "catalog_number" },
         { data: "unit_issue" },
         { data: "consumed" },
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