@extends('layouts.master')

@section('css')
<!-- Table css -->
<link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet"
    type="text/css" media="screen">
@endsection


@section('content')

<div class="card">

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-responsive table-bordered table-sm">
                <thead>
                    <tr>

                        <th>Employee Name</th>
                        <th>Employee Position</th>
                        <th>Employee ID</th>
                        @php
                        $today = today();
                        $dates = [];

                        for ($i = 1; $i < $today->daysInMonth + 1; ++$i) {
                            $dates[] = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
                            }

                            @endphp
                            @foreach ($dates as $date)
                            <th>
                                {{ $date }}
                            </th>

                            @endforeach

                    </tr>
                </thead>

                <tbody>
                    @if(!Auth::guard('employee')->check())
                    <form action="{{ route('check_store') }}" method="post">
                        <button type="submit" class="btn btn-success" style="display: flex; margin:10px">Submit</button>
                        @csrf
                        @endif
                        @foreach ($employees as $employee)
                        @if(!Auth::guard('employee')->check())
                        <input type="hidden" name="emp_id" value="{{ $employee->id_employees }}">
                        @endif
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>{{ $employee->id_employees }}</td>






                            @for ($i = 1; $i < $today->daysInMonth + 1; ++$i)


                                @php
                                $date_picker = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
                                $date_obj = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->startOfDay();
                                $today_start = now()->startOfDay();
                                $is_future_date = $date_obj->gt($today_start);

                                $check_attd = \App\Models\Attendance::query()
                                ->where('emp_id', $employee->id_employees)
                                ->where('attendance_date', $date_picker)
                                ->where('type', 0) // Hanya check-in (type = 0)
                                ->first();
                                @endphp
                                <td>
                                    @if($is_future_date)
                                    {{-- Tanggal belum terjadi - tampilkan checkbox kosong --}}
                                    @if(!Auth::guard('employee')->check())
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input class="form-check-input"
                                                name="attd[{{ $date_picker }}][{{ $employee->id_employees }}]"
                                                type="checkbox"
                                                value="1">
                                            <span class="ml-2">Hadir</span>
                                        </label>
                                    </div>
                                    @else
                                    <div class="text-center">
                                        <span class="text-muted">-</span>
                                    </div>
                                    @endif
                                    @else
                                    {{-- Tanggal sudah lewat - tampilkan status --}}
                                    @if(!Auth::guard('employee')->check())
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input class="form-check-input"
                                                name="attd[{{ $date_picker }}][{{ $employee->id_employees }}]"
                                                type="checkbox"
                                                @if (isset($check_attd)) checked @endif
                                                value="1">
                                            <span class="ml-2">Hadir</span>
                                        </label>
                                    </div>
                                    @else
                                    <div class="text-center">
                                        @if (isset($check_attd))
                                        @if ($check_attd->status == 1)
                                        <i class="fa fa-check text-success" title="Hadir - On Time"></i>
                                        @else
                                        <i class="fa fa-check text-danger" title="Hadir - Late"></i>
                                        @endif
                                        @else
                                        <i class="fas fa-times text-danger" title="Tidak Hadir"></i>
                                        @endif
                                    </div>
                                    @endif
                                    @endif
                                </td>

                                @endfor
                        </tr>
                        @endforeach
                        @if(!Auth::guard('employee')->check())
                    </form>
                    @endif


                </tbody>


            </table>
        </div>
    </div>
</div>
@endsection