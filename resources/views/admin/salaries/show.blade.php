@extends('layouts.master')

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Salary Details</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('salaries.index') }}">Salaries</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
</div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Detail Gaji - {{ $salary->employee->name }}</h5>
                <div class="card-tools">
                    <a href="{{ route('salaries.edit', $salary) }}" class="btn btn-warning btn-sm">
                        <i class="mdi mdi-pencil mr-1"></i>Edit
                    </a>
                    <a href="{{ route('salaries.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left mr-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Karyawan</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nama:</strong></td>
                                <td>{{ $salary->employee->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Employee ID:</strong></td>
                                <td>{{ $salary->employee->id_employees }}</td>
                            </tr>
                            <tr>
                                <td><strong>Posisi:</strong></td>
                                <td>{{ $salary->employee->position ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bulan:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($salary->month)->format('F Y') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h6>Rincian Gaji</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Gaji Pokok:</strong></td>
                                <td class="text-right">Rp {{ number_format($salary->basic_salary, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Tunjangan:</strong></td>
                                <td class="text-right text-success">+ Rp {{ number_format(array_sum($salary->allowances), 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Potongan:</strong></td>
                                <td class="text-right text-danger">- Rp {{ number_format(array_sum($salary->deductions), 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Gaji Bersih:</strong></td>
                                <td class="text-right"><strong class="text-primary">Rp {{ number_format($salary->net_salary, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if(count($salary->allowances) > 0)
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6>Tunjangan Detail</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Jenis Tunjangan</th>
                                        <th class="text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salary->allowances as $key => $value)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                        <td class="text-right">Rp {{ number_format($value, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if(count($salary->deductions) > 0)
                    <div class="col-md-6">
                        <h6>Potongan Detail</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Jenis Potongan</th>
                                        <th class="text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salary->deductions as $key => $value)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                        <td class="text-right">Rp {{ number_format($value, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-4">
                    <div class="col-12">
                        <h6>Informasi Sistem</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Dibuat:</strong></td>
                                <td>{{ $salary->created_at->format('d F Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Diperbarui:</strong></td>
                                <td>{{ $salary->updated_at->format('d F Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection