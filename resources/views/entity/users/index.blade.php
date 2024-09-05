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
                    <h1 class="m-0">Employees</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Employees</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="/user/create" class="btn btn-dark"><i class="fas fa-plus"></i></a>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0" style="">
                <table class="table table-head-fixed text-nowrap">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>NIK</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($users as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $item->name }}</td>
                                <td>{{ $item->nik }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>{{ $item->email }}</td>
                                <td>
                                    @if ($item->role == 1)
                                        Pengurus
                                    @endif
                                    @if ($item->role == 2)
                                        General Manager
                                    @endif
                                    @if ($item->role == 3)
                                        Manager
                                    @endif
                                    @if ($item->role == 4)
                                        KA Unit
                                    @endif
                                    @if ($item->role == 5)
                                        Staff
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#modal-info{{ $item->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>


                                    <a href="/user/edit/{{ $item->id }}" class="btn btn-warning"><i
                                            class="fas fa-pen"></i></a>
                                    <a href="/user/destroy/{{ $item->id }}" class="btn btn-danger"><i
                                            class="fas fa-trash"></i></a>
                                </td>
                            </tr>

                            <!-- Modal untuk setiap pengguna -->
                            <div class="modal fade" id="modal-info{{ $item->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="modal-info-label{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modal-info-label{{ $item->id }}">User
                                                Information</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="card card-secondary card-outline">
                                                <div class="card-body box-profile">
                                                    <div class="text-center">
                                                        @if ($item->gender == 'P')
                                                            <img class="profile-user-img img-fluid img-circle"
                                                                src="{{ asset('assets/images/female_icon.png') }}"
                                                                alt="User profile picture">
                                                        @endif
                                                        @if ($item->gender == 'L')
                                                            <img class="profile-user-img img-fluid img-circle"
                                                                src="{{ asset('assets/images/male_icon.png') }}"
                                                                alt="User profile picture">
                                                        @endif
                                                    </div>

                                                    <h3 class="profile-username text-center">{{ $item->name }}</h3>
                                                    <p class="text-muted text-center">{{ $item->nik }}</p>

                                                    <ul class="list-group list-group-unbordered mb-3">
                                                        <li class="list-group-item">
                                                            <b>Phone</b> <a class="float-right">{{ $item->phone }}</a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Email</b> <a class="float-right">{{ $item->email }}</a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Position</b> <a class="float-right">
                                                                @if ($item->role == 1)
                                                                    Pengurus
                                                                @endif
                                                                @if ($item->role == 2)
                                                                    General Manager
                                                                @endif
                                                                @if ($item->role == 3)
                                                                    Manager
                                                                @endif
                                                                @if ($item->role == 4)
                                                                    KA Unit
                                                                @endif
                                                                @if ($item->role == 5)
                                                                    Staff
                                                                @endif
                                                            </a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Gender</b> <a
                                                                class="float-right">{{ $item->gender == 'L' ? 'Male' : 'Female' }}</a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Address</b> <a class="float-right text-right"
                                                                style="width: 250px">{{ $item->address }}</a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Join Date</b> <a class="float-right">
                                                                @if ($item->join_date != null)
                                                                    {{ Carbon\Carbon::parse($item->join_date)->format('d M Y') }}
                                                                @endif
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8">No employees found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
        <!-- /.card -->
    </section>
@endsection
