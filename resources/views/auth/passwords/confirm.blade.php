@extends('layouts.auth')

@php($title = 'Reset')
@php($page = 'login-page')
@section('content')
    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-7">
                    <img src="{{asset('images/forgot-password.png')}}" alt="">
                </div>
                <div class="col-md-6 col-lg-5">
                    <div class="login-box bg-white box-shadow border-radius-10">
                        <div class="login-title">
                            <h2 class="text-center text-primary">{{ __('Confirm Password') }}</h2>
                            <p>{{ __('Please confirm your password before continuing.') }}</p>
                        </div>

                        <form class="form"  action="{{ route('password.confirm') }}" method="post">
                            {{csrf_field()}}

                            <div class="form-group">
                                <div class="input-group custom  mb-0">
                                    <input name="password" type="password" placeholder="Password" class="form-control">
                                    <div class="input-group-append custom">
                                        <small class="input-group-text"><i class="dw dw-padlock1"></i></small>
                                    </div>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <small class="text-danger"><strong>{{ $errors->first('password') }}</strong></small>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="input-group custom  mb-0">
                                    <input name="password_confirmation" type="password" placeholder="Confirm Password" class="form-control">
                                    <div class="input-group-append custom">
                                        <small class="input-group-text"><i class="dw dw-padlock1"></i></small>
                                    </div>
                                </div>
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <small class="text-danger"><strong>{{ $errors->first('password_confirmation') }}</strong></small>
                                </span>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="input-group mb-0">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">  {{ __('Confirm Password') }} </button>
                                    </div>
                                    <hr>
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
