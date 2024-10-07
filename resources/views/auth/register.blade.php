<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Hire people to complete your tasks, earn money completing tasks</title>
    <meta name="author" content="FixAppUg, EM-TECH Global">
    <meta content="Hire skilled people to help you complete your tasks or earn money completing tasks using FixApp" name="description">
    <meta content="job, gig, work, flexible work, part time work, household chores, errands, trustworhty, carpentar services, Handyman, Tasks, Uganda, On demand, Services" name="keywords">
	
	
	<!-- Favicons -->
	<link href="assets/img/favicon.png" rel="icon">
	<link href="assets/img/favicon.png" rel="apple-touch-icon">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/jquery/jquery-ui/jquery-ui.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
	
</head>

<body style="background-image: url(./assets/img/register-bg.jpg); background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: cover;">
	<!-- ======= Header ======= -->
	<header id="header" class="fixed-top  header-transparent ">
	<div class="container d-flex align-items-center justify-content-between">

	  <div class="logo">
		<!-- <h1><a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></h1> -->
		<!-- Uncomment below if you prefer to use an image logo -->
		<h1> <a href="{{ url('/') }}"><img src="assets/img/logo.png" alt="" class="img-fluid"></a> <a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></h1>
	  </div>
      </div>
	</header><!-- End Header -->

    <div class="container">
        <div class="row d-flex flex-column min-vh-100 justify-content-center align-items-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <a href="{{ url('/') }}"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3 row">
                                <label for="name" class="col-md-4 col-form-label text-end">
                                    {{ __('Full Name') }} :
                                </label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="dob" class="col-md-4 col-form-label text-end">
                                    {{ __('Date Of Birth') }} :
                                </label>

                                <div class="row col-md-6">
                                    <input id="dob" type="text" data-format="YYYY-MM-DD" data-template="D MMM YYYY" class="form-control @error('dob') is-invalid @enderror"
                                        name="dob" value="{{ old('dob') }}" required autocomplete="dob" autofocus>

                                    @error('dob')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="email" class="col-md-4 col-form-label text-end">
                                    {{ __('E-Mail Address') }} :
                                </label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="email" class="col-md-4 col-form-label text-end">
                                    {{ __('Phone Number') }} :
                                </label>

                                <div class="col-md-6">
                                    <input id="phoneNumber" type="text" class="form-control @error('phoneNumber') is-invalid @enderror"
                                        name="phoneNumber" value="{{ old('phoneNumber') }}" required autocomplete="phoneNumber" autofocus>

                                    @error('phoneNumber')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="password" class="col-md-4 col-form-label text-end">
                                    {{ __('Password') }} :
                                </label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-end">
                                    {{ __('Confirm Password') }} :
                                </label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-6 offset-md-4">
                                    <a class="btn btn-link" href="{{ route('login') }}">
                                        {{ __('Already Registered! Login') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
<!-- <script src="{{ asset('assets/jquery/jquery-ui/external/jquery/jquery.js') }}"></script> -->
<!-- <script src="{{ asset('assets/jquery/jquery-ui/jquery-ui.js') }}"></script> -->
<script src="{{ asset('assets/js/moment.js') }}"></script>
<script src="{{ asset('assets/combodate-1.0.7/combodate.js') }}"></script>
<script>
    $('#dob').combodate({
        value: new Date(),
        minYear: 1950,
        maxYear: moment().format('YYYY'),
        //customClass: 'form-control'
    }); 
</script>
</body>
    </html>
