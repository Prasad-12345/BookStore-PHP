@component('mail::message')
#Introduction

Hello
Your order is cancelled successfully

User = {{$user}}<br>
BookName = {{$bookName}}<br>
BookAuthor = {{$bookAuthor}}<br>
BookPrice = {{$bookPrice}}<br>
quantity = {{$quantity}}<br>
TotalPrice = {{$totalPrice}}<br>
OrderId = {{$orderId}}<br>
<br>
{{config('app.name')}}
@endcomponent