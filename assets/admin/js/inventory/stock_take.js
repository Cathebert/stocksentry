"use strict";
var stock_take = $("#stock_taking").val();
var checked = [];
var obj = [];
var inventory = $("#inventory_taking").val();
var stoke;
$("#stock_take").on("click", function (e) {
    e.preventDefault();

    $.ajax({
        method: "GET",
        url: stock_take,

        success: function (data) {
            //console.log(data)
            $("#add_certi").html(data);
            $("#infor").modal("show"); // show bootstrap modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
});
function inputFile() {
    var inpufile=$('#inputFile').val();


  Swal.fire({
      title: "Select a CSV file",
      showCancelButton: true,
      confirmButtonText: "Upload",
      confirmButtonColor: "#3085d6",
      reverseButtons:true,
      input: "file",
      inputAttributes: {
          accept: ".csv",
          "aria-label": "Upload your CSV file",
      },
      onBeforeOpen: () => {
          $(".swal2-file").change(function () {
              var reader = new FileReader();
              reader.readAsDataURL(this.files[0]);
          });
      },
  }).then((file) => {
      if (file.value) {
          var formData = new FormData();
          var file = $(".swal2-file")[0].files[0];
          formData.append("fileToUpload", file);
          $.ajax({
              headers: {
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
              },
              method: "post",
              url: inpufile,
              data: formData,
              processData: false,
              contentType: false,
              success: function (resp) {
                  Swal.fire("Uploaded", resp.message, "success");
              },
              error: function () {
                  Swal.fire({
                      type: "error",
                      title: "Oops...",
                      text: "Something went wrong!",
                  });
              },
          });
      }
  });

 }

 function saveAll() {
    let expected=$('#expected').val()
    let custom_start = $("#start_date").val();
    let supervisor=$('#supervisor').val();
    let employees = $("#employees").val();
   
if(!supervisor){
    $.alert({
        icon: "fa fa-warning",
        title: "Missing information!",
        type: "orange",
        content: "Please select supervisor!",
    });
    return;  
}
   
if (!employees) {
    $.alert({
        icon: "fa fa-warning",
        title: "Missing information!",
        type: "orange",
        content: "Please select employees involved!",
    });
    return;
}
   if(obj.length==0){
     $.alert({
         icon: "fa fa-warning",
         title: "Missing information!",
         type: "orange",
         content: "Please enter stock quantities!",
     });
     return ;
   }
    
     //var custom_end = $("#end_date").val();

     if (!custom_start) {
       $.alert({
           icon: "fa fa-warning",
           title: "Missing information!",
           type: "orange",
           content: "Please Set Stock date!",
       });
       return; 
     }
   
     
     console.log(obj);
    
      doUpdate();
 }
 function doUpdate(period) {
     let save_all = $("#inventory_save_all").val();
     let consumed_form_data = $("#stock_save_form").serialize();
     $.ajaxSetup({
         headers: {
             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
         },
     });
     $.ajax({
         method: "POST",
         dataType: "JSON",
         url: save_all,
         data: {
             consumed_form_data,
             consumed: obj,
             period: period,
         },

         success: function (data) {
             //console.log(data)
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
                  LoadTable();
                 //$("#sel_" + id).prop("checked", true);
                 $("#fa_" + id).removeClass("fa-save");
                 $("#fa_" + id).addClass("fa-check");
               
                 obj.length=0;
              
                
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
             // show bootstrap modal
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
 }

 function getPhysicalCount(id, name) {
   
     var w = $("#s_" + name).val();
    var my_id = name;
    var q = $("#s_" + name).val();
    if (!w) {
        document.getElementById("se_"+ name).checked == false;
    } else {
        document.getElementById('se_'+name).checked == true;
    }

    let startsWithBan = obj.find((item) => item.startsWith(name+"_"));

  
    if (document.getElementById('se_'+name).checked == true) {
          if (startsWithBan) {
              obj= arrayRemove(obj, startsWithBan);
          } else {
              console.log("No element starts with " + name + "_");
          }
        if ($("#s_" + name).val()) {
            
            obj.push(my_id + "_" + q);
                        console.log(obj);
         
     } else {
            obj = arrayRemove(obj, startsWithBan);
         }
     }
     console.log(obj);
    
 
     
 }
 function saveConsumed(id) {
     // console.log(consumed_form)

     var period = $("#period").val();
     
     var quantity = $("#s_" + id).val();
     let expected=$('#expected').val()
    let custom_start = $("#start_date").val();
    let supervisor=$('#supervisor').val();
    let employees = $("#employees").val();
   
if(!supervisor){
    $.alert({
        icon: "fa fa-warning",
        title: "Missing information!",
        type: "orange",
        content: "Please select supervisor!",
    });
    return;  
}
   
if (!employees) {
    $.alert({
        icon: "fa fa-warning",
        title: "Missing information!",
        type: "orange",
        content: "Please select employees involved!",
    });
    return;
}
     if (!quantity) {
         $("#s_" + id).focus();
         return;
     }

     if (custom_start) {
         saveConsumedItem(id, quantity);
    
     } else {
         $.alert({
             icon: "fa fa-warning",
             title: "Missing information!",
             type: "orange",
             content: "Please Select Period or Set range !",
         });
     }
 }
 function saveConsumedItem(id, quantity, period) {
     var save_selected = $("#save_selected").val();
     var stock_save_form =$("#stock_save_form").serialize();
     $.ajaxSetup({
         headers: {
             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
         },
     });
     $.ajax({
         method: "POST",
         dataType: "JSON",
         url: save_selected,
         data: {
             stock_save_form,
             id: id,
             consumed: quantity,
             period: period,
         },

         success: function (data) {
             //console.log(data)
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
                 
                 LoadTable();
                // $("#sel_" + id).prop("checked", true);
                 $("#fa_"+id).removeClass("fa-save");
                 $("#fa_"+id).addClass("fa-check");
                  $("#fa_"+id).prop("disabled",true);
             
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
             // show bootstrap modal
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
 }
 function AddIdToList(id) {
  
     if (document.getElementById(id).checked == true) {
         if (checked.includes(id)) {
         } else {
             var quantity = $("#s_" + id).val();
             console.log("Quantity: "+quantity);
             checked.push(id);
             quantities.push(quantity);
         }
     } else {
         if (checked.includes(id)) {
             checked = checked.filter(function (item) {
                 return item !== id;
             });
         }
     }
     console.log(checked);
     console.log(quantities);
 }


stoke = $("#inventories_taking").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy:true,
    destroy:true,
    scrollCollapse: true,
    //scrollY: "200px",
    info: true,

    lengthMenu: [10, 20, 50],
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

    AutoWidth: true,
    columns: [
        { data: "id", width: "3%" },
        { data: "name", width:'30%' },
        { data: "code" },
        { data: "batch_number" },
        { data: "unit" },
        { data: "consumed" },
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
  stoke.on("click", "tbody tr", function () {
        let data = stoke.row(this).data();
        console.log(data)
        
        if (document.getElementById("se_"+data["item_id"]).checked == false) {
            document.getElementById("se_"+data["item_id"]).checked = true;
            $("#s_" + data["item_id"]).focus();
        } else {
            document.getElementById("se_"+data["item_id"]).checked = false;
            var id = data["item_id"];
            var q = $("#s_" + data["item_id"]).val();
            $("#s_" + data["item_id"]).val("");
            var item = id + "_" + q;
        
            obj = arrayRemove(obj, item);
        }
    })

    function arrayRemove(arr, value) {
    return arr.filter(function (item) {
        return item != value;
    });
}
function SelectArea(value){
    const select_area=$('#selected_location').val();
  
   stoke = $("#inventories_taking").DataTable({
       processing: true,
       serverSide: true,
       paging: true,
       destroy: true,
       scrollCollapse: true,
      // scrollY: "200px",
       info: true,

       lengthMenu: [10, 20, 50],
       responsive: true,
       order: [[0, "desc"]],
       oLanguage: {
           sProcessing:
               "<div class='loader-container'><div id='loader'></div></div>",
       },
       ajax: {
           url: select_area,
           dataType: "json",
           type: "GET",
           data: {
               id:value,
           },
       },

       AutoWidth: true,
       columns: [
           { data: "id", width: "3%" },
        { data: "name", width:'30%' },
        { data: "code" },
        { data: "batch_number" },
        { data: "unit" },
        { data: "consumed" },
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

function LoadTable(){
    toke = $("#inventories_taking").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy: true,
    destroy: true,
    scrollCollapse: true,
    //scrollY: "200px",
    info: true,

    lengthMenu: [10, 20, 50],
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

    AutoWidth: true,
    columns: [
        { data: "id", width: "3%" },
        { data: "name", width:'30%' },
        { data: "code" },
        { data: "batch_number" },
        { data: "unit" },
        { data: "consumed" },
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
function getItems(value){
    if(value==-1){
        LoadTable() 
    }
    let location = $("#item_locate").val();

    stoke = $("#inventories_taking").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        destroy: true,
        scrollCollapse: true,
        // scrollY: "200px",
        info: true,

        lengthMenu: [10, 20, 50],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: location,
            dataType: "json",
            type: "GET",
            data: {
                id: value,
            },
        },

        AutoWidth: true,
        columns: [
            { data: "id", width: "3%" },
            { data: "name", width: "30%" },
            { data: "code" },
            { data: "batch_number" },
            { data: "unit" },
            { data: "consumed" },
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
function downloadItemSelected(){
    let download = $("#download_item").val();
    let value = $("#items_list").val();
      $.ajax({
          method: "GET",
         
          url: download,
          data: {
              
             id: value,
              
          },

          success: function (data) {
              console.log(data)
              window.location=data.url;
          },
          error: function (jqXHR, textStatus, errorThrown) {
              // console.log(get_case_next_modal)
              alert("Error " + errorThrown);
          },
      });   
}