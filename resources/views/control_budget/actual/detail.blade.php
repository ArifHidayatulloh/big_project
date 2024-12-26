@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cost Review</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Cost Review</a></li>
                        <li class="breadcrumb-item active">Actual</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content pb-3 pt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <h5 class="font-weight-bold mb-0">Actual Spent Details</h5>
                <div class="card-tools ml-auto">
                    <a class="btn btn-outline-light" data-toggle="modal" data-target="#addActualModal">
                        <i class="fas fa-plus-circle"></i> Add Actual
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Informasi Anggaran Bulanan -->
                <h5 class="font-weight-bold">Monthly Budget Information</h5>
                <table class="table table-striped table-sm">
                    <tr>
                        <th>Description</th>
                        <td>{{ $monthlyBudget->description->description_text ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Planned Budget</th>
                        <td>Rp {{ number_format($monthlyBudget->planned_budget, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Month</th>
                        <td>{{ $monthlyBudget->month }} {{ $monthlyBudget->year }}</td>
                    </tr>
                </table>

                <!-- Detail Pengeluaran Aktual -->
                <h5 class="font-weight-bold mt-4">Actual Spent Details</h5>
                @if ($monthlyBudget->actual->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>Date</th>
                                    <th>No Source</th>
                                    <th>Description</th>
                                    <th>
                                        <a href="?sort_column=actual_spent&sort_order={{ request('sort_order') == 'asc' ? 'desc' : 'asc' }}"
                                            class="text-white" style="text-decoration: none;">
                                            Amount <i
                                                class="fas fa-sort{{ request('sort_column') == 'actual_spent' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }}"></i>
                                        </a>
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyBudget->actual as $actual)
                                    <tr>
                                        <td class="align-middle">{{ $actual->date }}</td>
                                        <td>{{ $actual->no_source }}</td>
                                        <td>{{ $actual->description }}</td>
                                        <td>Rp {{ number_format($actual->actual_spent, 2, ',', '.') }}</td>
                                        <td>
                                            <a href="" class="btn btn-warning btn-sm" data-toggle="modal"
                                                data-target="#editActualModal{{ $actual->id }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#deleteModal{{ $actual->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Modal Hapus -->
                                    <div class="modal fade" id="deleteModal{{ $actual->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="deleteModalLabel{{ $actual->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $actual->id }}">Delete
                                                        Actual</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this record? This action cannot be
                                                    undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel</button>
                                                    <form action="/actual/destroy/{{ $actual->id }}"
                                                        method="get">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editActualModal{{ $actual->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="editActualModalLabel{{ $actual->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <form action="/actual/update/{{ $actual->id }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="editActualModalLabel">Edit Actual Record
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Hidden Field for Monthly Budget ID -->
                                                        <input type="hidden" name="monthly_budget_id"
                                                            value="{{ $monthlyBudget->id }}">

                                                        <!-- Date Input -->
                                                        <div class="form-group">
                                                            <label for="date">Date</label>
                                                            <input type="date" class="form-control" id="date"
                                                                name="date" required
                                                                value="{{ old('date', $actual->date) }}">
                                                            @error('date')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- No Source Input -->
                                                        <div class="form-group">
                                                            <label for="no_source">No Source</label>
                                                            <input type="text" class="form-control" id="no_source"
                                                                name="no_source" required
                                                                value="{{ old('no_source', $actual->no_source) }}">
                                                            @error('no_source')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- Description Input -->
                                                        <div class="form-group">
                                                            <label for="description">Description</label>
                                                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $actual->description) }}</textarea>
                                                            @error('description')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- Amount Input -->
                                                        <div class="form-group row">
                                                            <label for="ribuan" class="col-sm-2 col-form-label">Actual
                                                                Spent</label>
                                                            <div class="col-sm-5">
                                                                <input type="text" class="form-control" id="ribuan"
                                                                    name="ribuan" onkeyup="formatRibuan(this)"
                                                                    value="{{ old('ribuan', number_format(floor($actual->actual_spent), 0, ',', '.')) }}"
                                                                    required>
                                                                @error('ribuan')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <label for="desimal"
                                                                class="col-sm-3 col-form-label text-lg-center">Decimal</label>
                                                            <div class="col-sm-2">
                                                                <input type="text" class="form-control" id="desimal"
                                                                    name="desimal" maxlength="2"
                                                                    value="{{ old('desimal', number_format(fmod($actual->actual_spent, 1) * 100, 0, '', '') ?: '00') }}"
                                                                    required>
                                                                @error('desimal')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save
                                                            Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                            <!-- Baris untuk total pengeluaran -->
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-left font-weight-bold">Total Actual</td>
                                    <td class="font-weight-bold">Rp {{ number_format($totalSpent, 2, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No actual spent records available for this budget.</p>
                @endif

                <!-- Back to previous page -->
                <a href="{{ url('/cost-review/' . $costReviewId  . '?year=' . $monthlyBudget->year . '&month=' . $monthlyBudget->month) }}"
                    class="btn btn-outline-secondary mt-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

            </div>
        </div>
    </section>


    <!-- Modal -->
    <div class="modal fade" id="addActualModal" tabindex="-1" role="dialog" aria-labelledby="addActualModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="/actual/store" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addActualModalLabel">Add Actual</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden Field for Monthly Budget ID -->
                        <input type="hidden" name="monthly_budget_id" value="{{ $monthlyBudget->id }}">

                        <!-- Date -->
                        <div class="form-group">
                            <label for="source">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>

                        <!-- No Source -->
                        <div class="form-group">
                            <label for="no_source">No. Source</label>
                            <input type="text" class="form-control" id="no_source" name="no_source" required>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <!-- Actual Spent -->
                        <div class="form-group row">
                            <label for="ribuan" class="col-sm-2 col-form-label">Actual Spent</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="ribuan" name="ribuan"
                                    onkeyup="formatRibuan(this)"required>
                            </div>

                            <label for="desimal" class="col-sm-2 col-form-label text-lg-center">Decimal</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" id="desimal" name="desimal" maxlength="2"
                                    value="00" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection



@section('scripts')
    <script>
        function formatRibuan(input) {
            let value = input.value;

            // Hapus karakter non-angka
            value = value.replace(/[^0-9]/g, '');

            // Tambahkan titik setiap ribuan
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    </script>
@endsection
