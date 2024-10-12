"use strict"
let contract_url=$('#contract_url').val();

var t = "";
t = $("#contract").DataTable({
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
        url: contract_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: true,

    columns: [
        { data: "id" },
        { data: "contract_number" },
        { data: "contract_name" },
        { data: "contract_desc" },
        { data: "contract_start" },
          { data: "frequency" },
        { data: "contract_unit" },
        { data: "contract_end" },
         { data: "sub_type" },
          { data: "supplier" },
           { data: "status" },
            { data: "action",width:"50%" },
             { data: "delete" },
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
function showContractAdd(){
    let showAddModal=$('#shoW_modal').val();
    $.ajax({
        method: "GET",

        url: showAddModal,
      

        success: function (data) {
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Contract Add ");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
function viewContract(id){
    let viewContract = $("#view_contract").val();
    $.ajax({
        method: "GET",

        url: viewContract,
        data: {
            id: id,
        },

        success: function (data) {
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Contract Details");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
function editContract(id){
    let editContract = $("#edit_contract").val();
    $.ajax({
        method: "GET",

        url: editContract,
        data: {
            id: id,
        },

        success: function (data) {
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Edit Contract ");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
function updateContract(id) {
    let updateContract = $("#update_contract").val();
    $.ajax({
        method: "GET",

        url: updateContract,
        data: {
            id: id,
        },

        success: function (data) {
            $("#receive_item").html(data);
            $("#inforg").modal("show"); // show bootstrap modal
            $(".modal-title").text(" Contract Update ");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // console.log(get_case_next_modal)
            alert("Error " + errorThrown);
        },
    });
}
function filterByNumber(val){
let value=val
let type='number';
search(value,type)

}

function filterByName(val){
    let value=val
    let type='name';
    search(value,type)
}
function search(value,type){
    let filter = $("#filter").val();
    t = $("#contract").DataTable({
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
            url: filter,
            dataType: "json",
            type: "GET",
            data: {
                value: value,
                type: type,
            },
        },

        AutoWidth: true,

        columns: [
      { data: "id" },
        { data: "contract_number" },
        { data: "contract_name" },
        { data: "contract_desc" },
        { data: "contract_start" },
        { data: "frequency" },
        { data: "contract_unit" },
        { data: "contract_end" },
         { data: "sub_type" },
          { data: "supplier" },
           { data: "status" },
            { data: "action",width:"50%" },
             { data: "delete" },
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

function deleteContract(id) {
    var delete_contract = $("#delete_contract").val();
    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to delete this contract?!",
        buttons: {
            Oky: {
                btnClass: "btn-danger",
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
                        url: delete_contract,
                        dataType: "JSON",
                        data: {
                            id: id,
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
                          location.reload();
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



function calculateEndDate(value){
   const start=$('#contract_startdate').val();
   if(!start){
    $('#contract_startdate').focus();
    return;
   }
    const frequency=$('#contract_frequency').val();
    if(!frequency){
      $('#contract_frequency').focus();
    }
  if(value==1){
   
const a = dayjs(start);
const b = a.add(frequency, 'M')
const c=dayjs(b).format('YYYY-MM-DD')
$('#contract_enddate').val(c);
  }
   if(value==2){
   
const a = dayjs(start);
const b = a.add(frequency, 'y')
const c=dayjs(b).format('YYYY-MM-DD')
$('#contract_enddate').val(c);
  }
}

function contractType(value){
  if(value==2){
    document.getElementById('show_supplier').hidden=true
     document.getElementById('contractor').hidden=false

  }
  else{
     document.getElementById('show_supplier').hidden=false
      document.getElementById('contractor').hidden=true
  }
}