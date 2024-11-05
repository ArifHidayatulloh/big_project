@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard Overview</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <!-- Small Box -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>12</h3>
                            <p>Departments</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-briefcase"></i>
                        </div>
                        <a href="/department" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>75%</h3>
                            <p>Task Completion</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-checkmark"></i>
                        </div>
                        <a href="#" class="small-box-footer">Details <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>44</h3>
                            <p>Employees</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="/user" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>24</h3>
                            <p>Pending Issues</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-alert-circled"></i>
                        </div>
                        <a href="#" class="small-box-footer">Details <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-info card-outline">
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
                                    <b>Join Date</b> <a class="float-right">{{ Auth::user()->join_date ? \Carbon\Carbon::parse(Auth::user()->join_date)->format('d M Y')  : '' }}</a>
                                </li>
                            </ul>

                            <a href="#" class="btn btn-info btn-block"><b>Edit Profile</b></a>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Performance Overview</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <canvas id="unitPerformanceChart" style="height: 200px;"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <canvas id="userPerformanceChart" style="height: 200px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity List -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activities</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                <li class="item">
                                    <div class="product-info">
                                        <a href="#" class="product-title">Employee of the Month
                                            <span class="badge badge-success float-right">Achievement</span></a>
                                        <span class="product-description">
                                            Awarded to {{ Auth::user()->name }}
                                        </span>
                                    </div>
                                </li>
                                <li class="item">
                                    <div class="product-info">
                                        <a href="#" class="product-title">Department Goal Met
                                            <span class="badge badge-info float-right">Milestone</span></a>
                                        <span class="product-description">
                                            Sales target achieved 80%
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('styles')
    <style>
        #unitPerformanceChart, #userPerformanceChart {
    width: 100% !important;
    height: 300px !important;
}
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data for Unit Performance
        const unitLabels = {!! json_encode($unitPerformance->keys()) !!};
        const unitData = {!! json_encode($unitPerformance) !!};

        const unitChartData = {
            labels: unitLabels,
            datasets: [
                {
                    label: 'On Progress',
                    data: unitLabels.map(unit => unitData[unit].find(status => status.status === 'On Progress')?.total || 0),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                },
                {
                    label: 'Done',
                    data: unitLabels.map(unit => unitData[unit].find(status => status.status === 'Done')?.total || 0),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                },
            ]
        };

        const unitPerformanceChart = new Chart(document.getElementById('unitPerformanceChart').getContext('2d'), {
            type: 'doughnut', // Tipe chart pie
            data: {
                labels: unitLabels,
                datasets: [{
                    data: [
                        ...unitLabels.map(unit => unitData[unit].find(status => status.status === 'On Progress')?.total || 0),
                        ...unitLabels.map(unit => unitData[unit].find(status => status.status === 'Done')?.total || 0)
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)', // Warna untuk On Progress
                        'rgba(75, 192, 192, 0.6)', // Warna untuk Done
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw; // Menampilkan label dan data
                            }
                        }
                    }
                }
            }
        });

        // Data for User Performance
        const userLabels = {!! json_encode($userPerformance->keys()) !!};
        const userData = {!! json_encode($userPerformance) !!};

        const userChartData = {
            labels: userLabels,
            datasets: [
                {
                    label: 'On Progress',
                    data: userLabels.map(user => userData[user].find(status => status.status === 'On Progress')?.total || 0),
                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                },
                {
                    label: 'Done',
                    data: userLabels.map(user => userData[user].find(status => status.status === 'Done')?.total || 0),
                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                },
            ]
        };

        const userPerformanceChart = new Chart(document.getElementById('userPerformanceChart').getContext('2d'), {
            type: 'bar',
            data: userChartData,
            options: {
                responsive: true,
                scales: { x: { beginAtZero: true } }
            }
        });
    </script>
@endsection

