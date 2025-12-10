@extends('layouts.master')

@php
// Prepare chart data for JavaScript
$chartLabels = isset($monthlyLabels) ? $monthlyLabels : ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7', 'Day 8'];
$chartData = isset($monthlyData) ? $monthlyData : [0, 0, 0, 0, 0, 0, 0, 0];

// Training chart data
$trainingChartLabels = isset($trainingMonthlyLabels) ? $trainingMonthlyLabels : [];
$trainingChartData = isset($trainingMonthlyData) ? $trainingMonthlyData : [];
@endphp

@section('css')
<link rel="stylesheet" href="{{ URL::asset('plugins/chartist/css/chartist.min.css') }}">
@endsection

@section('breadcrumb')
<div class="col-sm-6 text-left">
    <h4 class="page-title">Dashboard</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Welcome to Attendance Management System</li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <span class="ti-id-badge" style="font-size: 20px"></span>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white-50">Total <br> Employees</h5>
                    <h4 class="font-500">{{$data[0]}} </h4>
                    <span class="ti-user" style="font-size: 71px"></span>

                </div>
                <div class="pt-2">
                    <div class="float-right">
                        <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                    </div>
                    <p class="text-white-50 mb-0">More info</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="ti-alarm-clock" style="font-size: 20px"></i>
                    </div>
                    <h6 class="font-16 text-uppercase mt-0 text-white-50">On Time <br> Percentage</h6>
                    <h4 class="font-500">{{$data[3]}} %<i class="text-danger ml-2"></i></h4>
                    <span class="peity-donut" data-peity='{ "fill": ["#02a499", "#f2f2f2"], "innerRadius": 28, "radius": 32 }' data-width="72" data-height="72">{{$data[3]}}/{{count($data)}}</span>

                </div>
                <div class="pt-2">
                    <div class="float-right">
                        <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                    </div>

                    <p class="text-white-50 mb-0">More info</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class=" ti-check-box " style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white-50">On Time <br> Today</h5>
                    <h4 class="font-500">{{$data[1]}} <i class=" text-success ml-2"></i></h4>
                    <span class="peity-donut" data-peity='{ "fill": ["#02a499", "#f2f2f2"], "innerRadius": 28, "radius": 32 }' data-width="72" data-height="72">{{$data[1]}}/{{count($data)}}</span>

                </div>
                <div class="pt-2">
                    <div class="float-right">
                        <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                    </div>

                    <p class="text-white-50 mb-0">More info</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="ti-alert" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white-50">Late <br> Today</h5>
                    <h4 class="font-500">{{$data[2]}}<i class=" text-success ml-2"></i></h4>
                    <span class="peity-donut" data-peity='{ "fill": ["#02a499", "#f2f2f2"], "innerRadius": 28, "radius": 32 }' data-width="72" data-height="72">{{$data[2]}}/{{count($data)}}</span>

                </div>
                <div class="pt-2">
                    <div class="float-right">
                        <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                    </div>

                    <p class="text-white-50 mb-0">More info</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-9">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title mb-5">Monthly Report</h4>
                <div id="chart-with-area" class="ct-chart earning ct-golden-section"></div>
            </div>
        </div>
        <!-- end card -->
    </div>

    <div class="col-xl-3">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title mb-4">Attendance Analytics</h4>
                <div class="wid-peity mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">On Time</p>
                        <h5 class="mb-0 text-success">{{ $onTimeThisMonth ?? 0 }}</h5>
                    </div>
                    <small class="text-muted d-block mb-2">This Month</small>
                    <span class="peity-line" data-width="100%" data-peity='{ "fill": ["rgba(2, 164, 153,0.3)"],"stroke": ["rgba(2, 164, 153,0.8)"]}' data-height="60">{{ implode(',', $onTimeData ?? []) }}</span>
                    <small class="text-muted d-block mt-1">Last 8 Days</small>
                </div>
                <div class="wid-peity mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">Late</p>
                        <h5 class="mb-0 text-danger">{{ $lateThisMonth ?? 0 }}</h5>
                    </div>
                    <small class="text-muted d-block mb-2">This Month</small>
                    <span class="peity-line" data-width="100%" data-peity='{ "fill": ["rgba(220, 53, 69,0.3)"],"stroke": ["rgba(220, 53, 69,0.8)"]}' data-height="60">{{ implode(',', $lateData ?? []) }}</span>
                    <small class="text-muted d-block mt-1">Last 8 Days</small>
                </div>
                <div class="wid-peity">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">Total</p>
                        <h5 class="mb-0 text-primary">{{ $totalAttendanceThisMonth ?? 0 }}</h5>
                    </div>
                    <small class="text-muted d-block mb-2">This Month</small>
                    <span class="peity-line" data-width="100%" data-peity='{ "fill": ["rgba(2, 164, 153,0.3)"],"stroke": ["rgba(2, 164, 153,0.8)"]}' data-height="60">{{ implode(',', $monthlyData ?? []) }}</span>
                    <small class="text-muted d-block mt-1">Last 8 Days</small>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<!-- Training Section -->
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title mb-4">Training Overview (Last 12 Months)</h4>
                <div id="training-chart" class="ct-chart earning ct-golden-section"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title mb-4">Training Statistics</h4>
                <div class="wid-peity mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">Total Trainings</p>
                        <h5 class="mb-0 text-primary">{{ $totalTrainings ?? 0 }}</h5>
                    </div>
                </div>
                <div class="wid-peity mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">Completed</p>
                        <h5 class="mb-0 text-success">{{ $completedTrainings ?? 0 }}</h5>
                    </div>
                </div>
                <div class="wid-peity mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">Ongoing</p>
                        <h5 class="mb-0 text-warning">{{ $ongoingTrainings ?? 0 }}</h5>
                    </div>
                </div>
                <div class="wid-peity">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">Planned</p>
                        <h5 class="mb-0 text-info">{{ $plannedTrainings ?? 0 }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<!-- Training Table Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mt-0 header-title">Training This Month</h4>
                    <a href="{{ route('trainings.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus mr-1"></i>Add Training
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainingsThisMonth ?? [] as $training)
                            <tr>
                                <td>{{ $training->employee->name ?? 'N/A' }}</td>
                                <td>{{ $training->title }}</td>
                                <td><span class="badge badge-info">{{ $training->category }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($training->start_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($training->end_date)->format('M d, Y') }}</td>
                                <td>
                                    @if($training->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                    @elseif($training->status == 'ongoing')
                                    <span class="badge badge-warning">Ongoing</span>
                                    @elseif($training->status == 'planned')
                                    <span class="badge badge-info">Planned</span>
                                    @else
                                    <span class="badge badge-danger">Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No training records for this month.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
@endsection

@section('script')
<!--Chartist Chart-->
<script src="{{ URL::asset('plugins/chartist/js/chartist.min.js') }}"></script>
<script src="{{ URL::asset('plugins/chartist/js/chartist-plugin-tooltip.min.js') }}"></script>
<!-- peity JS -->
<script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
<script>
    // Chart data from server
    window.chartLabelsData = <?php echo json_encode($chartLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    window.chartDataData = <?php echo json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    // Training chart data
    window.trainingChartLabelsData = <?php echo json_encode($trainingChartLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    window.trainingChartDataData = <?php echo json_encode($trainingChartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    $(document).ready(function() {
        //Line chart with area - using real data
        var chartLabels = window.chartLabelsData;
        var chartData = window.chartDataData;

        if (document.querySelector("#chart-with-area")) {
            new Chartist.Line(
                "#chart-with-area", {
                    labels: chartLabels,
                    series: [chartData],
                }, {
                    low: 0,
                    showArea: true,
                    plugins: [Chartist.plugins.tooltip()],
                }
            );
        }

        // Training chart
        var trainingChartLabels = window.trainingChartLabelsData;
        var trainingChartData = window.trainingChartDataData;

        if (document.querySelector("#training-chart")) {
            new Chartist.Line(
                "#training-chart", {
                    labels: trainingChartLabels,
                    series: [trainingChartData],
                }, {
                    low: 0,
                    showArea: true,
                    plugins: [Chartist.plugins.tooltip()],
                    lineSmooth: Chartist.Interpolation.cardinal({
                        tension: 0.5
                    })
                }
            );
        }

        // Peity donut charts
        $(".peity-donut").each(function() {
            $(this).peity("donut", $(this).data());
        });

        // Peity line charts
        $(".peity-line").each(function() {
            $(this).peity("line", $(this).data());
        });
    });
</script>
@endsection