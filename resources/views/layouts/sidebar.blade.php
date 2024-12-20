<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('landing') }}" class="brand-link" style="text-decoration: none">
        <x-application-logo />
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="image">
                    @if (Auth::user()->upload_foto)
                        <img src="{{ asset('storage/' . Auth::user()->upload_foto) }}" class="img-circle elevation-2" alt="User Image" style="width: 60px; height: 60px;">
                    @else
                        <img src="{{ asset('lte/dist/img/images.jpg') }}" class="img-circle elevation-2" alt="Default User Image" style="width: 60px; height: 60px;">
                    @endif
                </div>

                <div class="info ms-2" style="max-width: 150px; white-space: nowrap;">
                    <span class="d-block" style="font-size: 14px; color: rgb(255, 255, 255); word-wrap: break-word;">{{ Auth::user()->name }}</span>
                    <span class="text-muted" style="color: rgb(255, 255, 255); font-size: 14px;">
                        {{ Auth::user()->role }}
                        <span class="online-status ms-2"></span>
                    </span>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="gear-icon">
                <i class="fas fa-cog"></i>
            </a>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Initial Recognition -->
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('report-initial-recognition.index') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span class="d-none d-md-inline" style="font-size: 14px;">Initial Recognition</span>
                    </a>
                </li>

                <hr class="sidebar-divider">

                <div class="sidebar-heading" style="color: white; margin-left:14px; font-size: 14px;">
                    Menu
                </div>

                <!-- Report Dropdown -->
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center ">
                        <i class="fas fa-file"></i>
                        <p class="ms-2 mb-0 text-start" style="font-size: 14px;">Report</p>
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Effective -->
                        <li class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center">
                                <i class="fas fa-signal"></i>
                                <p class="ms-2 mb-0 text-start" style="white-space: nowrap; font-size: 14px;">Effective</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('report-acc-eff.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Accrual Interest</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-amorcost-eff.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Cost</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-amorinitcost-eff.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Initial Cost</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-amorinitfee-eff.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Initial Fee</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-expectcfeff-eff.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Expected Cashflow</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-journal-eff.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Journal</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-outstanding-eff.view', ['id_pt' => Auth::user()->id_pt]) }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Outstanding</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-initial-recognition.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Initial Recognition</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Simple Interest -->
                        <li class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center">
                                <i class="fas fa-money-bill-wave"></i>
                                <p class="ms-2 mb-0 text-start" style="white-space: nowrap; font-size: 14px;">Simple Interest</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('report-acc-si.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Accrual Interest</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-amorcost-si.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Cost</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-amorinitcost-si.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Initial Cost</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-amorinitfee-si.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Initial Fee</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-expectcf-si.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Expected Cashflow</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-journal-si.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Journal</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-outstanding-si.index') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Outstanding</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('report-initial-recognition.simple-interest') }}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Initial Recognition</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Securities -->
                        <li class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center">
                                <i class="bi bi-shield-lock" style="font-size: 15px;"></i>
                                <p class="ms-2 mb-0 text-start" style="white-space: nowrap; font-size: 14px;">Securities</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('report-calculated-accrual-coupon.index')}}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Calculated Accrual Coupon</p>
                                    </a>
                                    <a href="{{route('report-amortised-cost.index')}}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Cost</p>
                                    </a>
                                    <a href="{{route('report-amortised-initial-disc.index')}}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Initial Disc</p>
                                    </a>
                                    <a href="{{route('report-amortised-initial-prem.index')}}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Amortised Initial Prem</p>
                                    </a>
                                    <a href="{{route('report-expected-cashflow.index')}}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Expected Cash Flow</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <!-- Upload Data Files -->
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-fw fa-folder-open"></i>
                            <p class="ms-2 mb-0" style="font-size: 14px;">Upload Data Files</p>
                        </div>
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Simple Interest -->
                        <li class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center">
                                <p class="ms-2 mb-0 text-start" style="white-space: nowrap; font-size: 14px;">Simple Interest</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('simple-interest.tblmaster.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload File tblmaster_tmpcorporate</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('corporate.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload File tblcorporateloancabangdetail</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('corporate.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload Data Outstanding</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Effective -->
                        <li class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center">
                                <p class="ms-2 mb-0 text-start" style="white-space: nowrap; font-size: 14px;">Effective</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('effective.tblmaster.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload File tblmaster_tmp</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('effective.outstanding.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload Data Outstanding</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Administrator Options -->
                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
                    <div class="sidebar-heading" style="color: white; margin-left:15px; margin-top:10px; font-size: 14px;">
                        Options
                    </div>
                @endif

                <!-- User Management for Admin -->
                @if (Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.usermanajemen') }}" class="nav-link d-flex align-items-center">
                            <i class="bi bi-people-fill"></i>
                            <p class="ms-2 mb-0" style="font-size: 14px;">User Management</p>
                        </a>
                    </li>
                @endif

                <!-- User Management for Superadmin -->
                @if (Auth::user()->role === 'superadmin')
                    <li class="nav-item">
                        <a href="{{ route('usermanajemen') }}" class="nav-link d-flex align-items-center">
                            <i class="bi bi-people-fill"></i>
                            <p class="ms-2 mb-0" style="font-size: 14px;">User Management</p>
                        </a>
                    </li>
                @endif

                <!-- Mapping for Superadmin -->
                @if (Auth::user()->role === 'superadmin')
                    <li class="nav-item">
                        <a href="{{ route('mappings.index') }}" class="nav-link d-flex align-items-center">
                            <i class="bi bi-list-check"></i>
                            <p class="ms-2 mb-0" style="font-size: 14px;">Mapping</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>

<style>
    .online-status {
        width: 10px;
        height: 10px;
        background-color: green;
        border-radius: 50%;
        display: inline-block;
    }

    .gear-icon {
        color: #b8c7ce;
        font-size: 14px;
        transition: color 0.5s ease;
    }

    .gear-icon:hover {
        color: #ffffff;
    }

    .main-sidebar {
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 250px;
        position: fixed;
        transition: none;
    }

    .nav-treeview .nav-link {
        padding-left: 30px;
    }

    .nav-treeview .nav-treeview .nav-link {
        padding-left: 50px;
    }

    .nav-treeview .nav-treeview .nav-treeview .nav-link {
        padding-left: 70px;
    }

    .nav-link i {
        margin-right: 10px;
        transition: color 0.5s ease;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 10px;
        color: #fff;
        transition: background-color 0.5s ease, color 0.3s ease;
    }

    .nav-link p {
        margin: 0;
        transition: color 0.5s ease;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }
</style>