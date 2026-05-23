@extends('template.app')
@section('title', 'Crear asistencia')
@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Crear asistencia</li>
  </ol>
</nav>
<div class="col-lg-6">
	<div class="card">
		<div class="card-body">
			<form id="storeForm">
				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">Alumno</label>
							<select type="text" class="form-select" name="client_id" id="ts-clients"></select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">Fecha</label>
							<input type="date" class="form-control" name="date" value="{{ now()->format('Y-m-d') }}">
						</div>
					</div>
				</div>
				<button class="btn btn-primary">Guardar</button>
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
			url: '{{ route('attendances.store') }}',
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
                    }).catch(()=> {
                        callback([]);
                    });
            },
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            render: {
                no_results: function(data, escape){
                    return '<div class="no-results">No se encontraron resultados</div>';
                }
            }
        });
    });
</script>
@endsection