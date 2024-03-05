<div class="modal-header">
        <h5 class="modal-title">Contract Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <h5 class="card-title"> <strong>Contract</strong></h5>
        <form method="post" id="contract_form" action="{{route('contract.save')}}">
          @csrf
          <input type="hidden" class="form-control" id="sub_url" value="{{route('contract.save')}}">
    
 

  
 
<hr></br>
  <div class="row">

    <label for="exampleInputPassword1">Contract  Name: {{$contract->contract_name}}</label>
    
  

    <label for="exampleInputPassword1">Contract  Number :{{$contract->contract_number}}</label>
   
 
  
    <label for="exampleInputPassword1">Contract Description: {{$contract->contract_description}}</label>
   
  
    <label for="exampleInputPassword1">Contract Start Date:{{date('d,M Y',strtotime($contract->contract_startdate))}}</label>

    <label for="exampleInputPassword1">Subscription Type:{{$contract->name}}</label>
 <label for="exampleInputPassword1">Subscription Description:{{$contract->description}}</label>
  <label for="exampleInputPassword1">Supplier:{{$contract->supplier_name}}</label>
 
   



</div>
</form>
      </div>
    </div>
    <div class="card">
 <h5 class="card-header">
   <strong>Contract update History </strong>
</h5>
  <div class="card-body">
    <table class="table table-sm table-striped" id="contract_details"  width="100%">
<thead class="thead-light">
    <tr>
       
     <th width="5%"> #</th>
         <th width="10%">Start Date </th>
          <th width="10%">End Date</th>
          <th width="30%">Updated BY</th>
			</tr>
           
  </thead>
  <tbody>

    @php
        $i=1;
    @endphp
@foreach ($contract_details as $details )
  <tr>
    <td>{{$i}}</td>
    <td>  {{date('d,M Y',strtotime($details->start_date))  }} </td>
    <td>  {{ date('d,M Y',strtotime($details->end_date)) }} </td>
    <td>  {{ $details->name.' '.$details->last_name }} </td>

    @php
        $i++
    @endphp
    </tr>
@endforeach

</tbody>
</table>
  </div>
</div>
  </div>



</div>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $('#contract_details').DataTable();
        });
    </script>
@endpush