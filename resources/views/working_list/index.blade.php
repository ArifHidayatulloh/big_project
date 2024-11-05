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
                    <h1 class="m-0">Working List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Working List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <a href="/working-list/create" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus"></i> <b>Working List</b>
                </a>
                <button class="btn btn-link p-0 ml-auto" type="button" data-toggle="collapse" data-target="#filterCollapse"
                    aria-expanded="false" aria-controls="filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>

            <div class="collapse" id="filterCollapse">
                <div class="card-body">
                    <!-- Filter Section -->
                    <form action="/working-list" method="GET">
                        <div class="row">
                            <!-- Filter Department -->
                            <div class="col-md-4 form-group">
                                <label for="department">Department:</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ request('department') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter PIC -->
                            <div class="col-md-4 form-group">
                                <label for="pic">PIC:</label>
                                <select name="pic" id="pic" class="form-control form-control-sm">
                                    <option value="">Select PIC</option>
                                    @foreach ($pics as $pic)
                                        <option value="{{ $pic->id }}"
                                            {{ request('pic') == $pic->id ? 'selected' : '' }}>
                                            {{ $pic->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Status -->
                            <div class="col-md-4 form-group">
                                <label for="status">Status:</label>
                                <div>
                                    <label><input type="checkbox" name="status[]" value="Outstanding"
                                            {{ is_array(request('status')) && in_array('Outstanding', request('status')) ? 'checked' : '' }}>
                                        Outstanding</label>
                                    <label><input type="checkbox" name="status[]" value="On Progress"
                                            {{ is_array(request('status')) && in_array('On Progress', request('status')) ? 'checked' : '' }}>
                                        On Progress</label>
                                    <label><input type="checkbox" name="status[]" value="Done"
                                            {{ is_array(request('status')) && in_array('Done', request('status')) ? 'checked' : '' }}>
                                        Done</label>
                                    <label><input type="checkbox" name="status[]" value="Requested"
                                            {{ is_array(request('status')) && in_array('Requested', request('status')) ? 'checked' : '' }}>
                                        Requested</label>
                                    <label><input type="checkbox" name="status[]" value="Rejected"
                                            {{ is_array(request('status')) && in_array('Rejected', request('status')) ? 'checked' : '' }}>
                                        Rejected</label>
                                </div>
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
                            <div class="col-md-4 form-group d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter"></i> Apply
                                </button>
                                <a href="/working-list" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                    <!-- End of Filter Section -->
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <!-- Export Button -->
        <div class="card">
            <div class="card-footer d-flex align-items-center justify-content-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-dark dropdown-toggle rounded-0" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <form action="/export/working_list" method="GET">
                            @if (is_array(request('status')))
                                @foreach (request('status') as $status)
                                    <input type="hidden" name="status[]" value="{{ $status }}">
                                @endforeach
                            @else
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <input type="hidden" name="dep_code" value="{{ request('dep_code') }}">
                            <input type="hidden" name="pic" value="{{ request('pic') }}">

                            <!-- Filter by created_at date range -->
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
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-body table-responsive p-0"
                style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border-radius: 10px;">
                <table class="table table-hover table-striped text-nowrap">
                    <thead style="background: linear-gradient(to right, #007bff, #00c6ff); color: white;">
                        <tr class="text-center">
                            <th style="border-top-left-radius: 10px;">#</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Working List</th>
                            <th class="text-center">PIC</th>
                            <th class="text-center">Related PIC</th>
                            <th class="text-center">Deadline</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Status Comment</th>
                            <th class="text-center">Score</th>
                            <th style="border-top-right-radius: 10px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($workingLists as $item)
                            <tr class="hover-highlight {{ $item->is_priority == 1 ? 'table-warning' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->department->name }}</td>
                                <td class="text-center working-list-col" title="{{ $item->name }}"
                                    data-toggle="tooltip">{{ $item->name }}</td>
                                <td>{{ $item->picUser->name }}</td>
                                <td>
                                    @if ($item->relatedpic)
                                        @foreach ($item->relatedPicNames as $relpic)
                                            {{ $relpic }}<br>
                                        @endforeach
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}
                                    <br>
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($item->deadline)->format('g:i A') }}
                                </td>
                                <td>
                                    @if ($item->status == 'Outstanding')
                                        <span class="badge badge-danger">Outstanding</span>
                                    @elseif($item->status == 'On Progress')
                                        <span class="badge badge-warning">On Progress</span>
                                    @elseif($item->status == 'Done')
                                        <span class="badge badge-success">Done</span>
                                    @elseif($item->status == 'Requested')
                                        <span class="badge badge-info">Requested</span>
                                    @else
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                                <td style="text-transform: capitalize">
                                    @if ($item->status_comment != null)
                                        {{ $item->status_comment }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($item->score != null)
                                        <p
                                            style="color: {{ $item->score >= 100 ? 'mediumspringgreen' : ($item->score >= 80 ? 'mediumseagreen' : ($item->score >= 50 ? 'slategray' : 'darkred')) }}">
                                            {{ $item->score }}</p>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="/working-list/{{ $item->id }}" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No working lists found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>

    <section class="content pb-2">
        <div class="card">
            <div class="card-footer">
                {{ $workingLists->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>

@endsection

@section('styles')
    <style>
        .table .working-list-col {
            max-width: 200px;
            /* Ganti dengan lebar maksimum yang diinginkan */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Initialize Bootstrap tooltip
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
