<div class="modal-header">
                                                    <h5 class="modal-title" >Edit User</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="{{ route('lab_user.update') }}" id="edit_user_form">
                                                         <input type="hidden" id="check" name="check" value="0"/>
                                                        <input type="hidden" id="id" name="id" value="{{$id}}"/>
                                                            <input type="hidden" id="update_url"  value="{{ route('lab_user.update') }}"/>

                                                        @csrf
                                                

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="first_name">First Name</label>
                                                                <input type="text" class="form-control" id="first_name" name="name" value="{{ $user->name }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="last_name">Last Name</label>
                                                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="email">Email</label>
                                                                <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="phone_number">Phone Number</label>
                                                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="occupation">Position</label>
                                                                <input type="text" class="form-control" id="user_position" name="occupation" value="{{ $user->occupation }}">
                                                            </div>

                                                            
                                                       
                                                            <div class="row">
                                                                <div class="col-md-4 form-group" id="labs">
                                                                    <label for="laboratory_id">Laboratory</label>
                                                               <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" onchange="getLabName(this.value)">
                                                                        @foreach ($labs as $lab)
                                                                            <option value="{{ $lab->id }}" {{ $user->laboratory_id == $lab->id ? 'selected' : '' }}>{{ $lab->lab_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 form-group" id="req"></div>
                                                            </div>
                                                     
 @if(auth()->user()->laboratory_id==0)
                                                            <div class="col-md-4 form-group"  id='not_coldroom'>
                                                                <label for="type">User Role</label>
                                                                <select class="form-control" id="user_role" name="user_type" style="width: 75%">
                                                                  
                                                                        <option value="1" {{ $user->authority == 1 ? 'selected' : '' }}>Admin</option>
                                                                        <option value="2" {{ $user->authority == 2 ? 'selected' : '' }}>Lab Manager</option>
                                                                        
                                                                        <option value="3" {{ $user->authority == 3 ? 'selected' : '' }}>User</option>
                                                                   
                                                                </select>
                                                            </div>
                                                        </div>
   


@elseif(auth()->user()->laboratory_id==99)
<div class="col-md-4 col-sm-12 col-xs-12 form-group" id='coldroom' >

    <label for="exampleInputPassword1">User Role</label>
    <select class="form-control" id="user_role" name="cold_type" style="width: 75%">

   
  
 
  <option value="4">ColdRoom Manager</option> 
    <option value="4">ColdRoom User</option> 
  </select>
  </div>
  
  
  @else
  
    <div class="col-md-4 col-sm-12 col-xs-12 form-group" id='my_lab' >

    <label for="exampleInputPassword1">User Role</label>
    <select class="form-control" id="user_role" name="lab_type" style="width: 75%">

   
  
 
  <option value="2">Lab Manager</option>
  <option value="3">Lab User</option>
   
  </select>
  </div>
  @endif
  
  


                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary" id="submit">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>