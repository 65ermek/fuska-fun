@extends('layouts.admin')

@section('title',  __('admin.dashboard'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $waitingTopPayments }}</h3>
                        <p>{{ __('admin.payments_waiting') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <a href="{{ route('admin.top-payments.waiting') }}" class="small-box-footer">
                        Zobrazit platby <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $countTodayVisits }}</h3>
                        <p>{{ __('admin.active_users_today') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $customers }}</h3>
                        <p>{{ __('admin.users_total') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $jobsToday }}</h3>
                        <p>{{ __('admin.jobs_today') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- LEFT COLUMN: AREA CHART -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Visits Overview</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="visitsChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: MAP -->
            <div class="col-md-4">
                <div class="card bg-gradient-primary">
                    <div class="card-header border-0">
                        <h3 class="card-title">Visitors Map (CZ)</h3>
                    </div>
                    <div class="card-body">
                        <div id="cz-map" style="height: 250px;"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        /* -------------------------
           TEST 1: INIT CHART
        --------------------------- */
        try {
            const labels = {!! json_encode($chartLabels) !!};
            const data = {!! json_encode($chartData) !!};
            const ctx = document.getElementById('visitsChart');

            if (!ctx) {
                console.error("❌ Canvas #visitsChart not found!");
            } else {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Visits',
                            data: data,
                            fill: true,
                            borderColor: 'rgba(0, 100, 255, .7)',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

        } catch (err) {
            console.error("❌ Chart creation error:", err);
        }
    });
</script>
@endpush

