@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Category</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cost Review</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content pb-3">
        <div class="card table-responsive shadow-sm">
            <div class="card-header">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-plus"></i>
                        <b>Set</b>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#addCategoryModal">New
                                Category</a></li>
                        <li><a class="dropdown-item" href="#" data-toggle="modal"
                                data-target="#addSubCategoryModal">New Sub Category</a></li>
                        <li><a class="dropdown-item" href="#" data-toggle="modal"
                                data-target="#addDescriptionModal">New Description</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Description Group</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="text-primary"><strong>{{ $category->category_name }}</strong></td>
                                <td class="text-center">
                                    <a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal"
                                        data-target="#editCategoryModal{{ $category->id }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ url('/category/destroy-category/' .$category->id) }}" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to delete this item?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                                <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editCategoryModalLabel">Category</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ url('/category/update-category/' . $category->id) }}"
                                                method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="category_name">Category</label>
                                                        <input type="text" class="form-control" id="category_name"
                                                            name="category_name" required
                                                            value="{{ $category->category_name }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                            @if ($category->subcategory->isEmpty())
                                <tr>
                                    <td class="pl-4">No subcategories available</td>
                                    <td></td>
                                </tr>
                            @else
                                @foreach ($category->subcategory as $subcategory)
                                    <tr>
                                        <td class="pl-4">{{ $subcategory->sub_category_name }}</td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-outline-warning" data-toggle="modal"
                                                data-target="#editSubCategoryModal-{{ $subcategory->id }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ url('/category/destroy-subcategory/' .$subcategory->id) }}" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                        <div class="modal fade" id="editSubCategoryModal-{{ $subcategory->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="editSubCategoryModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editSubCategoryModalLabel">Sub
                                                            Category</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form
                                                        action="{{ url('/category/update-subcategory/' . $subcategory->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="category_id">Category</label>
                                                                <select class="form-control" id="category_id"
                                                                    name="category_id" required>
                                                                    @forelse ($categories as $category)
                                                                        @if ($subcategory->category_id == $category->category_id)
                                                                            <option value="{{ $category->id }}" selected>
                                                                                {{ $category->category_name }}</option>
                                                                        @else
                                                                            <option value="{{ $category->id }}">
                                                                                {{ $category->category_name }}</option>
                                                                        @endif
                                                                    @empty
                                                                        Empty
                                                                    @endforelse
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="sub_category_name">Sub Category</label>
                                                                <input type="text" class="form-control"
                                                                    id="sub_category_name" name="sub_category_name"
                                                                    required
                                                                    value="{{ $subcategory->sub_category_name }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                    @if ($subcategory->description_grouping->isEmpty())
                                        <tr>
                                            <td class="pl-5">No description groups available</td>
                                            <td></td>
                                        </tr>
                                    @else
                                        @foreach ($subcategory->description_grouping as $group)
                                            <tr>
                                                <td class="pl-5">{{ $group->name }}</td>
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-outline-warning" data-toggle="modal"
                                                        data-target="#editDescriptionModal-{{ $group->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ url('/category/destroy-group/' .$group->id) }}" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this item?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                                <div class="modal fade" id="editDescriptionModal-{{ $group->id }}"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="editDescriptionModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editDescriptionModalLabel">New
                                                                    Description</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form action="{{ url('/category/update-group/' .$group->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label for="sub_category_id">Sub Category</label>
                                                                        <select class="form-control" id="sub_category_id"
                                                                            name="sub_category_id" required>
                                                                            @foreach ($sub_categories as $subCategory)
                                                                                @if ($group->sub_category_id == $subCategory->id)
                                                                                    <option
                                                                                        value="{{ $subCategory->id }}" selected>
                                                                                        {{ $subCategory->category->category_name }}
                                                                                        -
                                                                                        {{ $subCategory->sub_category_name }}
                                                                                    </option>
                                                                                @else
                                                                                    <option
                                                                                        value="{{ $subCategory->id }}">
                                                                                        {{ $subCategory->category->category_name }}
                                                                                        -
                                                                                        {{ $subCategory->sub_category_name }}
                                                                                    </option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="name">Group Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="name" name="name" required value="{{ $group->name }}">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Close</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">Save</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <a href="{{ url('/cost-review') }}" class="btn btn-outline-secondary btn-m">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </section>

    {{-- Modal add category --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ url('/category/store-category') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_name">Category</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Sub Category -->
    <div class="modal fade" id="addSubCategoryModal" tabindex="-1" role="dialog"
        aria-labelledby="addSubCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubCategoryModalLabel">New Sub Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/category/store-subcategory" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                @forelse ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @empty
                                    kosong
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sub_category_name">Sub Category</label>
                            <input type="text" class="form-control" id="sub_category_name" name="sub_category_name"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Description -->
    <div class="modal fade" id="addDescriptionModal" tabindex="-1" role="dialog"
        aria-labelledby="addDescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDescriptionModalLabel">New Description</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/category/store-group" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="sub_category_id">Sub Category</label>
                            <select class="form-control" id="sub_category_id" name="sub_category_id" required>
                                @foreach ($sub_categories as $subCategory)
                                    <option value="{{ $subCategory->id }}">{{ $subCategory->category->category_name }} -
                                        {{ $subCategory->sub_category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Group Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .table tbody tr td {
            vertical-align: middle;
        }

        .pl-4 {
            padding-left: 2rem !important;
            font-style: italic;
            color: #6c757d;
        }

        .pl-5 {
            padding-left: 3rem !important;
            color: #495057;
        }

        .no-data {
            font-style: italic;
            color: #dc3545;
        }

        .dropdown-menu a {
            font-weight: bold;
            color: #007bff;
        }

        .dropdown-menu a:hover {
            background-color: #e9ecef;
            color: #0056b3;
        }

        .btn-outline-primary {
            background-color: #ffffff;
            border-color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: #ffffff;
        }

        .card {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            overflow: hidden;
        }

        .card-header {
            background-color: #e9ecef;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .table-responsive {
            padding: 1rem;
        }
    </style>
@endsection
