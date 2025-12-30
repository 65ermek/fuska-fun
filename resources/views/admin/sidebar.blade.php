<!-- resources/views/admin/sidebar.blade.php -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('favicon.ico') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">@lang('admin.dashboard')</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @auth
                    <img src="{{ Auth::user()->avatar_url }}"
                         class="img-circle elevation-2"
                         alt="{{ Auth::user()->name }}"
                         style="width: 33px; height: 33px; object-fit: cover;">
                @else
                    <img src="{{ asset('images/avatars/default-avatar.png') }}"
                         class="img-circle elevation-2"
                         alt="Guest"
                         style="width: 33px; height: 33px; object-fit: cover;">
                @endauth
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name ?? 'Admin User' }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>@lang('admin.dashboard')</p>
                    </a>
                </li>

                <!-- Users - проверка роли -->
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-shield"></i>
                                <p>@lang('admin.moderators')</p>
                            </a>
                        </li>
                    @endif
                @endauth
                <!-- Customers - проверка роли -->
                @auth
                    @if(in_array(auth()->user()->role, ['admin', 'manager']))
                        <li class="nav-item">
                            <a href="{{ route('admin.customers.index') }}"
                               class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>@lang('admin.customers')</p>
                            </a>
                        </li>
                    @endif
                @endauth
                <!-- Jobs -->
                <li class="nav-item">
                    <a href="{{ route('admin.jobs.index') }}" class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-briefcase"></i>
                        <p>@lang('admin.jobs')</p>
                    </a>
                </li>
                <!-- Удаление фото - проверка роли -->
                @auth
                    @if(in_array(auth()->user()->role, ['admin']))
                        <li class="nav-item">
                            <a href="{{ route('admin.photos.cleanup') }}" class="nav-link {{ request()->routeIs('admin.photos.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-trash"></i>
                                <p>@lang('admin.photo_cleanup')</p>
                            </a>
                        </li>
                    @endif
                @endauth
                @auth
                    @if(in_array(auth()->user()->role, ['admin', 'manager']))
                        <li class="nav-item">
                            <a href="{{ route('admin.job-categories.index') }}" class="nav-link {{ request()->routeIs('admin.job-categories.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-briefcase"></i>
                                <p class="text-nowrap text-truncate mb-0" style="max-width: 160px;">
                                    @lang('admin.job_categories')
                                </p>
                            </a>
                        </li>
                    @endif
                @endauth
                {{-- В resources/views/layouts/admin/sidebar.blade.php --}}
                <li class="nav-item">
                    <a href="{{ route('admin.contact-requests.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>@lang('admin.contact_requests')</p>
                    </a>
                </li>
                <!-- Multi-level menu example -->
                <li class="nav-item {{ request()->is('admin/categories*') || request()->is('admin/tags*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('admin/categories*') || request()->is('admin/tags*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>
                            Content
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link {{ request()->is('admin/tags*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tags</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
