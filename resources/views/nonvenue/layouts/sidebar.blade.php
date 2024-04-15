@php
    $auth_user = Auth::guard('nonvenue')->user();
    $auth_user_role = $auth_user->get_role;
    $uri = Route::currentRouteName();
    $VendorCategory = \App\Models\VendorCategory::get();
    function isActiveRoute($routeName, $parameter = null) {
    $currentRoute = Route::currentRouteName();
    $isActive = $currentRoute === $routeName;
    if ($parameter) {
        $isActive &= request()->segment(4) == $parameter;
    }
    return $isActive ? 'active' : '';
    }
    function isActiveRouteMenu($routeName, $parameter = null) {
    $currentRouteName = Route::currentRouteName();
    $isActive = $currentRouteName === $routeName;
    if ($parameter !== null) {
        $currentParameter = request()->segment(4); // Adjust the segment number based on your URL structure
        $isActive &= ($currentParameter == $parameter);
    }
    return $isActive ? 'menu-open' : '';
}


@endphp
<aside class="main-sidebar sidebar-dark-danger" style="background: var(--wb-dark-red);">
    <a href="{{ route('nonvenue.dashboard') }}" class="brand-link text-center">
        <img src="{{ asset('wb-logo2.webp') }}" alt="AdminLTE Logo" style="width: 80% !important;">
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <a href="javascript:void(0);"
                    onclick="handle_view_image('{{ $auth_user->profile_image }}', '{{ route('nonvenue.updateProfileImage') }}')">
                    <img src="{{ $auth_user->profile_image }}"
                        onerror="this.src = null; this.src='{{ asset('/images/default-user.png') }}'"
                        class="img-circle elevation-2" alt="User Image" style="width: 43px; height: 43px;">
                </a>
            </div>
            <div class="info text-center py-0">
                <a href="javascript:void(0);" class="d-block">{{ $auth_user->name }} - {{ $auth_user_role->name }}</a>
                <span class="text-xs text-bold" style="color: #c2c7d0;">{{ $auth_user->venue_name ?: 'N/A' }}</span>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('nonvenue.dashboard') }}"
                        class="nav-link {{ $uri == 'nonvenue.dashboard' ? 'active' : '' }}">

                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('nonvenue.lead.list') }}"
                        class="nav-link w-100 {{ $uri == 'nonvenue.lead.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-star"></i>
                        <p>Leads</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('nonvenue.task.list') }}"
                        class="nav-link w-100 {{ $uri == 'nonvenue.task.list' ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Tasks</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('nonvenue.vendor.help.list') }}"
                        class="nav-link w-100 {{ $uri == 'nonvenue.vendor.help.list' ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Vendor Help Messages</p>
                    </a>
                </li>
                <li class="nav-item {{ isActiveRouteMenu('nonvenue.vendor.list') }}">
                    <a href="javascript:void(0);"
                       class="nav-link nonvenue-crm_collapse_link {{ isActiveRoute('nonvenue.vendor.list') }}">
                        <i class="nav-icon fab fa-slack"></i>
                        <p>Vendors
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach ($VendorCategory as $lists)
                            <li class="nav-item">
                                <span class="nav-link d-flex justify-content-between align-items-center {{ isActiveRoute('nonvenue.vendor.list', $lists->id) }}">
                                    <a href="{{ route('nonvenue.vendor.list', $lists->id) }}"
                                       class="link_prop w-100 vendor-categories_link {{ isActiveRoute('nonvenue.vendor.list', $lists->id) }}">
                                        <i class="fa fa-paperclip nav-icon"></i>
                                        <p>{{ $lists->name }}</p>
                                    </a>
                                </span>
                            </li>
                        @endforeach
                    </ul>
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
                sidebar_collapsible_elem.setAttribute('data-collapse', 0); // 0 means: collapse
                document.body.classList.add('sidebar-collapse');
            }
        }
    }
    initialize_sidebar_collapse();
</script>
