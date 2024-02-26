    "use strict";
      var url=$('#post_url').val();
var fetch_data=$('#fetch_data').val();
     var item_search=$('#item_search').val();
	 var item_id='';
	  var t;
  var table_url=$('#loadTable').val()
		var search=$('#search_item').val();
  

	//--->save whole row entery > end


	 $( "#search_item" ).autocomplete({
   
        source: function( request, response ) {


                 $('#add_item').text('Searching...')
          $.ajax({
            url:item_search,
            type: 'GET',
            dataType: "json",
            data: {
               search: request.term
            },
            success: function( data ) {
              if(data.length>0){
               response( data );
            }
            else{
              data.push({
                        id: 0,
                        label: "No results found"
                    });
                    
                   response(data)  
                      $('#add_item').text('Add')
                    $('#add_item').prop('disabled',true);
            }
            }
          });
        },
        select: function (event, ui) {
                if (ui.item.id != 0) {
           $('#search_item').val(ui.item.label);
           console.log(ui.item); 
		   item_id=ui.item.id;
		 $('#add_item').prop('disabled',false);
         
        $('#add_item').text('Add')
                }
           return false;
        }
      });
	  $('#add_item').on('click',function(e){
        
		e.preventDefault();
		var recieve_item=$('#recieve_url').val();
		  console.log(item_id);

		    $.ajax({
        method:'GET',
        url:recieve_item,
        data:{
            id:item_id,
        },
       

        success: function (data) {

         
             $('#receive_item').html(data);
            $('#inforg').modal('show'); // show bootstrap modal
             $('.modal-title').text('Update Item Details'); 
          	 $('#add_item').prop('disabled',true);
            $("#search_item").val("");
        },
        error: function (jqXHR, textStatus, errorThrown) {
           // console.log(get_case_next_modal)
            alert('Error '+errorThrown);
        }
    });
	  })

   
     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        t = $('#received_items').DataTable({
            "processing": true,
            "serverSide": true,
           "paging" : false,
            "info": false,
              "dom": 'lrtip',
               "language": {
      "emptyTable": "No data available. Search item in the search box click add"
    },
 "lengthMenu": [5, 10, 15],
             "responsive":true,
            "order": [[0, "desc"]],
            "oLanguage": {sProcessing: "<div class='loader-container'><div id='loader'></div></div>"},
            "ajax": {
                "url": table_url,
                "dataType": "json",
                "type": "GET",
               
            },
     'initComplete':function(settings, json){
       var total=parseFloat(json.total).toFixed(2);
        $("#cost").val(total);
        var countj =json.count;
        if(countj>0){
          $("#save_received").prop('disabled',false);
           
        }

     },
            AutoWidth: false,
            "columns": [
                {"data": "id", width: '3%'},
                {"data": "description", width: '30%'},
                {"data":"unit"},
                {"data": "quantity"},
                {"data": "batch"},
                {"data": "expiry"},
                {"data":'cost'},
                 {"data":'total'},
                {"data":'action'}
                
                

            ],
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [-1], //last column
                    "orderable": false, //set not orderable
                },
                {
                    "targets": [-2], //last column
                    "orderable": false, //set not orderable
                },
                {
                    "targets": [-3], //last column
                    "orderable": false, //set not orderable
                }
               
               
            ],
           
        });

function InitializeForm(id){
   
 const receive_from = $("#lab_id").val();
 const get_sections =$('#get_sections').val();
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
                html+=' <label for="section_id">Receiving Unit</label>'
            html+='<select class="form-control" id="section_id" name="section_id" style="width: 75%"  required>'
            html+='<option value=""></option>'
            data.sections.forEach((element) => {
       
       html +=
           '<option value='+element.id+'>' +
           element.section_name +
           "</option>";
   
                console.log(element.section_name)
             });
             //console.log(data.sections);
html+='</select>'
              $("#req").html(html);
              $("#req").show(); 
               document.getElementById("req").hidden = false; 
         }
         else{
             $("#req").show(); 
             document.getElementById('req').hidden=true; 
         }
       
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + errorThrown);
     },
 });
  
}

$('#save_received').on('click',function(){
   var lab_id = $("#lab_id").val();
   var supplier = $("#supplier_id").val();
   var select_id = $("#section_id").val();
   var gnr_number=$('#grn_number').val();
if(!lab_id){
$.alert({
    icon: "fa fa-warning",
    title: "Missing information!",
    type: "orange",
    content: "Please Select Laboratory/store!",
});
return;
}
 

   if (!supplier) {
       $.alert({
           icon: "fa fa-warning",
           title: "Missing information!",
           type: "orange",
           content: "Please select supplier!",
       });
       return;
   }
   if(!gnr_number){
  $.alert({
      icon: "fa fa-warning",
      title: "Missing information!",
      type: "orange",
      content: "Please Enter GNR number!",
  });
    return;
   }

   
var data=$('#receving_form').serialize();
var save_url=$('#save_form_item').val();
console.log(data);

      $.ajaxSetup({
          headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
      });
      $.ajax({
          method:"POST",
          dataType:"JSON",
          url: save_url,
          data: data,
          success: function (data) {
            console.log('GRN :'+data.grn_number);
            var ght = data.grn_number;
              // Welcome notification
              // Welcome notification
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

            $("#receving_form")[0].reset();
            PrintGRN(data.grn_number); 
            $('#print_grn').prop('disabled',false);

         t.destroy();
        LoadTable();
          },
      });


})

//print grn form

function PrintGRN(id){
  var print_item = $("#print_url").val();

$.ajax({
    method: "GET",

    url: print_item,
    data: {
        id: id,
    },

    success: function (data) {
        $("#receive_item").html(data);
        $("#inforg").modal("show"); // show bootstrap modal
        $(".modal-title").text("Print GRN ");
    },
    error: function (jqXHR, textStatus, errorThrown) {
        // console.log(get_case_next_modal)
        alert("Error " + errorThrown);
    },
});
}
function LoadTable() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    t = $("#received_items").DataTable({
        processing: true,
        serverSide: true,
        paging: false,
        info: false,
        dom: "lrtip",
        lengthMenu: [5, 10, 15],
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
        initComplete: function (settings, json) {
            var total = parseFloat(json.total).toFixed(2);
            $("#cost").val(total);
            var countj = json.count;
            if (countj > 0) {
                $("#save_received").prop("disabled", false);
            }
        },
        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "description", width: "30%" },
            { data: "unit" },
            { data: "quantity" },
            { data: "batch" },
            { data: "expiry" },
            { data: "cost" },
            { data: "total" },
            { data: "action" },
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
function DeleteItem(id) {
    const deleteItem = $("#delete_item").val();

   
$.confirm({
    title: "Confirm!",
    content:
        "Are you sure you want to remove this entry?",
    buttons: {
        Oky: {
            btnClass: "btn-warning",
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
                    dataType: "JSON",
                    url: deleteItem,
                    data: {
                        id:id
                    },
                    success: function (data) {
                        // Welcome notification
                        // Welcome notification
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
                        t.destroy();
                        LoadTable();
                    },
                });
            },
        },

        cancel: function () {},
    },
});
}
function removeAll(){
    const deleteAll = $("#delete_all").val();

    $.confirm({
        title: "Confirm!",
        content: "Are you sure you want to clear all the entries?",
        buttons: {
            Oky: {
                btnClass: "btn-warning",
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
                        dataType: "JSON",
                        url: deleteAll,
                       
                        success: function (data) {
                            // Welcome notification
                            // Welcome notification
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
                            t.destroy();
                            LoadTable();
                        },
                    });
                },
            },

            cancel: function () {},
        },
    });
}