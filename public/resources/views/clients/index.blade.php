@extends('template.app')

@section('title', 'Alumnos')

@section('content')
<nav class="mb-2">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
		<li class="breadcrumb-item active">Alumnos</li>
	</ol>
</nav>
<div class="card">
	<div class="card-header d-flex justify-content-between flex-column flex-sm-row gap-2">
		<div>
			<button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#createModal">
				<i class="ti ti-plus icon"></i> Crear nuevo
			</button>
			<a href="{{ route('clients.excel', request()->all()) }}" class="btn btn-success mb-2"><i class="ti ti-download icon"></i> Excel</a>
			<a href="{{ route('client_data.excel') }}" class="btn btn-success mb-2"><i class="ti ti-download icon"></i> Datos antropométricos</a>
		</div>
		<div class="text-center">
			<span class="d-block small">
				Tienes un total de
			</span>
			<span class="fs-2 fw-bold text-primary">
				S/{{ number_format($total, 2) }}
			</span>
		</div>
	</div>
	<div class="card-body border-bottom">
		<form action="">
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
						<label class="form-label">Año / Mes fecha inicial</label>
						<div class="input-group">
							@php 
							$months = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Setiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
							@endphp
							<select class="form-select" name="year">
								<option value="">Todos</option>
								@for($i = 2024; $i <= 2030; $i++)
								<option value="{{ $i }}" @if(request()->year == $i) selected @endif>{{ $i }}</option>
								@endfor
							</select>
							<select class="form-select" name="month">
								<option value="">Todos</option>
								@foreach($months as $number => $name)
								<option value="{{ $number }}" @if($number == request()->month) selected @endif>{{ $name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="mb-3">
						<label class="form-label">Tipo de servicio</label>
						<select class="form-select" name="sessions">
							<option value="">Todos</option>
							<option value="1" @if(request()->sessions == 1) selected @endif>1 sesión</option>
							<option value="8" @if(request()->sessions == 8) selected @endif>8 sesiones</option>
							<option value="12" @if(request()->sessions == 12) selected @endif>12 sesiones</option>
						</select>
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
					<th>DNI</th>
					<th>Nombre</th>
					<th>Servicio</th>
					<th>Total</th>
					<th>Fecha inicial</th>
					<th>Fecha final</th>
					<th>Fecha pago</th>
					<th>Perfil</th>
					<th></th>
					<th>Acción</th>
				</tr>
			</thead>
			<tbody>
				@if($clients->count() > 0)
				@foreach($clients as $client)
				<tr>
					<td>{{ $client->document }}</td>
					<td>{{ $client->name }}</td>
					<td>{{ optional($client->service)->name }}</td>
					<td>S/{{ number_format($client->total, 2) }}</td>
					<td>{{ $client->start_date->format('d/m/Y') }}</td>
					<td>{{ optional($client->end_date)->format('d/m/Y') }}</td>
					<td>{{ optional($client->payment_date)->format('d/m/Y') }}</td>
					<td>{{ $client->profile() }}</td>
					<td>
						@if(strtotime($client->end_date) >= strtotime(date('Y-m-d')))
						<span class="badge bg-success"></span>
						@else
						<span class="badge bg-danger"></span>
						@endif
					</td>
					<td>
						<div class="d-flex gap-2">
							@if(strtotime($client->end_date) < strtotime(date('Y-m-d')))
							<button class="btn btn-icon btn-primary btn-renew" data-id="{{ $client->id }}" title="Renovar">
								<i class="ti ti-refresh icon"></i>
							</button>
							@endif
							<button class="btn btn-icon btn-primary btn-services" data-id="{{ $client->id }}" title="Historial">
								<i class="ti ti-list icon"></i>
							</button>
							<button class="btn btn-icon btn-primary btn-reset" data-id="{{ $client->id }}" title="Reiniciar contraseña">
								<i class="ti ti-lock-open icon"></i>
							</button>
							<button class="btn btn-icon btn-primary btn-data" data-id="{{ $client->id }}" title="Datos antropométricos">
								<i class="ti ti-file-text icon"></i>
							</button>
							<button class="btn btn-icon btn-primary btn-edit" data-id="{{ $client->id }}" title="Editar">
								<i class="ti ti-pencil icon"></i>
							</button>
							<button class="btn btn-icon btn-danger btn-delete" data-id="{{ $client->id }}" title="Eliminar">
								<i class="ti ti-x icon"></i>
							</button>
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
	@if($clients->hasPages())
	<div class="card-footer d-flex align-items-center">
		{{ $clients->withQueryString()->links() }}
	</div>
	@endif
</div>

<div class="modal modal-blur fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
								<label class="form-label required">DNI</label>
								<input type="text" class="form-control" name="document" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Nombre</label>
								<input type="text" class="form-control" name="name" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Fecha de nacimiento</label>
								<input type="date" class="form-control" name="birth_date">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Sexo</label>
								<select class="form-select" name="sex">
									<option value="">Seleccionar</option>
									<option value="M">Masculino</option>
									<option value="F">Femenino</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Correo electrónico</label>
								<input type="text" class="form-control" name="email" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Teléfono</label>
								<input type="text" class="form-control" name="phone" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Teléfono de emergencia</label>
								<input type="text" class="form-control" name="emergency_phone" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha inicial</label>
								<input type="date" class="form-control" name="start_date">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha final</label>
								<input type="date" class="form-control" name="end_date">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha de pago</label>
								<input type="date" class="form-control" name="payment_date">
							</div>
						</div>
						<div class="col-lg-12">
							<div class="mb-3">
								<label class="form-label"><button type="button" class="btn btn-primary" id="btn-services">Servicios <i class="ti ti-chevron-down"></i></button></label>
								<div class="table-responsive" id="divServices" style="display: none;">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th></th>
												<th>Nombre</th>
												<th>Sesiones</th>
												<th>Precio</th>
											</tr>
										</thead>
										<tbody>
											@foreach($services as $service)
											<tr>
												<td>
													<input type="radio" class="form-check-input" name="service_id" value="{{ $service->id }}">
												</td>
												<td>{{ $service->name }}</td>
												<td>{{ $service->sessions }}</td>
												<td>S/{{ $service->price }}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="mb-3">
								<label class="form-label">Observación</label>
								<textarea class="form-control" name="observation"></textarea>
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
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
								<label class="form-label required">DNI</label>
								<input type="text" class="form-control" name="document" id="editDocument" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Nombre</label>
								<input type="text" class="form-control" name="name" id="editName" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Fecha de nacimiento</label>
								<input type="date" class="form-control" name="birth_date" id="editBirthDate">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Sexo</label>
								<select class="form-select" name="sex" id="editSex">
									<option value="">Seleccionar</option>
									<option value="M">Masculino</option>
									<option value="F">Femenino</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Correo electrónico</label>
								<input type="text" class="form-control" name="email" id="editEmail" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Teléfono</label>
								<input type="text" class="form-control" name="phone" id="editPhone" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Teléfono de emergencia</label>
								<input type="text" class="form-control" name="emergency_phone" id="editEmergencyPhone" autocomplete="off">
							</div>
						</div>
						<div class="col-lg-12">
							<div class="mb-3">
								<label class="form-label required">Servicio</label>
								<table class="table table-bordered">
									<thead>
										<tr>
											<th></th>
											<th>Nombre</th>
											<th>Periodo</th>
											<th>Sesiones</th>
											<th>Precio</th>
										</tr>
									</thead>
									<tbody>
										@foreach($services as $service)
										<tr>
											<td>
												<input type="radio" class="form-check-input service" name="service_id" value="{{ $service->id }}">
											</td>
											<td>{{ $service->name }}</td>
											<td>{{ $service->period }}</td>
											<td>{{ $service->sessions }}</td>
											<td>S/{{ $service->price }}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha inicial</label>
								<input type="date" class="form-control" name="start_date" id="editStartDate">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha final</label>
								<input type="date" class="form-control" name="end_date" id="editEndDate">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha de pago</label>
								<input type="date" class="form-control" name="payment_date" id="editPaymentDate">
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

<div class="modal modal-blur fade" id="renewModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<form id="renewForm" method="POST">
				<div class="modal-header">
					<h5 class="modal-title">Renovar</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha inicial</label>
								<input type="date" class="form-control" name="start_date">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha final</label>
								<input type="date" class="form-control" name="end_date">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label required">Fecha de pago</label>
								<input type="date" class="form-control" name="payment_date">
							</div>
						</div>
						<div class="col-lg-12">
							<div class="table-responsive mb-3">
								
								<table class="table table-bordered">
									<thead>
										<tr>
											<th></th>
											<th>Nombre</th>
											<th>Sesiones</th>
											<th>Precio</th>
										</tr>
									</thead>
									<tbody>
										@foreach($services as $service)
										<tr>
											<td>
												<input type="radio" class="form-check-input" name="service_id" value="{{ $service->id }}">
											</td>
											<td>{{ $service->name }}</td>
											<td>{{ $service->sessions }}</td>
											<td>S/{{ $service->price }}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" id="renewId">
					<button type="button" class="btn me-auto" data-bs-dismiss="modal"><i class="ti ti-x icon"></i> Cerrar</button>
					<button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy icon"></i> Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="servicesModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Historial</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Servicio</th>
								<th>Total</th>
								<th>Fecha inicial</th>
								<th>Fecha final</th>
								<th>Fecha pago</th>
							</tr>
						</thead>
						<tbody id="tbl-services"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="dataModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Datos antropométricos</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<button type="button" class="btn btn-primary mb-4 btn-form" data-target="#divForm1">Calc. % de grasa<i class="ti ti-chevron-down"></i></button>
				<button type="button" class="btn btn-primary mb-4 btn-form" data-target="#divForm2">Calc. ICC <i class="ti ti-chevron-down"></i></button>
				<button type="button" class="btn btn-primary mb-4 btn-form" data-target="#divForm3">Calc. IMC <i class="ti ti-chevron-down"></i></button>
				
				<div class="mb-4" id="divForm1" style="display: none">
					<form method="POST" class="dataForm">
						<input type="hidden" name="calculo" value="grasa">
						<div class="row">
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label">TRICIPITAL (TRICEPS)</label>
									<input type="text" class="form-control" name="triceps">
								</div>
							</div>
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label">SUB ESCAPULAR</label>
									<input type="text" class="form-control" name="subescapular">
								</div>
							</div>
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label">SUPRA ILIACO</label>
									<input type="text" class="form-control" name="suprailiaco">
								</div>
							</div>
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label">ABDOMINAL</label>
									<input type="text" class="form-control" name="abdominal">
								</div>
							</div>
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label">CUADRICIPITAL (MUSLO)</label>
									<input type="text" class="form-control" name="muslo">
								</div>
							</div>
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label">PERONEAL (PANTORRILLA)</label>
									<input type="text" class="form-control" name="pantorrilla">
								</div>
							</div>
						</div>
						<input type="hidden" id="dataId">
						<button type="submit" class="btn btn-primary">Guardar</button>
					</form>
				</div>
				<div class="mb-4" id="divForm2" style="display: none">
					<form method="POST" class="dataForm">
						<input type="hidden" name="calculo" value="icc">
						<div class="row">
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label">ABDOMINAL</label>
									<input type="text" class="form-control" name="abdominal_2">
								</div>
							</div>
							<div class="col-md-3">
								<div class="mb-3">
									<label class="form-label">CADERA</label>
									<input type="text" class="form-control" name="cadera">
								</div>
							</div>
						</div>
						<input type="hidden" id="dataId">
						<button type="submit" class="btn btn-primary">Guardar</button>
					</form>
				</div>
				<div class="mb-4" id="divForm3" style="display: none">
					<form method="POST" class="dataForm">
						<input type="hidden" name="calculo" value="imc">
						<div class="row">
							<div class="col-md-2">
								<div class="mb-3">
									<label class="form-label">PESO</label>
									<input type="text" class="form-control" name="peso">
								</div>
							</div>
							<div class="col-md-2">
								<div class="mb-3">
									<label class="form-label">TALLA</label>
									<input type="text" class="form-control" name="talla">
								</div>
							</div>
						</div>
						<input type="hidden" id="dataId">
						<button type="submit" class="btn btn-primary">Guardar</button>
					</form>
				</div>

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
								<th></th>
							</tr>
						</thead>
						<tbody id="tbl-data-1"></tbody>
					</table>
				</div>

				<h5>INDICE CINTURA CADERA (ICC)</h5>
				
				<div class="table-responsive mb-4">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>FECHA</th>
								<th>ABDOMINAL</th>
								<th>CADERA</th>
								<th>RESULTADO</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="tbl-data-2"></tbody>
					</table>
				</div>

				<h5>INDICE MASA CORPORAL (IMC)</h5>
				
				<div class="table-responsive mb-4">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>FECHA</th>
								<th>PESO</th>
								<th>TALLA</th>
								<th>RESULTADO</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="tbl-data-3"></tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>

	$('#storeForm').submit(function(e){
		e.preventDefault();

		$.ajax({
			url: '{{ route('clients.store') }}',
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
			url: '{{ route('clients.index') }}' + '/' + id + '/edit/',
			method: 'GET',
			success: function(data){
				$('#editDocument').val(data.document);
				$('#editName').val(data.name);
				$('#editBirthDate').val(data.birth_date);
				$('#editSex').val(data.sex);
				$('#editEmail').val(data.email);
				$('#editPhone').val(data.phone);
				$('#editEmergencyPhone').val(data.emergency_phone);
				$('input.service[value="'+data.service_id+'"]').prop('checked', true);
				$('#editStartDate').val(data.start_date);
				$('#editEndDate').val(data.end_date);
				$('#editPaymentDate').val(data.payment_date);
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
			url: '{{ route('clients.index') }}' + '/' + id + '',
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

	$(document).on('click', '.btn-reset', function(){

		var id = $(this).data('id');

		ToastConfirm.fire({
			text: '¿Estás seguro que deseas reiniciar la contraseña?',
		}).then((result) => {
			
			if(result.isConfirmed){

				$.ajax({
					url: '{{ route('clients.index') }}' + '/' + id + '/reset',
					method: 'GET',
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

	$(document).on('click', '.btn-delete', function(){

		var id = $(this).data('id');

		ToastConfirm.fire({
			text: '¿Estás seguro que deseas eliminar el registro?',
		}).then((result) => {
			
			if(result.isConfirmed){

				$.ajax({
					url: '{{ route('clients.index') }}' + '/' + id,
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

	$('#btn-services').click(function(){
		$('#divServices').toggle();
	});

	$('.btn-form').click(function(){
		target = $(this).data('target');
		console.log(target);
		$('#divForm1').hide();
		$('#divForm2').hide();
		$('#divForm3').hide();
		$(target).show();
	});

	$(document).on('click', '.btn-renew', function(){

		var id = $(this).data('id');

		$('#renewId').val(id);

		$('#renewModal').modal('show');

	});

	$('#renewForm').submit(function(e){
		e.preventDefault();

		var id = $('#renewId').val();

		$.ajax({
			url: '{{ route('clients.index') }}' + '/' + id + '/renew',
			method: 'POST',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
					$('#renewModal').modal('hide');
					$('#renewForm')[0].reset();

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

	$(document).on('click', '.btn-services', function(){

		$('#tbl-services').html('');

		var id = $(this).data('id');

		$.ajax({
			url: '{{ route('clients.index') }}' + '/' + id + '/services',
			method: 'GET',
			data: $(this).serialize(),
			success: function(data){
				var html = '';

				data.forEach(function(client_service){
					html += `
						<tr>
							<td>${client_service.service}</td>
							<td>${client_service.total}</td>
							<td>${client_service.start_date}</td>
							<td>${client_service.end_date}</td>
							<td>${client_service.payment_date ?? ''}</td>
						</tr>
					`;
				});

				console.log(html);

				$('#tbl-services').html(html);

				$('#servicesModal').modal('show');
			},
			error: function(err){
				ToastError.fire({ text: 'Ocurrió un error' });
			}
		});


	});

	$(document).on('click', '.btn-data', function(){

		$('#tbl-data').html('');

		var id = $(this).data('id');

		$('#dataId').val(id);

		$.ajax({
			url: '{{ route('clients.index') }}' + '/' + id + '/data',
			method: 'GET',
			data: $(this).serialize(),
			success: function(data){
				var html_1 = '';
				var html_2 = '';
				var html_3 = '';

				data.data_1.forEach(function(client_data){
					html_1 += `
						<tr>
							<td>${client_data.date ?? ''}</td>
							<td>${client_data.triceps ?? ''}</td>
							<td>${client_data.subescapular ?? ''}</td>
							<td>${client_data.suprailiaco ?? ''}</td>
							<td>${client_data.abdominal ?? ''}</td>
							<td>${client_data.muslo ?? ''}</td>
							<td>${client_data.pantorrilla ?? ''}</td>
							<td>${client_data.calculo ?? ''} - ${client_data.resultado ?? ''}</td>
							<td>
								<button class="btn btn-sm btn-danger btn-delete-data" data-client_id="${client_data.client_id}" data-id="${client_data.id}" title="Eliminar">
									<i class="ti ti-x"></i>
								</button>
							</td>
						</tr>
					`;
				});

				data.data_2.forEach(function(client_data){
					html_2 += `
						<tr>
							<td>${client_data.date ?? ''}</td>
							<td>${client_data.abdominal_2 ?? ''}</td>
							<td>${client_data.cadera ?? ''}</td>
							<td>${client_data.calculo ?? ''} - ${client_data.resultado ?? ''}</td>
							<td>
								<button class="btn btn-sm btn-danger btn-delete-data" data-client_id="${client_data.client_id}" data-id="${client_data.id}" title="Eliminar">
									<i class="ti ti-x"></i>
								</button>
							</td>
						</tr>
					`;
				});

				data.data_3.forEach(function(client_data){
					html_3 += `
						<tr>
							<td>${client_data.date ?? ''}</td>
							<td>${client_data.peso ?? ''}</td>
							<td>${client_data.talla ?? ''}</td>
							<td>${client_data.calculo ?? ''} - ${client_data.resultado ?? ''}</td>
							<td>
								<button class="btn btn-sm btn-danger btn-delete-data" data-client_id="${client_data.client_id}" data-id="${client_data.id}" title="Eliminar">
									<i class="ti ti-x"></i>
								</button>
							</td>
						</tr>
					`;
				});

				$('#tbl-data-1').html(html_1);
				$('#tbl-data-2').html(html_2);
				$('#tbl-data-3').html(html_3);

				$('#dataModal').modal('show');
			},
			error: function(err){
				ToastError.fire({ text: 'Ocurrió un error' });
			}
		});


	});

	$('.dataForm').submit(function(e){
		e.preventDefault();

		var id = $('#dataId').val();

		$.ajax({
			url: '{{ route('clients.index') }}' + '/' + id + '/data',
			method: 'POST',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
					$('#dataModal').modal('hide');

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

	$(document).on('click', '.btn-delete-data', function(){

		var id = $(this).data('id');
		var client_id = $(this).data('client_id');

		ToastConfirm.fire({
			text: '¿Estás seguro que deseas eliminar el registro?',
		}).then((result) => {
			
			if(result.isConfirmed){

				$.ajax({
					url: '{{ route('clients.index') }}' + '/' + client_id + '/data',
					method: 'DELETE',
					data: { id },
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