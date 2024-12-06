 @extends('layouts.main')

@section('title','User Log')
@section('content')


<div class="container">
	<h1>USER LOG ERRORS RESOLUTION LIST</h1>
	<table class="table table-bordered  table-striped" id="logs" width="100%" cellspacing="0">
           <thead>
		<tr>
			<th>No</th>
			<th>Email</th>
			<th >Message</th>
			<th>Is Resolved</th>
            <th>Logged Date</th>
              <th>Resolved Date</th>
			<th>Action</th>
		</tr>
</thead>
   <tbody>
		@if($logs->count())
			@foreach($logs as $key => $log)
			<tr>
				<td>{{ ++$key }}</td>
				<td>{{ $log->sender_email }}</td>
				<td>{{ $log->message }}</td>

                @if($log->is_resolved=='no')
                <td ><span class="badge badge-danger">{{ $log->is_resolved }}</span></td>
                @else
                <td><span class="badge badge-success">{{ $log->is_resolved }}</span></td>
                @endif


				<td>{{ $log->created_at }}</td>
                <td>{{ $log->updated_at }}</td>
                  @if($log->is_resolved=='no')
				<td><a type="button" class="btn btn-success btn-sm"  id="{{$log->id}}" onclick="markLog(this.id)">Mark Resolved</a></td>
                @else
                 <td><span class="badge badge-success"><i class="fa fa-check"></i> Resolved</span></td>
@endif


			</tr>
			@endforeach
		@endif
            </tbody>
	</table>
 </div>
 </section>
 <script type="text/javascript">
function markLog(id){
	let mark_log_url="{{route('mark-resolved-log')}}"

	$.ajaxSetup({
       headers: {
           "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
       },
   });
   $.ajax({
       method: "POST",
       dataType: "JSON",
       url: mark_log_url,
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
