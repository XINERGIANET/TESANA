@extends('template.app')

@section('title', 'Mis asistencias')

@section('content')
<nav class="mb-2">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ route('index_client') }}">Inicio</a></li>
		<li class="breadcrumb-item active">Mis asistencias</li>
	</ol>
</nav>

<div class="card">
	<!-- Formulario de búsqueda -->
	<div class="card-body border-bottom">
		<form>
			<div class="row">
				<div class="col-md-12">
					<label class="form-label">
						<input type="checkbox" name="old" value="1" @if(request()->old == 1) checked @endif> Mostrar asistencias anteriores
					</label>
				</div>
			</div>
			<button type="submit" class="btn btn-primary"><i class="ti ti-search icon"></i> Buscar</button>
		</form>
	</div>

	<!-- Tabla de asistencias -->
	<div class="table-responsive">
		<table class="table card-table table-vcenter">
			<thead>
				<tr>
					<th>Alumno</th>
					<th>Fecha</th>
				</tr>
			</thead>
			<tbody>
				@if($attendances->count() > 0)
				@foreach($attendances as $attendance)
				<tr>
					<td>{{ optional($attendance->client)->name }}</td>
					<td>{{ $attendance->date->format('d/m/Y') }}</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="2" align="center">No se han encontrado registros</td>
				</tr>
				@endif
			</tbody>
		</table>
	</div>

	<!-- Paginación -->
	@if($attendances->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $attendances->withQueryString()->links() }}
	</div>
	@endif
</div>
@endsection