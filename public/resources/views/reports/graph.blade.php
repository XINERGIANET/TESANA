@extends('template.app')

@section('title', 'Reportes')

@section('content')
<nav class="mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    <li class="breadcrumb-item">Reportes</li>
    <li class="breadcrumb-item active">Gráfico de ventas</li>
  </ol>
</nav>
<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title">Gráfico de ventas por mes</h5>
    <div>
      <canvas id="chart1"></canvas>
    </div>
  </div>
</div>
@endsection

@php
  $salesByMonth = App\Models\Sale::select(DB::raw('MONTH(date) as month'), DB::raw('SUM(total) as total'))
    ->whereYear('date', date('Y'))
    ->groupBy('month')
    ->orderBy('month', 'asc')
    ->get();

  $totalSalesByMonth = [0,0,0,0,0,0,0,0,0,0,0,0];

  foreach($salesByMonth as $sale){
    $totalSalesByMonth[$sale->month-1] = floatval($sale->total);
  }
@endphp

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  $(document).ready(function(){
    const ctx_chart1 = document.getElementById('chart1');

    new Chart(ctx_chart1, {
      type: 'bar',
      data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
          {
            label: 'Ventas',
            data: {{ json_encode($totalSalesByMonth); }},
            borderWidth: 1
          }
        ]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  });
</script>
@endsection