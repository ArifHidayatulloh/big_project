@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Budget Categories for {{ $unit->name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/control-budget">Control Budget</a></li>
                        <li class="breadcrumb-item active">Budget Categories</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12 text-right">
                    <div class="dropdown">
                        <a href="/control-budget/monthly-budget/{{ $unit->id }}" class="btn btn-dark"><i
                                class="far fa-plus-square"></i> Plan</a>
                        <a href="/control-budget/cost-overview/{{ $unit->id }}" class="btn btn-secondary"><i
                                class="far fa-eye"></i> Review</a>
                        <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="far fa-plus-square"></i> Add
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"
                            style="width: 200px;">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addCategoryModal">
                                Budget Category
                            </a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addSubcategoryModal">
                                Subcategory
                            </a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addDescriptionModal">
                                Description
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @if ($budgetCategories->isEmpty())
                    <p>No budget categories found for this unit.</p>
                @else
                    <div class="table-responsive">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Budget Category</th>
                                    <th>Subcategory</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($budgetCategories as $category)
                                    @php
                                        $totalSubcategoryRows = $category->subcategories->sum(function ($subcategory) {
                                            return $subcategory->descriptions->count() ?: 1;
                                        });
                                    @endphp

                                    <tr>
                                        <td rowspan="{{ $totalSubcategoryRows }}">
                                            {{ $category->name }}
                                            <div class="dropdown float-right">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton-{{ $category->id }}" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                </button>
                                                <div class="dropdown-menu"
                                                    aria-labelledby="dropdownMenuButton-{{ $category->id }}">
                                                    <a class="dropdown-item" href="#" data-toggle="modal"
                                                        data-target="#editCategoryModal-{{ $category->id }}">Edit</a>
                                                    <a class="dropdown-item"
                                                        href="/control-budget/destroyCategory/{{ $category->id }}"
                                                        onclick="return confirm('Are you sure?')">Delete</a>
                                                </div>
                                            </div>
                                        </td>

                                        @foreach ($category->subcategories as $subcategory)
                                            @php
                                                $totalDescriptionRows = $subcategory->descriptions->count() ?: 1;
                                            @endphp

                                            @if ($loop->first)
                                                <td rowspan="{{ $totalDescriptionRows }}">
                                                    {{ $subcategory->name }}
                                                    <div class="dropdown float-right">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenuButton-{{ $subcategory->id }}"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                        </button>
                                                        <div class="dropdown-menu"
                                                            aria-labelledby="dropdownMenuButton-{{ $subcategory->id }}">
                                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                                data-target="#editSubcategoryModal-{{ $subcategory->id }}">Edit</a>
                                                            <a class="dropdown-item"
                                                                href="/control-budget/destroySubcategory/{{ $subcategory->id }}"
                                                                onclick="return confirm('Are you sure?')">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>

                                                @foreach ($subcategory->descriptions as $description)
                                                    @if (!$loop->first)
                                    <tr>
                                        <td>
                                            {{ $description->description }}
                                            <div class="dropdown float-right">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton-{{ $description->id }}" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">

                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="dropdownMenuButton-{{ $description->id }}">
                                                    <a class="dropdown-item" href="#" data-toggle="modal"
                                                        data-target="#editDescriptionModal-{{ $description->id }}">Edit</a>
                                                    <a class="dropdown-item"
                                                        href="/control-budget/destroyDescription/{{ $description->id }}"
                                                        onclick="return confirm('Are you sure?')">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <td>
                                        {{ $description->description }}
                                        <div class="dropdown float-right">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                id="dropdownMenuButton-{{ $description->id }}" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">

                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="dropdownMenuButton-{{ $description->id }}">
                                                <a class="dropdown-item" href="#" data-toggle="modal"
                                                    data-target="#editDescriptionModal-{{ $description->id }}">Edit</a>
                                                <a class="dropdown-item"
                                                    href="/control-budget/destroyDescription/{{ $description->id }}"
                                                    onclick="return confirm('Are you sure?')">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                @endforeach

                @if ($subcategory->descriptions->isEmpty())
                    <td> - </td>
                @endif
            @else
                <tr>
                    <td rowspan="{{ $totalDescriptionRows }}">
                        {{ $subcategory->name }}
                        <div class="dropdown float-right">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenuButton-{{ $description->id }}" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="dropdownMenuButton-{{ $description->id }}">
                                <a class="dropdown-item" href="#" data-toggle="modal"
                                    data-target="#editDescriptionModal-{{ $description->id }}">Edit</a>
                                <a class="dropdown-item" href="/control-budget/destroyDescription/{{ $description->id }}"
                                    onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                    </td>

                    @foreach ($subcategory->descriptions as $description)
                        @if (!$loop->first)
                <tr>
                    <td>
                        {{ $description->description }}
                        <div class="dropdown float-right">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="dropdownMenuButton-{{ $description->id }}" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">

                            </button>
                            <div class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="dropdownMenuButton-{{ $description->id }}">
                                <a class="dropdown-item" href="#" data-toggle="modal"
                                    data-target="#editDescriptionModal-{{ $description->id }}">Edit</a>
                                <a class="dropdown-item" href="/control-budget/destroyDescription/{{ $description->id }}"
                                    onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @else
                <td>
                    {{ $description->description }}
                    <div class="dropdown float-right">
                        <button class="btn btn-secondary dropdown-toggle" type="button"
                            id="dropdownMenuButton-{{ $description->id }}" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">

                        </button>
                        <div class="dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownMenuButton-{{ $description->id }}">
                            <a class="dropdown-item" href="#" data-toggle="modal"
                                data-target="#editDescriptionModal-{{ $description->id }}">Edit</a>
                            <a class="dropdown-item" href="/control-budget/destroyDescription/{{ $description->id }}"
                                onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </td>
                @endif
                @endforeach

                @if ($subcategory->descriptions->isEmpty())
                    <td> - </td>
                @endif
                </tr>
                @endif
                @endforeach

                @if ($category->subcategories->isEmpty())
                    <td> - </td>
                @endif
                </tr>
                @endforeach
                </tbody>

                </table>
            </div>
            @endif
        </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <!-- Modal for adding Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="/control-budget/storeCategory" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Add Budget Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                        <div class="form-group">
                            <label for="category-name">Category Name</label>
                            <input type="text" name="name" class="form-control" id="category-name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">SAVE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for adding Subcategory -->
    <div class="modal fade" id="addSubcategoryModal" tabindex="-1" aria-labelledby="addSubcategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="/control-budget/storeSubcategory" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSubcategoryModalLabel">Add Subcategory</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                        <div class="form-group">
                            <label for="subcategory-category">Category</label>
                            <select name="category_id" class="form-control" id="subcategory-category" required>
                                @foreach ($budgetCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="subcategory-name">Subcategory Name</label>
                            <input type="text" name="name" class="form-control" id="subcategory-name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">SAVE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for adding Description -->
    <div class="modal fade" id="addDescriptionModal" tabindex="-1" aria-labelledby="addDescriptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="/control-budget/storeDescription" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDescriptionModalLabel">Add Description</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                        <div class="form-group">
                            <label for="description-category">Category</label>
                            <select name="subcategory_id" class="form-control" id="description-category" required>
                                @foreach ($budgetCategories as $category)
                                    @foreach ($category->subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}">{{ $category->name }} -
                                            {{ $subcategory->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description-text">Description</label>
                            <input type="text" name="description" class="form-control" id="description" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">SAVE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for editing Category -->
    @foreach ($budgetCategories as $category)
        <div class="modal fade" id="editCategoryModal-{{ $category->id }}" tabindex="-1"
            aria-labelledby="editCategoryModalLabel-{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="/control-budget/updateCategory/{{ $category->id }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCategoryModalLabel-{{ $category->id }}">Edit Budget Category
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit-category-name-{{ $category->id }}">Category Name</label>
                                <input type="text" name="name" class="form-control"
                                    id="edit-category-name-{{ $category->id }}" value="{{ $category->name }}" required>
                                <input type="text" name="unit_id" value="{{ $category->unit_id }}" hidden>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">UPDATE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <!-- Modal for editing Subcategory -->
    @foreach ($budgetCategories as $category)
        @foreach ($category->subcategories as $subcategory)
            <div class="modal fade" id="editSubcategoryModal-{{ $subcategory->id }}" tabindex="-1"
                aria-labelledby="editSubcategoryModalLabel-{{ $subcategory->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="/control-budget/updateSubcategory/{{ $subcategory->id }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editSubcategoryModalLabel-{{ $subcategory->id }}">Edit
                                    Subcategory</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="edit-subcategory-category-{{ $subcategory->id }}">Category</label>
                                    <select name="category_id" class="form-control"
                                        id="edit-subcategory-category-{{ $subcategory->id }}" required>
                                        @foreach ($budgetCategories as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ $cat->id == $subcategory->category_id ? 'selected' : '' }}>
                                                {{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit-subcategory-name-{{ $subcategory->id }}">Subcategory Name</label>
                                    <input type="text" name="name" class="form-control"
                                        id="edit-subcategory-name-{{ $subcategory->id }}"
                                        value="{{ $subcategory->name }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">UPDATE</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endforeach

    <!-- Modal for editing Description -->
    @foreach ($budgetCategories as $category)
        @foreach ($category->subcategories as $subcategory)
            @foreach ($subcategory->descriptions as $description)
                <div class="modal fade" id="editDescriptionModal-{{ $description->id }}" tabindex="-1"
                    aria-labelledby="editDescriptionModalLabel-{{ $description->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="/control-budget/updateDescription/{{ $description->id }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editDescriptionModalLabel-{{ $description->id }}">Edit
                                        Description</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="edit-description-category-{{ $description->id }}">Category</label>
                                        <select name="subcategory_id" class="form-control"
                                            id="edit-description-category-{{ $description->id }}" required>
                                            @foreach ($budgetCategories as $cat)
                                                @foreach ($cat->subcategories as $subcat)
                                                    <option value="{{ $subcat->id }}"
                                                        {{ $subcat->id == $description->subcategory_id ? 'selected' : '' }}>
                                                        {{ $cat->name }} - {{ $subcat->name }}
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-description-text-{{ $description->id }}">Description</label>
                                        <input name="description" class="form-control"
                                            id="edit-description-text-{{ $description->id }}" required
                                            value="{{ $description->description }}"></input>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">UPDATE</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        @endforeach
    @endforeach
@endsection

@section('styles')
    <style>
        /* Media query for mobile devices */
        @media (max-width: 767px) {

            .table td,
            .table th {
                padding: 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
@endsection
