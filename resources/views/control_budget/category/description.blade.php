@extends('layouts.app')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Descriptions</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">{{ $costReview->review_name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content pb-3">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Budget Descriptions</h3>
                <div class="card-tools">
                    <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDescriptionModal">
                        <i class="fas fa-plus"></i> Add Description
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Subcategory</th>
                            <th>Description</th>
                            <th>Description Group</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($descriptions as $index => $description)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $description->subcategory->category->category_name ?? 'N/A' }}</td>
                                <td>{{ $description->subcategory->sub_category_name ?? 'N/A' }}</td>
                                <td>{{ $description->description_text }}</td>
                                <td>{{ $description->grouping->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-outline-warning" data-toggle="modal"
                                        data-target="#editDescriptionModal{{ $description->id }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ url('/description/destroy-description/' .$description->id) }}" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to delete this item?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <!-- Add Description Modal -->
                            <div class="modal fade" id="editDescriptionModal{{ $description->id }}" tabindex="-1"
                                role="dialog" aria-labelledby="editDescriptionModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ url('/description/update-description/' .$description->id) }}" method="post">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addDescriptionModalLabel">Edit
                                                    Description</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <input type="hidden" name="cost_review_id" required
                                                value="{{ $costReview->id }}">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="subcategory">Subcategory</label>
                                                    <select name="sub_category_id" id="subcategory" class="form-control">
                                                        <!-- Subcategories are populated dynamically -->
                                                        @foreach ($subcategories as $subcategory)
                                                            @if ($description->sub_category_id == $subcategory->id)
                                                                <option value="{{ $subcategory->id }}" selected>
                                                                    {{ $subcategory->sub_category_name }}</option>
                                                            @else
                                                                <option value="{{ $subcategory->id }}">
                                                                    {{ $subcategory->sub_category_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description_group">Description Group</label>
                                                    <select name="description_grouping_id" id="groups"
                                                        class="form-control">
                                                        <!-- Subcategories are populated dynamically -->
                                                        @foreach ($groupings as $group)
                                                            @if ($description->description_grouping_id == $group->id)
                                                                <option value="{{ $group->id }}" selected>
                                                                    {{ $group->name }}</option>
                                                            @else
                                                                <option value="{{ $group->id }}">
                                                                    {{ $group->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <input type="text" name="description_text" id="description" class="form-control" required value="{{ $description->description_text }}"/>
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
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No descriptions available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ url('/cost-review/' . $costReview->id) }}" class="btn btn-outline-secondary btn-m">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </section>

    <!-- Add Description Modal -->
    <div class="modal fade" id="addDescriptionModal" tabindex="-1" role="dialog"
        aria-labelledby="addDescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ url('/description/store-description') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDescriptionModalLabel">Add Description</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input type="hidden" name="cost_review_id" required value="{{ $costReview->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="subcategory">Subcategory</label>
                            <select name="sub_category_id" id="subcategory" class="form-control">
                                <!-- Subcategories are populated dynamically -->
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ $subcategory->sub_category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description_group">Description Group</label>
                            <select name="description_grouping_id" id="groups" class="form-control">
                                <!-- Subcategories are populated dynamically -->
                                @foreach ($groupings as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description_text" id="description" class="form-control" required></textarea>
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
