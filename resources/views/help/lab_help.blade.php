@extends('provider.layout.main')

@section('title','User Help')
@section('content')

 <h1 style="text-align:center"><b> Quick Guide </b></h1>
    <p style="text-align:center">This is a quick guide to help you use the system effectively. Click on the link and follow the instructions given </p><br>

<div class="container">
	<div class="accordion" id="accordionExample">
  <div class="card">
    <div class="card-header" id="headingOne">
      <h5 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Receiving Items from  Supplier
        </button>
      </h5>
    </div>

    <div id="collapseOne" class="collapse collapsed" aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="card-body">
         <div class="row">
        <div class="col">
          <ol>
             <li>
               - Click <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu.
             </li>
             <li>
              - Click <strong>Receive Stock </strong>from the Sub-Menu

             </li>
             <li>
               -Enter Details like Supplier, GRN Number , details of from  person who checked  and who reviewed the items
             </li>
 
              <li>
               -Search the item to receive, once found  select it and click <button class="btn btn-success"> add</button>.A pop up will appear with details of the item
             </li>
             <li>
               -Enter details like batch number, expiry date e.t.c

             </li>
             
              <li>
               -Once all the Item details click <button class="btn btn-primary"> Add</button>.

             </li>
            <li>
               -The item will be added to the <strong>Item receive list</strong>.
             </li>
           <li>
               -You can clear the  list or remove the an individual item.
             </li>
               <li>
               -Repeat until the items on the Delivery Note have been completed then click <button class="btn btn-primary"> Save </button>.
             </li>

            
             <li>
               -Once saved it will show you a report of the items received.
             </li>
              <li>
              -Select All receipt to view the report of items which have been received or historical delivery notes

             </li>
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/receive_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>
       

        
      </div>
    </div>
  </div>
  
  <!---------------------------------------consolidating orders------------------------>
  <div class="card">
    <div class="card-header" id="headingTw">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTw" aria-expanded="false" aria-controls="collapseTwo">
          Consolidating orders
        </button>
      </h5>
    </div>
    <div id="collapseTw" class="collapse" aria-labelledby="headingTw" data-parent="#accordionExample">
      <div class="card-body">
        <div class="row">
        <div class="col">
          <ol>
            <li>
               - Click  <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu.
             </li>
             <li>
              - Click <strong>Issue Stock </strong>from the Sub-Menu

             </li>
             <li>
               -The select add items, the system brings items that are currently available in stores including the number of days remaining to expire.

             </li>

              <li>
               -Add the quantities to be ordered for all the items you need from stores and save
             </li>
             <li>
               -Approve the requisition under Pending approval if you have authority to approve or request the approver to approve. Once approved the list goes to stores for processing 


             </li>

               <li>
               -To view old requisition for that section, select view requisition list .
             </li>
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/request_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>
    
    
    
    <!---end body---->
    </div>
    </div>
  </div>
  <!------------------------------------------Requisition------------------------->
  <div class="card">
    <div class="card-header" id="headingThree">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
       Sharing items Between Laboratories
        </button>
      </h5>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               - Click  <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu.
             </li>
             <li>
              - Click <strong>Issue Stock </strong>from the Sub-Menu

             </li>
             <li>
               -The Click on <strong>Item Issue </strong>, Select the Laboratory which you want to send the items.

             </li>

              <li>
               -Select the person from the other Laboratory who will receive the items.

             </li>
             <li>
               -Click Add to add the items that you want to share with the other section from your section.


             </li>

               <li>
               -Click save
             </li>

              <li>
               -Once approved the receiving  lab will need to go <strong>Inventory</strong> then <strong>Issue stock</strong> in main menu, then <strong>Receive Issued</strong> tab  and click <strong>Accept</strong> for items to reflect in their lab and deduct from the where the items were shared from
             </li>
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/issue_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>
    
    




      <!---end card---->
    </div>
  </div>
</div>
<!------------------end Issuing---------->


<!------------conducting physical count------>
<div class="card">
    <div class="card-header" id="heading4">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
       Conducting Physical Count 
        </button>
      </h5>
    </div>
    <div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               - Click  <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu and Click <strong>Inventory </strong>.
             </li>
             <li>
              -Select the supervisor for that section and Employees involved in the physical count

             </li>
             <li>
               -Enter the physical balance for the items which you have counted, you can count different items and save individually or enter a couple and save as one by clicking <strong>Save all</strong>.


             </li>

              <li>
               -Once saved click on <strong>Stock History</strong> taken to approve or view the stock take that has happened.


             </li>
             <li>
               -Before approving view the if you have any variances between system and physical count.



             </li>

               <li>
               -Once approved the balances for those items will match the physical balances found..

             </li>

              
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/stock_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>
<!------end physical count ---->

<!------------conducting consumption------>
<div class="card">
    <div class="card-header" id="heading5">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
      Recording  Consumption Data 
        </button>
      </h5>
    </div>
    <div id="collapse5" class="collapse" aria-labelledby="heading5" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               - Click  <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu and Click <strong>Inventory </strong>.
             </li>
             <li>
              -Select <strong>Update Consumption</strong> data  tab 


             </li>
             <li>
               -Items consumption data can be captured by entering the amount consumed for running test and saving  or clicking <strong>update all</strong>.



             </li>

              <li>
               -Once consumption data has been recorded the system will lock for that day to avoid duplication and open the next day at 1PM


             </li>
             <li>
               -To view previous consumption data click on consumption history




             </li>

             
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/consumption_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>


<!----end consumption--->


<!------------conducting Adjustment----->
<div class="card">
    <div class="card-header" id="heading6">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
       Adjusting Stock/Inventory 
        </button>
      </h5>
    </div>
    <div id="collapse6" class="collapse" aria-labelledby="heading6" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               - Click  <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu and Click <strong>Inventory </strong>.
             </li>
             <li>
              -Select <strong>Update Stock Adjustment </strong> data  tab 


             </li>
             <li>
               -Search for the item to adjust.


             </li>

              <li>
               -Select if you want to add or subtract from the current available balance


             </li>
             <li>
               -Add notes on the cause of the adjustment.

             </li>

             <li>
               -Click view adjustment to approve or cancel the adjustment which has bee made

             </li>
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/adjustment_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>
<!-------end adjustment----------->



<!------------conducting Disposal----->
<div class="card">
    <div class="card-header" id="heading7">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
     Disposing of Stock Stock/Inventory 
        </button>
      </h5>
    </div>
    <div id="collapse7" class="collapse" aria-labelledby="heading7" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               - Click  <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu and Click <strong>Inventory </strong>.
             </li>
             <li>
              -Select <strong> Stock Disposal </strong>   tab 


             </li>
             <li>
               -Select items to dispose to add items you want to dispose of


             </li>

              <li>
               -Once the items have been added select the reason why you are disposing of the items, damaged, donated or expired.



             </li>
             <li>
               -Click Disposed items to view previous disposals and approve requested disposals, for those with authority to dispose it will approve automatically


             </li>

            
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/disposal_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>
<!-----------------------------end disposal-------------->

<!----------------------------Ordering Items for purchase----->

<div class="card">
    <div class="card-header" id="heading8">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse8" aria-expanded="false" aria-controls="collapse7">
   Order Item For Purchase
        </button>
      </h5>
    </div>
    <div id="collapse8" class="collapse" aria-labelledby="heading8" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               - Click  <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu and Click <strong>Inventory </strong>.
             </li>
             <li>
              -Select <strong> Stock Forecasting  </strong>   tab 


             </li>
             <li>
               -Select Lead time and  click load inventory


             </li>

              <li>
               -Select  items you want to order .



             </li>
             <li>
               -Click <strong>Run Forecast</strong> , You can adjust the orders by entering in the order box. Once satisfied you can click <strong> Place Order</strong>


             </li>
 <li>
               -This action requires approval 


             </li>
         <li>
              


             </li>    
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/order_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>

<!----------------------------End ordering------------------>
<!----Approve order for purchase-------------------->

<div class="card">
    <div class="card-header" id="heading9">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse9" aria-expanded="false" aria-controls="collapse9">
  Approve Orders
        </button>
      </h5>
    </div>
    <div id="collapse9" class="collapse" aria-labelledby="heading9" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               - Click  <i class="fas fa-boxes"></i> <strong>Inventory</strong> From Main Menu and Click <strong>Inventory </strong>.
             </li>
             <li>
              -Select <strong> Stock Forecasting  </strong>   tab 


             </li>
             <li>
               -Click on <button class="btn btn-info"><i class="fa fa-check"></i>Approve Orders</button>. A list of orders will appear. You can <button class="btn btn-info"><i class="fa fa-eye"></i>View</button> /<button class="btn btn-danger"> <i class="fa fa-trash"></i>Deny </button>/ <button class="btn btn-success"> <i class="fa fa-check"> </i> Approve</button> the order


             </li>

              <li>
               -Once approved the  <button class="btn btn-success"> <i class="fa fa-check"> </i> Approve</button> action changes to <span class='badge badge-success'><i class='fa fa-check'></i> Approved</span> and if is denied it changes to <span class='badge badge-danger'><i class='fa fa-trash'></i> Denied</span>



             </li>
             <li>
               -Once Approved the orders are ready for consolidation at the Store

             </li>
 
         <li>
              


             </li>    
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/approve_order_purchase.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>



<!------end Approve order for purchase------------->

<!------creating items----->

<div class="card">
    <div class="card-header" id="heading11">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse11" aria-expanded="false" aria-controls="collapse11">
 Creating,Editing and Deleting Items 
        </button>
      </h5>
    </div>
    <div id="collapse11" class="collapse" aria-labelledby="heading11" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               -<strong>On Creating</strong>: Click   <strong><i class="fas fa-fw fas fa-list"></i>
                    <span>Items </span> </strong> on your left and then click on <strong>Create Item </strong>.
             </li>
             <li>
              -Enter/select Item details in the provided form. Once done Click on <button class="btn btn-primary">Save</button> and your item will be saved.


             </li>
             <li>
               -<strong>On Editing</strong>:
            Click  <strong><i class="fas fa-fw fas fa-list"></i>
                    <span>Items </span></strong>on your left and then click on <strong>Items List </strong>. The list of items load. click on <a  style="color:#3B71CA" > <i class="fa fa-edit" title="Edit Item"></i></a>. After making changes click on <button class="btn btn-primary">Save Changes</button>
             </li>

             <li>
              - <strong>On Deleting </strong>: Follow steps as Editing but click on <a  style="color:red"> <i class="fa fa-trash"  title="Delete"></i></a>. After Accepting deletion the item will be removed
              </li>

             
 
         <li>
              


             </li>    
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/create_item.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>

<!---end creating items--->
<!-- creating users--->
<div class="card">
    <div class="card-header" id="heading13">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse13" aria-expanded="false" aria-controls="collapse13">
 Creating,Viewing ,Editing , Deleting  and Resetting Users 
        </button>
      </h5>
    </div>
    <div id="collapse13" class="collapse" aria-labelledby="heading13" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               -<strong>On Creating</strong>: Click   <strong> <i class="fas fa-fw fa-users"></i>
                    <span>Users</span> </strong>  on your left and then click on <strong>Add User </strong>.
             </li>
             <li>
              -Select Laboratory the user will be part of,enter other details(firstname, lastname ,email etc) Once done Click on <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button> and user is saved.Note that the username will be the one used to log in the system.


             </li>
             <li>
               -<strong>On Viewing and Editing</strong>:
            Click  <strong><i class="fas fa-fw fa-users"></i>
                    <span>Users</span></strong>on your left and then click on <strong>View Users </strong>. The list of users load. click on <button  class="btn btn-info" > <i class="fa fa-edit" title="Edit Item"></i> Edit</button>. After making changes click on <button class="btn btn-primary">Save Changes</button>
             </li>

             <li>
              - <strong>On Deleting </strong>: Follow steps as Editing but click on <button  class="btn btn-danger"> <i class="fa fa-trash"  title="Delete"></i> Delete</button>. After Accepting deletion the user will be removed from the list
              </li>

             <li>
              - <strong>On Resetting Passwords </strong>: Follow steps as Editing but click on <button  class="btn btn-warning"> <i class="fa fa-lock"  title="Delete"></i> Reset</button>. After Accepting reset confirmation the user will receive email with password reset instructions 
              </li>
             
 
         <li>
              


             </li>    
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/create_user.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>

<!----- end creating users---->

<!---create Supplier---->
<div class="card">
    <div class="card-header" id="heading14">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse14" aria-expanded="false" aria-controls="collapse14">
 Creating,Viewing ,Editing , Deleting  Suppliers
        </button>
      </h5>
    </div>
    <div id="collapse14" class="collapse" aria-labelledby="heading14" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               -<strong>On Creating</strong>: Click   <strong> <i class="fas fa-fw fas fa-handshake"></i>
                    <span>Supplier</span> </strong>  on your left and then click on <strong>Add Supplier </strong>.
             </li>
             <li>
              -Enter Supplier details(Supplier name, Contact Person ,email etc) Once done Click on <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button> 


             </li>
             <li>
               -<strong>On Viewing and Editing</strong>:
            Click  <strong><i class="fas fa-fw fas fa-handshake"></i>
                    <span>Supplier</span></strong>on your left and then click on <strong>All Suppliers </strong>. The list of Supplier load. click on <button  class="btn btn-info" > <i class="fa fa-edit" title="Edit Item"></i> Edit</button>. After making changes click on <button class="btn btn-primary">Save Changes</button>
             </li>

             <li>
              - <strong>On Deleting </strong>: Follow steps as Editing but click on <button  class="btn btn-warning"> <i class="fa fa-eye"  title="Delete"></i> Hide</button>. After Accepting deletion the user will be removed from the list
              </li>

            
             
  
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/create_supplier.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>



<!---end create supplier--->
<!----scheduling Reports----> 
<div class="card">
    <div class="card-header" id="heading16">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse16" aria-expanded="false" aria-controls="collapse16">
Scheduling Reports
        </button>
      </h5>
    </div>
    <div id="collapse16" class="collapse" aria-labelledby="heading16" data-parent="#accordionExample">
      <div class="card-body">
      
       <div class="row">
        <div class="col">
          <ol>
            <li>
               -<strong>On Creating</strong>: Click   <strong>  <i class="fas fa-fw fa-chart-area"></i>
                    <span>Reports</span> </strong> .Under <strong>Automation</strong> Click on View/Schedule Reports
             </li>
             <li>
              -Click on <button class="btn btn-primary"><i class="fa fa-plus"> </i> Schedule</button>. Select the frequency ,the report type,Report recipient and report format.  Once done Click on <button class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button> 


             </li>
             <li>
               -<strong>Deactivating Scheduled Reports </strong>:
          Click   <strong>  <i class="fas fa-fw fa-chart-area"></i>
                    <span>Reports</span> </strong>.Under <strong>Automation</strong> Click on View/Schedule Reports then click on <button class="btn btn-success"><i class="fa fa-pause"> </i>Deactivate</button>
                </li>

             <li>
              - <strong>On Deleting </strong>: Follow steps as Deactivating but click on <button  class="btn btn-danger"> <i class="fa fa-trash"  title="Delete"></i> </button>.The Scheduled report will be removed from the list
              </li>

            
             
  
        </ol>
    </div>
       <div class="col-6">
        <div>
            <video width="450" height="600"  controls>
              <source src="{{ url('/').'/public/upload/help/schedule_report.mp4' }}" type="video/mp4">
 
                Your browser does not support the video tag.
             </video>
       </div>
    </div>

  </div>

      <!---end card---->
    </div>
  </div>
</div>





<!----end Scheduling Report---->
</div>
 </div>
@endsection

@push('js')

@endpush