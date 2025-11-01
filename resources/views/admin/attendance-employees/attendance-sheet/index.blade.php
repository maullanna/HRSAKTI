@extends('layouts.master')
@section('content')

    <div class="card">
        <div class="card-header bg-success text-white">
            TimeTable
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm" id="printTable">
                    <thead>
                        <tr >

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
                            <th style="">
                            
                                
                                    {{ $date }}
                            
                        </th>
                      

                            @endforeach

                        </tr>
                    </thead>

                    <tbody>





                        @foreach ($employees as $employee)

                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>{{ $employee->id_employees }}</td>






                                @for ($i = 1; $i < $today->daysInMonth + 1; ++$i)


                                    @php
                                        
                                        $date_picker = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
                                        
                                        $check_attd = \App\Models\Attendance::query()
                                            ->where('emp_id', $employee->id_employees)
                                            ->where('attendance_date', $date_picker)
                                            ->first();
                                        
                                    @endphp
                                    <td>
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
                                    </td>

                                @endfor
                            </tr>
                        @endforeach





                    </tbody>


                </table>
            </div>
        </div>
    </div>
@endsection

