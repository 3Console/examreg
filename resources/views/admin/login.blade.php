<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
      <title>Login</title>
      <!-- Scripts -->
  </head>
  <body>

    <div id="page_login" class="clearfix">

      <div class="header-content">
        <div class="login-logo text-center">
          <a href="/" class="box_logo">
            <img src="/images/default-logo.png" alt="">  ExamReg
          </a>
        </div>
      </div>

      <div class="content_login clearfix">
        <div class="login-heading text-center">
          <div class="guest-page-form-header text-center">
              <span class="title-l"></span>
              <h3 class="title-amount"> Login Admin </h3>
              <span class="title-l"></span>
          </div>
        </div>
        <div class="clearifx"></div>
        <form class="login-form" role="form" method="POST" action="{{ url('/admin/login') }}">
          @csrf
          <div class="clearfix form-group-login{{ $errors->has('email') ? ' has-error' : '' }} ">
            <!-- <div class="input-title">Email</div> -->
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" maxlength = "190" >
            @if ($errors->has('email'))
            <span class="message help-block">
              {{ $errors->first('email') }}
            </span>
            @endif
          </div>
          
          <div class="clearfix form-group-login{{ $errors->has('password') ? ' has-error' : '' }}">
            <!-- <div class="input-title">Password</div> -->
            <input type="password" class="form-control" name="password" placeholder="Password" maxlength = "190" >
            @if ($errors->has('password'))
            <span class="message help-block">
              {{ $errors->first('password') }}
            </span>
            @endif
          </div>
          <button type="submit" class="btn-login">
            <i class="fa fa-btn fa-sign-in"></i>Login
          </button>
        </form>

      </div>
    </div>

    <style lang="scss" scoped>
      button {
        outline: 0px auto -webkit-focus-ring-color;
        outline-offset: 0px;
        outline: none !important;
      }

      button:focus, 
      button.focus, 
      button:active:focus, 
      button:active.focus, 
      button.active:focus, 
      button.active.focus {
        outline: 0px auto -webkit-focus-ring-color;
        outline-offset: 0px;
        outline: none !important;
      }
      select,
      input {
        outline-offset: 0px !important;
        outline: -webkit-focus-ring-color auto 0px !important;
        outline: none !important;
      }
      #page_login input:-webkit-autofill,
      #page_login input:-webkit-autofill:hover, 
      #page_login input:-webkit-autofill:focus,
      #page_login textarea:-webkit-autofill,
      #page_login textarea:-webkit-autofill:hover,
      #page_login textarea:-webkit-autofill:focus,
      #page_login select:-webkit-autofill,
      #page_login select:-webkit-autofill:hover,
      #page_login select:-webkit-autofill:focus {
        -webkit-text-fill-color: #e4e4e4;
        -webkit-box-shadow: 0 0 0px 1000px #000 inset;
        transition: background-color 5000s ease-in-out 0s;
      }
      #page_login {
        background: #0e454c;
        min-height: 100vh;
        font-family: "Montserrat", sans-serif;
      }
      .content_login {
        width: 360px;
        max-width: 100%;
        margin: 90px auto; 
      }
      .header-content {
        width: 100%;
        height: 100px;
        background: #12575f;
        margin: 0 auto;
        position: relative;
      }
      .login-logo img{
        width: 60px;
        height: 60px;
        margin: 20px 0px 20px 0px;
      }
      a.box_logo, a.box_logo:hover {
        color: #ffffff;
        font-size: 20px;
        text-transform: uppercase;
        font-family: "OpenSans-Bold", sans-serif;
        letter-spacing: 5px;
        text-decoration: none !important;
      }
      .login-heading {
        height: 50px;
        font-weight: bold;
        color: #2dac91;
        text-align: -webkit-center;
        margin: 5px 0px 20px 0px;
      }
      .guest-page-form-header {
        width: 360px;
        display: flex;
      }
      .title-l {
        margin: 13px 0px 15px 0px;
        height: 1px;
        width: 114px;
        border-radius: 5px;
        background-color: #12575f;
      }
      .title-amount {
        width: 140px;
        margin: 0 0 20px;
        font-size: 20px;
        font-weight: bold;
      }
      .form-group-login {
        display: block;
        width: 100%;
        margin-bottom: 15px;
      }
      .form-group-login .input-title {
        color: #666666;
        font-size: 14px;
        margin-bottom: 5px;
        float: left;
        display: block;
        width: 100%;
      }
      .form-group-login .form-control {
        width: 100%;
        height: 50px;
        background: transparent !important;
        font-size: 14px;
        padding: 10px 2px;
        float: left;
        display: block;
        color: #e4e4e4 !important;
        outline: 0;
        border-width: 0px 0px 1px;
        border-radius: 0px;
        border-color: #536f71;
        -webkit-box-shadow: 0 0 0px 1000px #0e454c inset !important;
      }
      .btn-login {
        background: #2dac91;
        width: 360px;
        height: 50px;
        line-height: 20px;
        border-radius: 5px;
        text-align: center;
        text-transform: uppercase;
        color: white;
        border: 1px solid #2dac91;
        font-size: 18px;
        margin-top: 5px;
        padding: 14px 15px;
      }
      .has-error .message {
        display: contents;
        font-size: 90%;
        color: red;
      }
    </style>
  </body>
</html>
