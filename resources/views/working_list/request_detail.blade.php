@extends('layouts.app')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <br>
            <div class="card shadow-xl">
                <div class="card-body">
                    <!-- Header dengan background gradien halus -->
                    <div class="header  text-dark py-5 d-flex flex-column justify-content-center align-items-center rounded"
                        style="background: linear-gradient(to right, #007bff, #00c6ff); color:white;">
                        <h3 class="text-center font-weight-bold text-white">{{ $item->name }}</h3>
                        <p class="text-center mb-0 text-white">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</p>
                        <p class="text-center mb-0 text-white"><i class="fas fa-clock"></i>
                            {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</p>
                    </div>

                    <div class="section-details py-4">
                        <div class="row text-center">
                            <!-- Created By -->
                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="detail-box p-3 rounded bg-white shadow-sm">
                                    <strong>Created By</strong>
                                    <p>{{ $item->creator->name }}</p>
                                </div>
                            </div>

                            <!-- Deadline -->
                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="detail-box p-3 rounded bg-white shadow-sm">
                                    <strong>Deadline</strong>
                                    <p>{{ \Carbon\Carbon::parse($item->deadline)->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="detail-box p-3 rounded bg-white shadow-sm">
                                    <strong>Status</strong>
                                    <p>{{ $item->status }}</p>
                                </div>
                            </div>

                            <!-- Complete Date -->
                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="detail-box p-3 rounded bg-white shadow-sm">
                                    <strong>Complete Date</strong>
                                    @if ($item->complete_date)
                                        <p>{{ \Carbon\Carbon::parse($item->complete_date)->format('d M Y, H:i') }}</p>
                                    @else
                                        <p>No completion date</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Separator garis halus -->
                    <hr class="my-4" style="border-top: 1px solid #eaeaea;">

                    <!-- Comments dan Updates -->
                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <strong>Comments and Updates</strong>
                        </div>
                        <div class="comments-container p-4">
                            <ul class="list-unstyled">
                                @forelse($item->commentDepheads as $comment)
                                    <li class="border p-4 mb-4 rounded bg-white shadow-sm">
                                        <p><strong>Comment:</strong></p>
                                        <p class="ml-3">{{ $comment->comment }}</p>

                                        <div class="mt-4">
                                            <strong>Updates PIC:</strong>
                                            @forelse($comment->updatePics as $updatePic)
                                                <div class="card mb-3">
                                                    <div class="card-body p-3 bg-light">
                                                        <p class="mb-1"><strong>Update:</strong></p>
                                                        <p class="ml-3">{{ $updatePic->update }}</p>
                                                        @if ($updatePic->pdf_file)
                                                            <p class="mb-0"><strong>File:</strong>
                                                                <a href="{{ asset('storage/' . $updatePic->pdf_file) }}"
                                                                    target="_blank">Download PDF</a>
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted">No updates available for this comment</p>
                                            @endforelse
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-center">No comments or updates available</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="d-flex justify-content-between mt-5">
                        <div>
                            <a href="#" class="btn btn-success" data-toggle="modal" data-target="#approvalModal">
                                <i class="fas fa-thumbs-up"></i> Approve
                            </a>
                            <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-thumbs-down"></i> Reject
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <a href="/need_approval" class="btn btn-secondary mt-2 mb-5">Back to List</a>


            <!-- Modal untuk Input Approval -->
            <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approvalModalLabel">Approval Form</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ url('/need_approval/approve/' . $item->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <!-- Input Complete Date -->
                                <div class="form-group">
                                    <label for="deadline">Deadline Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="deadline" name="deadline"
                                        required value="{{ $item->deadline }}">
                                </div>
                                <!-- Input Complete Date -->
                                <div class="form-group">
                                    <label for="complete_date">Complete Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="complete_date"
                                        name="complete_date" required>
                                </div>

                                <!-- Input Status Comment -->
                                <div class="form-group">
                                    <label for="status_comment">Status Comment <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status_comment" name="status_comment" required>
                                        <option value="" disabled selected>Select status comment</option>
                                        <option value="completed">Completed</option>
                                        <option value="uncompleted">Uncompleted</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success"><i class="fas fa-thumbs-up"></i>
                                    Approve</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal untuk Input Reject -->
            <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approvalModalLabel">Reject Form</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ url('/need_approval/reject/' . $item->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <!-- Input Complete Date -->
                                <div class="form-group">
                                    <label for="reject">Rejected Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-m" rows="5" placeholder="Rejected Reason" name="reject_reason">{{ $item->reject_reason }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger"><i class="fas fa-thumbs-down"></i>
                                    Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom CSS untuk tampilan yang lebih rapi dan elegan -->
    <style>
        /* Style detail box */
        .detail-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        /* Comments and updates container */
        .comments-container {
            background-color: #fafafa;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Button style adjustments */
        .btn {
            /* border-radius: 30px; */
            font-size: 0.9rem;
        }

        /* Responsive design */
        @media (max-width: 767.98px) {
            .detail-box {
                margin-bottom: 20px;
            }
        }
    </style>
@endsection
