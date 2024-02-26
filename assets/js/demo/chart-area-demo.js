// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

// Area Chart Example
var topused_item_url=$('#top_used').val();
var ctx = document.getElementById("myAreaChart");
var months = [];
var count = [];
$.ajax({
        url: topused_item_url,

        type: "GET",
        dataType: "json",
      
        success: function (response) {
        //const flattenedItems = Object.values(response.).flatMap(monthItems => monthItems);
       const mostUsedItems = response.mostUsedItems;
           
          const labels = mostUsedItems.map((item) => item.month);
            mostUsedItems.forEach((item) => {
                // Access individual properties of each item

                if (!Array.isArray(item)) {
                    const userId = item.section_id;
                    const month = item.month;
                    const itemId = item.item_id;
                    const itemName = item.item_name;
                    const secName = item.section_name;
                    const usageCount = item.usage_count;
                    months.push(month);
                    count.push(usageCount);
                    console.log(
                        `Section ${secName} used ${itemName} the most in ${month}. Count: ${usageCount}`
                    );
                }

                const uniqueMonths = [
                    ...new Set(mostUsedItems.map((item) => item.month)),
                ];
                const uniqueItemNames = [
                    ...new Set(mostUsedItems.map((item) => item.item_name)),
                ];

                const datasets = uniqueItemNames.map((itemName) => {
                    return {
                        label: itemName,
                        data: uniqueMonths.map((month) => {
                            const item = mostUsedItems.find(
                                (i) =>
                                    i.item_name === itemName &&
                                    i.month === month
                            );
                            return item ? item.usage_count : 0;
                        }),
                        borderColor: getRandomColor(),
                        backgroundColor: "rgba(0, 0, 0, 0)",
                        fill: false,
                    };
                });

                // Create Line Chart using Chart.js
                const ctx = document
                    .getElementById("myAreaChart")
                    .getContext("2d");
                const myLineChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: uniqueMonths,
                        datasets: datasets,
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: "linear",
                                position: "bottom",
                            },
                            y: {
                                type: "linear",
                                position: "left",
                            },
                        },
                    },
                });
            });
        
         // $("#pie_load").prop('hide',true)
         // document.getElementById("pie_load").hidden=true;
        },
        error:function(error){

        }
      });
    function getRandomColor() {
        const letters = "0123456789ABCDEF";
        let color = "#";
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
