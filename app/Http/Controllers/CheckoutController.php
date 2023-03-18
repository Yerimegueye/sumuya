<?php

namespace App\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Session;
use App\Order;
 
class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.  
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        if(Cart::count()<=0){
            return redirect()->route('products.index');
        }
        
        Stripe::setApiKey('sk_test_51MgpQEJo4OhlZqGr8yYIMgVhcQfCHMKgmwUddYMKWUmbRNeNON0bTWAq3X8WVF07VOz8Vx4HNz42PXjtao1naFva00UgLTkIhi');
        $intent = paymentIntent::create([

                'amount' => round(Cart::total()),
                'currency' => 'eur',
            ]);
        
      $clientSecret = arr::get($intent, 'client_secret');
        return view('checkout.index', [
           'clientSecret' => $clientSecret
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {           

           
       $data = $request->json()->all();
       Log::info('Received payment data', ['data' => $data]);
       dd($data);
       
 
       $order = new Order();

       $order->payment_intent_id = $data['paymentIntent']['id'];
       $order->amount = $data['paymentIntent']['amount'];

       $order->payment_created_at = (new DataTime())->setTimestamp($data['paymentIntent']['created'])->format('Y-m-d H:i:s');

       $products = [];
       $i = 0;

         foreach(Cart::content() as $product){
            $products['product_'.$i][] = $product->model->title;
            $products['product_'.$i][] = $product->model->price;
            $products['product_'.$i][] = $product->qty;
            $i++;
         }
          
           $order->products = serialize($products);
           $order->user_id = 15;
           $order->save();

           if($data['paymentIntent']['status'] == 'succeeded'){

               Cart::destroy();
                   Session::flash('success', 'Votre commande a été bien traitée avec succes.');
               return response()->json(['success' => 'Payment Intent Succeeded']);
               
           } else{
             return response()->json(['error' => 'Payment Intent Not Succeeded']);
           }
        
            
    } 
     
    public function thankyou(){

        return Session::has('success') ? view('checkout.thankyou') : redirect()->route('products.index');
    }
    /*public function thankyou(){
         
          if(Session::has('success')) {

              return $this->thankyou();

          } else {
            
            redirect()->route('products.index');
        }
    }*/

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
