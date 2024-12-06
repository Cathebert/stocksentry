'use strict'
let t="";
 let requests_url = $("#stock_level").val();
 t = $("#stock_level_table").DataTable({
     processing: true,
     serverSide: true,
     destroy: true,
     paging: true,
     select: true,
     info: true,
     lengthMenu: [10, 20, 50],
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
        data:"id"
         },

         { data: "item_name",width:"30%" },
         { data: "catalog_number" },
     { data: "place_of_purchase" },
         { data: "unit_issue" },
         { data: "minimum_level" },
         { data: "maximum_level" },
         { data: "stock_on_hand" },
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

 function format(rowData) {
     let more_details = $("#stock_level_details").val();
     console.log(rowData);
     var div = $("<div/>").addClass("loading").text("Loading...");

     $.ajax({
         url: more_details,
         data: {
             id: rowData.id,
         },
         dataType: "json",
         success: function (json) {
             var info =
                 '<div class="table-responsive"><table class="table table-sm table-striped table-light"><thead> <tr>';
             info +=
                 '<th scope="col">Lab Name</th>';
             info +=
                 '<th scope="col">Quantity</th>';
             info += '</tr></thead> <tbody> <tr id="row1">';

             var x = 1;
             console.log(json.data.length);
             for (let index = 0; index < json.data.length; index++) {

                 info += "<td>" + json.data[index]["lab_name"] + "</td>";

                 info +=
                     "<td>" +
                     json.data[index]["quantity"] +
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

 function getLab(id){
 if(id==0){
    getAll();
 return
 }

 getLabData(id)

 }
 function getLabData(id){
 let get_lab_url=$('#lab_selected').val();


 t = $("#stock_level_table").DataTable({
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
         url: get_lab_url,
         dataType: "json",
         type: "GET",
         data:{
         id:id,
         },

     },

     AutoWidth: false,
     columns: [
         {
        data:"id"
         },

         { data: "item_name",width:"30%" },
         { data: "catalog_number" },
     { data: "place_of_purchase" },
         { data: "unit_issue" },
         { data: "minimum_level" },
         { data: "maximum_level" },
         { data: "stock_on_hand" },
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
 function getAll(){
 t = $('#stock_level_table').DataTable({
   processing: true,
   serverSide: true,
   destroy: true,
   paging: true,
   select: true,
   info: true,
   lengthMenu: [10, 20, 50],
   responsive: true,

   order: [[0, 'desc']],
   oLanguage: {
     sProcessing: "<div class='loader-container'><div id='loader'></div></div>",
   },
   ajax: {
     url: requests_url,
     dataType: 'json',
     type: 'GET',
   },

   AutoWidth: false,
   columns: [
     {
       data: 'id',
     },

     { data: 'item_name', width: '30%' },
     { data: 'catalog_number' },
     { data: 'place_of_purchase' },
     { data: 'unit_issue' },
     { data: 'minimum_level' },
     { data: 'maximum_level' },
     { data: 'stock_on_hand' },
     { data: 'status' },
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
