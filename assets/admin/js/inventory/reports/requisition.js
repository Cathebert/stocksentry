var t="";
    var p = "";
var requests_url = $("#load-requisition").val();

t = $("#requistion_table").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    paging: true,
    select: true,
    info: true,
    lengthMenu: [5, 10, 15],
    responsive: true,

    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: requests_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        {
            className: "dt-control",
            orderable: false,
            data: null,
            defaultContent: "",
        },

        { data: "code" },
        { data: "item_name" },
        { data: "catalog_number" },
        { data: "is_hazardous" },
        { data: "unit_issue" },
        { data: "store_temp" },
        { data: "total" },
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
t.on("click", "tbody tr", function () {
    let data = t.row(this).data();
    console.log(data);
    var tr = $(this).closest("tr");
    var row = t.row(tr);

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass("shown");
    } else {
        row.child(format(row.data())).show();
        tr.addClass("shown");
    }

    // alert("You clicked on " + data["available"] + "'s row");
});
$("#marked_requests tbody").on("click", "td.dt-control", function () {
    var tr = $(this).closest("tr");
    var row = t.row(tr);
    console.log(row);
    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass("shown");
    } else {
        row.child(format(row.data())).show();
        tr.addClass("shown");
    }
});

function format(rowData) {
    let more_details = $("#get_details").val();
    console.log(rowData);
    var div = $("<div/>").addClass("loading").text("Loading...");

    $.ajax({
        url: more_details,
        data: {
            id: rowData.item_id,
        },
        dataType: "json",
        success: function (json) {
            var info =
                '<div class="table-responsive"><table class="table table-sm table-striped table-light"><thead> <tr>';
            info +=
                '<th scope="col">#</th><th scope="col">Lab Name</th><th scope="col"> Requested Date</th><th scope="col">Requested By</th>';
            info +=
                '<th scope="col">Approved By</th><th scope="col">Cost</th> <th scope="col">Requested Quantity</th>';
            info += '</tr></thead> <tbody> <tr id="row1">';

            var x = 1;
            console.log(json.data.length);
            for (let index = 0; index < json.data.length; index++) {
                info += "<td>" + json.data[index]["sr_number"] + "</td>";
                info += "<td>" + json.data[index]["lab_name"] + "</td>";
                info += "<td>" + json.data[index]["requested_date"] + "</td>";
                info +=
                    "<td>" +
                    json.data[index]["name"] +
                    " " +
                    json.data[index]["last_name"] +
                    "</td>";
                info +=
                    "<td>" +
                    json.data[index]["approved_name"] +
                    " " +
                    json.data[index]["approved_lastname"] +
                    "</td>";
                info += "<td>" + json.data[index]["cost"] + "</td>";
                info +=
                    "<td>" +
                    json.data[index]["quantity_requested"] +
                    "</td></tr>";
                x++;
            }
            info += "</tbody></table></div>";
            console.log(info);
            div.html(info).removeClass("loading");
        },
    });

    return div;
}
function getSelectedRange(value) {
    if (value) {
        LoadData(value);
    }
    else{
     loadItemByName();      
    }
    if(value==99){
      loadItemByName()  
    }
}
function   LoadData(id){
   var  load_by_location_url=$('#load_by_location').val();

   t= $("#requistion_table").DataTable({
       processing: true,
       serverSide: true,
       destroy: true,
       paging: true,
       select: true,
       info: true,
       lengthMenu: [5, 10, 15],
       responsive: true,

       order: [[0, "desc"]],
       oLanguage: {
           sProcessing:
               "<div class='loader-container'><div id='loader'></div></div>",
       },
       ajax: {
           url: load_by_location_url,
           dataType: "json",
           type: "GET",
           data:{
            id:id
           }
       },

       AutoWidth: false,
       columns: [
           {
               className: "dt-control",
               orderable: false,
               data: null,
               defaultContent: "",
           },

           { data: "code" },
           { data: "item_name" },
           { data: "catalog_number" },
           { data: "is_hazardous" },
           { data: "unit_issue" },
           { data: "store_temp" },
           { data: "total" },
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
function  loadItemByName(){
  let requisition_url = $("#load-requisition").val();

  t = $("#requistion_table").DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      paging: true,
      select: true,
      info: true,
      lengthMenu: [5, 10, 15],
      responsive: true,

      order: [[0, "desc"]],
      oLanguage: {
          sProcessing:
              "<div class='loader-container'><div id='loader'></div></div>",
      },
      ajax: {
          url: requisition_url,
          dataType: "json",
          type: "GET",
      },

      AutoWidth: false,
      columns: [
          {
              className: "dt-control",
              orderable: false,
              data: null,
              defaultContent: "",
          },

          { data: "code" },
          { data: "item_name" },
          { data: "catalog_number" },
          { data: "is_hazardous" },
          { data: "unit_issue" },
          { data: "store_temp" },
          { data: "total" },
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
function getSelected(period){
    if(period==99){
        loadItemByName();
        return;
    }

     if (period == 10) {
         document.getElementById("custom").hidden = false;
         $("#start").focus();
     } else {
         document.getElementById("custom").hidden = true;
         getData(period);
     }
}
function getData(id){
     let requisition_by_period_url = $("#load-requisition_by_period").val();

     t = $("#requistion_table").DataTable({
         processing: true,
         serverSide: true,
         destroy: true,
         paging: true,
         select: true,
         info: true,
         lengthMenu: [5, 10, 15],
         responsive: true,

         order: [[0, "desc"]],
         oLanguage: {
             sProcessing:
                 "<div class='loader-container'><div id='loader'></div></div>",
         },
         ajax: {
             url: requisition_by_period_url,
             dataType: "json",
             type: "GET",
             data: {
                 period: id,
             },
         },

         AutoWidth: false,
         columns: [
             {
                 className: "dt-control",
                 orderable: false,
                 data: null,
                 defaultContent: "",
             },

             { data: "code" },
             { data: "item_name" },
             { data: "catalog_number" },
             { data: "is_hazardous" },
             { data: "unit_issue" },
             { data: "store_temp" },
             { data: "total" },
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
function getLastDate() {
    let period = 10;
    let start = $("#start").val();
    let end = $("#end").val();
    if (!start) {
        $("#start").focus();
        return;
    }
    getCustom(period, start, end);
}

function getCustom(period,start,end){
  let requisition_by_period_url = $("#load-requisition_by_period").val();

  t = $("#requistion_table").DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      paging: true,
      select: true,
      info: true,
      lengthMenu: [5, 10, 15],
      responsive: true,

      order: [[0, "desc"]],
      oLanguage: {
          sProcessing:
              "<div class='loader-container'><div id='loader'></div></div>",
      },
      ajax: {
          url: requisition_by_period_url,
          dataType: "json",
          type: "GET",
          data: {
              period: period,
              start_date: start,
              end_date:end,
          },
      },

      AutoWidth: false,
      columns: [
          {
              className: "dt-control",
              orderable: false,
              data: null,
              defaultContent: "",
          },

          { data: "code" },
          { data: "item_name" },
          { data: "catalog_number" },
          { data: "is_hazardous" },
          { data: "unit_issue" },
          { data: "store_temp" },
          { data: "total" },
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