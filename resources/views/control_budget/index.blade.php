@extends('layouts.app')
@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/images/logo_koperasi_indonesia.png') }}" alt="AdminLTELogo"
            height="60" width="60">
    </div>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Control Budget</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Control Budget</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    @if (Auth::user()->role == 1 || Auth::user()->role == 2 || Auth::user()->role == 3)
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @forelse ($departments as $department)
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 class="text-lg">{{ $department->name }}</h3>

                                    <p>Department Budget</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-briefcase"></i>
                                </div>
                                <a href="/control-budget/cost-overview/{{ $department->id }}"
                                    class="small-box-footer text-dark">More
                                    info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @empty
                        <p>No department found</p>
                    @endforelse
                </div>
            </div>
        </section>
    @elseif(Auth::user()->unit_id != 1)
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @forelse ($departments as $department)
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 class="text-lg">{{ $department->name }}</h3>

                                    <p>Department Budget</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-briefcase"></i>
                                </div>
                                <a href="/control-budget/expenses/{{ $department->id }}" class="small-box-footer text-dark">More
                                    info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @empty
                        <p>No department found</p>
                    @endforelse
                </div>
            </div>
        </section>
    @else
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @forelse ($departments as $department)
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 class="text-lg">{{ $department->name }}</h3>

                                    <p>Department Budget</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-briefcase"></i>
                                </div>
                                <a href="/control-budget/unit/{{ $department->id }}" class="small-box-footer text-dark">More
                                    info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @empty
                        <p>No department found</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

@endsection
