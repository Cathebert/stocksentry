var obj = [];
 var checked = [];
 var arr = [];
 var idList=[];
 var changed=[]
var check_quantity = $("#check_quantity").val();
  var p = "";
function getReceiver(id) {
    var get_receiver = $("#get_receiver").val();
    $.ajax({
        method: "GET",
        dataType: "JSON",
        url: get_receiver,
        data: {
            id: id,
        },
        success: function (data) {
            console.log(data);
            var sel = $("#received_by");
            sel.empty();
            for (var i = 0; i < data.length; i++) {
                sel.append(
                    '<option value="' +
                        data[i].id +
                        '">' +
                        data[i].name +
                        "</option>"
                );
            }
        },
    });
}
$("#add_new_request").on("click", function () {
     obj.length=0;
 var showitem_list = $("#showitemList").val();
 $.ajax({
     method: "GET",

     url: showitem_list,

     success: function (data) {
         $("#request_datails").html(data);
         $("#inforg").modal("show"); // show bootstrap modal
         $(".modal-title").text("Order Item List ");
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + errorThrown);
     },
 });
});

function removeItemFromList(item){

    let index = obj.indexOf(item);

    if (index !== -1) {
        // Remove the item using splice
        obj.splice(index, 1);
    }
      console.log(obj);
}
function getText(id, name) {
    var w = $("#q_" + name).val();
      var check_quantity = $("#check_quantity").val();
     var quantity=name+'_';
      var startsWithBan = obj.find((item) =>item.startsWith(quantity));
             
         if (!w) {
             document.getElementById(name).checked = false;
             if (startsWithBan) {
                 obj = arrayRemove(obj, startsWithBan);
             } 
         } else {
           
             document.getElementById(name).checked = true;
            
 document.getElementById('l_'+name).hidden=false;
             if (startsWithBan) {
                 obj = arrayRemove(obj, startsWithBan);
             } else {
                var item_quantity =name+'_'+w
              //validateQuantity(name,w)
               // obj.push(item);
                 $.ajax({
                method: "GET",
                dataType: "JSON",
                url: check_quantity,
                data: {
                    id: name,
                    quantity:w,
                },

                success: function (data) {
                    if (data.error == false) {
                        obj.push(item_quantity);
                        document.getElementById('l_'+name).hidden=true;
                        console.log(obj);
                    } else {
                         document.getElementById('l_'+name).hidden=true;
                        $.alert({
                            icon: "fa fa-danger",
                            title: "Error",
                            type: "red",
                            content: data.message,
                        });
                         $("#q_"+name).val('');
                         document.getElementById(name).checked = false
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // console.log(get_case_next_modal)
                    alert("Error " + errorThrown);
                },
            });
             }
          
         }
}
function validateQuantity(id,quantity){
  var check_quantity = $("#check_quantity").val();
 
  let item_quantity = $(id+"_"+quantity).val();

           
}
$("#save_issue").on("click", function () {
    var save_issued_url = $("#save_issued_url").val();
    var form_data = $("#issue-form-data").serialize();
    const searchParams = new URLSearchParams(form_data);
    form_data = Object.fromEntries(searchParams);
    var siv = $("#sr_number").val();
    var from = $("#lab_id").val();
   
    if (!siv) {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please enter stock transfer number!",
        });
        return;
    }
    if (!from) {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please Select where you issuing from!",
        });
        return;
    }
   if(changed.length>0){
    
    obj=changed;
   }
    console.log(obj);
   

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: save_issued_url,
        data: {
            form_data,
            quantity: obj,
            
        },
        success: function (data) {
            if (data.error == false) {
                toastr.options = {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toast-top-right",
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                };
                toastr["success"](data.message);

                $("#issue-form-data")[0].reset();
                $("#items").show();
                $("#irec").hide();
                $("#cost").val("0.00");
                $("#save_issue").prop("disabled", true);
                var request=$('#requests-badge').text();
                obj.length=0;
                var add_one = parseInt(request);
                var added=add_one+1;
                $("#requests-badge").text(added);
                $('#sr_number').val(data.sr_number)
                location.reload()
            } else {
                toastr.options = {
                    closeButton: true,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toast-top-right",
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                };
                toastr["error"](data.message);
            }
            // Welcome notification
            // Welcome notification
        },
    });
});


function getSelectedFromModal(){
    //reset checkboxes
     var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    // Iterate through checkboxes and uncheck them
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false;
    });
     var selected_items = $("#selected_items").val();
    if(!obj.length>0){
          $.alert({
              icon: "fa fa-warning",
              title: "Missing information!",
              type: "orange",
              content: "Please Select Items to order",
          });
        return;
    }
     console.log(checked);
     $.ajaxSetup({
         headers: {
             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
         },
     });
     $.ajax({
         method: "GET",
         dataType: "JSON",
         url: selected_items,
         data: {
            
             items: obj,
         },
         success: function (data) {
             var gh = data.data.length;
             var q = data.quantity;
             console.log(q);
             console.log(gh);
             var html = "";
             var cost = 0;
             var total = 0;
             var row="row1"
             html +=
                 '<div class="table-responsive"><table class="table table-sm" id="irec" width="100%"><thead class="thead-light"> <tr><th scope="col">CODE #</th><th scope="col">Item</th><th scope="col">UOM</th><th scope="col">Quantity</th>';
             html +=
                 '<th scope="col">Batch #</th><th scope="col">Expiry</th><th scope="col">Cost</th><th scope="col">Total</th><th>Action</th></tr></thead> <tbody> <tr id="row1">';
             for (let i = 0; i < gh; i++) {
                idList.push(data.data[i]["id"]);
                 cost = data.quantity[i] * data.data[i]["cost"];
                 total = total + cost;
                 html += "<td>" + data.data[i]["code"] + "</td>";
                 html += "<td>" + data.data[i]["item_name"] + "</td>";
                 html += "<td>" + data.data[i]["unit_issue"] + "</td>";
                 html += "<td id=quant_"+data.data[i]["id"]+">" + data.quantity[i] + "</td>";
                 html += "<td>" + data.data[i]["batch_number"] + "</td>";
                 html += "<td>" + data.data[i]["expiry_date"] + "</td>";
                 html += "<td id=cost_"+data.data[i]["id"]+">" + data.data[i]["cost"] + "</td>";
                 html += "<td id=total_"+data.data[i]["id"]+">" + cost + "</td>";
                  html +=
                      "<td><button type='button' onclick='editRow("+data.data[i]["id"]+")'><i class='fa fa-edit'></i></button> || <button type='button' id="+data.data[i]["id"]+" onclick='removeRow(this,this.id)' ><i class='fa fa-trash' style='color:red'></i></button</td></tr>";
             }
             html += " </tbody></table></div>";
             console.log(total);
             var total_cost = parseFloat(total).toFixed(2);
             $("#cost").val(total_cost);
             $("#items").hide();
             $("#close_item_modal").click();
             $("#real_table").html(html);
             console.log(html);
             $("#save_issue").prop("disabled", false);
            
         },
     });
 }
function removeRow(button,id) {

    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to delete this entry!",
        buttons: {
            Oky: {
                btnClass: "btn-danger",
                action: function () {
                    if(obj.length>0){
                 let startsWithword =obj.find((item) =>
                     item.startsWith(id+"_")
                 );
                 console.log("Starts With" + startsWithword);
console.log("Starts With Obj :" + obj);
                 if (startsWithword) {
                    obj=arrayRemove(obj,startsWithword)
                      if (changed.length > 0) {
                          changed = arrayRemove(changed, startsWithword);
                      }
                 } else {
                 }
                 console.log("Changed: "+obj)
                }else{

                }
                 // Find the parent row and remove it
                 var row = button.parentNode.parentNode;
                  var total_cost=0;
                
                 
console.log("Not Spliced"+idList);
          


idList = arrayRemove(idList, id); 
                 console.log("Spliced" + idList);

                  for (var x = 0; x < idList.length; x++) {
                      
                     

                      var d = document.getElementById("total_"+idList[x]).innerText;
                      var tot_cost = parseInt(d);
                      total_cost = total_cost + tot_cost;
                      
                  }

                  $("#cost").val(total_cost); 
                    row.remove(); 
                    
                },
            },

            cancel: function () {},
        },
    });

    
}
function arrayRemove(arr, value) {
    return arr.filter(function (item) {
        return item != value;
    });
}
function editRow(id) {
    // Get the row and its cells
    let quantityElement = document.getElementById("quant_" + id);
    let costElement = document.getElementById("cost_" + id);
    let quantity = quantityElement.innerText;
    let cost = costElement.innerText;

    // Prompt for new values
    let newQuantity = prompt("Enter new quantity:", quantity);
    if (isNaN(newQuantity) || newQuantity === null) {
        alert("Enter a valid number");
        return;
    }

    let newQuantityInt = parseInt(newQuantity);
    if (isNaN(newQuantityInt)) {
        alert("Enter a valid number");
        return;
    }
    let costInt = parseInt(cost);
    checkQuantity(id, newQuantityInt, costInt);
}
 $("#pending_request_approval").on('click',function(){
     var showpendingrequest = $("#showpending_requests").val();
     $.ajax({
         method: "GET",

         url: showpendingrequest,
         

         success: function (data) {
             $("#request_datails").html(data);
             $("#inforg").modal("show"); // show bootstrap modal
             $(".modal-title").text("Pending Requisition Approvals ");
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
 })

 //aprroved requests
 $("#approved_request").on("click", function () {
     var showapprovedrequest = $("#showapproved_requests").val();
     $.ajax({
         method: "GET",

         url: showapprovedrequest,

         success: function (data) {
             $("#request_datails").html(data);
             $("#inforg").modal("show"); // show bootstrap modal
             $(".modal-title").text("Approved Requisitions ");
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
 });

 function AddIdToArray(id) {
     if (document.getElementById(id).checked == true) {
           $("#q_"+id).focus();
         document.getElementById("q_" + id).hidden = false;
     
         
     } else {
         $("#q_" + id).val('') 
 let startsWithBan = obj.find((item) =>
                 item.startsWith(id+"_")
                 
             );

             if (startsWithBan) {
                 obj = arrayRemove(obj, startsWithBan);
             } 
         
     }
  
 }
 $("#issue_items").on("select.dt", function (e, dt, type, indexes) {
     var data = dt.rows(indexes).data();
     console.log(data);
 });
function ApproveRequest(id){
    var approve_issued_item = $("#save_request_item").val();
  
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to approve this issue!",
        buttons: {
            Oky: {
                btnClass: "btn-success",
                action: function () {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });
                    $.ajax({
                        method: "POST",
                        url: approve_issued_item,
                        dataType: "JSON",
                        data: {
                            id: id,
                        },
                        success: function (data) {
                       toastr.options = {
                  "closeButton": true,
                  "debug": false,
                  "newestOnTop": false,
                  "progressBar": false,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "1000",
                  "timeOut": "5000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
                }
                toastr["success"](data.message);
                             $("#appro").text(data.count);
                         
                             LoadRequestData();
                        },
                        error: function (error) {
                            console.log(error);
                        },
                    });
                },
            },

            cancel: function () {},
        },
    });

    ///
}
function checkLab() {
    var lab = $("#lab_id").val();
    if (lab == 2) {
        document.getElementById("sec").hidden = false;
    } else {
        document.getElementById("sec").hidden = true;
    }
}
function LoadRequestData(){
    var p = "";
    var dat_url = $('#load_requests').val();

    p = $("#request_approvals_items").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        select: true,
        info: true,
        lengthMenu: [10, 50, 100],
        responsive: true,

        order: [[0, "asc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: dat_url,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "sr_number" },
            { data: "lab_id" },
            { data: "requested_by" },
            { data: "requested_date" },
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

function LoadApprovedData() {
    var p = "";
    var dat_url = $("#load_approved").val();

    p = $("#approvals_items").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        select: true,
        info: true,
        lengthMenu: [10, 50, 100],
        responsive: true,

        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: dat_url,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "sr_number" },
            { data: "lab_id" },
            { data: "requested_by" },
            { data: "requested_date" },
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
function VoidRequest(id) {
    console.log(id);
    var void_url = $("#void_request").val();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        url: void_url,
        dataType: "JSON",
        data: {
            id: id,
        },
        success: function (data) {
                 toastr.options = {
                  "closeButton": true,
                  "debug": false,
                  "newestOnTop": false,
                  "progressBar": false,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "1000",
                  "timeOut": "5000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
                }
                toastr["success"](data.message);
               $("#requests-badge").html(data.count);
           
               LoadRequestData();
        },
        error: function (error) {},
    });
}
function ViewRequest(id){

     const view_issue = $("#view_request").val();

     $.ajax({
         method: "GET",
         url: view_issue,
         data: {
             id: id,
         },

         success: function (data) {
             // $('#boot').click();
             $("#request_datails").html(data);
             $("#inforg").modal("show"); // show bootstrap modal
             $(".modal-title").text("View Details");
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + textStatus);
         },
     });
}
$("#requisition_list").on('click',function(){
  const requisition_list = $("#view_requisition_list").val();

  $.ajax({
      method: "GET",
      url: requisition_list,
      

      success: function (data) {
          // $('#boot').click();
          $("#request_datails").html(data);
          $("#inforg").modal("show"); // show bootstrap modal
          $(".modal-title").text("Requisition list");
      },
      error: function (jqXHR, textStatus, errorThrown) {
          // console.log(get_case_next_modal)
          alert("Error " + textStatus);
      },
  });  
})

function LoadRequestList() {
    var g = "";
    var dat_url = $("#requisition-list").val();

    g = $("#request_list").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        select: true,
        info: true,
        lengthMenu: [10, 50, 100],
        responsive: true,

        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: dat_url,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "sr_number" },
            { data: "lab_id" },
            { data: "requested_by" },
            { data: "requested_date" },
            { data: "status" },
            { data: "view" },
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

function checkQuantity(id, quantity, cost) {
    $.ajax({
        method: "GET",
        dataType: "JSON",
        url: check_quantity,
        data: {
            id: id,
            quantity: quantity,
        },

        success: function (data) {
            console.log(data);
            if (data.error == false) {
                let costInt = parseInt(cost);
                let total = quantity * costInt;
                let totalCost = 0;
                let quantityElement = document.getElementById("quant_" + id);
                // Update the row with the new values
                quantityElement.innerText = quantity;
                document.getElementById("total_" + id).innerText = total;
changed.length = 0;
                for (let x = 0; x < idList.length; x++) {
                    let item_id = idList[x];
                    let item_quantity = document.getElementById(
                        "quant_" + idList[x]
                    ).innerText;
                    let d = document.getElementById(
                        "total_" + idList[x]
                    ).innerText;
                    let tot_cost = parseInt(d);
                    totalCost += tot_cost;
                    changed.push(item_id + "_" + item_quantity);
                }

                $("#cost").val(totalCost);
            } else {
                $.alert({
                    icon: "fa fa-danger",
                    title: "Error",
                    type: "red",
                    content: data.message,
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}

function getData() {
  ///co
}