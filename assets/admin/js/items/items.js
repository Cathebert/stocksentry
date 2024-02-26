  "use strict";
var url=$('#post_ur').val();
var table_url=$('#item_data').val();
var edit_item=$('#item_edit').val();
var delete_item=$('#item_delete').val();
var checked =[];
var t;
        $('#form_id').on('click','#upload-result', function(e){
           
            e.preventDefault();
              $.ajaxSetup({
                  headers: {
                      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                          "content"
                      ),
                  },
              });
            $.ajax({
                method: "POST",
                dataType:"JSON",
                url: url,
                data: $('#form_id').serialize(),
                success: function(data) {
                $("#form_id").reset();
              // Welcome notification
               // Welcome notification
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
                t.destroy();
                 getItems();
                }
            });
            return false;
        });
      
        function getItems() {
            t = $("#items_table").DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                lengthMenu: [10, 50, 100],
                responsive: true,
                order: [[0, "desc"]],
                oLanguage: {
                    sProcessing:
                        "<div class='loader-container'><div id='loader'></div></div>",
                },
                ajax: {
                    url: table_url,
                    dataType: "json",
                    type: "GET",
                },

                AutoWidth: false,
                columns: [
             { data: "id", width: "3%" },
            { data: "name",width:"30%" },
            { data: "code", width: "3%" },
            { data: "cat_number" },
            { data: "image", width: "20%" },
            { data: "brand", width: "3%" },
            { data: "warehouse_size" },
            { data: "hazardous" },
            { data: "storage_temp" },
            { data: "unit_issue" },
            { data: "stock_level" },
            { data: "section" },
            { data: "options" },
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

        t = $("#items_table").DataTable({
            processing: true,
            serverSide: true,
            lengthMenu: [10, 50, 100],
            responsive: true,
            order: [[0, "desc"]],
            oLanguage: {
                sProcessing:
                    "<div class='loader-container'><div id='loader'></div></div>",
            },
            ajax: {
                url: table_url,
                dataType: "json",
                type: "GET",
            },

            AutoWidth: false,
            columns: [
            { data: "id", width: "3%" },
            { data: "name",width:"30%" },
            { data: "code", width: "3%" },
            { data: "cat_number" },
            { data: "image", width: "20%" },
            { data: "brand", width: "3%" },
            { data: "warehouse_size" },
            { data: "hazardous" },
            { data: "storage_temp" },
            { data: "unit_issue" },
            { data: "stock_level" },
            { data: "section" },
            { data: "options" },
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
function EditItem(id){

   // alert(id);
    $.ajax({
        method:'GET',

        url: edit_item,
        data:{
            id:id,
        },
       

        success: function (data) {

         
             $('#edit_item').html(data);
            $('#infor').modal('show'); // show bootstrap modal
             $('.modal-title').text('Update Item Details'); 
          
           
        },
        error: function (jqXHR, textStatus, errorThrown) {
           // console.log(get_case_next_modal)
            alert('Error '+errorThrown);
        }
    });
 
}
function deleteItem(id){
    Swal.fire({
  title: 'Are you sure?',
  text: "You won't be able to revert this!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Yes, delete it!'
}).then((result) => {
  if (result.isConfirmed) {
    //delete 
   
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method:'POST',
            dataType:'JSON',
            url: delete_item,
        data:{
            id:id,
        },
        success:function(data){

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
    getItems();
        },
        error:function(error){

        }
        })
    
  }
})
}
function selectCheckedItem(id){
   
        if (document.getElementById(id).checked == true) {
            if (checked.includes(id)) {
            } else {
             
                checked.push(id);
                
            }
        } else {
            if (checked.includes(id)) {
                checked = checked.filter(function (item) {
                    return item !== id;
                });
            }
        }
 console.log(checked);
   
}
function CheckBoxSelect(id){
     var checkboxes = document.getElementsByName('check')
    checkboxes.forEach((item) => {
        if (item.checked ==true) 
        item.checked=false
    })
 var check= document.getElementById('check_'+id).checked=true;
var id=id;
}

function inputFile() {
      Swal.fire({
          title: "Are you sure?",
          text: "All  the items will be replaced by items on the file!.Do you wish to Continue?",
          icon: "danger",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, Continue!",
      }).then((result) => {
          if (result.isConfirmed) {
              //delete
goAhead()
        
          }
      });

//file
 

 }
 function deactivateItem(){
    var deactivate_item=$('#deactivate').val();
    if(checked.length>0){
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: deactivate_item,
        data: {
            deactivated: checked,
        },
        success: function (data) {
            Swal.fire({
                title: "Deactivated!",
                text: data.message,
                icon: "success",
                confirmButtonColor: "#3085d6",
            });
        },
        error: function (error) {},
    });
    }
    else{
        alert( "nothing Selected")
    }
 }

 function goAhead(){
    var item_csvlist=$('#item-csvlist').val();
     Swal.fire({
         title: "Select a CSV file",
         showCancelButton: true,
         confirmButtonText: "Upload",
         confirmButtonColor: "#3085d6",
         reverseButtons: true,
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
                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                         "content"
                     ),
                 },
                 method: "post",
                 url: item_csvlist,
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

 function searchItemByCode(){
    var value=$('#item_code').val()
var type="code";

   
    LoadSearchedItems(value,type)
 }
function searchByName(){
     var value = $("#item_name").val();
     var type = "name";

 ;
     LoadSearchedItems(value, type);  
}

function searchByLab(value) {
  var id=value;
  if(id==99){
            getItems()
            return
  }
    var type = "lab";

    LoadSearchedItems(id, type);
}
function searchBySection(value) {
  
    var type = "section";

  
    LoadSearchedItems(value, type);
}
function searchByCategory(){
     var value = $("#inputGroupSelect01").val();
     var type = "category";

   
     LoadSearchedItems(value, type);
}



 function LoadSearchedItems(value, type){
var search_url=$('#search_url').val();
    t = $("#items_table").DataTable({
            processing: true,
            serverSide: true,
            destroy:true,
            lengthMenu: [10, 50, 100],
            responsive: true,
            order: [[0, "desc"]],
            oLanguage: {
                sProcessing:
                    "<div class='loader-container'><div id='loader'></div></div>",
            },
            ajax: {
                url: search_url,
                dataType: "json",
                type: "GET",
                data:{
                    value:value,
                    type:type,
                },
            },

            AutoWidth: false,

            columns: [
            { data: "id", width: "3%" },
            { data: "name",width:"30%" },
            { data: "code", width: "3%" },
            { data: "cat_number" },
            { data: "image", width: "20%" },
            { data: "brand", width: "3%" },
            { data: "warehouse_size" },
            { data: "hazardous" },
            { data: "storage_temp" },
            { data: "unit_issue" },
            { data: "stock_level" },
            { data: "section" },
            { data: "options" },
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

 $('#export_list').on('click',function(e){
     e.preventDefault();
     let download=$('#export').val();
    // let form =$('#form_id').serialize();
   $.ajaxSetup({
                       headers: {
                           "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                               "content"
                           ),
                       },
                   });
            $.ajax({
                method: "GET",
                dataType:"JSON",
                url: download,
                data: $('#form_id').serialize(),
                success: function(data) {
                     window.location=data.path;
              console.log(data.url);
               
                },
                error: function(error){
                    
                }
                
           
            });
            return false;
       
 })