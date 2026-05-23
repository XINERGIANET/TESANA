@extends('template.app')

@section('title', 'Cobranzas')

@section('content')
<nav class="mb-2">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
		<li class="breadcrumb-item active">Cobranzas</li>
	</ol>
</nav>
<div class="card">
	<div class="card-header d-flex justify-content-between flex-column flex-sm-row gap-2">
		<div>
			<a href="{{ route('payments.index') }}" class="btn btn-primary">
				<i class="ti ti-cash icon"></i> Historial de pagos
			</a>
			<a href="{{ route('charges.excel', request()->all()) }}" class="btn btn-success"><i class="ti ti-download icon"></i> Excel</a>
		</div>
		<div class="text-center">
			<span class="d-block small">
				Total de deudas
			</span>
			<span class="fs-2 fw-bold text-primary">
				S/{{ number_format($total, 2) }}
			</span>
		</div>
	</div>
	<div class="card-body border-bottom">
		<form>
			<div class="row">
				<div class="col-lg-3">
					<div class="mb-3">
						<label class="form-label">DNI</label>
						<input type="text" class="form-control" name="document" value="{{ request()->document }}">
					</div>
				</div>
				<div class="col-lg-3">
					<div class="mb-3">
						<label class="form-label">Nombre</label>
						<input type="text" class="form-control" name="name" value="{{ request()->name }}">
					</div>
				</div>
				<div class="col-lg-3">
					<div class="mb-3">
						<label class="form-label">Estado</label>
						<select class="form-select" name="status">
							<option value="">Todos</option>
							<option value="paid" @if(request()->status == 'paid') selected @endif>Pagado</option>
							<option value="unpaid" @if(request()->status == 'unpaid') selected @endif>No pagado</option>
						</select>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="mb-3">
						<label class="form-label">Fecha de pago</label>
						<input type="date" class="form-control" name="payment_date" value="{{ request()->payment_date }}">
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
					<th>Nombre</th>
					<th>Servicio</th>
					<th>Fecha inicial</th>
					<th>Fecha final</th>
					<th>Total</th>
					<th>Deuda</th>
					<th>Pagado</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if($client_services->count() > 0)
				@foreach($client_services as $client_service)
				<tr>
					<td>{{ optional($client_service->client)->name }}</td>
					<td>{{ optional($client_service->service)->name }}</td>
					<td>{{ $client_service->start_date->format('d/m/Y') }}</td>
					<td>{{ $client_service->end_date->format('d/m/Y') }}</td>
					<td>S/{{ $client_service->total }}</td>
					<td>S/{{ $client_service->debt }}</td>
					<td>
						@if($client_service->paid)
						<span class="badge bg-success">Sí</span>
						@else
						<span class="badge bg-danger">No</span>
						@endif

					</td>
					<td>
						<div class="d-flex gap-2">
							@if(!$client_service->paid)
							<button class="btn btn-primary btn-icon btn-payment" data-id="{{ $client_service->id }}" data-amount="{{ $client_service->debt }}" title="Agregar pago">
								<i class="ti ti-cash icon"></i>
							</button>
							@endif
						</div>
					</td>		
				</tr>
				@endforeach
				@else
				<tr>
					<td colspan="8" align="center">No se han encontrado resultados</td>
				</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if($client_services->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $client_services->withQueryString()->links() }}
	</div>
	@endif
</div>

<div class="modal modal-blur fade" id="createPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<form id="storePaymentForm" method="POST">
				<div class="modal-header">
					<h5 class="modal-title">Agregar pago</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Monto</label>
								<input type="text" class="form-control" name="amount" id="amount">
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
					<input type="hidden" name="client_service_id" id="clientServiceId">
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

	$(document).on('click', '.btn-payment', function(){

		var id = $(this).data('id');
		var amount = $(this).data('amount');

		$('#clientServiceId').val(id);
		$('#amount').val(amount);



		$('#createPaymentModal').modal('show');

	});

	$('#storePaymentForm').submit(function(e){
		e.preventDefault();

		$.ajax({
			url: '{{ route('payments.store') }}',
			method: 'POST',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
					$('#createPaymentModal').modal('hide');
					$('#storePaymentForm')[0].reset();
					
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
</script>
@endsection