
var get_inventory_health=$('#inventory_health').val();
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