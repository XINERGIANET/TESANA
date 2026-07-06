@extends('template.app')

@section('title', 'Dashboard de resultados')

@section('styles')
<style>
	.dashboard-card {
		cursor: pointer;
		transition: transform .15s ease, box-shadow .15s ease;
	}

	.dashboard-card:hover,
	.dashboard-card:focus {
		transform: translateY(-2px);
		box-shadow: 0 .5rem 1.25rem rgba(15, 23, 42, .10);
		outline: none;
	}

	.dashboard-summary-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
		gap: 1rem;
	}

	.dashboard-summary-item {
		border: 1px solid #dce7f5;
		border-radius: 12px;
		padding: .85rem 1rem;
		background: #f8fbff;
	}

	.dashboard-summary-label {
		display: block;
		font-size: .75rem;
		text-transform: uppercase;
		color: #6c7a91;
		margin-bottom: .25rem;
	}

	.dashboard-summary-value {
		font-size: 1.1rem;
		font-weight: 600;
		color: #1f2a44;
	}
</style>
@endsection

@section('content')

<div class="d-block d-lg-flex justify-content-between align-items-center">
	<p>Bienvenido a Tesana Pilates Studio. Tienes <b>{{ $expired }}</b> alummo(s) próximo(s) a vencer. <a href="{{ route('clients.index') }}">Ver más información</a></p>

	<button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#createClientModal">
		<i class="ti ti-plus icon"></i> Nuevo alumno
	</button>
</div>

@if($birthdays->count() > 0)
<div class="mb-4">
	<p class="mb-0">Mañana cumple años ({{ 1 }}) alumno(s):</p>
	<ul>
		@foreach($birthdays as $client)
		<li>{{ $client->name }}: {{ optional($client->birth_date)->format('d/m/Y') }}</li>
		@endforeach
	</ul>
</div>
@endif

<form action="">
	<div class="row">
		<div class="col-md-2">
			<div class="mb-3">
				<select class="form-select" name="year">
					@for($i = 2024; $i <= 2030; $i++)
					<option value="{{ $i }}" @if($year == $i) selected @endif>{{ $i }}</option>
					@endfor
				</select>
			</div>
		</div>
		<div class="col-md-2">
			<div class="mb-3">
				@php 
				$months = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Setiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
				@endphp
				<select class="form-select" name="month">
					<option value="">Mes</option>
					@foreach($months as $number => $name)
					<option value="{{ $number }}" @if($number == request()->month) selected @endif>{{ $name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-auto">
			<button type="submit" class="btn btn-primary"><i class="ti ti-filter icon"></i> Filtrar</button>
		</div>
	</div>
</form>

<div class="row">
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-primary-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="clients" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">ALUMNOS</h5>
				<span class="d-block fs-2 text-center">{{ $clients }}</span>
			</div>
		</div>
	</div>
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-primary-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="incomes" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">INGRESOS</h5>
				<span class="d-block fs-2 text-center">S/{{ number_format($totalIncomes, 2) }}</span>
			</div>
		</div>
	</div>
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-danger-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="expenses" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">EGRESOS</h5>
				<span class="d-block fs-2 text-center">S/{{ number_format($totalExpenses, 2) }}</span>
			</div>
		</div>
	</div>
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-success-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="rentability" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">% RENTABILIDAD</h5>
				<span class="d-block fs-2 text-center">{{ ($totalExpenses == 0 || $totalIncomes == 0) ? 0 : number_format($totalExpenses / $totalIncomes, 2) }} %</span>
			</div>
		</div>
	</div>
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-dark-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="cash" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">CAJA</h5>
				<span class="d-block fs-2 text-center">S/{{ number_format($totalIncomes - $totalExpenses, 2) }}</span>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-primary-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="new_clients" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">A. NUEVOS</h5>
				<span class="d-block fs-2 text-center">{{ $new_clients }}</span>
			</div>
		</div>
	</div>
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-primary-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="recurrent_clients" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">A. RECURRENTES</h5>
				<span class="d-block fs-2 text-center">{{ $recurrent_clients }}</span>
			</div>
		</div>
	</div>
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-warning-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="sessions_1" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">1 SESIÓN</h5>
				<span class="d-block fs-2 text-center">{{ $clients_1_session }} <br> S/{{ number_format($total_1_session, 2) }}</span>
			</div>
		</div>
	</div>
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-warning-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="sessions_8" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">8 SESIONES</h5>
				<span class="d-block fs-2 text-center">{{ $clients_8_sessions }} <br> S/{{ number_format($total_8_sessions, 2) }}</span>
			</div>
		</div>
	</div>
	<div class="col-xl-2 col-lg-3 col-md-4">
		<div class="card bg-warning-lt mb-4 dashboard-card js-dashboard-card" data-detail-key="sessions_12" tabindex="0" role="button">
			<div class="card-body">
				<h5 class="text-truncate">12 SESIONES</h5>
				<span class="d-block fs-2 text-center">{{ $clients_12_sessions }} <br> S/{{ number_format($total_12_sessions, 2) }}</span>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="card mb-4">
			<div class="card-body">
				<h5 class="card-title">Gráfico de ingresos / egresos por mes</h5>
				<div>
					<canvas id="chart1"></canvas>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card mb-4">
			<div class="card-body">
				<h5 class="card-title">Gráfico de egresos</h5>
				<div>
					<canvas id="chart2"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="dashboardDetailModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="dashboardDetailTitle">Detalle</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="dashboardDetailSummary" class="dashboard-summary-grid mb-4"></div>
				<div class="table-responsive">
					<table class="table table-vcenter table-striped" id="dashboardDetailTable">
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>
				<div id="dashboardDetailEmpty" class="text-muted"></div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="createClientModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<form id="storeClientForm" method="POST">
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
								<table class="table table-bordered" id="tbl-services" style="display: none;">
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
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Clientes activos en {{ $months[now()->month] }} {{ now()->year }}</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Fecha de inicio</th>
                        <th>Fecha de fin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients_active_current_month as $client)
                        <tr>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ \Carbon\Carbon::parse($client->start_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($client->end_date)->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay clientes activos este mes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

@php
$monthServiceSales = App\Models\Client::active()->select(DB::raw('MONTH(start_date) as month'), DB::raw('SUM(total) as total'))
	->whereYear('start_date', $year)
	->groupBy('month')
	->orderBy('month', 'asc')
	->get();

$monthProductSales = App\Models\Sale::active()->select(DB::raw('MONTH(date) as month'), DB::raw('SUM(total) as total'))
	->whereYear('date', $year)
	->groupBy('month')
	->orderBy('month', 'asc')
	->get();

$monthIncomes = App\Models\Income::active()->select(DB::raw('MONTH(date) as month'), DB::raw('SUM(amount) as total'))
	->whereYear('date', $year)
	->groupBy('month')
	->orderBy('month', 'asc')
	->get();

$monthExpenses = App\Models\Expense::active()->select(DB::raw('MONTH(date) as month'), DB::raw('SUM(amount) as total'))
	->whereYear('date', $year)
	->groupBy('month')
	->orderBy('month', 'asc')
	->get();

$totals = [
	'incomes' => [0,0,0,0,0,0,0,0,0,0,0,0],
	'expenses' => [0,0,0,0,0,0,0,0,0,0,0,0],
];

foreach($monthServiceSales as $sale){
	$totals['incomes'][$sale->month-1] += floatval($sale->total);
}

foreach($monthProductSales as $sale){
	$totals['incomes'][$sale->month-1] += floatval($sale->total);
}

foreach($monthIncomes as $sale){
	$totals['incomes'][$sale->month-1] += floatval($sale->total);
}

foreach($monthExpenses as $expense){
	$totals['expenses'][$expense->month-1] += floatval($expense->total);
}


$expenses = [];
$costs = App\Models\Cost::active()->get();
foreach($costs as $cost){
	$total = App\Models\Expense::active()->where('cost_id', $cost->id)->whereYear('date', $year)->when(request()->month, function($query, $month){
        return $query->whereMonth('date', $month);
    })->sum('amount');

	if($total > 0){
		$expenses[] = [
			'name' => $cost->name,
			'total' => floatval($total)
		];
	}
}

@endphp

@php
	$startMonth = request()->month ? intval(request()->month) : 1;
	$endMonth = now()->month;

	$monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
	$labels = array_slice($monthLabels, $startMonth - 1, $endMonth - $startMonth + 1);

	// Filtra los datos para el gráfico solo en ese rango de meses
	$filteredIncomes = array_slice($totals['incomes'], $startMonth - 1, $endMonth - $startMonth + 1);
	$filteredExpenses = array_slice($totals['expenses'], $startMonth - 1, $endMonth - $startMonth + 1);
@endphp


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	const dashboardDetails = @json($dashboardDetails, JSON_UNESCAPED_UNICODE);

	function renderDashboardDetail(detail) {
		const summaryContainer = $('#dashboardDetailSummary');
		const tableHead = $('#dashboardDetailTable thead');
		const tableBody = $('#dashboardDetailTable tbody');
		const emptyMessage = $('#dashboardDetailEmpty');

		$('#dashboardDetailTitle').text(detail.title || 'Detalle');
		summaryContainer.empty();
		tableHead.empty();
		tableBody.empty();
		emptyMessage.text('');

		(detail.summary || []).forEach(function(item) {
			summaryContainer.append(
				'<div class="dashboard-summary-item">' +
					'<span class="dashboard-summary-label">' + item.label + '</span>' +
					'<span class="dashboard-summary-value">' + item.value + '</span>' +
				'</div>'
			);
		});

		if ((detail.headers || []).length > 0) {
			let headRow = '<tr>';
			detail.headers.forEach(function(header) {
				headRow += '<th>' + header + '</th>';
			});
			headRow += '</tr>';
			tableHead.html(headRow);
		}

		if ((detail.rows || []).length > 0) {
			detail.rows.forEach(function(row) {
				let bodyRow = '<tr>';
				(detail.headers || []).forEach(function(header) {
					bodyRow += '<td>' + (row[header] ?? '-') + '</td>';
				});
				bodyRow += '</tr>';
				tableBody.append(bodyRow);
			});
			$('#dashboardDetailTable').show();
		} else {
			$('#dashboardDetailTable').hide();
			emptyMessage.text(detail.empty || 'No hay información disponible.');
		}
	}

	function openDashboardDetail(key) {
		if (!dashboardDetails[key]) {
			return;
		}

		renderDashboardDetail(dashboardDetails[key]);
		const modal = new bootstrap.Modal(document.getElementById('dashboardDetailModal'));
		modal.show();
	}

	$(document).ready(function(){
		const ctx_chart1 = document.getElementById('chart1');
		const ctx_chart2 = document.getElementById('chart2');

		new Chart(ctx_chart1, {
			type: 'bar',
			data: {
				labels: {!! json_encode($labels) !!},
				datasets: [
					{
						label: 'Ventas',
						data: {!! json_encode($filteredIncomes) !!},
						borderWidth: 1,
						backgroundColor: '#e0f0ff'
					},
					{
						label: 'Egresos',
						data: {!! json_encode($filteredExpenses) !!},
						borderWidth: 1,
						backgroundColor: '#ffe0e0'
					}
				]
			},
			options: {
				scales: {
					x: {
						grid: { display: false }
					},
					y: {
						grid: { display: false },
						beginAtZero: true
					}
				}
			}
		});

		new Chart(ctx_chart2, {
			type: 'doughnut',
			data: {
				labels: {!! json_encode(array_column($expenses, 'name'), JSON_UNESCAPED_UNICODE) !!},
				datasets: [
				{
					label: 'Egresos',
					data: {{ json_encode(array_column($expenses, 'total')) }},
					borderWidth: 1
				},
				]
			}
		});

		$('.js-dashboard-card').on('click keydown', function(event){
			if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') {
				return;
			}

			if (event.type === 'keydown') {
				event.preventDefault();
			}

			openDashboardDetail($(this).data('detail-key'));
		});
	});

	$('#storeClientForm').submit(function(e){
		e.preventDefault();

		$.ajax({
			url: '{{ route('clients.store') }}',
			method: 'POST',
			data: $(this).serialize(),
			success: function(data){
				if(data.status){
					$('#createClientModal').modal('hide');
					$('#storeClientForm')[0].reset();
					
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

	$('#btn-services').click(function(){
		$('#tbl-services').toggle();
	});

</script>
@endsection
