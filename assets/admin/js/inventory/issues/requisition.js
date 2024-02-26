
    var p = "";
    var dat_url = $("#get_requisitions").val();

    p = $("#requisitions").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        select: true,
        info: true,
        lengthMenu: [10, 50,100],
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
            { data: "sr" },
            { data: "request_lab" },
            { data: "request_date" },
            { data: "options" },
            {data:"marked"}
           
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
    function AcceptApprovedRequest(id){
       var approve_issued_item = $("#save_approved_request").val();

       $.confirm({
           title: "Confirm!",
           content: "Do you really  want to accept this order!",
           buttons: {
               Oky: {
                   btnClass: "btn-success",
                   action: function () {
                       $.ajaxSetup({
                           headers: {
                               "X-CSRF-TOKEN": $(
                                   'meta[name="csrf-token"]'
                               ).attr("content"),
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
                            if(data.error==false){
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
                             
                            }
                            else{
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
                               p.destroy();
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
 
    }
function ViewApprovedRequest(id) {
    const view_issue = $("#view_approved_request").val();

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
$('#show_consolidated_history').on('click',function(e){
    e.preventDefault();
 const view_issue = $("#view_consolidated_list").val();

 $.ajax({
     method: "GET",
     url: view_issue,
    

     success: function (data) {
         // $('#boot').click();
         $("#view_item_datails").html(data);
         $("#inforg").modal("show"); // show bootstrap modal
         $(".modal-title").text("Consolidation History");
     },
     error: function (jqXHR, textStatus, errorThrown) {
         // console.log(get_case_next_modal)
         alert("Error " + textStatus);
     },
 });
});
function LoadRequestData(){
     p = $("#requisitions").DataTable({
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
             { data: "sr" },
             { data: "request_lab" },
             { data: "request_date" },
             { data: "options" },
             { data: "marked" },
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
function getSRnumber(){
var value=$('#sr').val();

const type="SRNUMBER"
p.destroy();
filterData(value,type)
}
function getLab(value){
   
        const receive_from = $("#lab_id").val();
        const get_sections = $("#get_sections").val();
        $.ajax({
            method: "GET",
            dataType: "json",
            url: get_sections,
            data: {
                id: value,
            },

            success: function (data) {
                if (data.status == 0) {
                    var html = "";
                    html += ' <label for="section_id">Sections</label>';
                    html +=
                        '<select class="form-control" id="section_id" name="section_id" style="width: 75%" onchange="getSection(this.value)" required >';
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
    
var valu = value
const type = "LAB"
p.destroy();
filterData(valu, type);
}

function getSection(value) {
    var valu = value;
    const type = "SECTION";
    p.destroy();
    filterData(valu, type);
}
function filterData(value, type){
    var filter_url= $('#filter_url').val();
    var lab_id=$('#lab_id').val();
    p = $("#requisitions").DataTable({
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
            url: filter_url,
            dataType: "json",
            type: "GET",
            data: {
                type: type,
                value: value,
                lab_id:lab_id,
            },
        },

        AutoWidth: false,
        columns: [
            { data: "sr" },
            { data: "request_lab" },
            { data: "request_date" },
            { data: "options" },
            {data: "marked"}
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
function MarkForConsolidation(id){
    let mark_consolidate=$('#mark_consolidate').val();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        url: mark_consolidate,
        dataType: "JSON",
        data: {
            id: id,
        },
        success: function (data) {
           
            p.destroy();
            LoadRequestData();
        },
        error: function (error) {
            console.log(error);
        },
    }); 
}
$('#view_marked').on('click',function(e){
    e.preventDefault();
     const marked_consolidate = $("#view_marked_for_consolidation").val();

     $.ajax({
         method: "GET",
         url: marked_consolidate,
         

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
})

  
  function RemoveForConsolidation(id){
    let remove_consolidate=$('#remove_consolidate').val();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        method: "POST",
        url: remove_consolidate,
        dataType: "JSON",
        data: {
            id: id,
        },
        success: function (data) {
           
      
            reloadMarked();
             LoadRequestData();
        },
        error: function (error) {
            console.log(error);
        },
    }); 
}
 