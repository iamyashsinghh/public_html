@php
if(Auth::guard('admin')->check()){
$auth_user = Auth::guard('admin')->user();
}else if(Auth::guard('manager')->check()){
$auth_user = Auth::guard('manager')->user();
}else if(Auth::guard('nonvenue')->check()){
$auth_user = Auth::guard('nonvenue')->user();
}else if(Auth::guard('team')->check()){
$auth_user = Auth::guard('team')->user();
}

$uri_arr = explode(".", Route::currentRouteName());
$uri = end($uri_arr);

@endphp
<aside class="main-sidebar sidebar-dark-danger" style="background: var(--wb-dark-red);">
    <a href="{{route('admin.dashboard')}}" class="brand-link text-center">
        <img src="{{asset('wb-logo2.webp')}}" alt="AdminLTE Logo" style="width: 80% !important;">
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <a href="javascript:void(0);" onclick="handle_view_image('{{$auth_user->profile_image}}', '{{route('admin.team.updateProfileImage', $auth_user->id)}}')">
                    <img src="{{$auth_user->profile_image}}" onerror="this.src = null; this.src='{{asset('/images/default-user.png')}}'" class="img-circle elevation-2" alt="User Image" style="width: 43px; height: 43px;">
                </a>
            </div>
            <div class="info text-center py-0">
                <a href="javascript:void(0);" class="d-block">{{$auth_user->name}} - {{$auth_user->get_role->name}}</a>
                <span class="text-xs text-bold" style="color: #c2c7d0;">{{$auth_user->venue_name ?: 'N/A'}}</span>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('admin.dashboard')}}" class="nav-link {{$uri == "dashboard" ? 'active' : ''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link venue-crm_collapse_link">
                        <i class="nav-icon fab fa-app-store"></i>
                        <p>Venue CRM
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.team.list')}}" class="link_prop w-100 team_link">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Team Members</p>
                                </a>
                                {{-- <a href="{{route('admin.team.new')}}" class="team_link" title="Add New"><i class="fa fa-plus"></i></a> --}}
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{Route::currentRouteName() == 'admin.team.login_info' ? 'active': ''}}">
                                <a href="{{route('admin.team.login_info')}}" class="link_prop w-100">
                                    <i class="fas fa-right-to-bracket nav-icon"></i>
                                    <p>Team Login Info</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{Route::currentRouteName() == 'admin.availability.list' ? 'active': ''}}">
                                <a href="{{route('admin.availability.list')}}" class="link_prop w-100">
                                    <i class="fas fa-calendar-days nav-icon"></i>
                                    <p>Availability <span class="badge p-0 px-1 text-light" style="background: var(--wb-renosand);">new</span></p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.role.list')}}" class="link_prop w-100 roles_link">
                                    <i class="fa fa-shield-alt nav-icon"></i>
                                    <p>Roles</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.lead.list')}}" class="link_prop w-100 leads_link">
                                    <i class="fas fa-star nav-icon"></i>
                                    <p>Leads</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.bookings.list')}}" class="link_prop w-100 bookings_link">
                                    <i class="fas fa-bookmark nav-icon"></i>
                                    <p>Bookings</p>
                                </a>
                            </span>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link nonvenue-crm_collapse_link">
                        <i class="nav-icon fab fa-slack"></i>
                        <p>Non Venue CRM
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.vendorLocality.list')}}" class="link_prop w-100 vendor-localities_link">
                                    <i class="fa fa-paperclip nav-icon"></i>
                                    <p>Vendor Localities</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.vendorCategory.list')}}" class="link_prop w-100 vendor-categories_link">
                                    <i class="fa fa-paperclip nav-icon"></i>
                                    <p>Vendor Categories</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.vendor.list')}}" class="link_prop w-100 vendors_link">
                                    <i class="fa fa-users nav-icon"></i>
                                    <p>Vendors</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.nvlead.list')}}" class="link_prop w-100 nv-leads_link">
                                    <i class="fa fa-star nav-icon"></i>
                                    <p>NV Leads</p>
                                </a>
                            </span>
                        </li>
                    </ul>
                </li>
                {{-- <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link whatsapp-collapse_link">
                        <i class="nav-icon fab fa-slack"></i>
                        <p>Whatapp
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center  {{Route::currentRouteName() == 'whatsapp.campain.list' ? 'active': ''}}">
                                <a href="{{route('whatsapp.campain.list')}}" class="link_prop w-100 whatsapp-task_link">
                                    <i class="fa fa-paperclip nav-icon"></i>
                                    <p>Campaigns</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.vendor.list')}}" class="link_prop w-100 vendors_link">
                                    <i class="fa fa-users nav-icon"></i>
                                    <p>Logs</p>
                                </a>
                            </span>
                        </li>
                    </ul>
                </li> --}}
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link bdm-crm_collapse_link">
                        <i class="nav-icon fab fa-slack"></i>
                        <p>BDM CRM
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center {{ Route::currentRouteName() == 'admin.bdm.lead.list' ? 'active' : '' }}">
                                <a href="{{route('admin.bdm.lead.list')}}" class="link_prop w-100 bdm-lead_link">
                                    <i class="fa fa-paperclip nav-icon"></i>
                                    <p>Leads</p>
                                </a>
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-flex justify-content-between align-items-center">
                                <a href="{{route('admin.nvlead.list')}}" class="link_prop w-100 nv-leads_link">
                                    <i class="fa fa-star nav-icon"></i>
                                    <p>NV Leads</p>
                                </a>
                            </span>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview {{ Route::currentRouteName() == 'whatsapp.campain.list' ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link whatsapp-collapse_link {{ Route::is('whatsapp.*') ? 'active' : '' }}">
                        <i class="nav-icon fab fa-whatsapp"></i>
                        <p>
                            WhatsApp CRM
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="{{ Route::is('whatsapp.*') ? 'display: block;' : '' }}">
                        <li class="nav-item">
                            <a href="{{route('whatsapp.campain.templates')}}" class="nav-link {{ Route::currentRouteName() == 'whatsapp.campain.templates' ? 'active' : '' }}">
                                <i class="fa fa-paperclip nav-icon"></i>
                                <p>Templates</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('whatsapp.campain.campaign')}}" class="nav-link {{ Route::currentRouteName() == 'whatsapp.campain.campaign' ? 'active' : '' }}">
                                <i class="fa fa-paperclip nav-icon"></i>
                                <p>Campaign</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('whatsapp.campain.list')}}" class="nav-link {{ Route::currentRouteName() == 'whatsapp.campain.list' ? 'active' : '' }}">
                                <i class="fa fa-paperclip nav-icon"></i>
                                <p>Bulk Messaging Tasks</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('whatsapp.campain.logs')}}" class="nav-link {{ Route::currentRouteName() == 'whatsapp.campain.logs' ? 'active' : '' }}">
                                <i class="fa fa-paperclip nav-icon"></i>
                                <p>Bulk Messaging Logs</p>
                            </a>
                        </li>
                        {{-- ... other menu items ... --}}
                    </ul>
                    <li class="nav-item">
                        <a href="{{route('admin.activity.logs')}}" class="nav-link {{$uri == "activity.logs" ? 'active' : ''}}">

                            <i class="nav-icon fas fa-home"></i>
                            <p>Activty Logs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.editEnv')}}" class="nav-link {{$uri == "editEnv" ? 'active' : ''}}">
                            <i class="nav-icon fa-solid fa-gear"></i>
                             <p>CRM Configration</p>
                        </a>
                    </li>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
    $('.nav-sidebar').tree();
    function initialize_sidebar_collapse() {

        const sidebar_collapsible_elem = document.getElementById('sidebar_collapsible_elem');
        const localstorage_value = localStorage.getItem('sidebar_collapse');
        if (localstorage_value !== null) {
            if (localstorage_value == "true") {
                sidebar_collapsible_elem.setAttribute('data-collapse', 0); // 0 means: collapse
                document.body.classList.add('sidebar-collapse');
            }
        }
    }
    initialize_sidebar_collapse();
</script>
