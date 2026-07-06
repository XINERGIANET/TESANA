@extends('template.app')

@section('title', 'Ventas')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Ventas</li>
  </ol>
</nav>

<div class="card">
	<div class="card-header d-flex justify-content-between flex-column flex-sm-row gap-2">
		<div>
			<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
				<i class="ti ti-plus icon"></i> Crear nuevo
			</button>
		</div>
		<div>
		</div>
	</div>
	<div class="card-body border-bottom">
		<form>
			<div class="row">
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
			</div>
			<button type="submit" class="btn btn-primary"><i class="ti ti-filter icon"></i> Filtrar</button>
			<a href="{{ route('sales.index') }}" class="btn btn-danger"><i class="ti ti-trash icon"></i> Limpiar</a>
		</form>
	</div>
	<div class="table-responsive">
		<table class="table card-table table-vcenter">
			<thead>
				<tr>
					<th>Alumno</th>
					<th>Producto</th>
					<th>Cantidad</th>
					<th>Total</th>
					<th>Método de pago</th>
					<th>Fecha</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if($sales->count() > 0)
				@foreach($sales as $sale)
				<tr>
					<td>{{ optional($sale->client)->name }} </td>
					<td>{{ optional($sale->product)->name }} </td>
					<td>{{ $sale->quantity }}</td>
					<td>{{ $sale->total }}</td>
					<td>{{ optional($sale->payment_method)->name }} </td>
					<td>{{ $sale->date->format('d/m/Y') }}</td>
					<td>
						<div class="d-flex gap-2">
							<button class="btn btn-icon btn-primary btn-edit " data-id="{{ $sale->id }}">
								<i class="ti ti-pencil icon"></i>
							</button>
							<button class="btn btn-icon btn-danger btn-delete" data-id="{{ $sale->id }}">
								<i class="ti ti-x icon"></i>
							</button>
						</div>
					</td>
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="6" align="center">No se han encontrado resultados</td>
				</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if($sales->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $sales->withQueryString()->links() }}
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
  			  			<label class="form-label">Alumno</label>
  			  			<select class="form-select ts" name="client_id">
  			  				<option value="">Seleccionar</option>
  			  				@foreach($clients as $client)
  			  				<option value="{{ $client->id }}">{{ $client->name }}</option>
  			  				@endforeach
  			  			</select>
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Producto</label>
  			  			<select class="form-select ts" name="product_id">
  			  				<option value="">Seleccionar</option>
  			  				@foreach($products as $product)
  			  				<option value="{{ $product->id }}">{{ $product->name }}</option>
  			  				@endforeach
  			  			</select>
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Cantidad</label>
  			  			<input type="text" class="form-control" name="quantity" autocomplete="off">
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Método de pago</label>
  			  			<select class="form-select" name="payment_method_id">
  			  				<option value="">Seleccionar</option>
  			  				@foreach($payment_methods as $payment_method)
  			  				<option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
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
  			  			<label class="form-label">Alumno</label>
  			  			<select class="form-select" name="client_id" id="editClientId">
  			  				<option value="">Seleccionar</option>
  			  				@foreach($clients as $client)
  			  				<option value="{{ $client->id }}">{{ $client->name }}</option>
  			  				@endforeach
  			  			</select>
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Producto</label>
  			  			<select class="form-select" name="product_id" id="editProductId">
  			  				<option value="">Seleccionar</option>
  			  				@foreach($products as $product)
  			  				<option value="{{ $product->id }}">{{ $product->name }}</option>
  			  				@endforeach
  			  			</select>
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Cantidad</label>
  			  			<input type="text" class="form-control" name="quantity" id="editQuantity" autocomplete="off">
  			  		</div>
  			  	</div>
  			  	<div class="col-lg-6">
  			  		<div class="mb-3">
  			  			<label class="form-label required">Método de pago</label>
  			  			<select class="form-select" name="payment_method_id" id="editPaymentMethodId">
  			  				<option value="">Seleccionar</option>
  			  				@foreach($payment_methods as $payment_method)
  			  				<option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
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

	document.querySelectorAll('.ts').forEach((el)=>{
		let settings = {
			copyClassesToDropdown: false,
			dropdownClass: 'dropdown-menu ts-dropdown',
    	optionClass:'dropdown-item',
			render: {
				no_results: function(data, escape){
					return '<div class="no-results">No se encontraron resultados</div>'
				}
			}
		};
	 	new TomSelect(el,settings);
	});

	$('#storeForm').submit(function(e){
		e.preventDefault();

		$.ajax({
			url: '{{ route('sales.store') }}',
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
			url: '{{ route('sales.index') }}' + '/' + id + '/edit/',
			method: 'GET',
			success: function(data){
				$('#editClientId').val(data.client_id);
				$('#editProductId').val(data.product_id);
				$('#editQuantity').val(data.quantity);
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
			url: '{{ route('sales.index') }}' + '/' + id + '',
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
					url: '{{ route('sales.index') }}' + '/' + id,
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