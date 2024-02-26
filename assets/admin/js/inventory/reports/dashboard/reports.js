function getReport(value){
   switch (value) {
    case '0':
        $("#transform").text("Consumption Report");
         $("#period_selection").show();
        getConsumption();
        break;
   case '1':
     $("#transform").text("Stock Level Report");
     $("#period_selection").hide();
     getStockLevelReport();
     break;
     case '2':
         $("#transform").text("Requisition Report");
          $("#period_selection").show();
         getRequisitionReport();
    break;
    case '3':
         $("#transform").text("Orders Report");
          $("#period_selection").show();
         getOrdersReport();
         break;
    default:
         $("#transform").text("Consumption Report");
          $("#period_selection").show();
           getConsumption();
        break;
   }

}
function getPeriod(period){
let type=$("#reportType").val();

switch(type){
     case '0':
          getReportWithPeriod(type, period);
          break;

     case '2':
          getRequisitionWithPeriod(type,period);
          break;
     case '3':
          getOrderswithPeriod(type,period);
}
          
      




}