@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content pb-4">
        <div class="container-fluid">
            @if (Auth::user()->role == 1 || Auth::user()->role == 2)
            <div class="ion-group">
                <!-- Small boxes (Stat box) -->
                <div class="ion-group-title">ENTITY</div>
                <div class="row load-animation">
                    <!-- Small Box -->
                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $departments->count() }}</h3>
                                <p>Departments</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-briefcase"></i>
                            </div>
                            <a href="/department" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $employees->count() - 1 }}</h3>
                                <p>Employees</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="/user" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $costReviews->count() }}</h3>
                                <p>Cost Review</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="/control-budget" class="small-box-footer">More Info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="ion-group">
                <!-- Small boxes (Stat box) -->
                <div class="ion-group-title">WORKING LIST</div>
                <div class="row load-animation">
                    <!-- Small Box -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $workingListTotal }}</h3>
                                <p>Working Lists</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-clipboard"></i>
                            </div>
                            <a href="/working-list" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $workingListDone }}</h3>
                                <p>Done</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-checkmark"></i>
                            </div>
                            <a href="/working-list" class="small-box-footer">More Info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $workingListOnProgress }}
                                </h3>
                                <p>On Progress</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-layers"></i>
                            </div>
                            <a href="/working-list" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $workingListOverdue }}</h3>
                                <p>Overdue</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-alert-circled"></i>
                            </div>
                            <a href="/working-list" class="small-box-footer">Details <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card announcement-card shadow-lg">
                        <div class="card-header bg-warning text-white d-flex align-items-center">
                            <i class="fas fa-bullhorn mr-2"></i> <!-- Ikon bullhorn untuk perhatian -->
                            <div class="card-title font-weight-bold">
                                Announcement
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- @if ()

                            @else

                            @endif --}}
                            <p class="announcement-text bg-light p-3 border border-warning rounded text-sm">
                                <strong>Pengumuman</strong>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <div class="announcement-info">
                                <div class="who d-flex" style="gap:10px;">
                                    <p class="mt-1">{{ Auth::user()->name }}</p>
                                    <img class="img-circle img-sm"
                                         src="{{ asset('assets/images/' . (Auth::user()->gender == 'L' ? 'male_icon.png' : 'female_icon.png')) }}"
                                         alt="User Image">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card card-info card-outline card-table load-animation">
                        <div class="card-header">
                            <h3 class="card-title text-bold">Active Working Lists Due in the Next 7 Days</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-bordered text-sm pt-3">
                                <thead class="text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Department</th>
                                        <th>Working List</th>
                                        <th>PIC</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @forelse ($workingLists as $task)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $task->department->name }}</td>
                                            <td class="text-center working-list-col" title="{{ $task->name }}"
                                                data-toggle="tooltip">{{ $task->name }}</td>
                                            <td>{{ $task->picUser->name }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                                <br>
                                                <i class="fas fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($task->deadline)->format('H:i') }}
                                            </td>
                                            <td>
                                                @if ($task->status == 'Outstanding')
                                                    <span class="badge badge-danger">Outstanding</span>
                                                @elseif($task->status == 'On Progress')
                                                    <span class="badge badge-warning">On Progress</span>
                                                @elseif($task->status == 'Done')
                                                    <span class="badge badge-success">Done</span>
                                                @elseif($task->status == 'Requested')
                                                    <span class="badge badge-info">Requested</span>
                                                @else
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="/working-list/{{ $task->id }}"
                                                    class="btn btn-sm btn-primary shadow-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">No Wori</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="col-md-4">
                    <div class="card card-info card-outline pb-3 load-animation">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                    src="{{ asset('assets/images/' . (Auth::user()->gender == 'L' ? 'male_icon.png' : 'female_icon.png')) }}"
                                    alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
                            <p class="text-muted text-center">{{ Auth::user()->nik }}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Position</b> <a class="float-right">
                                        @if (Auth::user()->role == 1)
                                            Pengurus
                                        @elseif (Auth::user()->role == 2)
                                            General Manager
                                        @elseif (Auth::user()->role == 3)
                                            Manager
                                        @elseif (Auth::user()->role == 4)
                                            KA Unit
                                        @elseif (Auth::user()->role == 5)
                                            Staff
                                        @endif
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Phone</b> <a class="float-right">{{ Auth::user()->phone }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Email</b> <a class="float-right">{{ Auth::user()->email }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Address</b> <a class="float-right">{{ Auth::user()->address }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Join Date</b> <a
                                        class="float-right">{{ Auth::user()->join_date ? \Carbon\Carbon::parse(Auth::user()->join_date)->format('d M Y') : '' }}</a>
                                </li>
                            </ul>

                            <a href="#" class="btn btn-info btn-block" data-toggle="modal"
                                data-target="#editProfileModal{{ Auth::user()->id }}"><b>Edit Profile</b></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Modal Edit Profil --}}
    <div class="modal fade" id="editProfileModal{{ Auth::user()->id }}" tabindex="-1" role="dialog"
        aria-labelledby="editProfileModal{{ Auth::user()->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 10px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);">
                <div class="modal-header"
                    style="background: linear-gradient(to right, #007bff, #00c6ff); color: white; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <h5 class="modal-title" id="createProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/update_profile/{{ Auth::user()->id }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Input Name in its own row -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-m" id="name"
                                        name="name" value="{{ Auth::user()->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nik">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-m" id="nik"
                                        name="nik" value="{{ Auth::user()->nik }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control form-control-m" id="password"
                                        name="password">
                                    <small class="form-text text-muted">Leave blank if you don't want to change the
                                        password</small>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="tel" class="form-control form-control-m" id="phone"
                                        name="phone" value="{{ Auth::user()->phone }}">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control form-control-m" id="email"
                                        name="email" value="{{ Auth::user()->email }}">
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <div class="d-flex" style="gap: 10px; margin-left:30px; margin-top:5px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" value="L"
                                                {{ Auth::user()->gender == 'L' ? 'checked' : '' }} required>
                                            <label class="form-check-label">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" value="P"
                                                {{ Auth::user()->gender == 'P' ? 'checked' : '' }} required>
                                            <label class="form-check-label">Female</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="join_date">Join Date</label>
                                    <input type="date" class="form-control form-control-m" id="join_date"
                                        name="join_date" value="{{ Auth::user()->join_date }}">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control form-control-m" rows="5" placeholder="Address" name="address">{{ Auth::user()->address }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                style="border-radius: 20px; padding: 8px 16px;">Close</button>
                            <button type="submit" class="btn btn-primary"
                                style="border-radius: 20px; padding: 8px 16px;">Save Profile</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
    </section>


@endsection


@section('styles')
    <style>
        .card-table {
            width: 100% !important;
            height: 290px !important;
        }

        .table {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .table .working-list-col {
            max-width: 200px;
            /* Ganti dengan lebar maksimum yang diinginkan */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ion-group-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        .load-animation{
            animation: fadeIn 0.5s ease-in-out;
        }

        .announcement-card {
            border: 2px solid #ffc107;
            animation: fadeIn 0.5s ease-in-out;
            /* Animasi saat muncul */
            position: relative;
            overflow: hidden;
        }

        .announcement-card::before {
            content: 'NEW!';
            position: absolute;
            top: 10px;
            right: -40px;
            background: #dc3545;
            color: #fff;
            font-size: 0.9em;
            font-weight: bold;
            padding: 4px 8px;
            transform: rotate(45deg);
            z-index: 1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .announcement-text {
            font-size: 1.1em;
            color: #555;
        }

        .card-header.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }

        .who p {
            font-size: 0.9em;
            color: #555;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection


@section('scripts')
    <script>
        $(function() {
            // Initialize Bootstrap tooltip
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
