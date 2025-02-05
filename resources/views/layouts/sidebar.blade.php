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
                <!-- <li class="nav-item active">
                    <a class="nav-link" href="{{ route('report-initial-recognition.index') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span class="d-none d-md-inline" style="font-size: 14px;">Initial Recognition</span>
                    </a>
                </li> -->

                <hr class="sidebar-divider">

                <div class="sidebar-heading" style="color: white; margin-left:14px; font-size: 14px;">
                    Menu
                </div>

                <!-- Report Dropdown -->
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center">
                        <i class="fas fa-file"></i>
                        <p class="ms-2 mb-0 text-start" style="font-size: 14px;">Report Loan</p>
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Initial Recognition -->
                       <li class="nav-item">
                            <div class="sidebar-dropdown">
                                <a href="#" class="nav-link d-flex align-items-center sidebar-dropdown-toggle">
                                    <p class="ms-2 mb-0" style="font-size: 14px;">Report Initial Recognition</p>
                                    <i class="bi bi-chevron-right ms-auto"></i>
                                </a>
                                <div class="sidebar-dropdown-menu">
                                    <a href="{{ route('report-initial-recognition.index') }}" class="dropdown-link">Effective</a>
                                    <a href="{{ route('report-initial-recognition.simple-interest') }}" class="dropdown-link">Simple Interest</a>
                                </div>
                            </div>
                        </li>

                        <!-- Outstanding Report -->
                       <li class="nav-item">
                            <div class="sidebar-dropdown">
                                <a href="#" class="nav-link d-flex align-items-center sidebar-dropdown-toggle">
                                    <p class="ms-2 mb-0" style="font-size: 14px;">Report Outstanding</p>
                                    <i class="bi bi-chevron-right ms-auto"></i>
                                </a>
                                <div class="sidebar-dropdown-menu">
                                    <a href="{{ route('report-outstanding-eff.view', ['id_pt' => Auth::user()->id_pt]) }}" class="dropdown-link">Effective</a>
                                    <a href="{{ route('report-outstanding-si.view', ['id_pt' => Auth::user()->id_pt])}}" class="dropdown-link">Simple Interest</a>
                                </div>
                            </div>
                        </li>

                        <!-- Journal Report -->
                       <li clatss="nav-item">
                            <div class="sidebar-dropdown">
                                <a href="#" class="nav-link d-flex align-items-center sidebar-dropdown-toggle">
                                    <p class="ms-2 mb-0" style="font-size: 14px;">Report Journal</p>
                                    <i class="bi bi-chevron-right ms-auto"></i>
                                </a>
                                <div class="sidebar-dropdown-menu">
                                    <a href="{{ route('report-journal-eff.index') }}" class="dropdown-link">Effective</a>
                                    <a href="{{ route('report-journal-si.index') }}" class="dropdown-link">Simple Interest</a>
                                </div>
                            </div>
                        </li>

                        <!-- Detail Report -->
                        <!-- <li class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center">
                                <p class="ms-2 mb-0 text-start" style="white-space: nowrap; font-size: 14px;">Detail Report</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <div class="sidebar-dropdown">
                                        <a href="#" class="nav-link d-flex align-items-center sidebar-dropdown-toggle">
                                            <p class="ms-2 mb-0" style="font-size: 14px;">Accrual Interest</p>
                                            <i class="bi bi-chevron-right ms-auto"></i>
                                        </a>
                                        <div class="sidebar-dropdown-menu">
                                            <a href="{{ route('report-acc-eff.index') }}" class="dropdown-link">Effective</a>
                                            <a href="{{ route('report-acc-si.index') }}" class="dropdown-link">Simple Interest</a>
                                            <a href="#" class="dropdown-link">Securities</a>
                                        </div>
                                    </div>
                                    <div class="sidebar-dropdown">
                                        <a href="#" class="nav-link d-flex align-items-center sidebar-dropdown-toggle">
                                            <p class="ms-2 mb-0" style="font-size: 14px;">Amortised Cost</p>
                                            <i class="bi bi-chevron-right ms-auto"></i>
                                        </a>
                                        <div class="sidebar-dropdown-menu">
                                            <a href="{{ route('report-amorcost-eff.index') }}" class="dropdown-link">Effective</a>
                                            <a href="{{ route('report-amorcost-si.index') }}" class="dropdown-link">Simple Interest</a>
                                            <a href="#" class="dropdown-link">Securities</a>
                                        </div>
                                    </div>
                                    <div class="sidebar-dropdown">
                                        <a href="#" class="nav-link d-flex align-items-center sidebar-dropdown-toggle">
                                            <p class="ms-2 mb-0" style="font-size: 14px;">Amortised Initial Cost</p>
                                            <i class="bi bi-chevron-right ms-auto"></i>
                                        </a>
                                        <div class="sidebar-dropdown-menu">
                                            <a href="{{ route('report-amorinitcost-eff.index') }}" class="dropdown-link">Effective</a>
                                            <a href="{{ route('report-amorinitcost-si.index') }}" class="dropdown-link">Simple Interest</a>
                                            <a href="#" class="dropdown-link">Securities</a>
                                        </div>
                                    </div>
                                    <div class="sidebar-dropdown">
                                        <a href="#" class="nav-link d-flex align-items-center sidebar-dropdown-toggle">
                                            <p class="ms-2 mb-0" style="font-size: 14px;">Amortised Initial Fee</p>
                                            <i class="bi bi-chevron-right ms-auto"></i>
                                        </a>
                                        <div class="sidebar-dropdown-menu">
                                            <a href="{{ route('report-amorinitfee-eff.index') }}" class="dropdown-link">Effective</a>
                                            <a href="{{ route('report-amorinitfee-si.index') }}" class="dropdown-link">Simple Interest</a>
                                            <a href="#" class="dropdown-link">Securities</a>
                                        </div>
                                    </div>
                                    <div class="sidebar-dropdown">
                                        <a href="#" class="nav-link d-flex align-items-center sidebar-dropdown-toggle">
                                            <p class="ms-2 mb-0" style="font-size: 14px;">Expected Cashflow</p>
                                            <i class="bi bi-chevron-right ms-auto"></i>
                                        </a>
                                        <div class="sidebar-dropdown-menu">
                                            <a href="{{ route('report-expectcfeff-eff.index') }}" class="dropdown-link">Effective</a>
                                            <a href="{{ route('report-expectcf-si.index') }}" class="dropdown-link">Simple Interest</a>
                                            <a href="#" class="dropdown-link">Securities</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>-->

                        <!-- Effective -->
                        <!-- <li class="nav-item">
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
                        </li> -->

                        <!-- Simple Interest -->
                        <!-- <li class="nav-item">
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
                        </li> -->
                        </ul>
                </li>
                        
                        <!-- Securities -->
                        <li class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center">
                                <i class="bi bi-shield-lock" style="font-size: 15px;"></i>
                                <p class="ms-2 mb-0 text-start" style="white-space: nowrap; font-size: 14px;">Report Securities</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('securities.initial-recognition-treasury.index')}}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Initial Recognition</p>
                                    </a>
                                    <div class="sidebar-dropdown">
                                        <a href="#" class="nav-link d-flex  sidebar-dropdown-toggle">
                                            <p class=" mb-0" style="font-size: 14px;">Report Outstanding</p>
                                            <i class="bi bi-chevron-right ms-auto"></i>
                                        </a>
                                        <div class="sidebar-dropdown-menu">
                                            <a href="{{ route('securities.outstanding-balance-treasury.index') }}" class="dropdown-link">Outstanding FVTOCI</a>
                                            <a href="{{ route('securities.outstanding-balance-amortized-cost.index') }}" class="dropdown-link">Outstanding Amortized Cost</a>
                                        </div>
                                    </div>
                                    <a href="{{route('securities.evaluation-treasury-bond.index')}}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Evaluation Treasury Bond</p>
                                    </a>
                                    <a href="{{route('report-journal-securities.index')}}" class="nav-link">
                                        <p class="text-start" style="font-size: 14px;">Report Journal Securities</p>
                                    </a>
                                    <!-- <a href="{{route('report-calculated-accrual-coupon.index')}}" class="nav-link">
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
                                    </a> -->
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
                                    <a href="{{ route('simple-interest.outstanding.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-start" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-1 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload Data Outstanding</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('coaSimple.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-start" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-1 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Daftar Menu CoA</p>
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
                                        <p class="ms-2 mb-0 text-start" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload File tblmaster_tmp</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('effective.outstanding.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-start" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload Data Outstanding</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('coaEffective.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-start" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Daftar Menu CoA</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Securities -->
                        <li class="nav-item">
                            <a href="#" class="nav-link d-flex align-items-center">
                                <p class="ms-2 mb-0 text-start" style="white-space: nowrap; font-size: 14px;">Securities</p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('upload.securities.data.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Upload Data Securities</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('upload.securities.tblmaster_tmpbid.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">Master TmpBid</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('upload.price.securities.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">tblpricesecurities</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('upload.coa.securities.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">tblcoasecurities</p>
                                    </a>
                                </li>
                            </ul> 
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('upload.rating.securities.index') }}" class="nav-link d-flex align-items-center">
                                        <p class="ms-2 mb-0 text-center" style="width: 30px; font-size: 14px;">✦</p>
                                        <p class="ms-3 mb-0 flex-grow-1 text-start" style="font-size: 14px;">tblratingsecurities</p>
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
                    <li class="nav-item">
                        <a href="{{ route('dashboard.upload') }}" class="nav-link d-flex align-items-center">
                            <i class="bi bi-people-fill"></i>
                            <p class="ms-2 mb-0" style="font-size: 14px;">Upload Image Dashboard</p>
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

    .sidebar-dropdown {
        position: relative;
        width: 100%;
    }

    .sidebar-dropdown-toggle {
        width: 100%;
        color: #b8c7ce !important;
        transition: all 0.3s ease;
        font-size: 14px !important;
    }

    .sidebar-dropdown-toggle:hover {
        background-color: rgba(255,255,255,.1);
        color: #fff !important;
    }

    .sidebar-dropdown-toggle p {
        font-size: 14px !important;
    }

    .sidebar-dropdown-toggle i {
        font-size: 14px;
    }

    .sidebar-dropdown-menu {
        display: none;
        position: fixed;
        left: 250px;
        min-width: 200px;
        background-color: #343a40;
        border: 1px solid rgba(255,255,255,.1);
        z-index: 9999;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .sidebar-dropdown-menu.show {
        display: block;
    }

    .dropdown-link {
        display: block;
        padding: 8px 15px;
        color: #b8c7ce;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .dropdown-link:hover {
        background-color: rgba(255,255,255,.1);
        color: #fff;
        text-decoration: none;
    }

    /* Reset overflow untuk container */
    .wrapper,
    .main-sidebar,
    .sidebar,
    .nav-sidebar,
    .nav-item {
        overflow: visible !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.sidebar-dropdown-toggle');
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Tutup semua dropdown yang terbuka
            document.querySelectorAll('.sidebar-dropdown-menu').forEach(menu => {
                if (menu !== this.nextElementSibling) {
                    menu.classList.remove('show');
                }
            });
            
            // Toggle dropdown yang diklik
            const menu = this.closest('.sidebar-dropdown').querySelector('.sidebar-dropdown-menu');
            menu.classList.toggle('show');
            
            // Atur posisi menu
            if (menu.classList.contains('show')) {
                const rect = this.getBoundingClientRect();
                menu.style.top = `${rect.top}px`;
            }
        });
    });
    
    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sidebar-dropdown')) {
            document.querySelectorAll('.sidebar-dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
});
</script>