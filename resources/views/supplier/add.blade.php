 @extends('layouts.main')
@section('title','Add Supplier')
@section('content')


 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Suppliers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
  </ol>
</nav>
  
<div class="row" >
  
  <div class="col-sm-9">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Add Supplier</strong></h5>
        <form method="post" id="supplier_form">
          @csrf
          <input type="hidden" class="form-control" id="supplier_url" value="{{route('supplier.create')}}">
    
 

  
 
<hr></br>
  <div class="row">
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Supplier Name</label>
    <input type="text" class="form-control" id="supplier_name" name="supplier_name">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contact Person</label>
    <input type="text" class="form-control" id="contact_person" name="contact_person">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Address</label>
    <input type="text" class="form-control" id="address" name="address">
  </div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Email</label>
    <input type="text" class="form-control" id="email" name="email">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Phone Number</label>
    <input type="text" class="form-control" id="phone" name="phone_number">
  </div>
 
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract Expiry Date</label>
    <input type="date" class="form-control" id="contract_expiry" name="contract_expiry">
  </div>


</div>


  <button type="submit" class="btn btn-primary" id="submit" style="float:right">Save</button>
</form>
      </div>
    </div>
  </div>

  <div class="col-sm-3" hidden>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Expiry Stats</h5>
       <h4 class="small font-weight-bold">Good Condition </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar " role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"id="good">1</div>
                                    </div>
                                    <h4 class="small font-weight-bold">Warning condition </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="warning">2</div>
                                    </div>
                                    <h4 class="small font-weight-bold">Expired </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="expired">3</div>
                                    </div>
      </div>
    </div>
  </div>
</div>

<br>
  <div class="col-sm-12" hidden >
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><strong>Items Added Today</strong></h5>
        <div class="table-responsive">
        <table class="table table-sm">
<thead class="thead-light">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Description</th>
      <th scope="col">Unit</th>
      <th scope="col">Quantity</th>
       <th scope="col">Batch #</th>
        <th scope="col">Expiry Date</th>
    </tr>
  </thead>
  <tbody>
    <tr hidden>
      <th scope="row">1</th>
      <td>item</td>
      <td>Otto</td>
      <td>@mdo</td>
       <td>@mdo</td>
        <td>@mdo</td>
    </tr>
    <tr hidden>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
       <td>@mdo</td>
        <td>@mdo</td>
    </tr>
    <tr hidden>
      <th scope="row">3</th>
      <td>Larry</td>
      <td>the Bird</td>
      <td>@twitter</td>
       <td>@mdo</td>
        <td>@mdo</td>
    </tr>
  </tbody>
</table>
      </div>
       </div>
    </div>
  </div>
<script type="text/javascript">
  var fetch="{{route('stats')}}"
    $.ajax({
                method: "GET",
                dataType:"JSON",
                url: fetch,
                data: $('#form_id').serialize(),
                success: function(data) {
                  
                 $('#good').html(data.good)
                 $('#warning').html(data.warning)
                 $('#expired').html(data.expired)
                }
            });
  </script>
    <!-- /.container-fluid -->

            </div>
          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/supplier/add_supplier.js') }}"></script>
  
  
  
@endpush