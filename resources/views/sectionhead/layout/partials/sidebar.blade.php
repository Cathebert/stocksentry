        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('section.home')}}">
                 <img class="img-profile rounded-circle"
                                    src="{{ (!empty(auth()->user()->profile_img)) ? url('/public/uploads/profile_images/' . auth()->user()->profile_img) : asset('assets/img/undraw_profile.svg') }}" height="50px" width="50px">
              
                <div class="sidebar-brand-text mx-3">{{auth()->user()->name??"User"}} </div>
           
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="{{route('user.home')}}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
             main
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Stock Management</h6>
                        <a class="collapse-item" href="{{ route('section.new_receipt') }}">Receive Stock</a>
                        <a class="collapse-item" href="{{route('section.request')}}">Issue Stock</a>
                          
                           <hr>
                        <a class="collapse-item" href="{{route('section.bincard_inventory')}}" >Inventory</a>
                         
                           
                     
                    </div>
                </div>
            </li>
<!--items sidebar-->
 <li class="nav-item" hidden>
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseItems"
                    aria-expanded="true" aria-controls="collapseItems">
                    <i class="fas fa-fw fas fa-list"></i>
                    <span>Items </span>
                </a>
                <div id="collapseItems" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Items</h6>
                        
                         
                        <a class="collapse-item" href="{{route('item.show')}}">Items List</a>
                    
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item" hidden>
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fas fa-edit"></i>
                    <span>Adjustment</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Stock Adjustments</h6>
                        <a class="collapse-item" href="#">New Adjustment</a>
                        <a class="collapse-item" href="#">All Adjustments</a>
                         <a class="collapse-item" href="#"> Stock Disposal</a>
                       
                    
                    </div>
                </div>
            </li>

  
   
             <hr class="sidebar-divider d-none d-md-block">
            <!-- Nav Item - Charts -->
            <li class="nav-item" >
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Reports</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item" >
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Settings</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

         

        </ul>