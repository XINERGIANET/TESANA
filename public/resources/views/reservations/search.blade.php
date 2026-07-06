@extends('template.app')

@section('title', 'Mis reservas')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('index_client') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Mis reservas</li>
  </ol>
</nav>

<div class="card">
	<div class="table-responsive">
		<table class="table card-table table-vcenter">
			<thead>
				<tr>
					<th>Alumno</th>
					<th>Fecha</th>
					<th>Hora</th>
				</tr>
			</thead>
			<tbody>
				@if($reservations->count() > 0)
				@foreach($reservations as $reservation)
				<tr>
					<td>{{ optional($reservation->client)->name }}</td>
					<td>{{ optional($reservation->reservation_date)->format('d/m/Y') }}</td>	
					<td>{{ optional($reservation->reservation_time)->format('H:i') }}</td>	
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

	@if($reservations->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $reservations->withQueryString()->links() }}
	</div>
	@endif
</div>
@endsection

@section('scripts')
<script>
		$(document).ready(function(){
			new TomSelect('#ts-clients', {
				valueField: 'id',
				labelField: 'name',
				searchField: 'name',
				load: function(query, callback){
					var url = '{{ route('clients.api') }}?q=' + encodeURIComponent(query);
					fetch(url)
						.then(response => response.json())
						.then(data => {
							callback(data.items);
						}).catch(()=>{
							callback();
						});
				},
				copyClassesToDropdown: false,
				dropdownClass: 'dropdown-menu ts-dropdown',
	    	optionClass:'dropdown-item',
				render: {
					no_results: function(data, escape){
						return '<div class="no-results">No se encontraron resultados</div>'
					}
				}
			})
		})
</script>
@endsection