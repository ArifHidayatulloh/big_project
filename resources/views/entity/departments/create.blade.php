@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Department</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Department</li>
                        <li class="breadcrumb-item active">New</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

     <!-- /.content-header -->
     <section class="content pb-2">
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-header text-center" style="background: linear-gradient(to right, #007bff, #00c6ff); color: white; border-radius: 15px 15px 0 0;">
                <h4 class="m-0">New Department</h4>
            </div>
            <form action="/department/store" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Department <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-m" id="name" placeholder="Department Name" name="name" required>
                            </div>
                        </div>
                    </div>
                    <!-- Buttons Section -->
                    <div class="d-flex justify-content-between mt-4 align-items-center">
                        <a href="/department" class="btn btn-secondary btn-m">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-m">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
