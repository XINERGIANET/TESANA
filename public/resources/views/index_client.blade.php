@extends('template.app')

@section('title', 'Inicio')

@section('content')

@if(session()->has('client'))
<div>
	<p>Bienvenido a Tesana Pilates Studio.</p>

	<h3>Tu información</h3>

	@if($client)
	<ul>
		<li>DNI: {{ $client->document }}</li>
		<li>Nombre: {{ $client->name }}</li>
	</ul>
	@endif

	<h3>Servicios</h3>

	@if(count($client_services) > 0)
	<table class="table">
		<thead>
			<tr>
				<th>Servicio</th>
				<th>Fecha inicial</th>
				<th>Fecha final</th>
			</tr>
		</thead>
		<tbody>
			@foreach($client_services as $client_service)
			<tr>
				<td>{{ optional($client_service->service)->name }}</td>
				<td>{{ optional($client_service->start_date)->format('d/m/Y') }}</td>
				<td>{{ optional($client_service->end_date)->format('d/m/Y') }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	@endif

	<h3>Datos antropométricos</h3>

	@if(count($data_1) > 0)
	
	<h5>PORCENTAJE DE GRASA</h5>
	
	<div class="table-responsive mb-4">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>FECHA</th>
					<th>TRICIPITAL (TRICEPS)</th>
					<th>SUB ESCAPULAR</th>
					<th>SUPRA ILIACO</th>
					<th>ABDOMINAL</th>
					<th>CUADRICIPITAL (MUSLO)</th>
					<th>PERONEAL (PANTORRILLA)</th>
					<th>RESULTADO</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data_1 as $data)
				<tr>
					<td>{{ optional($data->date)->format('d/m/Y H:i') }}</td>
					<td>{{ $data->triceps }}</td>
					<td>{{ $data->subescapular }}</td>
					<td>{{ $data->suprailiaco }}</td>
					<td>{{ $data->abdominal }}</td>
					<td>{{ $data->muslo }}</td>
					<td>{{ $data->pantorrilla }}</td>
					<td>{{ $data->calc_1() }} - {{ $data->result_1() }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@endif


	@if(count($data_2) > 0)

	<h5>INDICE CINTURA CADERA (ICC)</h5>
	
	<div class="table-responsive mb-4">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>FECHA</th>
					<th>ABDOMINAL</th>
					<th>CADERA</th>
					<th>RESULTADO</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data_2 as $data)
				<tr>
					<td>{{ optional($data->date)->format('d/m/Y H:i') }}</td>
					<td>{{ $data->abdominal_2 }}</td>
					<td>{{ $data->cadera }}</td>
					<td>{{ $data->calc_2() }} - {{ $data->result_2() }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@endif


	@if(count($data_3) > 0)

	<h5>INDICE MASA CORPORAL (IMC)</h5>
	
	<div class="table-responsive mb-4">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>FECHA</th>
					<th>PESO</th>
					<th>TALLA</th>
					<th>RESULTADO</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data_3 as $data)
				<tr>
					<td>{{ optional($data->date)->format('d/m/Y H:i') }}</td>
					<td>{{ $data->peso }}</td>
					<td>{{ $data->talla }}</td>
					<td>{{ $data->calc_3() }} - {{ $data->result_3() }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@endif
</div>
@endif

@if(!session()->has('client'))
<div class="modal modal-blur fade" id="clientModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<form id="clientForm" method="POST">
				<div class="modal-header">
					<h5 class="modal-title">Alumno</h5>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label required">Ingresa tu DNI</label>
						<input type="text" class="form-control" name="document" autocomplete="off">
					</div>
					<button class="btn btn-primary">Buscar</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif

@endsection


@section('scripts')
<script>
	$(document).ready(function(){
		if($('#clientModal').length){
			$('#clientModal').modal('show');
		}
	});

	$('#clientForm').submit(function(e){
		e.preventDefault();

		$.ajax({
			url: '{{ route('clients.login') }}',
			method: 'POST',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
					$('#clientModal').modal('hide');
					$('#clientForm')[0].reset();
					
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