@extends('layouts.app')

@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/LOGO_KKI.png') }}" alt="AdminLTELogo"
            height="60" width="60">
    </div>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Working List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Edit Working List</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- Main content -->
    <section class="content pb-2">
        <div class="card shadow-sm" style="border-radius: 15px;">
            <div class="card-header text-center"
                style="background: linear-gradient(to right, #007bff, #00c6ff); color: white; border-radius: 15px 15px 0 0;">
                <h4 class="m-0">Edit Working List</h4>
            </div>
            <form action="/working-list/update/{{ $item->id }}" method="POST">
                @csrf
                <div class="card-body">
                    <!-- Department Combobox -->
                    <div class="form-group">
                        <label for="department">Department <span class="text-danger">*</span></label>
                        <select name="department_id" id="department" class="form-control" required>
                            <option disabled>Select department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ $department->id == $item->unit_id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Name Input -->
                    <div class="form-group">
                        <label for="name">Working List Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $item->name }}"
                            required>
                    </div>

                    <!-- PIC Combobox -->
                    <div class="form-group">
                        <label for="pic">PIC <span class="text-danger">*</span></label>
                        <select name="pic" id="pic" class="form-control" required>
                            <option disabled>Select PIC</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $item->pic ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="relatedpic">Related PIC <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap" style="max-height: 150px; overflow-y: auto;">
                                @foreach ($users as $user)
                                    <div class="form-check mr-2 mb-2" style="flex: 0 0 30%;">
                                        <input type="checkbox" name="relatedpic[]" value="{{ $user->id }}"
                                            class="form-check-input" id="relatedpic{{ $user->id }}"
                                            {{ in_array($user->id, $item->relatedpic) ? 'checked' : '' }}>
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
                                    value="1" {{ $item->is_priority == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_priority"><strong>Mark as Priority</strong></label>
                            </div>
                        </div>
                    </div>

                    <!-- Deadline (Datetime) -->
                    <div class="form-group">
                        <label for="deadline">Deadline <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="deadline" id="deadline" class="form-control"
                            value="{{ \Carbon\Carbon::parse($item->deadline)->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <!-- Complete Date (Nullable) -->
                    <div class="form-group">
                        <label for="complete_date">Complete Date</label>
                        <input type="datetime-local" name="complete_date" id="complete_date" class="form-control"
                            value="{{ $item->complete_date ? \Carbon\Carbon::parse($item->complete_date)->format('Y-m-d\TH:i') : '' }}">
                    </div>

                    <div class="form-group">
                        <label for="status_comment">Status Comment</label>
                        <select name="status_comment" id="status_comment" class="form-control">
                            <option value="" disabled selected>Select status comment</option>
                            <option value="">Without status</option>
                            <option value="completed" {{ old('status_comment', $item->status_comment) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="uncompleted" {{ old('status_comment', $item->status_comment) == 'uncompleted' ? 'selected' : '' }}>Uncompleted</option>
                        </select>
                    </div>


                    <!-- Comment Dephead (Dynamic) -->
                    <div class="form-group">
                        <label>Comment Dephead</label>
                        <button type="button" id="addCommentDephead" class="btn btn-secondary mb-2"><i
                                class="far fa-plus-square"></i></button>
                        <div id="commentDepheadContainer" class="border p-2 rounded" style="background-color: #f8f9fa;">
                            <p class="font-weight-bold">Comments:</p>
                            @foreach ($item->commentDepheads as $comment)
                                <div class="input-group mb-2">
                                    <input type="hidden" name="comment_dephead_ids[]" value="{{ $comment->id }}">
                                    <textarea name="comment_depheads[]" class="form-control" placeholder="Enter comment dephead" cols="1"
                                        rows="2" required style="white-space: pre-wrap;">{{ $comment->comment }}</textarea>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger removeComment"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <!-- Buttons Section -->
                    <div class="d-flex justify-content-between mt-4 align-items-center">
                        <a href="/working-list/{{ $item->id }}" class="btn btn-secondary btn-m">
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
        // Fungsi untuk menghapus komentar
        function attachRemoveEvent() {
            // Menambahkan event listener ke setiap tombol hapus
            const removeButtons = document.querySelectorAll('.removeComment');
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    button.closest('.input-group').remove();
                });
            });
        }

        // Panggil fungsi untuk menambahkan event listener ke tombol hapus yang sudah ada saat halaman dimuat
        attachRemoveEvent();

        document.getElementById('addCommentDephead').addEventListener('click', function() {
            // Create a new textarea for comment dephead
            var textareaWrapper = document.createElement('div');
            textareaWrapper.classList.add('input-group', 'mb-2');
            textareaWrapper.innerHTML = `
            <textarea name="comment_depheads[]" class="form-control" placeholder="Enter comment dephead" cols="1" rows="2" required style="white-space: pre-wrap;"></textarea>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger removeComment"><i class="fas fa-trash-alt"></i></button>
            </div>
        `;

            // Append to container
            document.getElementById('commentDepheadContainer').appendChild(textareaWrapper);

            // Attach remove event listener to the new button
            textareaWrapper.querySelector('.removeComment').addEventListener('click', function() {
                textareaWrapper.remove();
            });
        });
    </script>
@endsection
