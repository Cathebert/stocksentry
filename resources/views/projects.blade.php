 @extends('layouts.main')
@section('title','Projects')
@section('content')
 <div class="container-fluid">

 <!-- component -->
<div class="flex items-center justify-center w-screen min-h-screen p-10">
	<!-- Resice the preview panel to check the responsiveness -->

	<!-- Component Start -->
	<div class="row row-cols-4 g-3">
		@forelse ($projects as $project )
		<div class="col">
    <div class="card">
      <img src="https://mdbcdn.b-cdn.net/img/new/standard/city/041.webp" class="card-img-top"
        alt="Hollywood Sign on The Hill" />
      <div class="card-body">
        <h5 class="card-title"><strong> {{ ucwords($project->name) }}</strong></h5>

        <p class="card-text">
         {{ucfirst($project->description)}}
        </p>
		<p class="card-text">Location:
         {{$project->loacation??"Lilongwe"}}
        </p>
		<p class="card-text">Amount:
         {{$project->amount??"K0.00"}}
        </p>
		<p class="card-text">Payment Verified
        <i class="fa fa-check" style="color:green"  aria-hidden="true"></i>
        </p>
      </div>
    </div>
  </div>	
		@empty
			
		@endforelse
  

</div>
	<!-- Component End  -->

</div>
 <!-- /.container-fluid -->

  </div>
 @endsection