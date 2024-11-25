@extends('layouts.app')
@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/LOGO_KKI.png') }}" alt="AdminLTELogo"
            height="60" width="60">
    </div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Departments Users</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Departments Users</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="/depuser/create" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus"></i> <b>Add New</b>
                </a>
                <div class="card-tools mt-2 mr-1">
                    <form action="/department">
                        <div class="input-group input-group-sm ml-2 d-inline-flex" style="width: 150px;">
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
                            <th class="text-left">Employee</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th style="border-top-right-radius: 10px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($depUsers as $item)
                            <tr class="hover-highlight">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $item->user->name }}</td>
                                <td class="text-center">
                                    @if ($item->user->role == 1)
                                        Pengurus
                                    @elseif ($item->user->role == 3)
                                        Manager
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->department->name }}</td>
                                <td class="text-center">
                                    <a href="/depuser/edit/{{ $item->id }}" class="btn btn-sm btn-warning shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/depuser/destroy/{{ $item->id }}" class="btn btn-sm btn-danger shadow-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No departments users found</td>
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
                {{ $depUsers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>

@endsection
