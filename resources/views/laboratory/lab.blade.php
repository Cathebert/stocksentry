 @extends('layouts.main')
@section('title','Laboratory')
@section('content')
<div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Laboratories</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{$laboratory->lab_name??"Lab"}}</li>
  </ol>
</nav>





@forelse ($lab_sections as $section)
{{ $section->section_name }}
    
@empty
    
@endforelse






<!-- /.container-fluid -->

            </div>
          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/laboratory/add_laboratory.js') }}"></script>
  
  
  
@endpush