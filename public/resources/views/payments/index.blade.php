@extends('template.app')

@section('title', 'Pagos')

@section('content')
<nav class="mb-2">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
		<li class="breadcrumb-item active">Pagos</li>
	</ol>
</nav>
<div class="card">
	<div class="card-header d-flex justify-content-between flex-column flex-sm-row gap-2">
		<div>
			<a href="{{ route('payments.excel', request()->all()) }}" class="btn btn-success"><i class="ti ti-download icon"></i> Excel</a>
		</div>
		<div class="text-center">
			<span class="d-block small">
				Total de pagos
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
				<div class="col-md-3">
					<div class="mb-3">
						<label class="form-label">Fecha inicial</label>
						<input type="date" class="form-control" name="start_date" value="{{ request()->start_date }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="mb-3">
						<label class="form-label">Fecha final</label>
						<input type="date" class="form-control" name="end_date" value="{{ request()->end_date }}">
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
					<th>Monto</th>
					<th>Método de pago</th>
					<th>Fecha de pago</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if($payments->count() > 0)
				@foreach($payments as $payment)
				<tr>
					<td>{{ optional(optional($payment->client_service)->client)->name }}</td>
					<td>{{ optional(optional($payment->client_service)->service)->name }}</td>
					<td>S/{{ $payment->amount }}</td>
					<td>{{ optional($payment->payment_method)->name }}</td>
					<td>{{ $payment->date->format('d/m/Y') }}</td>
					<td>
						<button class="btn btn-icon btn-danger btn-delete" data-id="{{ $payment->id }}">
							<i class="ti ti-x icon"></i>
						</button>
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
	@if($payments->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $payments->withQueryString()->links() }}
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
					url: '{{ route('payments.index') }}' + '/' + id,
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