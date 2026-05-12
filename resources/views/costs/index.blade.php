@extends('template.app')

@section('title', 'Costos')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Costos</li>
  </ol>
</nav>

<div class="card">
	<div class="card-header d-flex justify-content-between flex-column flex-sm-row gap-2">
		<div>
			<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
				<i class="ti ti-plus icon"></i> Crear nuevo
			</button>
			<a href="{{ route('costs.excel', request()->all()) }}" class="btn btn-success"><i class="ti ti-download icon"></i> Excel</a>
		</div>
		<div>
			<form>
				<div class="input-group">
					<input type="text" class="form-control" placeholder="Buscar" name="search" value="{{ request()->search }}">
					<button type="submit" class="btn btn btn-icon">
						<i class="ti ti-search icon"></i>
					</button>
				</div>
			</form>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table card-table table-vcenter">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Tipo</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if($costs->count() > 0)
				@foreach($costs as $cost)
				<tr>
					<td>{{ $cost->name }}</td>
					<td>{{ $cost->type }}</td>
					<td>
						<div class="d-flex gap-2">
							<button class="btn btn-icon btn-primary btn-edit " data-id="{{ $cost->id }}">
								<i class="ti ti-pencil icon"></i>
							</button>
							<button class="btn btn-icon btn-danger btn-delete" data-id="{{ $cost->id }}">
								<i class="ti ti-x icon"></i>
							</button>
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
	@if($costs->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $costs->withQueryString()->links() }}
	</div>
	@endif
</div>

<div class="modal modal-blur fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
  	<div class="modal-content">
  		<form id="storeForm" method="POST">
  			<div class="modal-header">
  			  <h5 class="modal-title">Crear nuevo</h5>
  			  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  			</div>
  			<div class="modal-body">
  			  <div class="row">
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Nombre</label>
  			  			<input type="text" class="form-control" name="name" autocomplete="off">
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Tipo</label>
  			  			<select class="form-select" name="type">
  			  				<option value="">Seleccionar</option>
  			  				<option value="Fijo">Fijo</option>
  			  				<option value="Variable">Variable</option>
  			  			</select>
  			  		</div>
  			  	</div>
  			  </div>
  			</div>
  			<div class="modal-footer">
  			  <button type="button" class="btn me-auto" data-bs-dismiss="modal"><i class="ti ti-x icon"></i> Cerrar</button>
  			  <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy icon"></i> Guardar</button>
  			</div>
  		</form>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
  	<div class="modal-content">
  		<form id="editForm" method="POST">
  			<div class="modal-header">
  			  <h5 class="modal-title">Editar</h5>
  			  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  			</div>
  			<div class="modal-body">
  			  <div class="row">
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Nombre</label>
  			  			<input type="text" class="form-control" name="name" id="editName" autocomplete="off">
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Tipo</label>
  			  			<select class="form-select" name="type" id="editType">
  			  				<option value="">Seleccionar</option>
  			  				<option value="Fijo">Fijo</option>
  			  				<option value="Variable">Variable</option>
  			  			</select>
  			  		</div>
  			  	</div>
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

	$('#storeForm').submit(function(e){
		e.preventDefault();

		$.ajax({
			url: '{{ route('costs.store') }}',
			method: 'POST',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
					$('#createModal').modal('hide');
					$('#storeForm')[0].reset();
					
					location.reload();
				}else{
					ToastError.fire({ text: data.error ? data.error : 'Ocurrió un error' });
				}
			},
			error: function(err){
				ToastError.fire({ text: 'Ocurrió un error' });
			}
		});

	});

	$(document).on('click', '.btn-edit', function(){

		var id = $(this).data('id');

		$.ajax({
			url: '{{ route('costs.index') }}' + '/' + id + '/edit/',
			method: 'GET',
			success: function(data){
				$('#editName').val(data.name);
				$('#editType').val(data.type);
				$('#editId').val(data.id);
				$('#editModal').modal('show');
			},
			error: function(err){
				ToastError.fire({ text: 'Ocurrió un error' });
			}
		});

	});

	$('#editForm').submit(function(e){
		e.preventDefault();

		var id = $('#editId').val();

		$.ajax({
			url: '{{ route('costs.index') }}' + '/' + id + '',
			method: 'PATCH',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
					$('#editModal').modal('hide');
					$('#editForm')[0].reset();
					location.reload();
				}else{
					ToastError.fire({ text: data.error ? data.error : 'Ocurrió un error' });
				}
			},
			error: function(err){
				ToastError.fire({ text: 'Ocurrió un error' });
			}
		});

	});

	$(document).on('click', '.btn-delete', function(){

		var id = $(this).data('id');

		ToastConfirm.fire({
			text: '¿Estás seguro que deseas eliminar el registro?',
		}).then((result) => {
			
			if(result.isConfirmed){

				$.ajax({
					url: '{{ route('costs.index') }}' + '/' + id,
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