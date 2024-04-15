@php
$auth_user = Auth::guard('vendor')->user();

$uri = Route::currentRouteName();
@endphp
<aside class="main-sidebar sidebar-dark-danger" style="background: var(--wb-dark-red);">
    <a href="{{route('vendor.dashboard')}}" class="brand-link text-center">
        <img src="{{asset('wb-logo2.webp')}}" alt="AdminLTE Logo" style="width: 80% !important;">
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <a href="javascript:void(0);" onclick="handle_view_image('{{$auth_user->profile_image}}', '{{route('vendor.updateProfileImage')}}')">
                    <img src="{{$auth_user->profile_image}}" onerror="this.src = null; this.src='{{asset('/images/default-user.png')}}'" class="img-circle elevation-2" alt="User Image" style="width: 43px; height: 43px;">
                </a>
            </div>
            <div class="info py-0">
                <a href="javascript:void(0);" class="d-block text-wrap">{{$auth_user->business_name ?: 'N/A'}}</a>
                <span class="text-xs text-bold" style="color: #c2c7d0;">{{$auth_user->name}}</span>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('vendor.dashboard')}}" class="nav-link w-100 {{$uri == "vendor.dashboard" ? 'active' : ''}}">

                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('vendor.lead.list')}}" class="nav-link w-100 {{$uri == "vendor.lead.list" ? 'active' : ''}}">
                        <i class="nav-icon fas fa-star"></i>
                        <p>Leads</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('vendor.task.list')}}" class="nav-link w-100 {{$uri == "vendor.task.list" ? 'active' : ''}}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Tasks</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('vendor.meeting.list')}}" class="nav-link w-100 {{$uri == "vendor.meeting.list" ? 'active' : ''}}">
                        <i class="fas fa-business-time nav-icon"></i>
                        <p>Meetings</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('vendor.help.list')}}" class="nav-link w-100 {{$uri == "vendor.help.list" ? 'active' : ''}}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Rm Help Support</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<script>
    function initialize_sidebar_collapse() {
        const sidebar_collapsible_elem = document.getElementById('sidebar_collapsible_elem');
        const localstorage_value = localStorage.getItem('sidebar_collapse');
        if (localstorage_value !== null) {
            if (localstorage_value == "true") {
                sidebar_collapsible_elem.setAttribute('data-collapse', 0);
                document.body.classList.add('sidebar-collapse');
            }
        }
    }
    initialize_sidebar_collapse();
</script>