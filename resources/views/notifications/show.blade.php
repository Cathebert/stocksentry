
      <div class="modal-header">
        <h5 class="modal-title">Alert Center</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
         
        </button>
      </div>
      <div class="modal-body">
    

        <div class="card">
  <div class="card-header">
   Notification Details
  </div>
  <div class="card-body">
    <h5 class="card-title"></h5>
    <p class="card-text">Stock Taken on:{{ $notification->data['stock_take_date'] }} has {{ $notification->data['count'] }} items with discrepancies and needs review.</p>
@if(!empty($items)&&count($items)>0)
    <p class="card-text">Below is the list of items affected</p>

<ul class="list-group">
    @foreach($items as $item)
  <li class="list-group-item">{{$item->item_name}}  <span class="badge badge-secondary">{{ $item->remark }}</span></h1></li>
  @endforeach
 
</ul>
 @endif
  </div>
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
       
      </div>
    