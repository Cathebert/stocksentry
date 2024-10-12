@extends('layouts.app')
@section('title','Select Account')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">{{ __('SELECT ACCOUNT') }}</div>

                <div class="card-body">
                    <!---content here---->
                    <div class="list-group">
 @if (!empty($accounts))
     @foreach ($accounts as $account)
     
         <a href="{{ route('selected',['id'=>$account->id]) }}" class="list-group-item list-group-item-action">{{$account->lab_name}}</a>
     @endforeach
 @endif
  
 
</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
