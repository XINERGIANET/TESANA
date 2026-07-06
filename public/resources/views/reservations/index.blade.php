@extends('template.app')

@section('title', 'Reservaciones')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Reservaciones</li>
  </ol>
</nav>

<div class="card">
	<div class="card-header">
		<a href="{{ route('reservations.excel', request()->all()) }}" class="btn btn-success"><i class="ti ti-download icon"></i> Excel</a>
	</div>
	<div class="table-responsive">
		<table class="table card-table table-vcenter">
			<thead>
				<tr>
					<th>Alumno</th>
					<th>Fecha</th>
					<th>Hora</th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if($reservations->count() > 0)
				@foreach($reservations as $reservation)
				<tr>
					<td>{{ optional($reservation->client)->name }}</td>
					<td>{{ $reservation->reservation_date->format('d/m/Y') }}</td>
					<td>{{ $reservation->reservation_time->format('H:i') }}</td>
					<td>
						<div class="d-flex gap-2">
							<div class="d-flex gap-2">
								{{-- <button class="btn btn-icon btn-danger btn-delete" data-id="{{ $reservation->id }}">
									<i class="ti ti-x icon"></i>
								</button> --}}
							</div>
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
	@if($reservations->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $reservations->withQueryString()->links() }}
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
					url: '{{ route('reservations.index') }}' + '/' + id,
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