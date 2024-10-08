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
                    <h1 class="m-0">Departments</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Departments</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="/department/create" class="btn btn-dark"><i class="fas fa-plus"></i></a>
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
                <table class="table table-head-fixed table-striped text-nowrap">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th class="text-left">Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($departments as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-left">{{ $item->name }}</td>
                                <td>
                                    <a href="/department/edit/{{ $item->id }}" class="btn btn-warning"><i
                                            class="fas fa-pen"></i></a>
                                    <a href="/department/destroy/{{ $item->id }}" class="btn btn-danger"><i
                                            class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No departments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <!-- Optional pagination or footer here -->
            </div>
        </div>
        <!-- /.card -->
    </section>
@endsection
