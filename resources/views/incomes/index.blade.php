@extends('template.app')

@section('title', 'Ingresos')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Ingresos</li>
  </ol>
</nav>

<div class="card">
	<div class="card-header d-flex justify-content-between flex-column flex-sm-row gap-2">
		<div>
			<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
				<i class="ti ti-plus icon"></i> Crear nuevo
			</button>
			<a href="{{ route('incomes.excel', request()->all()) }}" class="btn btn-success"><i class="ti ti-download icon"></i> Excel</a>
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
					<th>Descripción</th>
					<th>Monto</th>
					<th>Método de pago</th>
					<th>Fecha</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if($incomes->count() > 0)
				@foreach($incomes as $income)
				<tr>
					<td>{{ $income->description }}</td>
					<td>{{ $income->amount }}</td>
					<td>{{ optional($income->payment_method)->name }}</td>
					<td>{{ $income->date->format('d/m/Y') }}</td>
					<td>
						<div class="d-flex gap-2">
							<button class="btn btn-icon btn-primary btn-edit " data-id="{{ $income->id }}">
								<i class="ti ti-pencil icon"></i>
							</button>
							<button class="btn btn-icon btn-danger btn-delete" data-id="{{ $income->id }}">
								<i class="ti ti-x icon"></i>
							</button>
						</div>
					</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="5" align="center">No se han encontrado resultados</td>
				</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if($incomes->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $incomes->withQueryString()->links() }}
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
  			  			<label class="form-label required">Descripcion</label>
  			  			<input type="text" class="form-control" name="description" autocomplete="off">
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Monto</label>
  			  			<input type="text" class="form-control" name="amount" autocomplete="off">
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Método de pago</label>
  			  			<select class="form-select" name="payment_method_id">
  			  				<option value="">Seleccionar</option>
  			  				@foreach($payment_methods as $payment_method)
  			  				<option value="{{ $payment_method->id }}">{{ $payment_method->name }} - {{ $payment_method->type }}</option>
  			  				@endforeach
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
  			  			<label class="form-label required">Descripcion</label>
  			  			<input type="text" class="form-control" name="description" id="editDescription" autocomplete="off">
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Monto</label>
  			  			<input type="text" class="form-control" name="amount" id="editAmount" autocomplete="off">
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Método de pago</label>
  			  			<select class="form-select" name="payment_method_id" id="editPaymentMethodId">
  			  				<option value="">Seleccionar</option>
  			  				@foreach($payment_methods as $payment_method)
  			  				<option value="{{ $payment_method->id }}">{{ $payment_method->name }} - {{ $payment_method->type }}</option>
  			  				@endforeach
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
			url: '{{ route('incomes.store') }}',
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
			url: '{{ route('incomes.index') }}' + '/' + id + '/edit/',
			method: 'GET',
			success: function(data){
				$('#editDescription').val(data.description);
				$('#editAmount').val(data.amount);
				$('#editPaymentMethodId').val(data.payment_method_id);
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
			url: '{{ route('incomes.index') }}' + '/' + id + '',
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
					url: '{{ route('incomes.index') }}' + '/' + id,
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