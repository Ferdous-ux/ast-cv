<aside
    class="admin-sidebar"
    id="adminSidebar"
>

    <div class="admin-sidebar__brand">

        <a
            href="{{ route('admin.dashboard') }}"
            class="admin-brand"
        >

            <span class="admin-brand__mark">
                A
            </span>

            <span class="admin-brand__text">

                <span class="admin-brand__name">
                    AST-CV
                </span>

                <span class="admin-brand__label">
                    Administration
                </span>

            </span>

        </a>

    </div>


    <div class="admin-sidebar__content">

        <nav
            class="admin-nav"
            aria-label="Main navigation"
        >


            {{-- Overview --}}
            <section class="admin-nav__section">

                <h2 class="admin-nav__label">
                    {{ __('navigation.overview') }}
                </h2>

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="admin-nav__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"
                >

                    <span class="admin-nav__icon">

                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                        </svg>

                    </span>

                    <span class="admin-nav__text">
                        {{ __('navigation.dashboard') }}
                    </span>

                    <span class="admin-nav__active-indicator"></span>

                </a>

            </section>


            {{-- Management --}}
            <section class="admin-nav__section">

                <h2 class="admin-nav__label">
                    {{ __('navigation.management') }}
                </h2>


                {{-- Staff --}}
                <a
                    href="{{ route('admin.staff.index') }}"
                    class="admin-nav__link {{ request()->routeIs('admin.staff.*') ? 'is-active' : '' }}"
                >

                    <span class="admin-nav__icon">

                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 21a8 8 0 0 1 16 0"></path>
                        </svg>

                    </span>

                    <span class="admin-nav__text">
                        {{ __('navigation.staff') }}
                    </span>

                    <span class="admin-nav__active-indicator"></span>

                </a>


                {{-- Users --}}
                <a
                    href="{{ route('admin.users.index') }}"
                    class="admin-nav__link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}"
                >

                    <span class="admin-nav__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>

                    </span>

                    <span class="admin-nav__text">
                        {{ __('navigation.users') }}
                    </span>

                    <span class="admin-nav__active-indicator"></span>

                </a>

{{-- Jobs --}}
<a
    href="{{ route('admin.jobs.index') }}"
    class="admin-nav__link {{ request()->routeIs('admin.jobs.*') ? 'is-active' : '' }}"
>
    <span class="admin-nav__icon">
        <svg viewBox="0 0 24 24">
            <rect x="3" y="7" width="18" height="13" rx="2"></rect>
            <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <path d="M3 12h18"></path>
            <path d="M10 12v2h4v-2"></path>
        </svg>
    </span>

    <span class="admin-nav__text">
        {{ __('navigation.jobs') }}
    </span>

    <span class="admin-nav__active-indicator"></span>
</a>


{{-- Resumes --}}
<a
    href="{{ route('admin.resumes.index') }}"
    class="admin-nav__link {{ request()->routeIs('admin.resumes.*') ? 'is-active' : '' }}"
>
    <span class="admin-nav__icon">
        <svg viewBox="0 0 24 24">
            <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path>
            <path d="M14 2v6h6"></path>
            <circle cx="10" cy="12" r="2"></circle>
            <path d="M7 18c.8-2 2-3 3-3s2.2 1 3 3"></path>
            <path d="M15 12h2"></path>
            <path d="M15 16h2"></path>
        </svg>
    </span>

    <span class="admin-nav__text">
        {{ __('navigation.resumes') }}
    </span>

    <span class="admin-nav__active-indicator"></span>
</a>


{{-- Content --}}
<section class="admin-nav__section">

    <h2 class="admin-nav__label">
        {{ __('navigation.content') }}
    </h2>


    {{-- Skills --}}
    {{-- Skills --}}
<a
    href="{{ route('admin.skills.index') }}"
    class="admin-nav__link {{ request()->routeIs('admin.skills.*') ? 'is-active' : '' }}"
>
    <span class="admin-nav__icon">
        <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9"></circle>
            <circle cx="12" cy="12" r="5"></circle>
            <circle cx="12" cy="12" r="1"></circle>
            <path d="M15 9l5-5"></path>
            <path d="M17 4h3v3"></path>
        </svg>
    </span>

    <span class="admin-nav__text">
        {{ __('navigation.skills') }}
    </span>

    <span class="admin-nav__active-indicator"></span>
</a>

    {{-- Languages --}}
    {{-- Languages --}}
<a
    href="{{ route('admin.languages.index') }}"
    class="admin-nav__link {{ request()->routeIs('admin.languages.*') ? 'is-active' : '' }}"
>
    <span class="admin-nav__icon">
        <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M3 12h18"></path>
            <path d="M12 3a15 15 0 0 1 0 18"></path>
            <path d="M12 3a15 15 0 0 0 0 18"></path>
        </svg>
    </span>

    <span class="admin-nav__text">
        {{ __('navigation.languages') }}
    </span>

    <span class="admin-nav__active-indicator"></span>
</a>

</section>


            {{-- Intelligence --}}
            <section class="admin-nav__section">

                <h2 class="admin-nav__label">
                    {{ __('navigation.intelligence') }}
                </h2>


                {{-- ATS --}}
                <a
                    href="{{ route('admin.ats.index') }}"
                    class="admin-nav__link {{ request()->routeIs('admin.ats.*') ? 'is-active' : '' }}"
                >

                    <span class="admin-nav__icon">

                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9"></circle>
                            <circle cx="12" cy="12" r="5"></circle>
                            <circle cx="12" cy="12" r="1"></circle>
                            <path d="M16 8l5-5"></path>
                            <path d="M17 3h4v4"></path>
                        </svg>

                    </span>

                    <span class="admin-nav__text">
                        {{ __('navigation.ats') }}
                    </span>

                    <span class="admin-nav__active-indicator"></span>

                </a>

            </section>


            {{-- System --}}
            <section class="admin-nav__section">

                <h2 class="admin-nav__label">
                    {{ __('navigation.system') }}
                </h2>


                {{-- Notifications --}}
                <a
                    href="{{ route('admin.notifications.index') }}"
                    class="admin-nav__link {{ request()->routeIs('admin.notifications.*') ? 'is-active' : '' }}"
                >

                    <span class="admin-nav__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>

                    </span>

                    <span class="admin-nav__text">
                        {{ __('navigation.notifications') }}
                    </span>

                    @if(auth()->user()->unreadNotificationsCount() > 0)

                        <span class="admin-nav__badge">
                            {{ auth()->user()->unreadNotificationsCount() }}
                        </span>

                    @endif

                    <span class="admin-nav__active-indicator"></span>

                </a>


                {{-- Activity Logs --}}
                <a
                    href="{{ route('admin.activity.index') }}"
                    class="admin-nav__link {{ request()->routeIs('admin.activity.*') ? 'is-active' : '' }}"
                >

                    <span class="admin-nav__icon">

                        <svg viewBox="0 0 24 24">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>

                    </span>

                    <span class="admin-nav__text">
                        {{ __('navigation.activity') }}
                    </span>

                    <span class="admin-nav__active-indicator"></span>

                </a>


                {{-- Roles --}}
                <a
                    href="{{ route('admin.roles.index') }}"
                    class="admin-nav__link {{ request()->routeIs('admin.roles.*') ? 'is-active' : '' }}"
                >

                    <span class="admin-nav__icon">

                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M6 21a6 6 0 0 1 12 0"></path>
                        </svg>

                    </span>

                    <span class="admin-nav__text">
                        {{ __('roles.title') }}
                    </span>

                    <span class="admin-nav__active-indicator"></span>

                </a>


                {{-- Permissions --}}
                <a
                    href="{{ route('admin.permissions.index') }}"
                    class="admin-nav__link {{ request()->routeIs('admin.permissions.*') ? 'is-active' : '' }}"
                >

                    <span class="admin-nav__icon">

                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>

                    </span>

                    <span class="admin-nav__text">
                        {{ __('permissions.title') }}
                    </span>

                    <span class="admin-nav__active-indicator"></span>

                </a>


                {{-- Settings --}}
                <a
                    href="#"
                    class="admin-nav__link"
                >

                    <span class="admin-nav__text">
                        {{ __('navigation.settings') }}
                    </span>

                </a>

            </section>

        </nav>

    </div>


    <div class="admin-sidebar__footer">

        <form
            method="POST"
            action="{{ route('admin.logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="admin-logout"
            >

                <span class="admin-nav__text">
                    {{ __('admin.logout') }}
                </span>

            </button>

        </form>

    </div>

</aside>


<div
    class="admin-sidebar-overlay"
    id="adminSidebarOverl-ay"
></div>