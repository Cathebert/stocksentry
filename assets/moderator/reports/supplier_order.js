'use strict'
let requests_url = $('#supplier-orders').val();
let t=""
t = $("#supplier_order_table").DataTable({
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
            data: "id",
        },
        { data: "item_name",width:"30%" },
        { data: "code" },

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
/*t.on("click", "tbody tr", function () {
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
});*/

function format(rowData) {
    let more_details = $("#supplier_details").val();
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
                '<th scope="col">Order #</th><th scope="col">Lab Name</th><th scope="col"> Ordered Date</th><th scope="col">Ordered By</th>';
            info +=
                '<th scope="col">Approved By</th><th scope="col">Supplier Name</th><th scope="col">Cost</th> <th scope="col">Ordered Quantity</th>';
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
                    info += "<td>" + json.data[index]["supplier"] + "</td>";
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
function getSelected(period) {
    if (period == 99) {
        loadItems();
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
function getData(period){
let requests_url_by_period = $("#supplier-orders_by_period").val();

t = $("#supplier_order_table").DataTable({
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
        url: requests_url_by_period,
        dataType: "json",
        type: "GET",
        data: {
            period: period,
        },
    },

    AutoWidth: false,
    columns: [
        {
            data: "id",
        },
        { data: "item_name", width: "30%" },
        { data: "code" },

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
function    loadItems(){
let requests_url = $("#supplier-orders").val();
t = $("#supplier_order_table").DataTable({
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
            data: "id",
        },
        { data: "item_name", width: "30%" },
        { data: "code" },

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

function getCustom(period, start, end) {
    let requests_url = $("#supplier-orders_by_period").val();
    t = $("#supplier_order_table").DataTable({
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
            data:{
                period:period,
                start_date:start,
                end_date:end
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
function getSelectedLocation(id){
     if (id == 99) {
        
         return;
     }
  let requests_url = $("#supplier-orders_by_location").val();
  t = $("#supplier_order_table").DataTable({
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
          data: {
              location: id,
              
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
function getSelectionBySupplier(id){
     if (id == 99) {
         loadItems();
         return;
     }

      let requests_url = $("#supplier-orders_by_supplier").val();
      t = $("#supplier_order_table").DataTable({
          processing: true,
          serverSide: true,
          destroy: true,
          paging: true,
          select: true,
          info: true,
          lengthMenu: [5, 10, 100],
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
              data: {
                  supplier: id,
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