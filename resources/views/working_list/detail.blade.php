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
                            {{ \Carbon\Carbon::parse($item->created_at)->format('g:i A') }}</p>
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
                                    <p>{{ \Carbon\Carbon::parse($item->deadline)->format('d M Y, g:i A') }}</p>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="detail-box p-3 rounded bg-white shadow-sm">
                                    <strong>Status</strong>
                                    <p>{{ $item->status }}
                                        @if ($item->status == 'Rejected')
                                            <a href="#" class="btn rounded-circle btn-sm btn-danger" data-toggle="modal"
                                                data-target="#rejectReasonModal">
                                                <i class="fas fa-question"></i>
                                            </a>
                                        @endif
                                    </p>

                                </div>
                            </div>

                            <!-- Complete Date -->
                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <div class="detail-box p-3 rounded bg-white shadow-sm">
                                    <strong>Complete Date</strong>
                                    @if ($item->complete_date)
                                        <p>{{ \Carbon\Carbon::parse($item->complete_date)->format('d M Y, g:i A') }}</p>
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
                                        <p class="ml-3" style="white-space: pre-wrap;">{{ $comment->comment }}</p>

                                        <div class="mt-4">
                                            <strong>Updates PIC:</strong>
                                            @forelse($comment->updatePics as $updatePic)
                                                <div class="card mb-3">
                                                    <div class="card-body p-3 bg-light">
                                                        <p class="mb-1"><strong>Update:</strong></p>
                                                        <p class="ml-3" style="white-space: pre-wrap;">{{ $updatePic->update }}</p>
                                                        @if ($updatePic->pdf_file)
                                                            <p class="mb-0"><strong>File:</strong>
                                                                <a href="{{ asset('storage/' . $updatePic->pdf_file) }}"
                                                                    target="_blank">Download PDF</a>
                                                            </p>
                                                        @endif
                                                        <div class="mt-2 d-flex justify-content-between align-item-center">
                                                            <div class="text-left">
                                                                <a href="/working-list/editUpdatePIC/{{ $updatePic->id }}"
                                                                    class="btn btn-sm btn-warning"><i
                                                                        class="fas fa-edit"></i>
                                                                    Edit</a>
                                                                <a href="/working-list/deleteUpdatePIC/{{ $updatePic->id }}"
                                                                    class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this update?')"><i
                                                                        class="fas fa-trash-alt"></i> Delete</a>
                                                            </div>
                                                            <!-- Update Info Positioned to the Right -->
                                                            <div class="text-right">
                                                                <span class="text-sm">Update by:
                                                                    {{ $updatePic->updator->name }}</span><br>
                                                                <span
                                                                    class="text-sm">{{ \Carbon\Carbon::parse($updatePic->updated_at)->format('d M Y, g:i A') }}</span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted">No updates available for this comment</p>
                                            @endforelse
                                        </div>
                                        <a href="/working-list/updatePIC/{{ $comment->id }}"
                                            class="btn btn-sm btn-primary mt-3 color-white">[+] Add Update</a>
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
                            <a href="/working-list/edit/{{ $item->id }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="/working-list/destroy/{{ $item->id }}" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this item?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </div>

                        @if ($item->status == 'Done' || $item->status == 'Outstanding')
                        @else
                            <form action="/working-list/requestActionPIC/{{ $item->id }}" method="post">
                                @csrf
                                <button class="btn btn-info" type="submit"
                                    onclick="return confirm('Do you want to request an action?')">
                                    <i class="fas fa-paper-plane"></i> Request Action
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>

            <a href="/working-list" class="btn btn-secondary mt-2 mb-5">Back to List</a>
        </div>
    </section>

    <!-- Modal untuk Input Reject Reason -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" role="dialog" aria-labelledby="rejectReasonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectReasonModalLabel">Reject Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejectReason">Reject Reason:</label>
                        <textarea class="form-control" id="reject" disabled>{{ $item->reject_reason }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
