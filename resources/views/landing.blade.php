<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="/assets/"
    data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Academic Tracker</title>
    <meta
        name="description"
        content="Academic Tracker is a comprehensive education management platform for schools to track students, teachers, parents, classes, assignments, and grades."
    />

    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"
    />

    <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css" />

    <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />

    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <div class="layout-page">
            <nav
                class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme mt-3"
                id="layout-navbar"
            >
                <div class="navbar-nav align-items-center">
                    <a href="{{ route('landing') }}" class="nav-link fw-bold">
                        <i class="bx bxs-graduation me-2"></i>Academic Tracker
                    </a>
                </div>

                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        <li class="nav-item d-none d-md-block me-3">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item d-none d-md-block me-3">
                            <a class="nav-link" href="#how">How It Works</a>
                        </li>

                        @auth
                            <li class="nav-item">
                                <a
                                    href="{{ Auth::user()->role->name === 'student' ? route('student-dashboard') : route('show-dashboard') }}"
                                    class="btn btn-primary text-white"
                                >
                                    Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="btn btn-primary text-white">Login</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </nav>

            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row">
                        <div class="col-lg-8 mb-4 order-0">
                            <div class="card">
                                <div class="d-flex align-items-end row">
                                    <div class="col-sm-7">
                                        <div class="card-body">
                                            <h5 class="card-title text-primary">Modern school tracking</h5>
                                            <p class="mb-4">
                                                Manage students, classes, activity types, and grades in one place with
                                                role-based dashboards for admins, teachers, parents, and students.
                                            </p>

                                            <div class="d-flex flex-wrap gap-2">
                                                @auth
                                                    <a
                                                        href="{{ Auth::user()->role->name === 'student' ? route('student-dashboard') : route('show-dashboard') }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        Open Dashboard
                                                    </a>
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Get Started</a>
                                                @endauth
                                                <a href="#features" class="btn btn-sm btn-outline-secondary">See Features</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5 text-center text-sm-left">
                                        <div class="card-body pb-0 px-0 px-md-4">
                                            <img
                                                src="/assets/img/illustrations/man-with-laptop-light.png"
                                                height="160"
                                                alt="Academic Tracker"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 order-1">
                            <div class="card h-100">
                                <div class="card-body">
                                    <span class="fw-semibold d-block mb-1">Built for</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-label-primary">Admins</span>
                                        <span class="badge bg-label-info">Teachers</span>
                                        <span class="badge bg-label-warning">Parents</span>
                                        <span class="badge bg-label-success">Students</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="pb-1 mb-4" id="features">Features</h5>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="bx bx-md bxs-graduation"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-0">Students</h5>
                                    </div>
                                    <p class="mb-0 text-muted">Track profiles, enrollments, and progress in a clean dashboard.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class="bx bx-md bxs-chalkboard"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-0">Classes</h5>
                                    </div>
                                    <p class="mb-0 text-muted">Organize classes by grade and subject, then manage rosters.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-warning">
                                                <i class="bx bx-md bxs-spreadsheet"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-0">Grades</h5>
                                    </div>
                                    <p class="mb-0 text-muted">Create activities and record grades with consistent, readable tables.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="bx bx-md bxs-user-detail"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-0">Roles</h5>
                                    </div>
                                    <p class="mb-0 text-muted">Role-based access for admins, teachers, parents, and students.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="pb-1 mb-4" id="how">How It Works</h5>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <span class="badge bg-label-primary mb-2">Step 1</span>
                                    <h5>Set up structure</h5>
                                    <p class="mb-0 text-muted">Configure grades, subjects, terms, and classes.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <span class="badge bg-label-primary mb-2">Step 2</span>
                                    <h5>Add people</h5>
                                    <p class="mb-0 text-muted">Create users, then link students with parents and classes.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <span class="badge bg-label-primary mb-2">Step 3</span>
                                    <h5>Track progress</h5>
                                    <p class="mb-0 text-muted">Record activities, publish grades, and keep everyone aligned.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer m-3">Swole Devs &copy {{now()->format('Y')}}</div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/vendor/libs/jquery/jquery.js"></script>
<script src="/assets/vendor/libs/popper/popper.js"></script>
<script src="/assets/vendor/js/bootstrap.js"></script>
<script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="/assets/vendor/js/menu.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
