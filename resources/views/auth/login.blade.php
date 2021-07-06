
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Manshaa Real E-Commerce</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="../../../../global_assets/css/icons/icomoon/styles.min.css" rel="stylesheet" type="text/css">
    <link href="{{ 'css/bootstrap.min.css' }}" rel="stylesheet" type="text/css">
    <link href="{{ 'css/bootstrap_limitless.min.css' }}" rel="stylesheet" type="text/css">
    <link href="{{ 'css/layout.min.css' }}" rel="stylesheet" type="text/css">
    <link href="{{ 'css/components.min.css' }}" rel="stylesheet" type="text/css">
    <link href="{{ 'css/colors.min.css' }}" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="{{ asset('global_assets/js/main/jquery.min.js') }} "></script>
	<script src="{{ asset('global_assets/js/main/bootstrap.bundle.min.js') }} "></script>
	<script src="{{ asset('global_assets/js/plugins/loaders/blockui.min.js') }} "></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="{{ 'js/app.js' }}"></script>
	<!-- /theme JS files -->

</head>

<body>
	<!-- Page content -->
	<div class="page-content">

		<!-- Main content -->
		{{-- <div class="content-wrapper"> --}}

			<!-- Content area -->
			<div class="container d-flex justify-content-center align-items-center">

				<!-- Login form -->
				<form class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

					<div class="card mb-0">
						<div class="card-body">
							<div class="text-center mb-3">
								<i class="icon-reading icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>
								<h5 class="mb-0">Login to your account</h5>
								<span class="d-block text-muted">Enter your credentials below</span>
							</div>

                            <div class="form-group form-group-feedback form-group-feedback-left">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="e-mail" name="email"  value="{{ old('email') }}" required autocomplete="email" autofocus>
                                {{-- <input type="text" class="form-control" placeholder="Username"> --}}
                                <div class="form-control-feedback">
                                    <i class="icon-user text-muted"></i>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

							<div class="form-group form-group-feedback form-group-feedback-left">
								<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="password" name="password" required autocomplete="current-password">
								<div class="form-control-feedback">
									<i class="icon-lock2 text-muted"></i>
								</div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
							</div>

                            <div class="form-group row">
                                <div class="col-md-8 offset-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group row mb-0">
                                <div class="col-md-10 offset-md-1">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
					</div>
				</form>
				<!-- /login form -->

			</div>
			<!-- /content area -->

		{{-- </div> --}}
		<!-- /main content -->

	</div>
	<!-- /page content -->

</body>
</html>
