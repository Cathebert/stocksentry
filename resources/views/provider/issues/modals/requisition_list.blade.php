 <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Requisition</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_request" onclick="RequestClose()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <input type="hidden" id="view_issue_siv" value="{{route('issue.view')}}"/>
          <input type="hidden" id="save_request_item" value="{{route('requests.approve')}}"/>
          <input type="hidden" id="load_requests" value="{{route('requests.show_pending')}}"/>
          <input type="hidden" id="void_request" value="{{route('requests.void')}}"/>
          <input type="hidden" id="requisition-list" value="{{route('requests.load-list')}}" />
           <div class="card">
  <div class="card-header text-danger" >
    <strong>Requisition List</strong>
  </div>
  <div class="card-body">
    <h5 class="card-title">List</h5>
     <div class="table-responsive">
        <table class="table table-sm table-striped" id="request_list"  width="100%">
<thead class="thead-light">
    <tr>
       
       <th width="5%"></th>
        <th width="5%"> SR # </th>
        <th width="20%"> Lab/Section </th>
        <th width="20%"> Requested By </th>
        <th width="20%"> Requested Date </th>
        <th >Status </th>
            <th >View Details</th> 
  </thead>
</table>
      </div>
  </div>
   </div>
   </div>
 <script type="text/javascript">
 function RequestClose(){

   $('#inforg').modal('hide');
}
     //  t
 </script>
 <script type="text/javascript">
    LoadRequestList()
    
    </script>