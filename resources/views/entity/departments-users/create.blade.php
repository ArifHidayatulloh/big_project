@extends('layouts.app')
@section('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Department User</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Departments Users</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content pb-2">
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-header text-center"
                style="background: linear-gradient(to right, #007bff, #00c6ff); color: white; border-radius: 15px 15px 0 0;">
                <h4 class="m-0">New Department User</h4>
            </div>
            <form action="/depuser/store" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="user_search">Employee <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="user_search" name="user_search"
                                    placeholder="Search employee..." autocomplete="off">
                                <div id="user_dropdown" class="dropdown-menu" style="display: none;"></div>
                            </div>
                            <div class="form-group">
                                <label for="department">Department <span class="text-danger">*</span></label>
                                <select class="custom-select form-control" id="department" name="unit_id" required>
                                    <option disabled selected>Select department</option>
                                    @forelse ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @empty
                                        <option value="">No department found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Buttons Section -->
                    <div class="d-flex justify-content-between mt-4 align-items-center">
                        <a href="/depuser" class="btn btn-secondary btn-m">
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
@section('styles')
    <style>
        #user_dropdown {
            position: absolute;
            z-index: 1000;
            /* Pastikan dropdown di atas elemen lain */
            width: calc(100% - 1rem);
            /* Sesuaikan dengan lebar input */
            max-height: 200px;
            /* Batas tinggi dropdown */
            overflow-y: auto;
            /* Scroll jika terlalu banyak item */
            background-color: white;
            /* Pastikan latar belakang dropdown putih */
            border: 1px solid #ced4da;
            /* Tambahkan border untuk dropdown */
            border-radius: 0.25rem;
            /* Tambahkan border-radius untuk dropdown */
            display: none;
            /* Sembunyikan dropdown secara default */
            top: calc(100% + 0.5rem);
            /* Menempatkan dropdown tepat di bawah input */
            left: 0;
            /* Mengatur posisi dropdown agar sejajar dengan input */
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#user_search').on('keyup', function() {
                let query = $(this).val();

                if (query.length > 0) {
                    $.ajax({
                        url: '/search-user',
                        method: 'GET',
                        data: {
                            term: query
                        },
                        success: function(data) {
                            $('#user_dropdown').empty();

                            if (data.length > 0) {
                                data.forEach(function(user) {
                                    $('#user_dropdown').append(
                                        '<a href="#" class="dropdown-item" data-id="' +
                                        user.id + '">' + user.name + '</a>'
                                    );
                                });
                                $('#user_dropdown').show(); // Tampilkan dropdown
                            } else {
                                $('#user_dropdown').append(
                                    '<a href="#" class="dropdown-item disabled">No users found</a>'
                                );
                                $('#user_dropdown').show();
                            }
                        }
                    });
                } else {
                    $('#user_dropdown').hide(); // Sembunyikan dropdown jika input kosong
                }
            });

            $(document).on('click', '.dropdown-item', function(e) {
                e.preventDefault();
                let userName = $(this).text();
                let userId = $(this).data('id');

                $('#user_search').val(userName);
                $('#user_id').remove();

                $('<input>').attr({
                    type: 'hidden',
                    id: 'user_id',
                    name: 'user_id',
                    value: userId
                }).appendTo('form');

                $('#user_dropdown').hide(); // Sembunyikan dropdown setelah memilih
            });

            // Sembunyikan dropdown jika klik di luar dropdown
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#user_search, #user_dropdown').length) {
                    $('#user_dropdown').hide();
                }
            });
        });
    </script>
@endsection
