var get_inventory_health=$('#inventory_health').val();
let details = $("#item-chart").val();
// Pie Chart Example



   

    $.ajax({
        url: get_inventory_health,

        type: "GET",
        dataType: "json",
      
        success: function (response) {
         // $("#pie_load").prop('hide',true)
          document.getElementById("pie_load").hidden=true;
          console.log(response)
           var count = new Array();
          
           for (var x = 0; x < response.data.length; x++) {
            
               count.push(response.data[x]);
           }
          var ctx = document.getElementById("myPieChart");
          var myPieChart = new Chart(ctx, {
              type: "doughnut",
              data: {
                  labels: [
                      "Expired",
                      "Expire in 30 days",
                      "Expire in 60 days",
                      "Expire in 90 days",
                      "Good",
                  ],
                  datasets: [
                      {
                          data: count,
                          backgroundColor: [
                              "#9c170e",
                              "#ff6e07",
                              "#9c7b0e",
                              "#849c0e",
                              "#0f7a10",
                          ],
                          hoverBackgroundColor: [
                              "#2e59d9",
                              "#fd7e14",
                              "#9c7b0e",
                          ],
                          hoverBorderColor: "rgba(234, 236, 244, 1)",
                      },
                  ],
              },
              options: {
                  maintainAspectRatio: false,
                  tooltips: {
                      backgroundColor: "rgb(255,255,255)",
                      bodyFontColor: "#858796",
                      borderColor: "#dddfeb",
                      borderWidth: 1,
                      xPadding: 15,
                      yPadding: 15,
                      displayColors: false,
                      caretPadding: 10,
                  },
                  legend: {
                      display: false,
                  },
                  cutoutPercentage: 80,

                  onClick: (e) => {
                      
                    var activePoints = myPieChart.getElementsAtEvent(e);

                    if (activePoints.length > 0) {
                        //get the internal index of slice in pie chart
                        var clickedElementindex = activePoints[0]["_index"];
getDetails(clickedElementindex);
                        //get specific label by index
                        var label =myPieChart.data.labels[clickedElementindex];
console.log(label);
                        //get value by index
                        var value = myPieChart.data.datasets[0].data[clickedElementindex];
console.log(value);
                        /* other stuff that requires slice's label and value */
                       getDetails(clickedElementindex,label);

                    }
                  },
              },
          });
        },
        error:function(error){

        }
      });
function getDetails(index,label){
  var get_details=$('#get_details').val();
  swal.fire({
      heightAuto: false,
      text: "Please wait...",
      timer: 1000,
  });
      $.ajax({
          method: "GET",

          url: get_details,
          data: {
              id: index,
              label:label,
          },

          success: function (data) {
              $("#add_certi").html(data);
              $("#infor").modal("show"); // show bootstrap modal
              $(".modal-title").text("Inventory Details");
          },
          error: function (jqXHR, textStatus, errorThrown) {
              // console.log(get_case_next_modal)
              alert("Error " + errorThrown);
          },
      });
}


  $.ajax({
      url: details,
      type: "GET",
      dataType: "json",

      success: function (response) {
          // $("#pie_load").prop('hide',true)
          document.getElementById("pie_load").hidden = true;
          $('#low').text(response.low)
           $("#medium").text(response.medium);
            $("#high").text(response.high);
          console.log(response);
          var ctx = document.getElementById("myround_pie");
          var count = new Array();
          var myPieChart = new Chart(ctx, {
              type: "doughnut",
              data: {
                  labels: [
                      "High Stock Item",
                      "Medium Stock Item",
                      "Low Stock Item",
                  ],
                  datasets: [
                      {
                          label: "Stock Level",
                          data: [response.high, response.medium, response.low],
                          backgroundColor: [
                              "rgb(54, 162, 235)",
                              "rgb(255, 205, 86)",
                              "rgb(255, 99, 132)",
                          ],
                          hoverOffset: 4,
                      },
                  ],
              },
              options: {
                  maintainAspectRatio: false,
                  tooltips: {
                      backgroundColor: "rgb(255,255,255)",
                      bodyFontColor: "#858796",
                      borderColor: "#dddfeb",
                      borderWidth: 1,
                      xPadding: 15,
                      yPadding: 15,
                      displayColors: false,
                      caretPadding: 10,
                  },
                  legend: {
                      display: false,
                  },
                  cutoutPercentage: 80,

                  onClick: (e) => {
                      var activePoints = myPieChart.getElementsAtEvent(e);

                      if (activePoints.length > 0) {
                          //get the internal index of slice in pie chart
                          var clickedElementindex = activePoints[0]["_index"];
                          getDetails(clickedElementindex);
                          //get specific label by index
                          var label =
                              myPieChart.data.labels[clickedElementindex];
                          console.log(label);
                          //get value by index
                          var value =
                              myPieChart.data.datasets[0].data[
                                  clickedElementindex
                              ];
                          console.log(value);
                          /* other stuff that requires slice's label and value */
                          getDetails(clickedElementindex, label);
                      }
                  },
              },
          });
      },
      error: function (error) {},
  });



let ctxt = document.getElementById("myround_pijje");
var mylevel = "";

  var reportChart = $("#stock-level-chart").val();

  var names = [];
  var minimum = [];
  var maximum = [];
  var available = [];

  $.ajax({
      url: reportChart,

      type: "GET",
      dataType: "json",

      success: function (response) {
          //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
          const consumption = response.data;
          console.log(consumption);
          for (var i = 0; i < consumption.length; i++) {
              names.push(consumption[i].item_name);
              minimum.push(consumption[i].minimum_level);
              maximum.push(consumption[i].maximum_level);
              available.push(consumption[i].stock_on_hand);
          }

          // Access individual properties of each item

         mylevel = new Chart(ctxt, {
              type: "bar",
              data: {
                  labels: names,
                  datasets: [
                      {
                          label: "Minimum",
                          backgroundColor: getRandomColor(),
                          borderColor: getRandomColor(),
                          borderWidth: 1,
                          data: minimum,
                      },
                      {
                          label: "Available",
                          backgroundColor: getRandomColor(),
                          borderColor: getRandomColor(),
                          borderWidth: 1,
                          data: available,
                      },
                      {
                          label: "Maximum",
                          backgroundColor: getRandomColor(),
                          borderColor: getRandomColor(),
                          borderWidth: 1,
                          data: maximum,
                      },
                  ],
              },
              options: {
                  scales: {
                      y: {
                          beginAtZero: true,
                      },
                  },
              },
          });

          // $("#pie_load").prop('hide',true)
          // document.getElementById("pie_load").hidden=true;
      },
      error: function (error) {},
  });


  //top ten consumed;

  let top_ten = "";
  let top_consumed = $("#top_consumed").val();
 top_ten  = $("#items_tabl").DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      paging: true,
      select: true,
      info: true,
      lengthMenu: [5, 20, 50],
      responsive: true,

      order: [[0, "desc"]],
      oLanguage: {
          sProcessing:
              "<div class='loader-container'><div id='loader'></div></div>",
      },
      ajax: {
          url: top_consumed,
          dataType: "json",
          type: "GET",
      },

      AutoWidth: false,
      columns: [
          { data: "preview" },
          { data: "item_name" },
          { data: "code" },
          { data: "total_consumed" },
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
  
  
  let latest_order=""
let orders = $("#get_latest").val();
   latest_order = $("#req").DataTable({
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
           url: orders,
           dataType: "json",
           type: "GET",
       },

       AutoWidth: false,
       columns: [
           { data: "sr" },
           { data: "request_lab" },
           { data: "request_date" },
           //{ data: "options", width: "40%" },
           
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