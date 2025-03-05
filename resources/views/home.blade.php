@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <!-- Alert When the User buys a Subscription -->
                    @if(session('success_payment'))
                        <div class="alert alert-success">
                            {{ session('success_payment') }}
                        </div>
                    @endif
                    
                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <popup path="/build/assets/popups/like-popup.svg">
                        <p>
                            Your Subscription is now Activated!
                        </p>
                    </popup>

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
