@extends('layouts.main')
@section('title','Scheduled Backups')
@section('content')
<div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('reports.show')}}">System</a></li>
    <li class="breadcrumb-item active" aria-current="page">System Backup</li>
  </ol>
</nav>


<div class="row" >
  <div class="col-sm-12">
     
   
      <div class="dropdow" style="text-align:right" >
      
       <button  class="btn btn-secondary" role="button" id="run_backup" >
  <i class="fa fa-download"> Run Backup</i>
</button>
&nbsp;&nbsp;

   <button  class="btn btn-success" role="button" id="restore_backup" onclick="uploadDocument()" >
  <i class="fa fa-upload"> Restore Backup</i>
</button>
&nbsp;&nbsp;
  <button  class="btn btn-primary" role="button" data-toggle="modal" data-target="#exampleModal" >
  <i class="fa fa-plus"> Schedule Backup</i>
</button>

</div>
   <div class="dropdow" style="text-align:left" >
       <button  class="btn btn-success" role="button" id="backup_list" >
  <i class="fa fa-hdd-o"> Backup List</i>
</button>


</div>
<br>
      <div class="card">
          
        
       
   <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">System Backup</h5>
   
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button><br>
         
      </div>
       
      <div class="modal-body">
 <form method="post"  action="{{route('backup.schedule')}}" id="schedule_report_form" name="schedule_report_form">
  @csrf
  <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label text-danger">Frequency *</label>
    <div class="col-sm-6">
     <select class="form-control" name="frequency" id="frequency" onchange="changeText(this.value)">
  <option value="1"selected>Daily</option>
  <option value="2" >Weekly</option>
  <option value="3">Monthly</option>
 
</select>
    </div>
    <label for="staticEmail" class="col-sm-3 col-form-label" id="report_infor">This will back up the database and a copy will be sent on a daily basis to the specified recipient</label>
  </div>

    <h6 > <strong > Recipient Details </strong> </6>
    <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label text-danger">Emails *</label>
    <div class="col-sm-9">
     <select class="form-control" id="email_list" style="width: 50%" name="back_up_receiver" required>
                                   
                                    @foreach($users as $user)
                                
                                        <option value="{{$user->id}}">{{$user->email}}</option>
                                    @endforeach
                                </select>
    </div>
  </div>
  <script type="text/javascript">
  $(document).ready(function() {
    $('#email_list').select2({
 placeholder: 'Select  ',
      allowClear: true,
  dropdownParent: $('#exampleModal .modal-content')
   
    });
    
    
   
});



</script>
    <div class="form-group row" hidden>
    <label for="staticEmail" class="col-sm-3 col-form-label">Attach as</label>
    <div class="col-sm-6">
     <div class="form-check">
  <input class="form-check-input" type="radio" name="attach_as" id="exampleRadios1" value="1" checked>
  <label class="form-check-label" for="exampleRadios1">
    PDF
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" name="attach_as" id="exampleRadios2" value="2">
  <label class="form-check-label" for="exampleRadios2">
    Excel
  </label>
</div>

    </div>
  </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" >Save changes</button>
      </div>
    </div>
  </div>
</div>

</form>




<!-------end modal-------------------->

       
		<div class="card-body">
            <input type="hidden" id="scheduled_backup_url" value="{{route('scheduled_backups.load')}}"/>
       <input type="hidden" id="deactivate" value="{{route('backup.deactivate')}}"/>
         <input type="hidden" id="delete" value="{{route('backup.delete')}}"/>
          <input type="hidden" id="view_backups" value="{{route('backups.showmodal')}}"/>
          <input type="hidden" id="generate_backup" value="{{route('backups.generate')}}"/>
          <input type="hidden" id="download_url" value="{{route('backups.download')}}"/>
          <input type="hidden" id="delete_url" value="{{route('delete.backedup')}}"/>
          <input type="hidden" id="delete_all" value="{{route('backups.clear_all')}}"/>
            <input type="hidden" id="upload_backup" value="{{route('backups.restore')}}"/>
          
            <h5 class="card-title"><strong>Scheduled Backups</strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="back_ups"  width="100%">
<thead class="thead-light">
    <tr>
         <th width="5%">#</th>
        
           <th width="20%">Frequency</th>
            <th width="20%">Scheduled By</th>
        
          
          <th width="20%">Recipient</th>
         
            <th width="10%">Status</th>
              <th width="10%">Action</th>
            <th width="10%">Delete</th>
			</tr>
           
  </thead>
</table>
      </div>
       </div>
   
</div>
</div>
</div>
         <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="receive_item">

    </div>
    </div>
    </div>      

            </div>
          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/inventory/reports/backup.js') }}"></script>
  
  
  
@endpush