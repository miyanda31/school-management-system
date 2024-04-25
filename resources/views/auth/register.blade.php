@extends('layouts.auth')

@php($title = 'Register')
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
                            <h2 class="text-center text-primary">Registration</h2>
                        </div>

                        <form class="form" action="{{route('register')}}" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <div class="input-group custom mb-0">
                                    <input value="{{old('username')}}" name="username" type="text" class="form-control form-control-lg" placeholder="Username">
                                    <div class="input-group-append custom">
                                        <small class="input-group-text"><i class="icon-copy dw dw-user1"></i></small>
                                    </div>
                                </div>
                                @if ($errors->has('username'))
                                    <div class="help-block">
                                        <small class="text-danger"><strong>{{ $errors->first('username') }}</strong></small>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                            <div class="input-group custom  mb-0">
                                <input value="{{old('email')}}" name="email" type="text" class="form-control form-control-lg" placeholder="Email Address (optional)">
                                <div class="input-group-append custom">
                                    <small class="input-group-text"><i class="icon-copy dw dw-email"></i></small>
                                </div>
                            </div>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                        <small class="text-danger"><strong>{{ $errors->first('email') }}</strong></small>
                                    </span>
                            @endif
                            </div>
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
                            <div class="form-group">
                            <div class="input-group custom  mb-0">
                                <input name="code" type="text" placeholder="Registration Code" class="form-control">
                                <div class="input-group-append custom">
                                    <small class="input-group-text"><i class="dw dw-key"></i></small>
                                </div>
                            </div>
                            @if ($errors->has('code'))
                                <div class="help-block">
                                        <small class="text-danger"><strong>{{ $errors->first('code') }}</strong></small>
                                </div>
                            @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="input-group mb-0">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
                                    </div>
                                    <hr>
                                    <div class="input-group mb-0">
                                        <a class="btn btn-outline-primary btn-lg btn-block" href="{{ route('login') }}">Login</a>
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
