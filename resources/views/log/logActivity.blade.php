 @extends('layouts.main')

@section('title','User Log')
@section('content')


<div class="container">
	<h1>Log Activity List</h1>
	<table class="table table-bordered  table-striped" id="logs" width="100%" cellspacing="0">
           <thead>
		<tr>
			<th>No</th>
			<th>Subject</th>
			<th >Description</th>
			<th>Performed By</th>
			<th width="100px">URL</th>
			<th>Method</th>
			<th>Ip</th>
			<th width="100px">User Agent</th>
			<th>Date</th>
			<th>Action</th>
		</tr>
</thead>
   <tbody>
		@if($logs->count())
			@foreach($logs as $key => $log)
			<tr>
				<td>{{ ++$key }}</td>
				<td>{{ $log->subject }}</td>
				<td>{{ $log->description }}</td>
				<td>{{ $log->performed_by }}</td>
				<td class="text-success" width="100px" >{{ $log->url }}</td>
				<td><label class="label label-info">{{ $log->method }}</label></td>
				<td class="text-warning">{{ $log->ip }}</td>
				<td class="text-danger">{{ $log->agent }}</td>
			
				<td>{{ $log->created_at }}</td>
				@if(auth()->user()->authority==1)
				<td><a type="button" class="btn btn-danger btn-sm"  id="{{$log->id}}" onclick="deleteLog(this.id)">Delete</a></td>
				@else
				<td></td>
				@endif
               
			</tr>
			@endforeach
		@endif
            </tbody>
	</table>
 </div>
 </section>
 <script type="text/javascript">
function deleteLog(id){
	let delete_url="{{route('delete-log')}}"

	$.ajaxSetup({
       headers: {
           "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
       },
   });
   $.ajax({
       method: "POST",
       dataType: "JSON",
       url: delete_url,
       data: {

           id:id,
       },

       success: function (data) {

           //console.log(data)

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
       error: function (jqXHR, textStatus, errorThrown) {
           // console.log(get_case_next_modal)
           alert("Error " + errorThrown);
       },
   });

}

</script>
           
 
           
        </div>
		  </div>
</div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            $('#logs').DataTable();
        });
    </script>
@endpush