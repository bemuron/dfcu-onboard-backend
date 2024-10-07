<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
	<meta content="" name="description">
    <meta content="" name="keywords">
	
	<!-- Favicons -->
	<link href="assets/img/favicon.png" rel="icon">
	<link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
	<!--<link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"> -->
	<!--<link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet"> -->
	<link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
	
</head>
<body>
	<!-- ======= Header ======= -->
	<header id="header" class="fixed-top  header-transparent ">
	<div class="container d-flex align-items-center justify-content-between">

	  <div class="logo">
<!--		<h1><a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></h1>-->
		<!-- Uncomment below if you prefer to use an image logo -->
		 <a href="{{ url('/') }}"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>
	  </div>

	  <nav id="navbar" class="navbar">
		
	  </nav><!-- .navbar -->

	</div>
	</header><!-- End Header -->
	
    <!--<div id="app"> -->
        

        <main class="py-4">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0 mt-3">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
    <!--</div>-->
	<!-- ======= Footer ======= -->
  <footer id="footer">

<!--	<div class="footer-newsletter">
	  <div class="container">
		<div class="row justify-content-center">
		  <div class="col-lg-6">
			<h4>Join Our Newsletter</h4>
			<p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
			<form action="" method="post">
			  <input type="email" name="email"><input type="submit" value="Subscribe">
			</form>
		  </div>
		</div>
	  </div>
	</div>-->

	<div class="footer-top">
	  <div class="container">
		<div class="row">

		  <div class="col-lg-3 col-md-6 footer-contact">
			<h3>FixAppUg</h3>
			<p>
<!--			  A108 Adam Street <br>
			  New York, NY 535022<br>
			  United States <br><br>
			  <strong>Phone:</strong> +1 5589 55488 55<br>-->
			  <strong>Email:</strong> contact_us@fixappug.com<br>
			</p>
		  </div>

<!--		  <div class="col-lg-3 col-md-6 footer-links">
			<h4>Useful Links</h4>
			<ul>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">About us</a></li>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Services</a></li>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Terms of service</a></li>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Privacy policy</a></li>
			</ul>
		  </div>

		  <div class="col-lg-3 col-md-6 footer-links">
			<h4>Our Services</h4>
			<ul>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
			  <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
			</ul>
		  </div>

		  <div class="col-lg-3 col-md-6 footer-links">
			<h4>Our Social Networks</h4>
			<p>Cras fermentum odio eu feugiat lide par naso tierra videa magna derita valies</p>
			<div class="social-links mt-3">
			  <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
			  <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
			  <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
			  <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
			  <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
			</div>
		  </div>-->

		</div>
	  </div>
	</div>

	<div class="container py-4">
	  <div class="copyright">
		&copy; Copyright 2022<strong><span>EM-TECH Global (U) LTD</span></strong>. All Rights Reserved
	  </div>
	  <div class="credits">
		<!-- All the links in the footer should remain intact. -->
		<!-- You can delete the links only if you purchased the pro version. -->
		<!-- Licensing information: https://bootstrapmade.com/license/ -->
		<!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/free-bootstrap-app-landing-page-template/ -->
<!--		Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>-->
	  </div>
	</div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
	<script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <!--<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>-->
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
	<!-- Template Main JS File -->
	<script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
