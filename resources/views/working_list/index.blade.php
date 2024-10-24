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
            <div class="card-header">
                <a href="/working-list/create" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus"></i> <b>Add New</b>
                </a>
                <div class="card-tools">
                    <!-- Filter Section -->
                    <div class="row ">
                        <div class="col-md-3">
                            <form action="/working-list" method="GET">
                                <select name="department" class="form-control">
                                    <option value="">Filter by Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ request('department') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                        </div>

                        <div class="col-md-3">
                            <select name="pic" class="form-control">
                                <option value="">Filter by PIC</option>
                                @foreach ($pics as $pic)
                                    <option value="{{ $pic->id }}" {{ request('pic') == $pic->id ? 'selected' : '' }}>
                                        {{ $pic->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">Filter by Status</option>
                                <option value="Outstanding" {{ request('status') == 'Outstanding' ? 'selected' : '' }}>
                                    Outstanding</option>
                                <option value="On Progress" {{ request('status') == 'On Progress' ? 'selected' : '' }}>On
                                    Progress</option>
                                <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>Done</option>
                                <option value="Requested" {{ request('status') == 'Requested' ? 'selected' : '' }}>
                                    Requested</option>
                                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="/working-list" class="btn btn-secondary">Reset</a>
                            </form>
                        </div>
                    </div>
                    <!-- End of Filter Section -->
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
                                <td class="text-center working-list-col" title="{{ $item->name }}" data-toggle="tooltip">{{ $item->name }}</td>
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
                                    @if($item->status_comment != null)
                                        {{ $item->status_comment }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @if($item->score != null)
                                    {{-- <i class="fas fa-star" style="color: {{ $item->score >= 100 ?'mediumspringgreen' : ($item->score >= 80?'mediumseagreen' : ($item->score >= 50?'slategray' : 'darkred')) }}"></i> --}}
                                    <p style="color: {{ $item->score >= 100 ?'mediumspringgreen' : ($item->score >= 80?'mediumseagreen' : ($item->score >= 50?'slategray' : 'darkred')) }}">{{ $item->score }}</p>
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
                                <td colspan="8" class="text-center">No working lists found</td>
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
    $(function () {
        // Initialize Bootstrap tooltip
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
