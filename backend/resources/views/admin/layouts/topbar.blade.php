<header class="admin-topbar">


    <div class="admin-topbar__left">


        <button
            type="button"
            class="admin-mobile-menu"
            id="adminMobileMenu"
            aria-label="Open navigation"
            aria-controls="adminSidebar"
            aria-expanded="false"
        >


            <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >

                <line x1="4" y1="6" x2="20" y2="6"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="18" x2="20" y2="18"></line>

            </svg>


        </button>








        <div class="admin-page-heading">


            <h1 class="admin-page-heading__title">

                @yield('page-title', __('admin.dashboard'))

            </h1>





            @hasSection('page-description')


                <p class="admin-page-heading__description">

                    @yield('page-description')

                </p>


            @endif



        </div>



    </div>












    <div class="admin-topbar__right">







        {{-- Notifications --}}


        <div class="admin-notification-wrapper">



            <button
                type="button"
                class="admin-notification"
                id="notificationButton"
                aria-label="Notifications"
            >



                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >

                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>

                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>

                </svg>





                @if(auth()->user()->unreadNotificationsCount() > 0)


                    <span class="admin-notification__badge">

                        {{ auth()->user()->unreadNotificationsCount() }}

                    </span>


                @endif



            </button>









            {{-- Notification Dropdown --}}


            <div
                class="admin-notification-dropdown"
                id="notificationDropdown"
            >



                <div class="notification-header">


                    <strong>

                        {{ __('notifications.title') }}

                    </strong>





                    <form
                        method="POST"
                        action="{{ route('admin.notifications.read-all') }}"
                    >

                        @csrf


                        <button
                            type="submit"
                            class="notification-read-all"
                        >

                            {{ __('notifications.mark_all_read') }}

                        </button>


                    </form>


                </div>









                @forelse(
                    auth()->user()
                    ->notifications()
                    ->latest()
                    ->limit(5)
                    ->get()
                    as $notification
                )




                    <div class="
                        admin-notification-item
                        {{ $notification->read_at ? '' : 'unread' }}
                    ">




                        <form
                            method="POST"
                            action="{{ route(
                                'admin.notifications.read',
                                $notification->id
                            ) }}"
                        >

                            @csrf




                            <button
                                type="submit"
                                class="notification-open"
                            >



                                <strong>


                                    {{ __(
                                        $notification->data['title']
                                        ??
                                        ''
                                    ) }}


                                </strong>






                                <p>


                                    {{ __(
                                        $notification->data['message']
                                        ??
                                        ''
                                    ) }}


                                </p>






                                <small>

                                    {{ 
                                        $notification
                                        ->created_at
                                        ->diffForHumans()
                                    }}

                                </small>



                            </button>



                        </form>








                        <form
                            method="POST"
                            action="{{ route(
                                'admin.notifications.destroy',
                                $notification->id
                            ) }}"
                        >

                            @csrf

                            @method('DELETE')



                            <button
                                type="submit"
                                class="notification-delete"
                                title="{{ __('notifications.deleted') }}"
                            >

                                ×

                            </button>


                        </form>






                    </div>





                @empty




                    <div class="admin-notification-empty">


                        {{ __('notifications.empty') }}


                    </div>




                @endforelse







                <a
                    href="{{ route('admin.notifications.index') }}"
                    class="notification-footer"
                >

                    {{ __('notifications.view_all') }}


                </a>





            </div>





        </div>












        {{-- Language Switcher --}}



        <form
            method="POST"
            action="{{ route('admin.locale') }}"
        >


            @csrf



            <input
                type="hidden"
                name="locale"
                value="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}"
            >





            <button
                type="submit"
                class="admin-language-switcher"
            >

                {{ app()->getLocale() === 'ar'
                    ? 'English'
                    : 'العربية'
                }}



            </button>



        </form>









        {{-- Current User --}}



        <div class="admin-user">



            <div class="admin-user__avatar">


                {{ strtoupper(
                    substr(auth()->user()->name,0,1)
                ) }}


            </div>







            <div class="admin-user__info">



                <span class="admin-user__name">

                    {{ auth()->user()->name }}

                </span>






                <span class="admin-user__role">


                    {{
                        auth()->user()
                        ->roles
                        ->first()?->name
                        ??
                        __('admin.admin')
                    }}



                </span>




            </div>




        </div>







    </div>



</header>