@extends('admin.layouts.app')


@section('title', __('activity.title'))


@section('page-title', __('activity.title'))


@section('page-description')
    {{ __('activity.subtitle') }}
@endsection





@section('content')


<div class="activity-page">





    {{-- Header --}}

    <section class="activity-header">


        <div class="activity-header__content">


            <div class="activity-icon">

                <svg viewBox="0 0 24 24">

                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>

                </svg>

            </div>



            <div>

                <h2>

                    {{ __('activity.title') }}

                </h2>


                <p>

                    {{ __('activity.description') }}

                </p>


            </div>


        </div>




        <div class="activity-total">


            <span>

                {{ __('activity.total') }}

            </span>


            <strong>

                {{ $activities->total() }}

            </strong>


        </div>



    </section>









    {{-- Card --}}


    <section class="dashboard-card activity-card">





        <div class="activity-toolbar">


            <div class="activity-search">


                <svg viewBox="0 0 24 24">

                    <circle cx="11" cy="11" r="8"></circle>

                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>

                </svg>



                <input
                    type="text"
                    placeholder="{{ __('activity.search') }}"
                >


            </div>




        </div>









        <div class="table-wrapper">



            <table class="admin-table">



                <thead>


                    <tr>


                        <th>#</th>


                        <th>
                            {{ __('activity.user') }}
                        </th>



                        <th>
                            {{ __('activity.action') }}
                        </th>



                        <th>
                            {{ __('activity.module') }}
                        </th>



                        <th>
                            {{ __('activity.description') }}
                        </th>



                        <th>
                            {{ __('activity.date') }}
                        </th>



                    </tr>


                </thead>







                <tbody>



                @forelse($activities as $activity)



                    <tr>



                        <td>

                            {{ $loop->iteration }}

                        </td>






                        <td>


                            <div class="activity-user">


                                <span class="activity-avatar">

                                    {{ strtoupper(
                                        substr(
                                            $activity->user?->name ?? 'S',
                                            0,
                                            1
                                        )
                                    ) }}

                                </span>



                                <span>

                                    {{ $activity->user?->name ?? __('activity.system') }}

                                </span>


                            </div>


                        </td>







                        <td>



                            <span
                                class="activity-action
                                activity-action--{{ strtolower($activity->action) }}"
                            >


                                {{ __('activity.actions.' . strtolower($activity->action)) }}


                            </span>



                        </td>







                        <td>


                            <span class="activity-module">


                                {{ $activity->module }}


                            </span>



                        </td>







                        <td class="activity-description">


                            {{ $activity->description }}



                        </td>







                        <td>


                            <time>


                                {{ $activity->created_at->diffForHumans() }}


                            </time>


                        </td>






                    </tr>





                @empty



                    <tr>


                        <td colspan="6" class="empty-state">


                            {{ __('activity.empty') }}



                        </td>


                    </tr>



                @endforelse




                </tbody>





            </table>



        </div>









        <div class="pagination-wrapper">


            {{ $activities->links() }}



        </div>






    </section>





</div>


@endsection