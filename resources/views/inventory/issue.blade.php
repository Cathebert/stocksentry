 @extends('layouts.main')
@section('title','Issues')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Issue</li>
  </ol>
</nav>




<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Issues </strong></h5>
      
       
    
 
            <div class="row">
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"> Items Requisitions</button>
  </li>

  <li class="nav-item" role="presentation">
    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Issue </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Receive Issued</button>
  </li>
   <li class="nav-item" role="presentation">
    <button class="nav-link" id="store-tab" data-bs-toggle="tab" data-bs-target="#store" type="button" role="tab" aria-controls="contact" aria-selected="false">Stock Forecasting</button>
  </li>

</ul>
<div class="tab-content" id="myTabContent">
<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">  
  @include('inventory.issues_tab.requisition')
</div>


  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
     @include('inventory.issues_tab.issue')
   </div>
  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
     @include('inventory.issues_tab.issue_to')
  </div>

   <div class="tab-pane fade" id="store" role="tabpanel" aria-labelledby="store-tab">
    @include('inventory.issues_tab.projection')
   </div>
</div>

</ul>
</div>

<!---content end-->



 <!-- /.container-fluid -->
<div class="modal fade" id="boot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approvals</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <input type="hidden" id="view_issue_siv" value="{{route('issue.view')}}"/>
           <div class="card">
  <div class="card-header text-danger" >
    <strong>Pending Approvals</strong>
  </div>
  <div class="card-body">
    <h5 class="card-title">Issues</h5>
     <div class="table-responsive">
        <table class="table table-sm table-striped" id="issue_approvals_items"  width="100%">
<thead class="thead-light">
    <tr>
       
       <th width="5%"></th>
         <th width="5%">SIV #</th>
          <th width="20%">Issue Date</th>
          <th width="20%">Issue To</th>
          <th width="20%">Issue By</th>
            <th width="20%">Status</th>
             <th width="20%">Action</th>
           
  </thead>
</table>
      </div>
  </div>

</script>
</div>
    </div>
  </div>
</div>

 <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close_item_modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="check_quantity" value="{{route('inventory.check_quantity')}}"/>
         <div class="table-responsive">
        <table class="table table-sm" id="issue_items" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
     <th scope="col">Code</th>
        <th scope="col">Generic Name</th>
         <th scope="col">Available </th>
          <th scope="col">Requested</th>
      <th scope="col">Brand</th>
  
       <th scope="col">Status</th>
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
        <div class="products items"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="test">Ok</button>
      </div>
    </div>
  </div>
</div>
</div>
<script type="text/javascript">
     var checked= [];
   var arr=[]
$('#test').on('click',function(){
  var selected_items="{{ route('inventory.getSelectedItems') }}"
console.log(checked);
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
 $.ajax({
          method: "GET",
          dataType: "JSON",
          url: selected_items,
          data:{
            selected:checked,
            items:obj,
          },
          success: function (data) {
            var gh=data.data.length;
            var q=data.quantity;
            console.log(q)
console.log(gh)
  var html="";
  var cost=0;
  var total=0;
  html+='<div class="table-responsive"><table class="table table-sm" id="irec" width="100%"><thead class="thead-light"> <tr><th scope="col">CODE #</th><th scope="col">Item</th><th scope="col">UOM</th><th scope="col">Quantity</th>';
      html+='<th scope="col">Batch #</th><th scope="col">Expiry</th><th scope="col">Cost</th><th scope="col">Total</th></tr></thead> <tbody>';          
  for(let i=0 ;i<gh;i++){
    cost=data.quantity[i]* data.data[i]['cost'];
    total=total+cost;
html+='<td>'+data.data[i]['id']+'</td>';
html+='<td>'+data.data[i]['item_name']+'</td>';
html+='<td>'+data.data[i]['unit_issue']+'</td>';
html+='<td>'+data.quantity[i]+'</td>';
html+='<td>'+data.data[i]['batch_number']+'</td>';
html+='<td>'+data.data[i]['expiry_date']+'</td>';
html+='<td>'+data.data[i]['cost']+'</td>';
html+='<td>'+cost+'</td></tr>';
  }
  html+=' </tbody></table></div>';
console.log(total);
var total_cost= parseFloat(total).toFixed(2)
$('#cost').val( total_cost);
  $('#items').hide();
  $('#close_item_modal').click();
  $('#real_table').html(html);
console.log(html);
$('#save_issue').prop('disabled',false);
          },
      });

}); 

function AddIdToArray(id){
  if(document.getElementById(id).checked==true){
    document.getElementById('q_'+id).hidden=false
  
    if(checked.includes(id)){

  }else{
checked.push(id);

  }
  }
  else{
      document.getElementById('q_'+id).hidden=true;
   if(checked.includes(id)){
checked = checked.filter(function(item) {
    return item !== id
})

  }
  }
console.log(checked);

}
$('#issue_items').on( 'select.dt', function ( e, dt, type, indexes ) {
       var data = dt.rows(indexes).data();
        console.log(data);
} );


  </script>

            </div>
          <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="view_item_datails">

          
</div>
            @endsection

            @push('js')
                <script src="{{asset('assets/admin/js/inventory/issues/requisition.js') }}"></script>
            <script src="{{asset('assets/admin/js/inventory/issues/add-issue.js') }}"></script>
         <script src="{{asset('assets/admin/js/inventory/issues/get-issued_to.js') }}"></script>
                <script src="{{asset('assets/admin/js/inventory/issues/approve-issued.js') }}"></script>
  
  
@endpush
