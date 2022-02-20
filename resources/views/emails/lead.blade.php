@component('mail::message')
# Website Lead

<b>Name:</b> {{$lead->name}}<br>
<b>Email:</b> {{$lead->email}}<br>
<b>Phone:</b> {{$lead->phone}}<br>
<b>Message:</b> {{$lead->message}}<br>
@endcomponent