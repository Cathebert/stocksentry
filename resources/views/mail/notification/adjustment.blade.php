<x-mail::message>
# Greetings
@component('mail::panel')
<strong>{{ $adjuster }} </strong> the following items in <strong> {{ $lab_name }} </strong> and are pending approval
@endcomponent

@component('mail::table')
 @php
        $x=1;
    @endphp
   | No | Item Name   | Initial Value   | Adjusted Value  |
   |:------:   |:------   |:-----------   |:--------: |
     @foreach ( $items as $item )
    | {{ $x }}| {{ $item->item_name }}| {{ $item->quantity }} |{{ $item->adjusted }} |
    @php
        $x++;
    @endphp
    @endforeach
@endcomponent

<br>
Please login to take appropriate action
@component('mail::button', ['url' =>$url ])
   Login
@endcomponent

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
