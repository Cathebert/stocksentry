var obj = [];
var checked = [];
var arr = [];
var idList = [];
var changed = [];
var check_quantity = $("#check_quantity").val();
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
            for (var i = 0; i < data.user.length; i++) {
                sel.append(
                    '<option value="' +
                        data.user[i].id +
                        '">' +
                        data.user[i].name +
                        " " +
                        data.user[i].last_name +
                        "(" +
                        data.user[i].occupation +
                        ")" +
                        "</option>"
                );
            }
            
        },
    });
}
$("#add_new_issue").on("click", function () {
  
     $('#my_save').removeClass('fa-save');
                $('#my_save').addClass('fa-spin fa-spinner');
                $("button[name='save_issue']").attr("disabled", "disabled").button('refresh');
    var p = "";
    var dat_url = $("#data_url").val();

    p = $("#issue_items").DataTable({
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
            url: dat_url,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
             { data: "name", width: "40%" },
            { data: "code", width: "15%" },
            { data: "batch_number", width: "15%" },
            { data: "available", width: "5%" },
            { data: "quantity", width: "30%" },
            { data: "status", width: "35%" },
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

});

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
$("#save_issue").on("click", function () {
    var save_issued_url = $("#save_issued_url").val();
    var form_data = $("#issue-form-data").serialize();
    const searchParams = new URLSearchParams(form_data);
    form_data = Object.fromEntries(searchParams);
    var siv = $("#siv").val();
    var from = $("#from_lab_id").val();
    var to = $("#to_lab_id").val();
    if (!siv) {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please enter Stock Transfer  number!",
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
    if (!to) {
        $.alert({
            icon: "fa fa-warning",
            title: "Missing information!",
            type: "orange",
            content: "Please Select where issuing to",
        });
        return;
    }
     if (changed.length > 0) {
         
         obj = changed;
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
          beforeSend: function () {
                    ajaxindicatorstart("loading data... please wait...");
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
location.reload()
                $("#issue-form-data")[0].reset();
                $("#items").show();
                $("#irec").hide();
                $("#cost").val("0.00");
                $("#save_issue").prop("disabled", true);
                 var request = $("#requests-badge").text();
                 obj.length = 0;
                 var add_one = parseInt(request);
                 var added = add_one + 1;
                 $("#requests-badge").text(added);
                  $("#siv").val(data.id);
                  document.getElementById('siv').value=data.id;
                  ajaxindicatorstop();
                  location.reload();
                  //alert(data.id)
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
function showApprovals() {
    const view_approvals = $("#view_approvals").val();

    $.ajax({
        method: "GET",
        url: view_approvals,

        success: function (data) {
            // $('#boot').click();
            $("#view_item_datails").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text("View Approvals");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + textStatus);
        },
    });
}
$("#show_approved").on("click", function () {
    var showapprovedrequest = $("#view_approved").val();
    $.ajax({
        method: "GET",

        url: showapprovedrequest,

        success: function (data) {
            $("#view_item_datails").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text("Approved Issues");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
});
$("#test").on("click", function () {
    var selected_items = $("#selected").val();
    console.log(obj);

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
            selected: checked,
            items: obj,
        },
         beforeSend: function () {
                    ajaxindicatorstart("loading data... please wait...");
                },
        success: function (data) {
        ajaxindicatorstop();
            var gh = data.data.length;
            var q = data.quantity;
            console.log(q);
            console.log(gh);
            var html = "";
            var cost = 0;
            var total = 0;
            var num=1;
            html +=
                '<div class="table-responsive"><table class="table table-bordered table-striped table-sm" id="irec" width="100%"><thead class="thead-light"> <tr><th scope="col">#</th><th scope="col">Item Name</th><th scope="col">CODE #</th><th scope="col">Batch Number</th><th scope="col">UOM</th>';
            html +=
                '<th scope="col">Expiry</th><th scope="col">Quantity</th><th scope="col">Cost</th><th scope="col">Total</th><th scope="col">Action</th></thead> <tbody><tr id="row1">';
            for (let i = 0; i < gh; i++) {
                 idList.push(data.data[i]["id"]);
                cost = data.quantity[i] * data.data[i]["cost"];
                total = total + cost;
                 html += "<td>" +num+ "</td>";
                html += "<td>" + data.data[i]["item_name"] + "</td>";
                html += "<td>" + data.data[i]["code"] + "</td>";
                html += "<td>" + data.data[i]["batch_number"] + "</td>";
              
                html += "<td>" + data.data[i]["unit_issue"] + "</td>";
                html += "<td>" + data.data[i]["expiry_date"] + "</td>";
                  html +=
                    "<td id=quant_" +
                    data.data[i]["id"] +
                    ">" +
                    data.quantity[i] +
                    "</td>";
                html +="<td id=cost_"+data.data[i]["id"]+">"+data.data[i]["cost"]+"</td>";
                html += "<td id=total_"+data.data[i]["id"]+">" + cost + "</td>";
                html +="<td><button type='button' onclick='editRow("+data.data[i]["id"]+")'><i class='fa fa-edit'></i></button> || <button type='button' id="+data.data[i]["id"]+" onclick='removeRow(this,this.id)' ><i class='fa fa-trash' style='color:red'></i></button</td></tr>";
            
                num++;
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

function ViewItem(id) {
    const view_issue = $("#view_issue_siv").val();

    $.ajax({
        method: "GET",
        url: view_issue,
        data: {
            id: id,
        },

        success: function (data) {
            // $('#boot').click();
            $("#view_item_datails").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text("View Details");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + textStatus);
        },
    });
}
function ApproveItem(id) {
    var approve_issued_item = $("#save_approve_issued_item").val();
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to approve this issue !",
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
                          beforeSend: function () {
                    ajaxindicatorstart("loading data... please wait...");
                },
                        success: function (data) {
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
                            var approved=$("#approved_badge").text();
                             var request = $("#requests-badge").text();
                           
                             var add_one = parseInt(request);
                             var added = add_one - 1;
                             var sub_one=parseInt(approved)
                             var subtracted=sub_one+1;
                             $("#requests-badge").text(added);
                             $("#approved_badge").text(subtracted)
                             ajaxindicatorstop();
                            reloadTable();
                        },
                        error: function (error) {
                            console.log();
                        },
                    });
                },
            },

            cancel: function () {},
        },
    });

    ///
}
function VoidItem(id) {
    console.log(id);
    var void_url = $("#void_url").val();
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
            $.alert({
                icon: "fa fa-success",
                title: "Done",
                type: "green",
                content: data.message,
            });
        },
        error: function (error) {},
    });
}
function removeRow(button, id) {
   
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to delete this entry!",
        buttons: {
            Oky: {
                btnClass: "btn-danger",
                action: function () {
                    if (obj.length > 0) {
                        let startsWithword = obj.find((item) =>
                            item.startsWith(id+"_")
                        );
                        console.log("Starts With" + startsWithword);
                        console.log("Starts With Obj :" + obj);
                        if (startsWithword) {
                            obj = arrayRemove(obj, startsWithword);
                            if(changed.length>0){
                              changed=arrayRemove(changed,startsWithword);  
                            }
                           
                        } else {
                        }
                       
                    } else {
                    }
                    // Find the parent row and remove it
                    var row = button.parentNode.parentNode;
                    var total_cost = 0;

                    console.log("Not Spliced" + idList);

                    idList = arrayRemove(idList, id);
                    console.log("Spliced" + idList);

                    for (var x = 0; x < idList.length; x++) {
                        var d = document.getElementById(
                            "total_" + idList[x]
                        ).innerText;
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
/**
 * Edits a row in a table.
 * Prompts the user to enter a new quantity for the selected row,
 * checks if the new quantity is valid, and then updates the row with the new quantity and calculates the total cost.
 * @param {number} id - The ID of the row to be edited.
 */
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
 checkQuantity(id, newQuantityInt,costInt);


 
}
function checkQuantity(id,quantity,cost){
     $.ajax({
         method: "GET",
         dataType: "JSON",
         url: check_quantity,
         data: {
             id: id,
             quantity: quantity,
         },

         success: function (data) {
            console.log(data)
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
                  console.log("Edit change"+changed)
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
function reloadTable(){
    t = $("#issue_approvals_items").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy:true,
    info: true,
    lengthMenu: [10, 20, 20],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: approval_issues,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "check", width: "3%" },
        { data: "id", width: "3%" },
        { data: "issue_date", width: "15%" },
        { data: "issue_to", width: "10%" },
        { data: "issue_by", width: "20%" },
        { data: "status", width: "10%" },
        { data: "action", width: "30%" },
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