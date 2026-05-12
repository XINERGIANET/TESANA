@extends('template.app')

@section('title', 'Crear reserva')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('index_client') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Crear reserva</li>
  </ol>
</nav>

<div class="col-lg-9">
	<div class="card">
		<div class="card-body">
			<form id="storeForm">
				<div class="row">
					<div class="col-lg-4">
						<div class="mb-3">
							<label class="form-label">Alumno</label>
							<select type="text" class="form-select" name="client_id">
								@if($client)
								<option value="{{ $client->id }}">{{ $client->name }}</option>
								@endif
							</select>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="mb-3">
							<label class="form-label">Fecha</label>
							<input type="date" class="form-control" name="reservation_date">
						</div>
					</div>
					<div class="col-lg-4">
						<div class="mb-3">
							<label class="form-label">Hora</label>
							<input type="time" class="form-control" name="reservation_time">
						</div>
					</div>
				</div>
				<button class="btn btn-primary"><i class="ti ti-device-floppy icon"></i> Guardar</button>
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
			url: '{{ route('reservations.store') }}',
			method: 'POST',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
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

</script>
@endsection