@extends('layouts.app')

@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/LOGO_KKI.png') }}" alt="AdminLTELogo"
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
                <div class="card-tools mt-2 mr-1">
                    <form action="/working-list">
                        <div class="input-group input-group-sm ml-2 d-inline-flex" style="width: 200px;">
                            <input type="search" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-body table-responsive p-0" style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border-radius: 10px;">
                <table class="table table-hover table-bordered text-nowrap text-sm">
                    <thead style="background: #007bff; color: white;">
                        <tr class="text-center">
                            <th style="border-top-left-radius: 10px;">#</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Working List</th>
                            <th class="text-center">PIC</th>
                            <th class="text-center">Related PIC</th>
                            <th class="text-center">Deadline</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Request At</th>
                            <th style="border-top-right-radius: 10px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($workingLists as $item)
                            <tr class="hover-highlight">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->department->name }}</td>
                                <td class="text-center working-list-col">{{ $item->name }}</td>
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
                                    <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($item->deadline)->format('H:i') }}
                                </td>
                                <td>
                                    @if ($item->status == 'Overdue')
                                        <span class="badge badge-danger">Overdue</span>
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
                                <td>
                                    {{ \Carbon\Carbon::parse($item->request_at)->format('d M Y') }}
                                    <br>
                                    <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($item->request_at)->format('H:i') }}
                                </td>
                                <td>
                                    <a href="/need_approval/{{ $item->id }}" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No working lists found</td>
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
