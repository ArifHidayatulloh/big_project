@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <!-- Form Card -->
                    <div class="card shadow-sm mt-4 mb-4">
                        <div class="card-header bg-primary text-white text-center">
                            <h3>Update for Comment</h3>
                        </div>
                        <div class="card-body">
                            <form action="/working-list/storeUpdatePICNew/{{ $updatePic->id }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <!-- Comment Section -->
                                <div class="form-group">
                                    <label for="comment" class="font-weight-bold">Comment:</label>
                                    <p class="border p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $updatePic->commentDephead->comment }}</p>
                                </div>

                                <!-- Update Section -->
                                <div class="form-group">
                                    <label for="update" class="font-weight-bold">Update:</label>
                                    <textarea id="update" name="update" class="form-control rounded" rows="4" required style="white-space: pre-wrap;">{{ $updatePic->update }}</textarea>
                                </div>

                                <!-- PDF Upload Section -->
                                <div class="form-group">
                                    <label for="pdf_file" class="font-weight-bold">Upload PDF (optional):</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="pdf_file" name="pdf_file" accept=".pdf">
                                        <label class="custom-file-label" for="pdf_file">Choose PDF file</label>
                                    </div>
                                </div>

                                @if ($updatePic->pdf_file)
                                    <p>Current File: <a href="{{ asset('storage/' . $updatePic->pdf_file) }}" target="_blank">Download PDF</a></p>
                                @endif

                                <!-- Buttons Section -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="/working-list/{{ $updatePic->commentDephead->working_list_id }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-arrow-left"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom Styles -->
    <style>
        .custom-file-input ~ .custom-file-label::after {
            content: "Browse";
        }

        .custom-file-input:focus ~ .custom-file-label {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .card {
            border-radius: 10px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Menampilkan nama file yang dipilih di label input file
        document.querySelector('.custom-file-input').addEventListener('change', function (e) {
            var fileName = document.getElementById("pdf_file").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    </script>
@endsection
