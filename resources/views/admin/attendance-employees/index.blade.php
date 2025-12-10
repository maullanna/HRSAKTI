@extends('layouts.master')

@section('css')
<!-- Table css -->
<link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Attendance</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Attendance</a></li>


    </ol>
</div>
@endsection
@section('button')
{{-- Attendance is automatically recorded, no manual add button needed --}}
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{-- Filter Form --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('attendance') }}" class="form-inline">
                            <div class="form-group mr-2 mb-2">
                                <label for="date_from" class="mr-2">Dari Tanggal:</label>
                                <input type="date" name="date_from" id="date_from"
                                    class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="form-group mr-2 mb-2">
                                <label for="date_to" class="mr-2">Sampai Tanggal:</label>
                                <input type="date" name="date_to" id="date_to"
                                    class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <button type="submit" class="btn btn-primary mr-2 mb-2">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                            <a href="{{ route('attendance') }}" class="btn btn-secondary mb-2">
                                <i class="mdi mdi-refresh"></i> Reset
                            </a>
                        </form>
                    </div>
                </div>

                {{-- Info Total Records & Performance --}}
                <div class="row mb-2">
                    <div class="col-md-8">
                        <p class="text-muted mb-0">
                            @if(method_exists($attendances, 'total') && $attendances->total())
                            Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }}
                            dari {{ number_format($attendances->total()) }} data attendance
                            @else
                            Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} data attendance
                            <small class="text-muted">(Total tidak ditampilkan untuk performa lebih cepat)</small>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 text-right">
                        @if(isset($performance))
                        <p class="text-muted mb-0">
                            <small>
                                <i class="mdi mdi-timer"></i>
                                Loading: <strong>{{ $performance['execution_time_sec'] }} detik</strong>
                                ({{ $performance['execution_time_ms'] }} ms) |
                                Memory: <strong>{{ $performance['memory_used_mb'] }} MB</strong>
                            </small>
                        </p>
                        @endif
                    </div>
                </div>

                <div class="table-rep-plugin">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                            <thead>
                                <tr>
                                    <th data-priority="1">Date</th>
                                    <th data-priority="2">Employee ID</th>
                                    <th data-priority="3">Name</th>
                                    <th data-priority="4">Attendance</th>
                                    <th data-priority="5">Time In</th>
                                    <th data-priority="6">Time Out</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date }}</td>
                                    <td>{{ $attendance->emp_id }}</td>
                                    <td>{{ $attendance->employee->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($attendance->status == 1)
                                        <span class="badge badge-primary badge-pill">On Time</span>
                                        @else
                                        <span class="badge badge-danger badge-pill">Late</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->attendance_time ?? 'N/A' }}</td>
                                    <td>{{ $attendance->time_out ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No attendance records found.</td>
                                </tr>
                                @endforelse


                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $attendances->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->

@endsection

@section('script')
<!-- Responsive-table-->
<script src="{{ URL::asset('plugins/RWD-Table-Patterns/dist/js/rwd-table.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize responsive table
        $('.table-responsive').responsiveTable({
            addDisplayAllBtn: 'btn btn-secondary'
        });

        // Initialize DataTable if available
        // DataTable pagination disabled karena menggunakan Laravel pagination
        if ($.fn.DataTable) {
            $('#datatable-buttons').DataTable({
                responsive: true,
                paging: false, // Disable DataTable pagination karena pakai Laravel pagination
                searching: true, // Tetap bisa search di halaman saat ini
                ordering: true,
                order: [
                    [0, 'desc']
                ],
                info: false, // Info sudah ditampilkan manual di atas
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "No data available",
                    paginate: {
                        next: "Next",
                        previous: "Prev"
                    }
                }
            });
        }
    });
</script>
@endsection