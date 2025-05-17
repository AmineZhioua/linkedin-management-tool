@extends('layouts.app')

@section('content')
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="card-title text-danger">Account Suspended</h1>
                            <p class="card-text">
                                Your account has been suspended as of <strong>Friday, May 16, 2025, 12:25 AM CET</strong>. 
                                For more information or to appeal this suspension, please contact support at <a href="mailto:support@example.com">support@example.com</a>.
                            </p>
                            <p class="card-text">
                                We apologize for any inconvenience this may cause. Please review our terms of service for details.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- / Content -->
                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
    </div>
</div>
@endsection