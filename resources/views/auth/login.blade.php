@extends('auth.master')

@section('content')
<div class="col-md-6">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">
                    <div class="text-center mb-3">
                        <img src="{{ asset('/images/logo-full.png') }}" alt="">
                    </div>
                    <h4 class="text-center mb-4">Sign in your account</h4>
                    @if( Session::has('msg') )
                        <p class="text-danger text-center">{{ Session::get('msg') }}</p>
                    @endif
                    <form action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="mb-1"><strong>Email</strong></label>
                            <input type="email" name="email" class="form-control">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="mb-1"><strong>Password</strong></label>
                            <input type="password" name="password" class="form-control">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </div>
                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                            <div class="form-group">
                               <div class="custom-control custom-checkbox ml-1">
                                    <input type="checkbox" class="custom-control-input" id="basic_checkbox_1">
                                    <label class="custom-control-label" for="basic_checkbox_1">Remember my preference</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <a href="{{ route('password.request') }}">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block">Sign Me In</button>
                        </div>
                    </form>
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection