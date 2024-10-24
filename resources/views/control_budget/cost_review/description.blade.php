@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $costReview->review_name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Budget Categories</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-plus"></i>
                        <b>Cost Review</b>
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
                <div class="card-tools">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-money-bill"></i> <b>Planned Budget</b>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/control-budget/planned_budget/{{ $costReview->id }}">New
                                Budget Plan</a></li>
                        <li><a class="dropdown-item" href="/control-budget/review_cost/{{ $costReview->id }}">Review Cost</a></li>
                    </ul>
                </div>
                {{-- <div class="card-tools mt-2 mr-1">
                    <a href="/control-budget/planned_budget/{{ $costReview->id }}" class="btn btn-primary">
                        <i class="fas fa-money-bill"></i> <b>Planned Budget</b>
                    </a>
                </div> --}}
            </div>
        </div>
    </section>

    <section class="content pb-4">
        <div class="container-fluid">
            @forelse($categories as $category)
                <!-- Category Block -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ $category->category_name }}</h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-warning" data-toggle="modal"
                                data-target="#editCategoryModal{{ $category->id }}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCategory({{ $category->id }})"><i
                                    class="fas fa-trash-alt"></i></button>
                            <button class="btn btn-tool" data-toggle="collapse" data-target="#category{{ $category->id }}">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body collapse show" id="category{{ $category->id }}">
                        <div class="row">
                            @forelse($category->subcategory as $subCategory)
                                <div class="col-md-12">
                                    <!-- Subcategory Block -->
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ $subCategory->sub_category_name }}</h3>
                                            <div class="card-tools">
                                                <button class="btn btn-sm btn-warning" data-toggle="modal"
                                                    data-target="#editSubCategoryModal{{ $subCategory->id }}"><i
                                                        class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deleteSubCategory({{ $subCategory->id }})"><i
                                                        class="fas fa-trash-alt"></i></button>
                                                <button class="btn btn-tool" data-toggle="collapse"
                                                    data-target="#subcategory{{ $subCategory->id }}">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Modal for Edit Subcategory -->
                                        <div class="modal fade" id="editSubCategoryModal{{ $subCategory->id }}"
                                            tabindex="-1" role="dialog"
                                            aria-labelledby="editSubCategoryModalLabel{{ $subCategory->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="editSubCategoryModalLabel{{ $subCategory->id }}">Edit
                                                            Subcategory</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="sub_category_name">Subcategory Name</label>
                                                                <input type="text" class="form-control"
                                                                    name="sub_category_name"
                                                                    value="{{ $subCategory->sub_category_name }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save
                                                                changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body collapse show" id="subcategory{{ $subCategory->id }}">
                                            <ul class="list-group">
                                                @forelse($subCategory->descriptions as $description)
                                                    <!-- Description Block -->
                                                    <li class="list-group-item">
                                                        <strong>{{ $description->description_text }}</strong>
                                                        <span
                                                            class="badge badge-info float-right">{{ $description->budget }}</span>
                                                        <div class="float-right">
                                                            <button class="btn btn-sm btn-warning" data-toggle="modal"
                                                                data-target="#editDescriptionModal{{ $description->id }}"><i
                                                                    class="fas fa-edit"></i></button>
                                                            <button class="btn btn-sm btn-danger"
                                                                onclick="deleteDescription({{ $description->id }})"><i
                                                                    class="fas fa-trash-alt"></i></button>
                                                        </div>
                                                    </li>

                                                    <!-- Modal for Edit Description -->
                                                    <div class="modal fade"
                                                        id="editDescriptionModal{{ $description->id }}" tabindex="-1"
                                                        role="dialog"
                                                        aria-labelledby="editDescriptionModalLabel{{ $description->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="editDescriptionModalLabel{{ $description->id }}">
                                                                        Edit
                                                                        Description</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <form action=" " method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label
                                                                                for="description_text">Description</label>
                                                                            <input type="text" class="form-control"
                                                                                name="description_text"
                                                                                value="{{ $description->description_text }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Save changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <li class="list-group-item">No descriptions found</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p>No subcategories found</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Modal for Edit Category -->
                <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="category_name">Category Name</label>
                                        <input type="text" class="form-control" name="category_name"
                                            value="{{ $category->category_name }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @empty
                <p>No categories found</p>
            @endforelse
        </div>
    </section>
    <!-- Modal for Adding Category -->
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
                <form action="/control-budget/storeCategory" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                            <input type="hidden" name="cost_review_id" value="{{ $costReview->id }}">
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
                <form action="/control-budget/storeSubcategory" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_id">Select Category</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                @forelse ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @empty
                                    kosong
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sub_category_name">Sub Category Name</label>
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
                <form action="/control-budget/storeDescription" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="sub_category_id">Select Sub Category</label>
                            <select class="form-control" id="sub_category_id" name="sub_category_id" required>
                                @foreach ($sub_categories as $subCategory)
                                    <option value="{{ $subCategory->id }}">{{ $subCategory->category->category_name }} - {{ $subCategory->sub_category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description_text">Description Text</label>
                            <input type="text" class="form-control" id="description_text" name="description_text"
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
@endsection
