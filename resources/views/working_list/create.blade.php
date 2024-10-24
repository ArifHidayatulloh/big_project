@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Working List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Working List</li>
                        <li class="breadcrumb-item active">New</li>
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
                <h4 class="m-0">New Working List</h4>
            </div>
            <form action="/working-list/store" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Department Combobox -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="department">Department <span class="text-danger">*</span></label>
                                <select name="department_id" id="department" class="custom-select form-control-m" required>
                                    <option disabled selected>Select department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Name Input -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Working List Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control form-control-m"
                                    required>
                            </div>
                        </div>

                        <!-- PIC Combobox -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pic">PIC <span class="text-danger">*</span></label>
                                <select name="pic" id="pic" class="custom-select form-control-m" required>
                                    <option disabled selected>Select PIC</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Related PIC (Checkbox) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="relatedpic">Related PIC <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap" style="max-height: 150px; overflow-y: auto;">
                                    @foreach ($users as $user)
                                        <div class="form-check mr-2 mb-2" style="flex: 0 0 30%;">
                                            <input type="checkbox" name="relatedpic[]" value="{{ $user->id }}"
                                                class="form-check-input" id="relatedpic{{ $user->id }}">
                                            <label class="form-check-label"
                                                for="relatedpic{{ $user->id }}">{{ $user->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Priority -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="icheck-primary d-inline">
                                    <input class="form-check-input" type="checkbox" id="is_priority" name="is_priority"
                                        value="1">
                                    <label class="form-check-label" for="is_priority"><strong>Mark as Priority</strong></label>
                                </div>
                            </div>
                        </div>

                        <!-- Deadline (Datetime) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="deadline">Deadline <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="deadline" id="deadline"
                                    class="form-control form-control-m" required>
                            </div>
                        </div>

                        <!-- Complete Date (Nullable) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="complete_date">Complete Date</label>
                                <input type="datetime-local" name="complete_date" id="complete_date"
                                    class="form-control form-control-m">
                            </div>
                        </div>

                        <!-- Comment Dephead (Dynamic) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Comment Dephead <span class="text-danger">*</span></label>
                                <button type="button" id="addCommentDephead" class="btn btn-secondary mb-2"><i
                                        class="far fa-plus-square"></i></button>
                                <div id="commentDepheadContainer" class="border p-2 rounded"
                                    style="background-color: #f8f9fa;">
                                    <p class="font-weight-bold">Comments:</p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Buttons Section -->
                    <div class="d-flex justify-content-between mt-4 align-items-center">
                        <a href="/working-list" class="btn btn-secondary btn-m">
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

@section('scripts')
    <script>
        document.getElementById('addCommentDephead').addEventListener('click', function() {
            // Create a new textarea for comment dephead
            var textareaWrapper = document.createElement('div');
            textareaWrapper.classList.add('form-group', 'mb-2');
            textareaWrapper.innerHTML = `
            <div class="input-group mb-2">
                <textarea name="comment_depheads[]" class="form-control" placeholder="Enter comment dephead" cols="1" rows="2" required style="white-space: pre-wrap;"></textarea>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger removeComment"><i
                                            class="fas fa-trash-alt"></i></button>
                </div>
            </div>
        `;

            // Append to container
            document.getElementById('commentDepheadContainer').appendChild(textareaWrapper);

            // Add event listener to remove button
            textareaWrapper.querySelector('.removeComment').addEventListener('click', function() {
                textareaWrapper.remove();
            });
        });
    </script>
@endsection
