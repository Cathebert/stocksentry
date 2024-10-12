<div class="modal-header">

        <h5 class="modal-title">Edit Entry</h5>
        <button type="button"  id="btn-close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
<div class="col-sm-12">

    <div class="card">

      <div class="card-body">
       @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <h5 class="card-title"> <strong>Edit  Inventory Details</strong></h5>
        <form method="post" id="inventory_form_edit" >
          @csrf
          <input type="hidden" class="form-control" id="inventory_save_edit" name="inventory_save_edit" value="{{route('inventory.save_edit') }}"/>
          <input type="hidden" class="form-control" id="id" name="id" value="{{$id}}"/>





<hr></br>
  <div class="row">
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item  Name</label>
    <input type="text" class="form-control" id="item_name" name="item_name" value="{{$inventory->item_name}}" readonly/>

  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Code</label>
    <input type="text" class="form-control" id="code" name="code" value="{{$inventory->code}}" readonly>

  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Batch Number</label>
    <input type="text" class="form-control" id="batch_number" name="batch_number" value="{{$inventory->batch_number}}" required>
<input type="hidden" class="form-control" id="old_batch_number" name="old_batch_number" value="{{$inventory->batch_number}}">
</div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Expiry Date</label>
    <input type="date" class="form-control" id="expiry" name="expiry" value="{{$inventory->expiry_date}}" required>
  </div>


</div>
  <button type="submit" class="btn btn-primary" id="save_inventory" style="float:right">Save</button>
</form>
      </div>
    </div>
  </div>



</div>
</div>
<script type="text/javascript">
$('#save_inventory').on('click',function(e){
    e.preventDefault();
let save_inventory_url=$("#inventory_save_edit").val()


    $.confirm({
        title: "Confirm!",
        content: "Do you really  want to update  these entries?!",
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
                        url:save_inventory_url,
                        dataType: "JSON",
                        data: $('#inventory_form_edit').serialize(),
                        success: function (data) {
                            if(data.error==false){
                                document.querySelector('button.btn-close').click();
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
})

</script>