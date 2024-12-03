@extends('layouts.app')
@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/LOGO_KKI.png') }}" alt="AdminLTELogo" height="60"
            width="60">
    </div>

    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Supplier Payment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Supplier Payment </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                @if (Auth::user()->unit != null)
                    @if (Auth::user()->unit->name == 'KKI MART')
                        <a href="#" class="btn btn-primary shadow-sm" data-toggle="modal"
                            data-target="#createScheduleModal">
                            <i class="fas fa-plus"></i> <b>Schedule</b>
                        </a>
                    @endif
                @endif
                <button class="btn btn-dark dropdown-toggle shadow-sm ml-auto" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" @if ($paymentSchedules->isEmpty()) disabled @endif>
                    <i class="fas fa-file-export"></i> Export
                </button>

                <div class="dropdown-menu dropdown-menu-right">
                    <form action="/export/payment_supplier" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                        <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                        <button type="submit" name="format" value="excel" class="dropdown-item">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form method="GET" action="/payment_schedule" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Invoice Number or Supplier Name" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Select Status</option>
                        <option value="Unpaid" {{ request('status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="purchase_date_from" class="form-control form-control-sm"
                        placeholder="Purchase Date From" value="{{ request('purchase_date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="purchase_date_to" class="form-control form-control-sm"
                        placeholder="Purchase Date To" value="{{ request('purchase_date_to') }}">
                </div>
                <div class="col-md-3 d-flex" style="gap:8px; justify-content:end;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="/payment_schedule" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sync"></i></a>
                </div>
            </div>
        </form>
    </section>


    <section class="content">
        <div class="card shadow-sm" style="border-radius:15px;">
            <div class="card-body table-responsive p-0"
                style="box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border-radius: 10px;">
                <table class="table table-bordered table-hover text-nowrap">
                    <thead style="background: #007bff; color: white;" class="text-sm">
                        <tr class="text-center align-middle">
                            <th class="text-center align-middle">#</th>
                            <th class="text-center align-middle">Invoice Number</th>
                            <th class="text-center align-middle">Supplier Name</th>
                            <th class="text-center align-middle">
                                <a href="?sort_by=payment_amount&sort_order={{ request('sort_order') == 'asc' ? 'desc' : 'asc' }}"
                                    class="text-white" style="text-decoration: none;">
                                    Payment <br> Amount
                                    <i
                                        class="fas fa-sort{{ request('sort_by') == 'payment_amount' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }}"></i>
                                </a>
                            </th>
                            <th class="text-center align-middle">
                                <a href="?sort_by=purchase_date&sort_order={{ request('sort_order') == 'asc' ? 'desc' : 'asc' }}"
                                    class="text-white" style="text-decoration: none;">
                                    Purchase <br> Date
                                    <i
                                        class="fas fa-sort{{ request('sort_by') == 'purchase_date' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }}"></i>
                                </a>
                            </th>
                            <th class="text-center align-middle">
                                <a href="?sort_by=due_date&sort_order={{ request('sort_order') == 'asc' ? 'desc' : 'asc' }}"
                                    class="text-white" style="text-decoration: none;">
                                    Due Date
                                    <i
                                        class="fas fa-sort{{ request('sort_by') == 'due_date' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }}"></i>
                                </a>
                            </th>
                            <th class="text-center align-middle">Status</th>
                            <th class="text-center align-middle">
                                <a href="?sort_by=paid_date&sort_order={{ request('sort_order') == 'asc' ? 'desc' : 'asc' }}"
                                    class="text-white" style="text-decoration: none;">
                                    Paid Date
                                    <i
                                        class="fas fa-sort{{ request('sort_by') == 'paid_date' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }}"></i>
                                </a>
                            </th>
                            <th class="text-center align-middle">Attachment</th>
                            <th class="text-center align-middle">Description</th>

                            @if (Auth::user()->unit != null)
                                <th class="text-center align-middle">Action</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="text-center text-sm">
                        @forelse ($paymentSchedules as $item)
                            <tr class="hover-highlight">
                                <td class="align-content-center text-center">{{ $loop->iteration }}</td>
                                <td class="text-left align-content-center">{{ $item->invoice_number }}</td>
                                <td class="text-left align-content-center">{{ $item->supplier_name }}</td>
                                <td class="align-content-center">Rp {{ number_format($item->payment_amount, 0, ',', '.') }}</td>
                                <td class="align-content-center">{{ \Carbon\Carbon::parse($item->purchase_date)->format('d M Y') }}
                                    <br>
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($item->purchase_date)->format('H:i') }}
                                </td>
                                <td class="align-content-center">
                                    {{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}
                                    <br>
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($item->due_date)->format('H:i') }}

                                    @if (\Carbon\Carbon::now() > \Carbon\Carbon::parse($item->due_date) && $item->status == 'Unpaid')
                                        {{-- Kondisi Overdue --}}
                                        <span class="text-danger font-weight-bold">
                                            <i class="fas fa-times-circle blinking"></i>
                                        </span>
                                    @elseif (\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->due_date)) <= 3 && $item->status == 'Unpaid')
                                        {{-- Kondisi Due Soon --}}
                                        <span class="text-warning font-weight-bold">
                                            <i class="fas fa-exclamation-triangle blinking"></i>
                                        </span>
                                    @else

                                    @endif
                                </td>

                                <td class="align-content-center">
                                    @if ($item->status == 'Unpaid')
                                        <span class="text-danger">Unpaid</span>
                                    @else
                                        <span class="text-success">Paid</span>
                                    @endif
                                </td>
                                <td class="align-content-center">
                                    @if ($item->paid_date != null)
                                        {{ \Carbon\Carbon::parse($item->paid_date)->format('d M Y') }}
                                        <br>
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($item->paid_date)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="align-content-center">
                                    @if ($item->attachment)
                                        <a href="#" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal"
                                            data-target="#pdfModal{{ $item->id }}"><i class="fas fa-paperclip"></i></a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="align-content-center">
                                    @if ($item->description != null)
                                        {{ $item->description }}
                                    @else
                                        -
                                    @endif
                                </td>
                                @if (Auth::user()->unit != null)
                                    <td class="text-center align-content-center">
                                        @if (Auth::user()->unit->name == 'KKI MART')
                                            <a href="javascript:void(0)" data-toggle="modal"
                                                data-target="#editScheduleModal{{ $item->id }}"
                                                class="btn btn-sm btn-warning shadow-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/payment_schedule/destroy/{{ $item->id }}"
                                                class="btn btn-sm btn-danger shadow-sm"
                                                onclick="confirm('Are you sure want to delete this item?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        @endif
                                        @if (Auth::user()->unit->name == 'TREASURY')
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                    data-target="#updatePaymentModal{{ $item->id }}"
                                                    class="btn btn-sm btn-info shadow-sm mr-1">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                                <form action="/payment_schedule/rollback/{{ $item->id }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <button class="btn btn-sm btn-warning shadow-sm"
                                                        onclick="confirm('Are you sure want to rollback this item?')">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            </tr>

                            <!-- Modal untuk Edit Schedule -->
                            <div class="modal fade" id="editScheduleModal{{ $item->id }}" tabindex="-1"
                                role="dialog" aria-labelledby="editScheduleModal{{ $item->id }}"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content rounded-3">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="createScheduleModalLabel">Edit Schedule</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="/payment_schedule/update/{{ $item->id }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <!-- Form input fields -->
                                                <div class="form-group">
                                                    <label for="invoice_number" class="font-weight-bold">Invoice Number
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="invoice_number"
                                                        name="invoice_number" required
                                                        style="border-radius: 5px; padding: 10px;"
                                                        value="{{ $item->invoice_number }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="supplier_name" class="font-weight-bold">Supplier Name
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="supplier_name"
                                                        name="supplier_name" required
                                                        style="border-radius: 5px; padding: 10px;"
                                                        value="{{ $item->supplier_name }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_amount" class="font-wight-bold">Payment Amount
                                                        <span class="text-danger">*</span></label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="payment_amount"
                                                            name="payment_amount" required oninput="formatRupiah(this)"
                                                            value="{{ number_format($item->payment_amount, 0, ',', '.') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="purchase_date" class="font-weight-bold">Purchase Date
                                                        <span class="text-danger">*</span></label>
                                                    <input type="datetime-local" class="form-control" id="purchase_date"
                                                        name="purchase_date" required
                                                        style="border-radius: 5px; padding: 10px;"
                                                        value="{{ $item->purchase_date }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="due_date" class="font-weight-bold">Due Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="datetime-local" class="form-control" id="due_date"
                                                        name="due_date" required
                                                        style="border-radius: 5px; padding: 10px;"
                                                        value="{{ $item->due_date }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save Schedule</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- End Of Edit Schedule Modal --}}

                            {{-- Update Schedule Modal --}}
                            <div class="modal fade" id="updatePaymentModal{{ $item->id }}" tabindex="-1"
                                role="dialog" aria-labelledby="updatePaymentModal{{ $item->id }}"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content rounded-3">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="createScheduleModalLabel">Update Schedule</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="/payment_schedule/edit/{{ $item->id }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <!-- Paid Date Input -->
                                                <div class="form-group">
                                                    <label for="paid_date" class="font-weight-bold">Paid Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="datetime-local" class="form-control" id="paid_date"
                                                        name="paid_date" required value="{{ $item->paid_date }}"
                                                        style="border-radius: 10px;">
                                                </div>

                                                <!-- Attachment Input -->
                                                <div class="form-group">
                                                    <label for="attachment" class="font-weight-bold">Attachment <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" class="form-control text-sm" id="attachment"
                                                        name="attachment" accept=".pdf">
                                                    <small class="form-text text-muted">Only PDF files are allowed. Max
                                                        size: <span class="text-danger">2 MB</span></small>
                                                </div>

                                                @if ($item->attachment)
                                                    <div class="form-group">
                                                        <p>Current File: <a
                                                                href="{{ asset('storage/' . $item->attachment) }}"
                                                                target="_blank">Download PDF</a></p>
                                                    </div>
                                                @endif

                                                <!-- Description Textarea -->
                                                <div class="form-group">
                                                    <label for="description" class="font-weight-bold">Description</label>
                                                    <textarea id="description" name="description" class="form-control" rows="4"
                                                        placeholder="Write your description here..." style="white-space: pre-wrap;">{{ $item->description }}</textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- End of Update Schedule Modal --}}

                            {{-- PDF View Modal --}}
                            <div class="modal fade" id="pdfModal{{ $item->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="pdfModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content rounded-3">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="pdfModalLabel{{ $item->id }}">Attachment
                                                Viewer</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <!-- PDF Viewer -->
                                            <div class="embed-responsive embed-responsive-16by9">
                                                <iframe src="{{ asset('storage/' . $item->attachment) }}"
                                                    class="embed-responsive-item" width="100%" height="500px"
                                                    allow="fullscreen"></iframe>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <!-- Optionally you can add a download button -->
                                            <a href="{{ asset('storage/' . $item->attachment) }}" class="btn btn-primary"
                                                download>
                                                <i class="fas fa-download"></i> Download PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End of PDF View Modal --}}
                        @empty
                            <tr class="hover-highlight">
                                <td colspan="11" class="text-center">No payment schedules found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </section>

    <section class="content pb-2">
        <div class="card">
            <div class="card-footer clearfix pt-3">
                {{ $paymentSchedules->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>

    <!-- Modal untuk Create Schedule -->
    <div class="modal fade" id="createScheduleModal" tabindex="-1" role="dialog"
        aria-labelledby="createScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createScheduleModalLabel">Create New Schedule</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/payment_schedule/store" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Form input fields -->
                        <div class="form-group">
                            <label for="invoice_number" class="font-weight-bold">Invoice Number <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                                required style="border-radius: 5px; padding: 10px;">
                        </div>
                        <div class="form-group">
                            <label for="supplier_name" class="font-weight-bold">Supplier Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name" required
                                style="border-radius: 5px; padding: 10px;">
                        </div>
                        <div class="form-group">
                            <label for="payment_amount" class="font-wight-bold">Payment Amount <span
                                    class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" class="form-control" id="payment_amount" name="payment_amount"
                                    required oninput="formatRupiah(this)">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="purchase_date" class="font-weight-bold">Purchase Date <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="purchase_date" name="purchase_date"
                                required style="border-radius: 5px; padding: 10px;">
                        </div>
                        <div class="form-group">
                            <label for="due_date" class="font-weight-bold">Due Date <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="due_date" name="due_date" required
                                style="border-radius: 5px; padding: 10px;">
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .blinking {
            animation: blinker 1.5s linear infinite;
            /* color: #ffc107; */
            /* Warna kuning untuk ikon peringatan */
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        function formatRupiah(input) {
            let value = input.value.replace(/[^,\d]/g, "").toString();
            let split = value.split(",");
            let rupiah = split[0].length % 3;
            let rupiahResult = split[0].substr(0, rupiah);
            let thousands = split[0].substr(rupiah).match(/\d{3}/gi);

            // Add thousands separator if there are thousands
            if (thousands) {
                let separator = rupiah ? "." : "";
                rupiahResult += separator + thousands.join(".");
            }

            rupiahResult = split[1] != undefined ? rupiahResult + "," + split[1] : rupiahResult;
            input.value = rupiahResult;
        }
    </script>
@endsection
