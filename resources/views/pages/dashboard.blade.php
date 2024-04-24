@extends('layouts.base')

@section('title')
    Dashboard
@endsection

@section('content')
    <script type="text/javascript" src="/assets/js/loader.js"></script>
{{--    <script type="text/javascript" src="/assets/js/chart.js"></script>--}}
    <script type="text/javascript">
        // Load the Visualization API and the corechart package
        google.charts.load('current', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded
        google.charts.setOnLoadCallback(drawChart);

        // Callback function to create and populate the data table
        function drawChart() {
            // Create the data table
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Month');
            data.addColumn('number', 'Absences');
            data.addRows([
                ['Jan', 35],
                ['Feb', 98],
                ['Mar', 53],
                ['Apr', 32],
                ['May', 54],
                ['Jun', 16],

            ]);

            // Set chart options
            var options = {
                title: 'Student Absences by Month',
                curveType: 'function', // Makes the line curved
                legend: { position: 'none' } // Hides the legend
            };

            // Instantiate and draw the chart
            var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));
            chart.draw(data, options);
        }

        // google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart4);

        function drawChart4() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Absences');

{{--            {{dd($absences)}}--}}
            @foreach($absences as $absence)
            data.addRow(['{{substr($absence->created_at, 5, 5)}}', {{$absence->absences}}]);
            @endforeach
            var options = {
                // title: 'Absences in the Past Month',
                hAxis: {title: 'Date'},
                vAxis: {title: 'Number of Absences'},
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div4'));
            chart.draw(data, options);
        }
    </script>
    <script type="text/javascript">
        // Load the Visualization API and the corechart package
        google.charts.load('current', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded
        google.charts.setOnLoadCallback(drawChart);

        // Callback function to create and populate the data table
        function drawChart() {
            // Create the data table
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Category');
            data.addColumn('number', 'Percentage');
            data.addRows([
                @foreach($classes as $class)
                ["{{$class->name.' '.$class->grade->name.' '.$class->subject->name}}", {{$class->students->count()}}],
                @endforeach
            ]);

            // Set chart options
            var options = {
                title: 'Class Distribution',
                pieHole: 0.4,
                legend: 'none'
            };

            // Instantiate and draw the chart
            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
<div class="row">
    <div class="col-lg-8 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Welcome Admin! 🎉</h5>
                        <p class="mb-4">
                            This is your academic tracker dashboard. From here you can update both the students and their parents about their
                            academic performance.
                        </p>

                        <a href="{{route('show-classes')}}" class="btn btn-sm btn-outline-primary">Open Classes</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img
                            src="/assets/img/illustrations/man-with-laptop-light.png"
                            height="140"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-md bx-user"></i>
                            </div>
                            <div class="dropdown">
                                <button
                                    class="btn p-0"
                                    type="button"
                                    id="cardOpt3"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                    <a class="dropdown-item" href="{{route('show-students')}}">View More</a>

                                </div>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Students</span>
                        <h3 class="card-title mb-2">{{$studentCount}}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-md bxs-user-check"></i>
                            </div>
                            <div class="dropdown">
                                <button
                                    class="btn p-0"
                                    type="button"
                                    id="cardOpt3"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                    <a class="dropdown-item" href="{{route('show-teachers')}}">View More</a>

                                </div>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Teachers</span>
                        <h3 class="card-title mb-2">{{$teachers->count()}}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="row">
    <!-- Order Statistics -->
    <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Class Distribution</h5>
                    <small class="text-muted">{{$classes->count()}} Total Classes</small>
                </div>
                <div class="dropdown">
                    <button
                        class="btn p-0"
                        type="button"
                        id="orederStatistics"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                        <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        <a class="dropdown-item" href="javascript:void(0);">Share</a>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
{{--                    <div class="d-flex flex-column align-items-center gap-1">--}}
{{--                        --}}
{{--                    </div>--}}
                    <div id="chart_div" style="width: 100%"></div>
                </div>
                <ul class="p-0 m-0">
                    @foreach($classes->take(5) as $class)
                    <li class="d-flex mb-4 pb-1">

                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">{{$class->name.' '.$class->grade->name.' '.$class->subject->name}}</h6>

                            </div>
                            <div class="user-progress">
                                <small class="fw-semibold">{{substr(str($class->students->count()/$studentCount*100),0,5)}}%</small>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <!--/ Order Statistics -->

    <!-- Expense Overview -->
    <div class="col-md-6 col-lg-4 order-1 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab">Absences</button>
                    </li>
                </ul>
            </div>
            <div class="card-body px-0">
                <div class="tab-content p-0">
                    <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
                        <div class="d-flex p-4 pt-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="../assets/img/icons/unicons/wallet.png" alt="User" />
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Absences</small>
                                <div class="d-flex align-items-center">
                                    <h6 class="mb-0 me-1">{{$this_week}}</h6>

                                </div>
                            </div>
                        </div>
                        <div id="chart_div4" style="width: 100%"></div>
                        <div class="d-flex justify-content-center pt-4 gap-2">

                            <div>
                                <p class="mb-n1 mt-1">Absences over the last 5 days</p>
                                @if($this_week>$prev_week)
                                <small class="text-muted">{{$this_week - $prev_week}} more than last week</small>
                                @elseif($prev_week>$this_week)
                                    <small class="text-muted">{{$prev_week - $this_week}} less than last week</small>
                                @elseif($prev_week == $this_week)
                                    <small class="text-muted">No change since last week</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Expense Overview -->

    <!-- Transactions -->
    <div class="col-md-6 col-lg-4 order-2 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Recently Enrolled</h5>

            </div>
            <div class="card-body">
                <ul class="p-0 m-0">
{{--                    {{dd($students)}}--}}
                    @foreach($students as $student)
                    <li class="d-flex mb-4 pb-1">

                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">

                                <h6 class="mb-0">{{$student->name}}</h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                                <span class="text-muted">{{$student->id_number}}</span>
                            </div>
                        </div>
                    </li>
                    @endforeach

                </ul>
            </div>
        </div>
    </div>
    <!--/ Transactions -->
</div>
@endsection
