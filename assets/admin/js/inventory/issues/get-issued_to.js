"use strict"
var y;
var received_issues = $("#received_issues").val();
var accept_issue=$("#accept_issue").val();



y = $("#issue_items_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy:true,
    info: true,
    lengthMenu: [10, 50, 100],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: received_issues,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "issue_date", width: "15%" },
        { data: "issue_from", width: "20%" },
        { data: "issue_to", width: "20%" },
        { data: "action", width: "20%" },
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

function ReceiveIssue(id){
      $.ajaxSetup({
          headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
      });
      $.ajax({
          method: "POST",
          url: accept_issue,
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
               reLoadTable();
          },
         
          error: function (error) {},
      });
}
function reLoadTable(){
  y = $("#issue_items_table").DataTable({
      processing: true,
      serverSide: true,
      paging: true,
      destroy: true,
      info: true,
      lengthMenu: [10, 50, 100],
      responsive: true,
      order: [[0, "desc"]],
      oLanguage: {
          sProcessing:
              "<div class='loader-container'><div id='loader'></div></div>",
      },
      ajax: {
          url: received_issues,
          dataType: "json",
          type: "GET",
      },

      AutoWidth: false,
      columns: [
          { data: "id", width: "3%" },
          { data: "issue_date", width: "15%" },
          { data: "issue_from", width: "20%" },
          { data: "issue_to", width: "20%" },
          { data: "action", width: "20%" },
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
function viewIssue(id) {
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
function getByStockTransferNumber(value){
    let search_by_number=$('#get_by_siv').val()
    
    y = $("#issue_items_table").DataTable({
      processing: true,
      serverSide: true,
      paging: true,
      destroy: true,
      info: true,
      lengthMenu: [10, 50, 100],
      responsive: true,
      order: [[0, "desc"]],
      oLanguage: {
          sProcessing:
              "<div class='loader-container'><div id='loader'></div></div>",
      },
      ajax: {
          url:  search_by_number,
          dataType: "json",
          type: "GET",
          data:{siv:value}
      },

      AutoWidth: false,
      columns: [
          { data: "id", width: "3%" },
          { data: "issue_date", width: "15%" },
          { data: "issue_from", width: "20%" },
          { data: "issue_to", width: "20%" },
          { data: "action", width: "20%" },
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

function getByDateRange(){
    let date_url=$('#get_date_url').val()
    let start_date=$('#issue_start_date').val()
    let end_date=$('#issue_end_date').val()
    
    if(! start_date){
           $.alert({
                icon: "fa fa-success",
                title: "Done",
                type: "green",
                content: "Please Select Start Date",
            });
            return
    }
    
     y = $("#issue_items_table").DataTable({
      processing: true,
      serverSide: true,
      paging: true,
      destroy: true,
      info: true,
      lengthMenu: [10, 50, 100],
      responsive: true,
      order: [[0, "desc"]],
      oLanguage: {
          sProcessing:
              "<div class='loader-container'><div id='loader'></div></div>",
      },
      ajax: {
          url:  date_url,
          dataType: "json",
          type: "GET",
          data:{
             start_date:start_date,
              end_date:end_date
              
          }
      },

      AutoWidth: false,
      columns: [
          { data: "id", width: "3%" },
          { data: "issue_date", width: "15%" },
          { data: "issue_from", width: "20%" },
          { data: "issue_to", width: "20%" },
          { data: "action", width: "20%" },
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



function getByLabSent(value){
    if(!value){
        return
    }
    let byLab_url=$('#get_sent_lab_url').val()
    
    
     y = $("#issue_items_table").DataTable({
      processing: true,
      serverSide: true,
      paging: true,
      destroy: true,
      info: true,
      lengthMenu: [10, 50, 100],
      responsive: true,
      order: [[0, "desc"]],
      oLanguage: {
          sProcessing:
              "<div class='loader-container'><div id='loader'></div></div>",
      },
      ajax: {
          url:  byLab_url,
          dataType: "json",
          type: "GET",
          data:{
             from_lab:value,
             
              
          }
      },

      AutoWidth: false,
      columns: [
          { data: "id", width: "3%" },
          { data: "issue_date", width: "15%" },
          { data: "issue_from", width: "20%" },
          { data: "issue_to", width: "20%" },
          { data: "action", width: "20%" },
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