     <div class="modal-header">
                                                    <h5 class="modal-title" >Edit User</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="{{ route('supplier.update') }}" id="edit_user_form">
                                                         <input type="hidden" id="check" name="check" value="0"/>
                                                        <input type="hidden" id="id" name="id" value="{{$id}}"/>
                                                            <input type="hidden" id="update_url"  value="{{ route('supplier.update') }}"/>

                                                        @csrf
                                                

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="first_name">Supplier Name</label>
                                                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ $supplier->supplier_name }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="last_name">Address </label>
                                                                <input type="text" class="form-control" id="address" name="address" value="{{ $supplier->address }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="email">Email</label>
                                                                <input type="text" class="form-control" id="email" name="email" value="{{ $supplier->email }}">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="phone_number">Phone Number</label>
                                                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{  $supplier->phone_number }}">
                                                            </div>
                                                             <div class="col-md-4 form-group">
                                                                <label for="phone_number">Contract Expiry</label>
                                                                <input type="date" class="form-control" id="expiry" name="expiry" value="{{ date('Y-m-d',strtotime($supplier->contract_expiry?? now()))}}">
                                                            </div>
                                                             </div>
                    
                                                <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary" id="submit">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>