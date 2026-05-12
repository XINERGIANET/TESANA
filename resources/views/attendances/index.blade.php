@extends('template.app')

@section('title', 'Asistencias')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Asistencias</li>
  </ol>
</nav>

<div class="card">
	<div class="card-header d-flex justify-content-between">
		<div>
			<a href="{{ route('attendances.create') }}" class="btn btn-primary"><i class="ti ti-plus icon"></i> Crear nuevo</a>
			<a href="{{ route('attendances.excel', request()->all()) }}" class="btn btn-success"><i class="ti ti-download icon"></i> Excel</a>
		</div>
		<div>
			<a href="{{ route('clients.sessions') }}" class="btn btn-primary">Sesiones pendientes</a>
		</div>
	</div>
	<div class="card-body border-bottom">
		<form>
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
				<div class="col-md-3">
					<div class="mb-3">
						<label class="form-label">Fecha inicio</label>
						<input type="date" class="form-control" name="start_date" value="{{ request()->start_date }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="mb-3">
						<label class="form-label">Fecha fin</label>
						<input type="date" class="form-control" name="end_date" value="{{ request()->end_date }}">
					</div>
				</div>
				<div class="col-md-12">
					<label class="form-label"><input type="checkbox" name="old" value="1" @if(request()->old == 1) checked @endif> Asistencias anteriores</label>
				</div>
			</div>
			<button type="submit" class="btn btn-primary"><i class="ti ti-search icon"></i> Buscar</button>
		</form>
	</div>
	<div class="table-responsive">
		<table class="table card-table table-vcenter">
			<thead>
				<tr>
					<th>Alumno</th>
					<th>Fecha</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if($attendances->count() > 0)
				@foreach($attendances as $attendance)
				<tr>
					<td>{{ optional($attendance->client)->name }}</td>
					<td>{{ $attendance->date->format('d/m/Y H:i') }}</td>
					<td>
						<div class="d-flex gap-2">
							<div class="d-flex gap-2">
								{{-- <button class="btn btn-icon btn-danger btn-delete" data-id="{{ $attendance->id }}">
									<i class="ti ti-x icon"></i>
								</button> --}}
							</div>
						</div>
					</td>		
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="3" align="center">No se han encontrado resultados</td>
				</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if($attendances->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $attendances->withQueryString()->links() }}
	</div>
	@endif
</div>
@endsection

@section('scripts')
<script>

	$(document).on('click', '.btn-delete', function(){

		var id = $(this).data('id');

		ToastConfirm.fire({
			text: '¿Estás seguro que deseas eliminar el registro?',
		}).then((result) => {
			
			if(result.isConfirmed){

				$.ajax({
					url: '{{ route('attendances.index') }}' + '/' + id,
					method: 'DELETE',
					success: function(data){
						location.reload();
					},
					error: function(err){
						ToastError.fire({ text: 'Ocurrió un error' });
					}
				});
				
			}

		});
	});
</script>
@endsection