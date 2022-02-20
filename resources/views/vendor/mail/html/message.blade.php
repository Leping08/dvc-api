@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<svg class="h-12" viewBox="0 0 280 245" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M192.058 179.423L229.891 245H269.904C277.602 245 282.413 236.667 278.564 230L220.633 129.66L192.058 179.423Z" class="text-lightBlue-700" fill="currentColor"></path><path d="M209.07 109.633L180.5 159.388L111.377 39.5761L131.34 5.00002C135.189 -1.66665 144.811 -1.66667 148.66 5L209.07 109.633Z" class="text-lightBlue-700" fill="currentColor"></path><path d="M154.402 245H206.801L180.54 199.481L154.402 245Z" class="text-lightBlue-500" fill="currentColor"></path><path d="M168.982 179.446L140 229.918L70.93 109.633L99.8281 59.5798L168.982 179.446Z" class="text-lightBlue-500" fill="currentColor"></path><path d="M59.3672 129.66L1.43596 230C-2.41304 236.667 2.39823 245 10.0962 245H125.598L59.3672 129.66Z" class="text-lightBlue-300" fill="currentColor"></path></svg>
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
