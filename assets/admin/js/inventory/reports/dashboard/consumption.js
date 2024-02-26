var ctx = document.getElementById("myAreaChart");
var myAreaChart=""
$(document).ready(function () {
    // Area Chart Example
let topused_item_url = $("#consumption-chart").val();

    let mconsumed = [];
    let count = [];
    $.ajax({
        url: topused_item_url,

        type: "GET",
        dataType: "json",

        success: function (response) {
            //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
            const consumption = response.data;
            console.log(consumption);
            for (var i = 0; i < consumption.length; i++) {
                mconsumed.push(consumption[i].item_name);
                count.push(consumption[i].consumed_quantity);
            }

            // Access individual properties of each item

         myAreaChart=   new Chart(ctx, {
                type: "bar",
                data: {
                    labels: mconsumed,
                    datasets: [
                        {
                            label: "Total consumed",
                            data: count,
                            backgroundColor: [
                                "rgba(255, 99, 132, 0.2)",
                                getRandomColor(),
                            ],
                            borderColor: [
                                getRandomColor(),
                                "rgb(255, 159, 64)",
                                "rgb(255, 205, 86)",
                                "rgb(75, 192, 192)",
                                "rgb(54, 162, 235)",
                                "rgb(153, 102, 255)",
                                "rgb(201, 203, 207)",
                            ],
                            borderWidth: 1,
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
});
ctx.onclick = (evt) => {
    const res = myAreaChart.getElementsAtEventForMode(
        evt,
        "nearest",
        { intersect: true },
        true
    );
    // If didn't click on a bar, `res` will be an empty array
    if (res.length === 0) {
        return;
    }
    // Alerts "You clicked on A" if you click the "A" chart
   // alert("You clicked on " + myAreaChart.data.labels[res[0].index]);
};
function getRandomColor() {
    const letters = "0123456789ABCDEF";
    let color = "#";
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function getStockLevelReport() {
   myAreaChart.destroy();
    var reportChart = $("#stock-level-chart").val();
    
    var names = [];
    var minimum = [];
    var maximum = [];
    var available=[];

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
                maximum.push(consumption[i].maximum_level)
                available.push(consumption[i].stock_on_hand)
            }

            // Access individual properties of each item

            myAreaChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels:names,
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
                            data:available,
                        },
                        {
                            label: "Maximum",
                            backgroundColor:getRandomColor(),
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
}
function getConsumption(){
     myAreaChart.destroy();
     let consumption_chart = $("#consumption-chart").val();
      
    let mconsumed = [];
    let count = [];
    $.ajax({
        url: consumption_chart,

        type: "GET",
        dataType: "json",

        success: function (response) {
            //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
            const consumption = response.data;
            console.log(consumption);
         for (var i = 0; i < consumption.length; i++) {
             mconsumed.push(consumption[i].item_name);
             count.push(consumption[i].consumed_quantity);
         }
            // Access individual properties of each item
            console.log(mconsumed);
            myAreaChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: mconsumed,
                    datasets: [
                        {
                            label: "# Consumed",
                            data: count,
                            backgroundColor: [
                                "rgba(255, 99, 132, 0.2)",
                                getRandomColor(),
                            ],
                            borderColor: [
                                getRandomColor(),
                                "rgb(255, 159, 64)",
                                "rgb(255, 205, 86)",
                                "rgb(75, 192, 192)",
                                "rgb(54, 162, 235)",
                                "rgb(153, 102, 255)",
                                "rgb(201, 203, 207)",
                            ],
                            borderWidth: 1,
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
}

function   getRequisitionReport(){
    let requisition = $("#requisition_chart").val();
  myAreaChart.destroy();
  let mconsumed = [];
  let count = [];
  $.ajax({
      url: requisition,

      type: "GET",
      dataType: "json",

      success: function (response) {
          //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
          const consumption = response.data;
          console.log(consumption);
          for (var i = 0; i < consumption.length; i++) {
              mconsumed.push(consumption[i].status);
              count.push(consumption[i].count);
          }

          // Access individual properties of each item

          myAreaChart = new Chart(ctx, {
              type: "bar",
              data: {
                  labels: mconsumed,
                  datasets: [
                      {
                        label:"", 
                          data: count,
                          backgroundColor: [
                              "rgba(255, 99, 132, 0.2)",
                              getRandomColor(),
                          ],
                          borderColor: [
                              getRandomColor(),
                              "rgb(255, 159, 64)",
                              "rgb(255, 205, 86)",
                              "rgb(75, 192, 192)",
                              "rgb(54, 162, 235)",
                              "rgb(153, 102, 255)",
                              "rgb(201, 203, 207)",
                          ],
                          borderWidth: 1,
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
}

function  getOrdersReport(){
    let orders = $("#orders_chart").val();

    myAreaChart.destroy();
    let mconsumed = [];
    let count = [];
    $.ajax({
        url: orders,

        type: "GET",
        dataType: "json",

        success: function (response) {
            //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
            const consumption = response.data;
            console.log(consumption);
            for (var i = 0; i < consumption.length; i++) {
                if (consumption[i].status=='yes'){
     mconsumed.push('Delivered');
                }
                else if(consumption[i].status=='no'){
                      mconsumed.push('Not delivered');
            }
            else{
               mconsumed.push(consumption[i].status);  
            }
                count.push(consumption[i].count);
            }

            // Access individual properties of each item

            myAreaChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: mconsumed,
                    datasets: [
                        {
                            label: "",
                            data: count,
                            backgroundColor: [
                                "rgba(255, 99, 132, 0.2)",
                                getRandomColor(),
                            ],
                            borderColor: [
                                getRandomColor(),
                                "rgb(255, 159, 64)",
                                "rgb(255, 205, 86)",
                                "rgb(75, 192, 192)",
                                "rgb(54, 162, 235)",
                                "rgb(153, 102, 255)",
                                "rgb(201, 203, 207)",
                            ],
                            borderWidth: 1,
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

}
function  getReportWithPeriod(type,period){
 myAreaChart.destroy();
 let period_chart = $("#period-chart").val();

 let mconsumed = [];
 let count = [];
 $.ajax({
     url: period_chart,

     type: "GET",
     dataType: "json",
     data:{
        period:period,
        type:type
     },

     success: function (response) {
         //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
         const consumption = response.data;
         console.log(consumption);
         for (var i = 0; i < consumption.length; i++) {
             mconsumed.push(consumption[i].item_name);
             count.push(consumption[i].consumed_quantity);
         }
         // Access individual properties of each item
         console.log(mconsumed);
         myAreaChart = new Chart(ctx, {
             type: "bar",
             data: {
                 labels: mconsumed,
                 datasets: [
                     {
                         label: "# Consumed",
                         data: count,
                         backgroundColor: [
                             "rgba(255, 99, 132, 0.2)",
                             getRandomColor(),
                         ],
                         borderColor: [
                             getRandomColor(),
                             "rgb(255, 159, 64)",
                             "rgb(255, 205, 86)",
                             "rgb(75, 192, 192)",
                             "rgb(54, 162, 235)",
                             "rgb(153, 102, 255)",
                             "rgb(201, 203, 207)",
                         ],
                         borderWidth: 1,
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
}
function getRequisitionWithPeriod(type,period){
myAreaChart.destroy();
let requi_chart = $("#period-chart").val();

  let mconsumed = [];
  let count = [];
  $.ajax({
      url: requi_chart,

      type: "GET",
      dataType: "json",
      data: {
          period: period,
          type: type,
      },
      success: function (response) {
          //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
          const consumption = response.data;
          console.log(consumption);
          for (var i = 0; i < consumption.length; i++) {
              mconsumed.push(consumption[i].status);
              count.push(consumption[i].count);
          }

          // Access individual properties of each item

          myAreaChart = new Chart(ctx, {
              type: "bar",
              data: {
                  labels: mconsumed,
                  datasets: [
                      {
                          label: "",
                          data: count,
                          backgroundColor: [
                              "rgba(255, 99, 132, 0.2)",
                              getRandomColor(),
                          ],
                          borderColor: [
                              getRandomColor(),
                              "rgb(255, 159, 64)",
                              "rgb(255, 205, 86)",
                              "rgb(75, 192, 192)",
                              "rgb(54, 162, 235)",
                              "rgb(153, 102, 255)",
                              "rgb(201, 203, 207)",
                          ],
                          borderWidth: 1,
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
   
}



function    getOrderswithPeriod(type,period){
myAreaChart.destroy();
let order_chart = $("#period-chart").val();
    let mconsumed = [];
    let count = [];
    $.ajax({
        url: order_chart,

        type: "GET",
        dataType: "json",
        data: {
            period: period,
            type: type,
        },
        success: function (response) {
            //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
            const consumption = response.data;
            console.log(consumption);
            for (var i = 0; i < consumption.length; i++) {
                if (consumption[i].status == "yes") {
                    mconsumed.push("Delivered");
                } else if (consumption[i].status == "no") {
                    mconsumed.push("Not delivered");
                } else {
                    mconsumed.push(consumption[i].status);
                }
                count.push(consumption[i].count);
            }

            // Access individual properties of each item

            myAreaChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: mconsumed,
                    datasets: [
                        {
                            label: "",
                            data: count,
                            backgroundColor: [
                                "rgba(255, 99, 132, 0.2)",
                                getRandomColor(),
                            ],
                            borderColor: [
                                getRandomColor(),
                                "rgb(255, 159, 64)",
                                "rgb(255, 205, 86)",
                                "rgb(75, 192, 192)",
                                "rgb(54, 162, 235)",
                                "rgb(153, 102, 255)",
                                "rgb(201, 203, 207)",
                            ],
                            borderWidth: 1,
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

}