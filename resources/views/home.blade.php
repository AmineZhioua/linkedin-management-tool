@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <!-- Alert When the User buys a Subscription using the (Popup.vue) Component -->
                    @if(session('success_payment'))
                        <popup path="/build/assets/popups/like-popup.svg">
                            <p>
                                {{ session('success_payment') }}
                            </p>
                        </popup>
                    @endif

                    <!-- Alert When the User successfully links his LinkedIn Account (Popup.vue) Component -->
                    @if(session('linkedin_success'))
                        <popup path="/build/assets/popups/like-popup.svg">
                            <p>
                                {{ session('linkedin_success') }}
                            </p>
                        </popup>
                    @endif
                    
                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
