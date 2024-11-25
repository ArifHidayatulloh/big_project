@extends('layouts.app')

@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/LOGO_KKI.png') }}" alt="AdminLTELogo" height="60"
            width="60">
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
                @if (Auth::user()->role == 1 || Auth::user()->role == 2)
                    <a href="/working-list/create" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus"></i> <b>Working List</b>
                    </a>
                @endif

                <button type="button" class="btn btn-dark dropdown-toggle ml-auto shadow-sm" data-toggle="dropdown"
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
    </section>

    <section class="content pb-2 text-sm">
        <form action="/working-list" method="GET">
            <div class="row g-2 justify-content-end">
                <!-- Filter Date From -->
                <div class="col-lg-2 col-md-3">
                    <label for="from_date" class="form-label fw-bold mb-1">From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control form-control-sm"
                        value="{{ request('from_date') }}">
                </div>

                <!-- Filter Date To -->
                <div class="col-lg-2 col-md-3">
                    <label for="to_date" class="form-label fw-bold mb-1">To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control form-control-sm"
                        value="{{ request('to_date') }}">
                </div>

                <!-- Buttons -->
                <div class="col-lg-1 col-md-12 text-end pt-md-4 pt-lg-4">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="/working-list" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sync"></i>
                    </a>
                </div>
            </div>
        </form>
    </section>



    <section class="content">
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-body table-responsive p-0"
                style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border-radius: 10px;">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead style="background: #007bff; color: white;" class="text-sm">
                        <tr class="text-center">
                            <th style="border-top-left-radius: 10px;">#</th>
                            <th class="text-center">
                                Department
                                <a href="#" data-toggle="dropdown" aria-expanded="false" class="text-white"><i class="fas fa-sort-down"></i></a>

                                <!-- Dropdown menu untuk Department -->
                                <div class="dropdown-menu dropdown-th dropdown-menu-right"
                                    aria-labelledby="dropdownMenuButton">
                                    @foreach ($departments as $department)
                                        <a class="dropdown-item"
                                            href="{{ url('/working-list') . '?' . http_build_query(array_merge(request()->query(), ['department' => $department->id])) }}">
                                            {{ $department->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </th>
                            <th class="text-center">Working List</th>
                            <th class="text-center">
                                PIC
                                <a href="#" data-toggle="dropdown" aria-expanded="false" class="text-white"><i class="fas fa-sort-down"></i></a>
                                <!-- Dropdown menu untuk PIC -->
                                <div class="dropdown-menu dropdown-th dropdown-menu-right"
                                    aria-labelledby="dropdownMenuButton">
                                    @foreach ($pics as $pic)
                                        <a class="dropdown-item"
                                            href="{{ url('/working-list') . '?' . http_build_query(array_merge(request()->query(), ['pic' => $pic->id])) }}">{{ $pic->name }}</a>
                                    @endforeach
                                </div>
                            </th>
                            <th class="text-center">Related PIC</th>
                            <th class="text-center">
                                Deadline
                                <a href="{{ url('/working-list') . '?' . http_build_query(array_merge(request()->query(), ['sort_by' => 'deadline', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white">
                                    <i class="fas fa-sort{{ request('sort_by') == 'deadline' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }}"></i>
                                </a>
                            </th>
                            <th class="text-center">Status
                                <a href="#" data-toggle="dropdown" aria-expanded="false" class="text-white"><i class="fas fa-sort-down"></i></a>
                                <!-- Dropdown menu untuk Status -->
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <form action="/working-list" method="GET" class="d-inline-block">
                                        <div class="px-3 py-2">
                                            <!-- Checkbox untuk setiap status -->
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="status_outstanding"
                                                    name="status[]" value="Outstanding"
                                                    {{ in_array('Outstanding', request('status', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="status_outstanding">Outstanding</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="status_on_progress"
                                                    name="status[]" value="On Progress"
                                                    {{ in_array('On Progress', request('status', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_on_progress">On
                                                    Progress</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="status_done"
                                                    name="status[]" value="Done"
                                                    {{ in_array('Done', request('status', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_done">Done</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="status_requested"
                                                    name="status[]" value="Requested"
                                                    {{ in_array('Requested', request('status', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_requested">Requested</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="status_rejected"
                                                    name="status[]" value="Rejected"
                                                    {{ in_array('Rejected', request('status', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_rejected">Rejected</label>
                                            </div>
                                        </div>
                                        <div class="px-3 py-2">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                Apply Filter
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </th>

                            <th class="text-center">Status Comment</th>
                            <th class="text-center">
                                Score
                                <a href="{{ url('/working-list') . '?' . http_build_query(array_merge(request()->query(), ['sort_by' => 'score', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white">
                                    <i class="fas fa-sort{{ request('sort_by') == 'score' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }}"></i>
                                </a>
                            </th>
                            <th style="border-top-right-radius: 10px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-sm">
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
                                    {{ \Carbon\Carbon::parse($item->deadline)->format('H:i') }}
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

        .dropdown-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-th {
            height: 200px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.fa-sort-down').on('click', function() {
                $(this).next('.dropdown-menu').toggleClass('show');
            });

            // Untuk menutup dropdown jika klik di luar dropdown
            $(document).click(function(event) {
                if (!$(event.target).closest('.dropdown-menu, .fa-sort-down').length) {
                    $('.dropdown-menu').removeClass('show');
                }
            });
        });

        $(function() {
            // Initialize Bootstrap tooltip
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
