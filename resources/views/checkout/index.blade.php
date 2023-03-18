@extends('layouts.master')
 
@section('extra-meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
  
@section('extra_script')
    <script src="https://js.stripe.com/v3/"></script>
@endsection 


@section('content')
    <div class="col-md-12">
        <h1>page de paiement</h1>
        <div class="row">
            <div class="col-md-6">
                <form action="{{route('checkout.store')}}" method="post" class="my-4" id="payment-form">
                    @csrf
                    <div id="card-element">
                        
                    </div>
                    <div id="card-errors" role="alert"></div>
                    <button class="btn btn-success mt-4" id="submit">Passer au paiement({{ getPrice(Cart::total())}})</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        var stripe = Stripe("pk_test_51MgpQEJo4OhlZqGrV1CXICGHxvgitH3bEu8BjC0OGZPcaKj3JEpgzlX5pgKg2MuumDADlZ9hNmrl1H2fRaBr3UOY005HEBMc8J");
        var elements = stripe.elements();
        var style = {
                        base: {
                            color: "#505652",
                            fontFamily: 'Helvetica Neue',
                            fontSmoothing: "antialiased",
                            fontSize: "20px",
                            "::placeholder": {
                                color: "#CFD7E0"
                            }
                            },
                        invalid: {
                            color: "#fa755a",
                            iconColor: "#fa755a"
                        }
                       };
                
        var card = elements.create("card", {style: style});
                card.mount("#card-element");
                card.addEventListener('change', ({error})=>{
                    const displayError = document.getElementById("card-errors");
                    if(error){
                        displayError.classList.add('alert', 'alert-warning');
                        displayError.textContent = error.message;
                    } else {
                        displayError.classList.remove('alert', 'alert-warning');
                        displayError.textContent = '';
                    }
                });
                var submitButton = document.getElementById('submit');

                submitButton.addEventListener('click', function(ev){
                    ev.preventDefault();
                    submitButton.disabled = true;
                    stripe.confirmCardPayment("{{$clientSecret}}", {
                        payment_method:{
                            card: card
                        }
                    }).then(function(result){
                        if(result.error){
                            submitButton.disabled = false;
                            console.log(result.error.message);
                        } else{
                            if(result.paymentIntent.status == 'succeeded'){
                                var paymentIntent = result.paymentIntent;

                                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                                var form = document.getElementById('payment-form');
                                var url = form.action;
                                var redirect='/merci';
                                fetch(
                                        url,
                                        {
                                            headers: {
                                                "Content-Type": "application/json",
                                                "Accept": "application/json, text-plain, */*",
                                                "X-Requested-With": "XMLHttpsRequest",
                                                 "X-CSRF-TOKEN": token
                                            },
                                            method: 'post',
                                            body: JSON.stringify({
                                                paymentIntent: paymentIntent
                                            })
                                        }).then((data)=>{
                                        console.log(data)
                                      // window.location.href = redirect;
                                    }).catch((error)=>{
                                        console.log(error)
                                    })    
                            }
                        }
                    });
                });
    </script>
@endsection
