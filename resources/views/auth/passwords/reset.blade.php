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
                            <h2 class="text-center text-primary">Password Reset</h2>
                        </div>

                        <form class="form" action="{{ route('password.update') }}" method="post">
                            {{csrf_field()}}
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="input-group custom mb-0">
                                <input value="{{old('email')}}" name="email" type="text" class="form-control form-control-lg" placeholder="Email Address">
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                                </div>
                            </div>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                        <span class="text-danger"><strong>{{ $errors->first('email') }}</strong></span>
                                    </span>
                            @endif

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
                                        <button type="submit" class="btn btn-primary btn-lg btn-block"> {{__('Reset Password')}} </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
