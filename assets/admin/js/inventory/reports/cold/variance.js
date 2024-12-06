var t;
let variance_report=$('#variance_report').val()

t = $("#variance_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    scrollCollapse: true,
destroy:true,
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
        {
             className: "dt-control",
             orderable: false,
             data: null,
             defaultContent: "",
         },
        { data: "stock_date", width: "30%" },
        { data: "lab_name" },
        { data: "supervised_by"},
        { data: "approved_by"},
        {data:'action'}

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
     let more_details = $("#stock_take_details").val();
     console.log(rowData);
     var div = $("<div/>").addClass("loading").text("loading...");

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
                 '<th scope="col">Item Name</th>';
             info +=
                 '<th scope="col">System Quantity</th>';
                     info +=
                 '<th scope="col">Pysical Count</th>';
                     info +=
                 '<th scope="col">Status</th>';
             info += '</tr></thead> <tbody> <tr id="row1">';

             var x = 1;
             console.log(json.data.length);
             for (let index = 0; index < json.data.length; index++) {

                 info += "<td>" + json.data[index]["item_name"] + "</td>";

                 info +=
                     "<td>" +
                     json.data[index]["system_quantity"] +
                     "</td>";
                  info +=
                     "<td>" +
                     json.data[index]["physical_count"] +
                     "</td>";
                     info +=
                     "<td>" +
                     json.data[index]["status"] +
                     "</td>";
                   info +="</tr>";
                 x++;
             }
             info += "</tbody></table></div>";
             console.log(info);
             div.html(info).removeClass("loading");
         },
     });

     return div;
 }

 function getSelectedLab(id){
 if(id==-1){
 loadAll();
 }
 let variance_url=$('#variance_lab').val()
 t = $('#variance_table').DataTable({
   processing: true,
   serverSide: true,
   paging: true,
   destroy: true,
   scrollCollapse: true,

   info: true,

   lengthMenu: [10, 15, 20],
   responsive: true,
   order: [[0, 'desc']],
   oLanguage: {
     sProcessing: "<div class='loader-container'><div id='loader'></div></div>",
   },
   ajax: {
     url: variance_url,
     dataType: 'json',
     type: 'GET',
     data: {
       lab_id: id,
     },
   },
   AutoWidth: false,
   columns: [
     {
       className: 'dt-control',
       orderable: false,
       data: null,
       defaultContent: '',
     },
     { data: 'stock_date', width: '30%' },
     { data: 'lab_name' },
     { data: 'supervised_by' },
     { data: 'approved_by' },
     { data: 'action' },
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


 function loadAll(){
 t = $('#variance_table').DataTable({
   processing: true,
   serverSide: true,
   paging: true,
   scrollCollapse: true,
   destroy: true,
   info: true,

   lengthMenu: [10, 15, 20],
   responsive: true,
   order: [[0, 'desc']],
   oLanguage: {
     sProcessing: "<div class='loader-container'><div id='loader'></div></div>",
   },
   ajax: {
     url: variance_report,
     dataType: 'json',
     type: 'GET',
   },
   AutoWidth: false,
   columns: [
     {
       className: 'dt-control',
       orderable: false,
       data: null,
       defaultContent: '',
     },
     { data: 'stock_date', width: '30%' },
     { data: 'lab_name' },
     { data: 'supervised_by' },
     { data: 'approved_by' },
     { data: 'action' },
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
function downloadVariance(id){
    let downloadVariance = $('#download_url').val();


     $.ajax({
       method: 'GET',
       dataType: 'JSON',
       url: downloadVariance,
       data: {
        id:id,
       },
       beforeSend: function () {
         ajaxindicatorstart('downloading data... please wait...');
       },
       success: function (data) {
         ajaxindicatorstop();
         window.location=data.path;

       },
       error:function(error){
ajaxindicatorstop();
       }
     });
}
