@extends('template.app')

@section('title', 'Flujo de caja')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Flujo de caja</li>
  </ol>
</nav>

<div class="card">
	<div class="card-body border-bottom">
		<form action="">
			<div class="row">
				<div class="col-md-3">
					<div class="mb-3">
						<label class="form-label">Año</label>
						<select class="form-select" name="year">
							<option value="">Seleccionar</option>
							@for($i = 2024; $i <= 2030; $i++)
							<option value="{{ $i }}" @if($year == $i) selected @endif>{{ $i }}</option>
							@endfor
						</select>
					</div>
				</div>
			</div>
			<button type="submit" class="btn btn-primary"><i class="ti ti-filter"></i> Filtrar</button>
		</form>
	</div>
	<div class="table-responsive">
		<table class="table card-table table-vcenter table-bordered small">
			<thead>
				<tr>
					<th>Flujo</th>
					<th>Ene</th>
					<th>Feb</th>
					<th>Mar</th>
					<th>Abr</th>
					<th>May</th>
					<th>Jun</th>
					<th>Jul</th>
					<th>Ago</th>
					<th>Set</th>
					<th>Oct</th>
					<th>Nov</th>
					<th>Dic</th>
				</tr>
			</thead>
			<tbody>
				<td colspan="13" class="fw-bold">Ingresos</td>
				<tr>
					<td>Ventas Servicios</td>
					@for($i = 1; $i <= 12; $i++)

					@php
					$serviceSales = App\Models\ClientService::active()->whereYear('start_date', $year)->whereMonth('start_date', $i)->sum('total');
					$totals['sales'][$i] = floatval($serviceSales);
					@endphp

					<td>
						S/{{ number_format($serviceSales, 2)  }}
					</td>
					@endfor
				</tr>
				<tr>
					<td colspan="13" class="fw-bold">Egresos</td>
				</tr>
				
				@php
				$costs = App\Models\Cost::active()->get();
				@endphp

				@foreach($costs as $cost)
				<tr>
					<td>{{ $cost->name }}</td>
					
					@for($i = 1; $i <= 12; $i++)

					@php
					$expenses = App\Models\Expense::active()->where('cost_id', $cost->id)->whereYear('date', $year)->whereMonth('date', $i)->sum('amount');
					$totals['expenses'][$i] += floatval($expenses);
					@endphp

					<td>
						S/{{ number_format($expenses, 2) }}
					</td>

					@endfor

				</tr>
				@endforeach


				<tr>
					<td class="fw-bold">Rentabilidad bruta</td>
					@for($i = 1; $i <= 12; $i++)

					@php
					$profit = ($totals['sales'][$i] + $totals['incomes'][$i]) - $totals['expenses'][$i];
					@endphp

					<td>
						S/{{ number_format($profit, 2) }}
					</td>
					@endfor
				</tr>
			</tbody>
		</table>
	</div>
</div>

@endsection