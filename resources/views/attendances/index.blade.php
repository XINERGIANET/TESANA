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
					<th>Servicio</th>
					<th>Fecha</th>
					<th>Accion</th>
				</tr>
			</thead>
			<tbody>
				@if($attendances->count() > 0)
				@foreach($attendances as $attendance)
				@php($clientService = $attendance->client_service)
				<tr>
					<td>{{ optional($attendance->client)->name }}</td>
					<td>
						@if($clientService)
							<div>{{ optional($clientService->service)->name }}</div>
							<small class="text-muted">
								{{ optional($clientService->start_date)->format('d/m/Y') }} - {{ optional($clientService->end_date)->format('d/m/Y') }}
							</small>
						@else
							<span class="text-muted">Sin servicio</span>
						@endif
					</td>
					<td>{{ $attendance->date->format('d/m/Y H:i') }}</td>
					<td>
						<div class="d-flex gap-2">
							<button class="btn btn-icon btn-primary btn-edit" data-id="{{ $attendance->id }}" title="Editar">
								<i class="ti ti-pencil icon"></i>
							</button>
							<button class="btn btn-icon btn-danger btn-delete" data-id="{{ $attendance->id }}" title="Eliminar">
								<i class="ti ti-x icon"></i>
							</button>
						</div>
					</td>
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
	@if($attendances->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $attendances->withQueryString()->links() }}
	</div>
	@endif
</div>

<div class="modal modal-blur fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<form id="editForm" method="POST">
				<div class="modal-header">
					<h5 class="modal-title">Editar asistencia</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label required">Alumno</label>
						<select class="form-select" name="client_id" id="editClient" required></select>
					</div>
					<div class="mb-3">
						<label class="form-label required">Fecha</label>
						<input type="date" class="form-control" name="date" id="editDate" required>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" id="editId">
					<button type="button" class="btn me-auto" data-bs-dismiss="modal"><i class="ti ti-x icon"></i> Cerrar</button>
					<button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy icon"></i> Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
	var editClientSelect = new TomSelect('#editClient', {
		valueField: 'id',
		labelField: 'name',
		searchField: ['name', 'document'],
		load: function(query, callback){
			var url = '{{ route('clients.api') }}?q=' + encodeURIComponent(query);
			fetch(url)
				.then(response => response.json())
				.then(data => {
					callback(data.items);
				}).catch(()=> {
					callback([]);
				});
		},
		copyClassesToDropdown: false,
		dropdownClass: 'dropdown-menu ts-dropdown',
		optionClass: 'dropdown-item',
		render: {
			option: function(data, escape){
				return '<div>' + escape(data.document) + ' - ' + escape(data.name) + '</div>';
			},
			item: function(data, escape){
				var document = data.document ? data.document + ' - ' : '';
				return '<div>' + escape(document + data.name) + '</div>';
			},
			no_results: function(data, escape){
				return '<div class="no-results">No se encontraron resultados</div>';
			}
		}
	});

	$(document).on('click', '.btn-edit', function(){
		var id = $(this).data('id');

		$.ajax({
			url: '{{ route('attendances.index') }}' + '/' + id + '/edit',
			method: 'GET',
			success: function(data){
				editClientSelect.clearOptions();
				editClientSelect.addOption({
					id: data.client_id,
					document: '',
					name: data.client
				});
				editClientSelect.setValue(data.client_id);
				$('#editDate').val(data.date);
				$('#editId').val(data.id);
				$('#editModal').modal('show');
			},
			error: function(err){
				ToastError.fire({ text: 'Ocurrio un error' });
			}
		});
	});

	$('#editForm').submit(function(e){
		e.preventDefault();

		var id = $('#editId').val();

		$.ajax({
			url: '{{ route('attendances.index') }}' + '/' + id,
			method: 'PATCH',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
					$('#editModal').modal('hide');
					$('#editForm')[0].reset();
					editClientSelect.clear();

					location.reload();
				}else{
					ToastError.fire({ text: data.error ? data.error : 'Ocurrio un error' });
				}
			},
			error: function(err){
				ToastError.fire({ text: 'Ocurrio un error' });
			}
		});
	});

	$(document).on('click', '.btn-delete', function(){
		var id = $(this).data('id');

		ToastConfirm.fire({
			text: 'Estas seguro que deseas eliminar el registro?',
		}).then((result) => {
			if(result.isConfirmed){
				$.ajax({
					url: '{{ route('attendances.index') }}' + '/' + id,
					method: 'DELETE',
					success: function(data){
						location.reload();
					},
					error: function(err){
						ToastError.fire({ text: 'Ocurrio un error' });
					}
				});
			}
		});
	});
</script>
@endsection
