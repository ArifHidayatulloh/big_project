@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Employee</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Employees</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="card card-secondary card-outline">
            <!-- /.card-header -->
            <form action="/user/store" method="post">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-border border-width-2" id="name"
                                    name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="nik">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-border border-width-2" id="nik"
                                    name="nik" required>
                            </div>
                            <div class="form-group">
                                <label for="nik">Gender <span class="text-danger">*</span></label>
                                <div class="choice d-flex" style="gap: 10px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="L"
                                            required>
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="P"
                                            required>
                                        <label class="form-check-label">Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="number" class="form-control form-control-border border-width-2" id="phone"
                                    name="phone">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control form-control-border border-width-2" id="email"
                                    name="email">

                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-border border-width-2"
                                    id="password" name="password" required>

                            </div>
                            <div class="form-group">
                                <label for="role">Position <span class="text-danger">*</span></label>
                                <select class="custom-select form-control-border border-width-2" id="role"
                                    name="role">
                                    <option disabled selected >Select position</option>
                                    <option value="1">Pengurus</option>
                                    <option value="2">General Manager</option>
                                    <option value="3">Manager</option>
                                    <option value="4">KA Unit</option>
                                    <option value="5">Staff</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="join_date">Join Date</label>
                                <input type="date" class="form-control form-control-border border-width-2" id="date"
                                    name="join_date">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control form-control-border border-width-2" rows="2" placeholder="Address" name="address"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="unit">Department</label>
                                <select class="custom-select form-control-border border-width-2" id="unit"
                                    name="unit_id">
                                    <option disabled selected>Select department</option>
                                    @forelse ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @empty
                                        <option value="">No department found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-secondary">SAVE</button>
                    <a href="/user" class="btn btn-default float-right">CANCEL</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection
