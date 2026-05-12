<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>Tesana Pilates Studio</title>
	<link rel="stylesheet" href="{{ asset('assets/css/tabler.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/tabler-vendors.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/tabler-icons.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/sweetalert2-theme-material-ui.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
	<link rel="icon" href="{{ asset('assets/images/xinergia-icon.svg') }}">
	@yield('styles')
</head>
<body>
	<div class="page">
		<aside class="navbar navbar-vertical navbar-expand-lg bg-black" data-bs-theme="dark">
			<div class="container-fluid">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<h1 class="navbar-brand navbar-brand-autodark">
					<a href="{{ url('/') }}">
						TESANA
					</a>
				</h1>
				<div class="navbar-nav flex-row d-lg-none">
					<div class="nav-item dropdown">
						<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
							<span class="avatar avatar-sm text-white">
								{{-- <i class="ti ti-user icon"></i> --}}
								<img src="{{ asset('assets/images/logo.jpg?v=1') }}">
							</span>
							<div class="d-none d-xl-block ps-2">
								<div>{{ auth()->user()->name }}</div>
								<div class="mt-1 small text-muted">{{ auth()->user()->user }}</div>
							</div>
						</a>
						<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
							@if(auth()->user()->isRole('admin'))
							<a href="{{ route('settings.index') }}" class="dropdown-item">Ajustes</a>
							@elseif(auth()->user()->isRole('client'))
							<a href="{{ route('settings_client.index') }}" class="dropdown-item">Ajustes</a>
							@endif
							<form method="POST" action="{{ route('auth.logout') }}">
								@csrf
								<a href="javascript:void(0)" class="dropdown-item" onclick="this.closest('form').submit()">Cerrar sesión</a>
							</form>
						</div>
					</div>
				</div>
				<div class="collapse navbar-collapse" id="sidebar-menu">
					<ul class="navbar-nav">

						@if(auth()->user()->isRole('counter'))

						<li class="nav-item">
							<a class="nav-link" href="{{ route('sales.index') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-cash icon"></i>
								</span>
								<span class="nav-link-title">
									Ventas
								</span>
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="{{ route('attendances.index') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-clock icon"></i>
								</span>
								<span class="nav-link-title">
									Asistencias
								</span>
							</a>
						</li>
						
						@elseif(auth()->user()->isRole('sales'))
						
						<li class="nav-item">
							<a class="nav-link" href="{{ route('sales.index') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-cash icon"></i>
								</span>
								<span class="nav-link-title">
									Ventas
								</span>
							</a>
						</li>

						@elseif(auth()->user()->isRole('client'))
						
						<li class="nav-item">
							<a class="nav-link" href="{{ route('index_client') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-home icon"></i>
								</span>
								<span class="nav-link-title">
									Inicio
								</span>
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="{{ route('attendances.search') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-clock icon"></i>
								</span>
								<span class="nav-link-title">
									Mis asistencias
								</span>
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="{{ route('reservations.create') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-plus icon"></i>
								</span>
								<span class="nav-link-title">
									Crear reserva
								</span>
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="{{ route('reservations.search') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-calendar icon"></i>
								</span>
								<span class="nav-link-title">
									Mis reservas
								</span>
							</a>
						</li>
						
						@elseif(auth()->user()->isRole('trainer'))

						<!-- Roles de entrenador -->

						@elseif(auth()->user()->isRole('admin'))

						<li class="nav-item">
							<a class="nav-link" href="{{ url('/') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-home icon"></i>
								</span>
								<span class="nav-link-title">
									Inicio
								</span>
							</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#navbar-register" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="true" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-edit icon"></i>
								</span>
								<span class="nav-link-title">
									Registro
								</span>
							</a>
							<div class="dropdown-menu">
								<div class="dropdown-menu-columns">
									<div class="dropdown-menu-column">
										<a class="dropdown-item" href="{{ route('services.index') }}">
											Servicios
										</a>
										<a class="dropdown-item" href="{{ route('clients.index') }}">
											Alumnos
										</a>
										<a class="dropdown-item" href="{{ route('products.index') }}">
											Productos
										</a>
										<a class="dropdown-item" href="{{ route('trainers.index') }}">
											Entrenadores
										</a>
										<a class="dropdown-item" href="{{ route('costs.index') }}">
											Costos
										</a>
										<a class="dropdown-item" href="{{ route('payment_methods.index') }}">
											Métodos de pago
										</a>
									</div>
								</div>
							</div>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('attendances.index') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-check icon"></i>
								</span>
								<span class="nav-link-title">
									Asistencias
								</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('reservations.index') }}" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-calendar icon"></i>
								</span>
								<span class="nav-link-title">
									Reservas
								</span>
							</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#navbar-register" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="true" >
								<span class="nav-link-icon d-md-none d-lg-inline-block">
									<i class="ti ti-cash icon"></i>
								</span>
								<span class="nav-link-title">
									Finanzas
								</span>
							</a>
							<div class="dropdown-menu">
								<div class="dropdown-menu-columns">
									<div class="dropdown-menu-column">
										<a class="dropdown-item" href="{{ route('incomes.index') }}">
											Ingresos
										</a>
										<a class="dropdown-item" href="{{ route('expenses.index') }}">
											Egresos
										</a>
										<a class="dropdown-item" href="{{ route('charges.index') }}">
											Cobranzas
										</a>
										<a class="dropdown-item" href="{{ route('cash-flow') }}">
											Flujo de caja
										</a>
										<a class="dropdown-item" href="{{ route('bank-payments.index') }}">
											Cronograma de pagos
										</a>
									</div>
								</div>
							</div>
						</li>
						@endif
						
					</ul>
				</div>
			</div>
		</aside>
		<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none" >
			<div class="container-xl">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="navbar-nav flex-row order-md-last">
					<div class="d-none d-md-flex">
						<a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Activar modo oscuro" data-bs-toggle="tooltip"
						data-bs-placement="bottom">
						<i class="ti ti-moon icon"></i>
					</a>
					<a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Activar modo claro" data-bs-toggle="tooltip"
					data-bs-placement="bottom">
					<i class="ti ti-sun icon"></i>
				</a>
			</div>
			<div class="nav-item dropdown">
				<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
					<span class="avatar avatar-sm">
						{{-- <i class="ti ti-user icon"></i> --}}
						<img src="{{ asset('assets/images/logo.jpg?v=1') }}">
					</span>
					<div class="d-none d-xl-block ps-2">
						<div>{{ auth()->user()->name }}</div>
						<div class="mt-1 small text-muted">{{ auth()->user()->user }}</div>
					</div>
				</a>
				<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
					@if(auth()->user()->isRole('admin'))
					<a href="{{ route('settings.index') }}" class="dropdown-item">Ajustes</a>
					@elseif(auth()->user()->isRole('client'))
					<a href="{{ route('settings_client.index') }}" class="dropdown-item">Ajustes</a>
					@endif

					<form method="POST" action="{{ route('auth.logout') }}">
						@csrf
						<a href="javascript:void(0)" class="dropdown-item" onclick="this.closest('form').submit()">Cerrar sesión</a>
					</form>
				</div>
			</div>
		</div>
		<div class="collapse navbar-collapse" id="navbar-menu">
		    {{-- <div>
		      <form action="" method="get" autocomplete="off" novalidate>
		        <div class="input-icon">
		          <span class="input-icon-addon">
		            <i class="ti ti-search icon"></i>
		          </span>
		          <input type="text" value="" class="form-control" placeholder="Buscar" aria-label="Search in website">
		        </div>
		      </form>
		  </div> --}}
		</div>
	</div>
</header>
<div class="page-wrapper">
	<!-- Page header -->
	<div class="page-header d-print-none">
		<div class="container-xl">
			<div class="row g-2 align-items-center">
				<div class="col">
					<h2 class="page-title">
						@yield('title')
					</h2>
				</div>
			</div>
		</div>
	</div>
	<!-- Page body -->
	<div class="page-body">
		<div class="container-xl">
			@if(session()->has('message'))
			<div class="alert alert-success">
				{{ session()->get('message') }}
			</div>
			@endif
			@if(session()->has('error'))
			<div class="alert alert-danger">
				{{ session()->get('error') }}
			</div>
			@endif
			@yield('content')
		</div>
	</div>
	<footer class="footer footer-transparent d-print-none">
		<div class="container-xl">
			<div class="row text-center align-items-center flex-row-reverse">
				<div class="col-lg-auto ms-lg-auto">
				</div>
				<div class="col-12 col-lg-auto mt-3 mt-lg-0">
					<ul class="list-inline list-inline-dots mb-0">
						<li class="list-inline-item">
							Copyright &copy; 2023
							<a href="/" class="link-secondary">Xinergia</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</footer>
</div>
</div>

<script src="{{ asset('assets/js/tabler.min.js') }}"></script>
<script src="{{ asset('assets/js/theme.min.js') }}"></script>
<script src="{{ asset('assets/js/tom-select.base.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	const ToastError = Swal.mixin({
		title: 'Error',
		icon: 'error',
		toast: true,
		position: 'bottom-end',
		timer: 2000,
		timerProgressBar: true
	});

	const ToastMessage = Swal.mixin({
		title: 'Mensaje',
		icon: 'success',
		toast: true,
		position: 'bottom-end',
		timer: 2000,
		timerProgressBar: true
	});

	const ToastConfirm = Swal.mixin({
		icon: 'question',
		showDenyButton: true,
		confirmButtonText: 'Aceptar',
		denyButtonText: 'Cancelar',
		toast: true,
		position: 'bottom-end'
	});
</script>
@yield('scripts')
</body>
</html>