<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>
<body>
    <h1>PAYMENT FORM</h1>
    <form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
        @csrf
        <div class="row" style="margin-bottom:40px;">
            <div class="col-md-8 col-md-offset-2">
                <p>
                    
                </p>
                <label for="email">EMAIL</label>
                <input type="hidden" name="email" value="olamoyeguntimothy@gmail.com"> {{-- required --}}
                <label for="orderId">ORDERID</label>
                <input type="hidden" name="orderID" value="347">
                <label for="">amount</label>
                <input type="" name="amount" value=""> {{-- required in kobo --}}
                <label for="">quantity</label>
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="currency" value="NGN">
                <input type="hidden" name="metadata" value="{{ json_encode($array = ['key_name' => 'value','item_name'=> 'rice']) }}" > {{-- For other necessary things you want to add to your payload. it is optional though --}}
                <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
                
               
            
    
    
                <p>
                    <button class="btn btn-success btn-lg btn-block" type="submit" value="Pay Now!">
                        <i class="fa fa-plus-circle fa-lg"></i> Pay Now!
                    </button>
                </p>
            </div>
        </div>
    </form>
</body>
</html>