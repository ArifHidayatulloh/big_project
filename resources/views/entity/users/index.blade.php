@extends('layouts.app')
@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/logo_koperasi_indonesia.png') }}" alt="AdminLTELogo"
            height="60" width="60">
    </div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Employee</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Employee</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="/user/create" class="btn btn-primary"><i class="fas fa-plus"></i> <b>Employee</b></a>
                <div class="card-tools mt-2 mr-1">
                    <form action="/user">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="search" class="form-control float-right" placeholder="Search..." name="search" value="{{ $search }}">
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
        <div class="card">
            <!-- Tabel terpisah dari card header -->
            <div class="card-body table-responsive p-0" style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border-radius: 10px;">
                <table class="table table-hover table-striped text-nowrap">
                    <thead style="background: linear-gradient(to right, #007bff, #00c6ff); color: white;">
                        <tr class="text-center">
                            <th style="border-top-left-radius: 10px;">#</th>
                            <th class="text-left">Name</th>
                            <th>NIK</th>
                            <th>Position</th>
                            <th style="border-top-right-radius: 10px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $item)
                            <tr class="hover-highlight">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $item->name }}</td>
                                <td class="text-center">{{ $item->nik }}</td>
                                <td class="text-center">
                                    @if ($item->role == 1)
                                        Pengurus
                                    @elseif ($item->role == 2)
                                        General Manager
                                    @elseif ($item->role == 3)
                                        Manager
                                    @elseif ($item->role == 4)
                                        KA Unit
                                    @else
                                        Staff
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm shadow-sm" data-toggle="modal"
                                        data-target="#modal-info{{ $item->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="/user/edit/{{ $item->id }}" class="btn btn-warning btn-sm shadow-sm"><i class="fas fa-edit"></i></a>
                                    <a href="/user/destroy/{{ $item->id }}" class="btn btn-danger btn-sm shadow-sm"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>

                            <!-- Modal for each user -->
                            <div class="modal fade" id="modal-info{{ $item->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="modal-info-label{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background: linear-gradient(to right, #007bff, #00c6ff);">
                                            <h5 class="modal-title text-white" id="modal-info-label{{ $item->id }}">User Information</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4 text-center">
                                                    @if ($item->gender == 'P')
                                                        <img class="profile-user-img img-fluid img-circle"
                                                            src="{{ asset('assets/images/female_icon.png') }}" alt="User profile picture">
                                                    @else
                                                        <img class="profile-user-img img-fluid img-circle"
                                                            src="{{ asset('assets/images/male_icon.png') }}" alt="User profile picture">
                                                    @endif
                                                    <h3 class="profile-username">{{ $item->name }}</h3>
                                                    <p class="text-muted">{{ $item->nik }}</p>
                                                </div>
                                                <div class="col-md-8">
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>Phone:</strong> <span class="float-right">{{ $item->phone }}</span></li>
                                                        <li class="list-group-item"><strong>Email:</strong> <span class="float-right">{{ $item->email }}</span></li>
                                                        <li class="list-group-item"><strong>Position:</strong> <span class="float-right">
                                                            @if ($item->role == 1) Pengurus
                                                            @elseif ($item->role == 2) General Manager
                                                            @elseif ($item->role == 3) Manager
                                                            @elseif ($item->role == 4) KA Unit
                                                            @else Staff @endif
                                                        </span></li>
                                                        <li class="list-group-item"><strong>Gender:</strong> <span class="float-right">{{ $item->gender == 'L' ? 'Male' : 'Female' }}</span></li>
                                                        <li class="list-group-item"><strong>Address:</strong> <span class="float-right">{{ $item->address }}</span></li>
                                                        <li class="list-group-item"><strong>Join Date:</strong> <span class="float-right">
                                                            @if ($item->join_date != null)
                                                                {{ Carbon\Carbon::parse($item->join_date)->format('d M Y') }}
                                                            @endif
                                                        </span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No employees found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->

        </div>
    </section>

    <section class="content pb-2">
        <div class="card">
            <div class="card-footer clearfix pt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>

@endsection
