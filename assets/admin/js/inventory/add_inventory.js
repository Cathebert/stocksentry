"use strict";
      var url=$('#post_url').val();
var table_url=$('#table_data').val();

var token=$('#token').val();
 var t;
        $('#form_id').on('click','#upload-result', function(e){
           
            e.preventDefault();
            var code= $('#code').val();
            var item_name = $("#item_name").val();
            var warehouse_size = $("#warehouse_size").val();
            var cat_number = $("#cat_number").val()
         
     if(!item_name)  {
         $.alert({
             icon: "fa fa-warning",
             title: "Missing item name!",
             type: "orange",
             content: "Please enter name",
         });
         return; 
     }   
     
 
          
     
    
       $('#upload-result').prop('disabled', true) 

 
         $.ajaxSetup({
             headers: {
                 "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
             },
         });
       
            $.ajax({
                method: "POST",
                dataType:"JSON",
                url: url,
                data: $('#form_id').serialize(),
                 beforeSend: function(){
 ajaxindicatorstart('saving data... please wait...')
  
   },
                success: function(data) {
                 ajaxindicatorstop();
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
                 $('#upload-result').prop('disabled', false) 
               
            $("#form_id")[0].reset();
            $('#uln').val(data.uln)
                t.destroy();
                 getTodaysItem()
                }
            });
            return false;
        });
      
       
var DatatableRemoteAjaxDemo = function () {

    var lsitDataInTable = function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        t = $("#added_item").DataTable({
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
                url: table_url,
                dataType: "json",
                type: "GET",
            },

            AutoWidth: false,
            columns: [
                { data: "id", width: "3%" },
                { data: "name" ,width:"30%" },
                { data: "code", width: "5%" },
                 { data: "cat_number" },
                { data: "image", width: "20%" },
                { data: "brand", width: "3%" },
                { data: "warehouse_size" },
                { data: "hazardous" },
                { data: "storage_temp" },
                { data: "unit_issue" },
                { data: "stock_level" },
                { data: "section" },
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

    //== Public Functions
    return {
        // public functions
        init: function () {
            lsitDataInTable();

        

        }
    };
}();
jQuery(document).ready(function () {
    DatatableRemoteAjaxDemo.init()
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

/**
 * get todays added items
 */


function  getTodaysItem(){
t = $("#added_item").DataTable({
    processing: true,
      destroy:true,
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
                { data: "name" ,width:"30%" },
                { data: "code", width: "5%" },
                 { data: "cat_number" },
                { data: "image", width: "20%" },
                { data: "brand", width: "3%" },
                { data: "warehouse_size" },
                { data: "hazardous" },
                { data: "storage_temp" },
                { data: "unit_issue" },
                { data: "stock_level",width:"20%" },
                { data: "section" },
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

t = $("#added_item").DataTable({
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
        url: table_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
      
                { data: "id", width: "3%" },
                { data: "name" ,width:"30%" },
                { data: "code", width: "5%" },
                { data: "cat_number" },
                { data: "image", width: "20%" },
                { data: "brand", width: "3%" },
                { data: "warehouse_size" },
                { data: "hazardous" },
                { data: "storage_temp" },
                { data: "unit_issue" },
                { data: "stock_level" },
                { data: "section" },
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

 function getSections(id) {
       const receive_from = $("#lab_id").val();
       const get_sections = $("#get_selected_lab").val();
       $.ajax({
           method: "GET",
           dataType: "json",
           url: get_sections,
           data: {
               id: id,
           },

           success: function (data) {
               if (data.status == 0) {
                   var html = "";
                   html += ' <label for="section_id">Sections</label>';
                   html +=
                       '<select class="form-control" id="section_id" name="section_id" style="width: 75%"  required>';
                   html += '<option value=""></option>';
                   data.sections.forEach((element) => {
                       html +=
                           "<option value=" +
                           element.id +
                           ">" +
                           element.section_name +
                           "</option>";

                       console.log(element.section_name);
                   });
                   //console.log(data.sections);
                   html += "</select>";
                   $("#req").html(html);
                   $("#req").show();
                   document.getElementById("req").hidden = false;
               } else {
                   $("#req").show();
                   document.getElementById("req").hidden = true;
               }
           },
           error: function (jqXHR, textStatus, errorThrown) {
               // console.log(get_case_next_modal)
               alert("Error " + errorThrown);
           },
       });
   }
   
 function ajaxindicatorstart(text)
{
    if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
    jQuery('body').append('<div id="resultLoading" style="display:none"><div><img src="https://stocksentry.org/assets/img/ajax-loader.gif"><div>'+text+'</div></div><div class="bg"></div></div>');
    }
 
    jQuery('#resultLoading').css({
        'width':'100%',
        'height':'100%',
        'position':'fixed',
        'z-index':'10000000',
        'top':'0',
        'left':'0',
        'right':'0',
        'bottom':'0',
        'margin':'auto'
    });
 
    jQuery('#resultLoading .bg').css({
        'background':'#000000',
        'opacity':'0.7',
        'width':'100%',
        'height':'100%',
        'position':'absolute',
        'top':'0'
    });
 
    jQuery('#resultLoading>div:first').css({
        'width': '250px',
        'height':'75px',
        'text-align': 'center',
        'position': 'fixed',
        'top':'0',
        'left':'0',
        'right':'0',
        'bottom':'0',
        'margin':'auto',
        'font-size':'16px',
        'z-index':'10',
        'color':'#ffffff'
 
    });
 
    jQuery('#resultLoading .bg').height('100%');
       jQuery('#resultLoading').fadeIn(300);
    jQuery('body').css('cursor', 'wait');
}
function ajaxindicatorstop()
{
    jQuery('#resultLoading .bg').height('100%');
       jQuery('#resultLoading').fadeOut(300);
    jQuery('body').css('cursor', 'default');
}