@extends('layouts.app')
@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/logo_koperasi_indonesia.png') }}" alt="AdminLTELogo"
            height="60" width="60">
    </div>

    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Supplier Payment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Supplier Payment </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <a href="#" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#create">
                    <i class="fas fa-plus"></i> <b>Schedule</b>
                </a>
                <button class="btn btn-link p-0 ml-auto" type="button" data-toggle="collapse" data-target="#filterCollapse"
                    aria-expanded="false" aria-controls="filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>

            <div class="collapse" id="filterCollapse">
                <div class="card-body">
                    <!-- Filter Section -->
                    <form action="/supplier-payment" method="GET">
                        <div class="row">
                            <!-- Filter Status -->
                            <div class="col-md-4 form-group">
                                <label for="status">Status:</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">Select Status</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>
                                        Paid</option>
                                </select>
                            </div>
                            <!-- Filter Date From -->
                            <div class="col-md-4 form-group">
                                <label for="from_date">From Date:</label>
                                <input type="date" name="from_date" id="from_date" class="form-control form-control-sm"
                                    value="{{ request('from_date') }}">
                            </div>

                            <!-- Filter Date To -->
                            <div class="col-md-4 form-group">
                                <label for="to_date">To Date:</label>
                                <input type="date" name="to_date" id="to_date" class="form-control form-control-sm"
                                    value="{{ request('to_date') }}">
                            </div>

                            <!-- Submit and Reset Buttons -->
                            <div class="col-md-12 form-group d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-primary btn-sm mr-2">
                                    <i class="fas fa-filter"></i> Apply
                                </button>
                                <a href="/working-list" class="btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
    </section>

    <section class="content">
        {{-- Export Button --}}
        <div class="card">
            <div class="card-footer d-flex align-items-center justify-content-end">
                <div class="btn-group">
                    <button class="btn btn-dark dropdown-toggle rounded-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <form action="/export/payment_schedule" method="GET">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                            <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                            <button type="submit" name="format" value="excel" class="dropdown-item">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </form>
                        <form action="/export-working-list" method="GET">
                            <button type="submit" name="format" value="pdf" class="dropdown-item">
                                <i class="fas fa-file-pdf"></i> Export to PDF
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="content">
        <div class="card shadow-sm" style="border-radius:15px;">
            <div class="card-body table-responsive p-0" style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border-radius: 10px;">
                <table class="table table-hover table-striped text-nowrap">
                    <thead style="background: linear-gradient(to right, #007bff, #00c6ff); color: white;">
                        <tr class="text-center">
                            <th style="border-top-leftlradiues:10px;">#</th>
                            <th class="text-center">Supplier Name</th>
                            <th class="text-center">Payment Amount</th>
                            <th class="text-center">Purchase Date</th>
                            <th class="text-center">Due Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Attachment</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr class="hover-highlight">
                            <td>1</td>
                            <td class="text-left">Supplier A</td>
                            <td>Rp 100.000</td>
                            <td>10 Oct 2024</td>
                            <td>20 Oct 2024</td>
                            <td>Pending</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="text-center">
                                <a href="/department/edit/" class="btn btn-sm btn-warning shadow-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/department/destroy/"
                                    class="btn btn-sm btn-danger shadow-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="hover-highlight">
                            <td>2</td>
                            <td class="text-left">Supplier A</td>
                            <td>Rp 100.000</td>
                            <td>10 Oct 2024</td>
                            <td>20 Oct 2024</td>
                            <td>
                                <span class="badge badge-danger text-md">Pending</span>
                            </td>
                            <td>-</td>
                            <td>-</td>
                            <td class="text-center">
                                <a href="/department/edit/" class="btn btn-sm btn-warning shadow-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/department/destroy/"
                                    class="btn btn-sm btn-danger shadow-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="hover-highlight">
                            <td>3</td>
                            <td class="text-left">Supplier A</td>
                            <td>Rp 100.000</td>
                            <td>10 Oct 2024</td>
                            <td>20 Oct 2024</td>
                            <td>
                                <span class="badge badge-success text-md">Paid</span>
                            </td>
                            <td><a href=""><i class="fas fa-file-pdf"></i> Bukti</a></td>
                            <td>Dibayar</td>
                            <td class="text-center">
                                <a href="/department/edit/" class="btn btn-sm btn-warning shadow-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/department/destroy/"
                                    class="btn btn-sm btn-danger shadow-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </section>
@endsection
