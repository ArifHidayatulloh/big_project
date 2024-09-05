@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Employee</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="/user">Employees</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="card card-secondary card-outline">
            <!-- /.card-header -->
            <form action="/user/update/{{ $user->id }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-border border-width-2" id="name"
                                    name="name" value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="nik">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-border border-width-2" id="nik"
                                    name="nik" value="{{ $user->nik }}" required>
                            </div>
                            <div class="form-group">
                                <label for="nik">Gender <span class="text-danger">*</span></label>
                                <div class="choice d-flex" style="gap: 10px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="L"
                                            {{ $user->gender == 'L' ? 'checked' : '' }} required>
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" value="P"
                                            {{ $user->gender == 'P' ? 'checked' : '' }} required>
                                        <label class="form-check-label">Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="number" class="form-control form-control-border border-width-2" id="phone"
                                    name="phone" value="{{ $user->phone }}">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control form-control-border border-width-2" id="email"
                                    name="email" value="{{ $user->email }}">
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control form-control-border border-width-2"
                                    id="password" name="password">
                                <small class="form-text text-muted">Leave blank if you don't want to change the
                                    password</small>
                            </div>
                            <div class="form-group">
                                <label for="role">Position <span class="text-danger">*</span></label>
                                <select class="custom-select form-control-border border-width-2" id="role"
                                    name="role">
                                    <option disabled>Select position</option>
                                    <option value="1" {{ $user->role == 1 ? 'selected' : '' }}>Pengurus</option>
                                    <option value="2" {{ $user->role == 2 ? 'selected' : '' }}>General Manager</option>
                                    <option value="3" {{ $user->role == 3 ? 'selected' : '' }}>Manager</option>
                                    <option value="4" {{ $user->role == 4 ? 'selected' : '' }}>KA Unit</option>
                                    <option value="5" {{ $user->role == 5 ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="join_date">Join Date</label>
                                <input type="date" class="form-control form-control-border border-width-2" id="join_date"
                                    name="join_date" value="{{ $user->join_date }}">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control form-control-border border-width-2" rows="2" placeholder="Address" name="address">{{ $user->address }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="unit">Department</label>
                                <select class="custom-select form-control-border border-width-2" id="unit"
                                    name="unit_id">
                                    <option disabled selected>Select department</option>
                                    @forelse ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ $user->unit_id == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}</option>
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
                    <button type="submit" class="btn btn-secondary">UPDATE</button>
                    <a href="/user" class="btn btn-default float-right">CANCEL</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection
