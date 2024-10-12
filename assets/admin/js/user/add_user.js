"use strict";
      var url=$('#post_url').val();

     
        $('#form_id').on('click','#submit', function(e){
       
         var name = $("#first_name").val()
         var last_name = $("#last_name").val()
         var email = $("#email").val();
         var user_position = $("#user_position").val();
         var user_role = $("#user_role").val();

          
         if (!name) {
             $.alert({
                 icon: "fa fa-warning",
                 title: "Missing information!",
                 type: "orange",
                 content: "Please provide name!",
             });
             $("#first_name").focus();
            e.preventDefault();
            return;
         }
         if (!last_name) {
             $.alert({
                 icon: "fa fa-warning",
                 title: "Missing information!",
                 type: "orange",
                 content: "Please provide Last name!",
             });
             $("#last_name").focus();
          e.preventDefault();
          return;
         }
         if (!email) {
             $.alert({
                 icon: "fa fa-warning",
                 title: "Missing information!",
                 type: "orange",
                 content: "Please provide email address!",
             });
             $("#email").focus();
             e.preventDefault();
             return
         }
         if (!user_position) {
             $.alert({
                 icon: "fa fa-warning",
                 title: "Missing information!",
                 type: "orange",
                 content: "Please provide position of user!",
             });
             $("#user_position").focus();
             e.preventDefault();
             return
         }
           $('#btn_loader').addClass('fa-spinner fa-spin');
                $("button[name='save_btn']").attr("disabled", "disabled");
            
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
                  beforeSend: function () {
                    ajaxindicatorstart("saving data... please wait...");
                },
                success: function(data) {
                  if(data.error==false){
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
                 $('#btn_loader').removeClass('fa-spinner fa-spin');
                 $("button[name='save_btn']").attr("disabled", false);
                    ajaxindicatorstop();
                }
                else{
                 ajaxindicatorstop();
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
                toastr["error"](data.message);  
                }
               // $("#form_id")[0].reset();
            }
           
            });
            return false;
        });
       function checkUserRole(value){
        if((value==2) ||(value== 3) || (value==4)){
            document.getElementById('labs').hidden=false;
        }
        else{
            document.getElementById("labs").hidden = true;
            document.getElementById("req").hidden = true;  
        }
       }
   function getSections(id) {
       const receive_from = $("#lab_id").val();
       const get_sections = $("#get_sections").val();
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
   function getLabName(value){
     const receive_from = $("#lab_id").val();
     const get_sections = $("#get_sections").val();
          if(value==99){
    document.getElementById("not_coldroom").hidden = true;  
 document.getElementById("coldroom").hidden = false;
   document.getElementById("my_lab").hidden = true;
  $("#check").val(1);
     }
  
if(value>0 && value<99){
 document.getElementById("my_lab").hidden = false;
 document.getElementById("not_coldroom").hidden = true;
  document.getElementById("coldroom").hidden = true;  
    $("#check").val(2);  
}

if(value==0){
  document.getElementById("not_coldroom").hidden = false; 
  document.getElementById("my_lab").hidden = true; 
  document.getElementById("coldroom").hidden = true; 
    $("#check").val(0);   
}
     $.ajax({
         method: "GET",
         dataType: "json",
         url: get_sections,
         data: {
             id: value,
         },

         success: function (data) {
            var name= data.name;
            $('#extension').text(name)
            $("#ext").val(name);
                 //console.log(data.sections);
                 
         },
         error: function (jqXHR, textStatus, errorThrown) {
             // console.log(get_case_next_modal)
             alert("Error " + errorThrown);
         },
     });
   }

   function updateUserName(lname){
    let name = $("#first_name").val();
    let first = name.charAt(0);
    $('#username').val(first+""+lname);
   }