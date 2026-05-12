@extends('template.app')

@section('title', 'Sesiones pendientes')

@section('content')
<nav class="mb-2">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
		<li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Alumnos</a></li>
		<li class="breadcrumb-item active">Sesiones pendientes</li>
	</ol>
</nav>
<div class="card">
	<div class="card-header">
		<a href="{{ route('clients.sessionsExcel', request()->all()) }}" class="btn btn-success"><i class="ti ti-download icon"></i> Excel</a>
	</div>
	<div class="card-body">
		<form action="">
			<div class="row">
				<div class="col-md-3">
					<div class="mb-3">
						<label class="form-label">DNI</label>
						<input type="text" class="form-control" name="document" value="{{ request()->document }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="mb-3">
						<label class="form-label">Nombre</label>
						<input type="text" class="form-control" name="name" value="{{ request()->name }}">
					</div>
				</div>
			</div>
			<button type="submit" class="btn btn-primary">
				<i class="ti ti-filter icon"></i> Filtrar
			</button>
		</form>
	</div>
	<div class="table-responsive">
		<table class="table card-table table-vcenter">
			<thead>
				<tr>
					<th>DNI</th>
					<th>Nombre</th>
					<th>Servicio</th>
					<th>Sesiones pendientes</th>
				</tr>
			</thead>
			<tbody>
				@if($clients->count() > 0)
				@foreach($clients as $client)
				<tr>
					<td>{{ $client->document }}</td>
					<td>{{ $client->name }}</td>
					<td>{{ optional($client->service)->name }}</td>
					<td>{{ $client->sessions }}</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="4" align="center">No se han encontrado resultados</td>
				</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if($clients->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $clients->withQueryString()->links() }}
	</div>
	@endif
</div>
@endsection