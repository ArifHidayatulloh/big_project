@extends('layouts.app')
@section('content')
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
                        <li class="breadcrumb-item active">New</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content pb-2">
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-header text-center" style="background: linear-gradient(to right, #007bff, #00c6ff); color: white; color: white; border-radius: 15px 15px 0 0;">
                <h4 class="m-0">New Employee</h4>
            </div>
            <form action="/user/store" method="post">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Input Name in its own row -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-m" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nik">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-m" id="nik" name="nik" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender <span class="text-danger">*</span></label>
                                <div class="d-flex" style="gap: 10px; margin-left:30px; margin-top:5px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="L" required>
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="P" required>
                                        <label class="form-check-label">Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-2">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control form-control-m" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control form-control-m" id="email" name="email">
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control form-control-m" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Position <span class="text-danger">*</span></label>
                                <select class="custom-select form-control-m" id="role" name="role" required>
                                    <option disabled selected>Select position</option>
                                    <option value="1">Pengurus</option>
                                    <option value="2">General Manager</option>
                                    <option value="3">Manager</option>
                                    <option value="4">KA Unit</option>
                                    <option value="5">Staff</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="join_date">Join Date</label>
                                <input type="date" class="form-control form-control-m" id="join_date" name="join_date">
                            </div>
                            <div class="form-group">
                                <label for="unit">Department</label>
                                <select class="custom-select form-control-m" id="unit" name="unit_id" required>
                                    <option disabled selected>Select department</option>
                                    @forelse ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @empty
                                        <option value="">No department found</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control form-control-m" rows="5" placeholder="Address" name="address"></textarea>
                            </div>

                        </div>
                    </div>
                    <!-- Buttons Section -->
                    <div class="d-flex justify-content-between mt-4 align-items-center">
                        <a href="/user" class="btn btn-secondary btn-m">
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
