"use strict"
var consumption_update = $("#consumption_update").val();
 var checked = [];
 var quantities=[];
var p;
var check=0;
var table="";
  var inventory = $("#load_inventory").val();
  var more_details=$('#more_details').val();
  var back = $("#back").val();
  var about_lab=$('#about').val();
p = $("#all_inventories").DataTable({
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
        {
            className: "dt-control",
            orderable: false,
            data: null,
            defaultContent: "",
        },

        { data: "name", width: "20%" },
        { data: "code", width: "15%" },
        { data: "batch_number" },
        { data: "unit" },
        { data: "available" },
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

 p.on("click", "tbody tr", function () {
    if(checked==0){
     let data = p.row(this).data();
//console.log(data['id'])
var tr = $(this).closest("tr");
var row = p.row(tr);

if (row.child.isShown()) {
    row.child.hide();
    tr.removeClass("shown");
} else {
    row.child(format(row.data())).show();
    tr.addClass("shown");
}
    }
    // alert("You clicked on " + data["available"] + "'s row");
 });
 $("#all_inventories tbody").on("click", "td.dt-control", function () {
     if ((checked == 0)) {
         var tr = $(this).closest("tr");
         var row = p.row(tr);

         if (row.child.isShown()) {
             row.child.hide();
             tr.removeClass("shown");
         } else {
             row.child(format(row.data())).show();
             tr.addClass("shown");
         }
     }
 });

 function format(rowData) {
    console.log(rowData)
     var div = $("<div/>").addClass("loading").text("Loading...");

     $.ajax({
         url: more_details,
         data: {
             name: rowData.id,
         },
         dataType: "json",
         success: function (json) {
            var info='<ul class="list-group">'
            for (let index= 0; index < json.data.length; index++) {
             info +=
                 ' <li class="list-group-item d-flex justify-content-between align-items-center" onclick="labInfo('+json.data[index]["id"]+','+json.data[index]["item_id"]+')"><strong>' +
                 json.data[index]["lab_name"] +
                 '</strong><span class="badge badge-primary badge-pill">' +
                 json.data[index]["quantity"] +
                 "</span></li>";
                
            }
            info += "</ul>";
            console.log(info)
             div.html(info).removeClass("loading");
         },
     });

     return div;
 }
 function labInfo(id,item_id){
    
    $.ajax({
        url:about_lab,
        data:{
            id:id,
            item:item_id
            
        },
        success: function (data) {
          $("#view_item_datails").html(data);
          $("#inforg").modal("show");   
        },
        error: function (error) {
            
        }
    })
 }
 function getSelected(value) {
    var lab_id=value
   
    if(lab_id==99){
     getAll()
    return;
    }
    
   
    getLocationData(lab_id);

 }
 function getLocationData(id){
  var getlocation=$('#location_inventory').val()
  
  table= $("#all_inventories").DataTable({
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
          url: getlocation,
          dataType: "json",
          type: "GET",
          data:{
            id:id,
          },
      },

      AutoWidth: false,
      columns: [
          { data: "id", width: "3%" },
        { data: "name", width: "20%" },
        { data: "code", width: "15%" },
        { data: "batch_number" },
        { data: "unit" },
        { data: "available" },
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
 
 function getAll(){
  p = $("#all_inventories").DataTable({
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
          {
              className: "dt-control",
              orderable: false,
              data: null,
              defaultContent: "",
          },

         { data: "name", width: "20%" },
        { data: "code", width: "15%" },
        { data: "batch_number" },
        { data: "unit" },
        { data: "available" },
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