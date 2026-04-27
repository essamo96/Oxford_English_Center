<div class="top-menu">
    <ul class="nav navbar-nav pull-right">
        <li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                data-close-others="true" aria-expanded="false">
                <i class="icon-bell"></i>
                @if ($closed_clases->count() != null || $Students->count() > 0)
                    <span
                        class="badge badge-default"><?= $Students->count() > 0 ? $closed_clases->count() + $Students->count() : $closed_clases->count() ?></span>
                @endif

            </a>
            <ul class="dropdown-menu">
                <li class="external">
                    @if ($closed_clases->count() != null || $Students->count() > 0)
                        <h3>You have
                            <strong><?= $Students->count() > 0 ? $closed_clases->count() + $Students->count() : $closed_clases->count() ?>
                                pending</strong>
                        </h3>
                    @else
                        <h3>لايوجد مجموعات منتهي حتي الحظة
                        </h3>
                    @endif
                </li>
                <li>
                    <div class="slimScrollDiv"
                        style="position: relative; overflow: hidden; width: auto; height: 250px;">
                        <ul class="dropdown-menu-list scroller" style="height: 250px; overflow: hidden; width: auto;"
                            data-handle-color="#637283" data-initialized="1">
                            @foreach ($closed_clases as $item)
                                <li>
                                    <a href="{{ route('closed_classes.view') }}">
                                        <span
                                            class="time">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>

                                        <i class="bi bi-check-circle mx-2"
                                            style="color: #ff6600 ; width:20px; font-size:18px;"></i> المدرس/ة <span
                                            style="color: #ffee01">{{ $item->Teacher->name }}</span> اغلق/ ت المجموعة
                                        <span style="color: #ffee01">{{ $item->Groups->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                            @if ($Students->count() > 0)
                                <li>
                                    <a href="{{ route('dashboard.view.membership') }}">
                                        <span
                                            class="time">{{ \Carbon\Carbon::parse($Students[0]->created_at)->diffForHumans() }}</span>

                                        <i class="bi bi-check-circle mx-2"
                                            style="color: #ff6600 ; width:20px; font-size:18px;"></i> هنالك <span
                                            style="color: #ffee01">{{ $Students->count() }}</span> طلابات جديدة بانتظار
                                        الموافقة
                                    </a>
                                </li>
                            @endif

                        </ul>
                        <div class="slimScrollBar"
                            style="background: rgb(99, 114, 131); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 121.359px;">
                        </div>
                        <div class="slimScrollRail"
                            style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(234, 234, 234); opacity: 0.2; z-index: 90; right: 1px;">
                        </div>
                    </div>
                </li>

            </ul>
        </li>

        <li class="dropdown dropdown-extended dropdown-tasks dropdown-dark" id="header_task_bar">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                data-close-others="true" aria-expanded="false">
                <i class="icon-calendar"></i>
                @if ($Groups->count() != null)
                    <span
                        class="badge badge-primary"><?= $Groups->count() ?></span>
                @endif
            </a>
            <ul class="dropdown-menu extended tasks">
                <li class="external">
                    @if ($Groups->count() != null)
                    <h3>You have
                        <span class="bold">{{$Groups->count()}} pending</span> tasks
                    </h3>
                    <a href="#">view all</a>
                    @endif
                </li>
                <li>
                    <div class="slimScrollDiv"
                        style="position: relative; overflow: hidden; width: auto; height: 275px;">
                        <ul class="dropdown-menu-list scroller" style="height: 275px; overflow: hidden; width: auto;"
                            data-handle-color="#637283" data-initialized="1">
                            @if ($Groups->count() != null)
                            @foreach ($Groups as $item)
                            <li>
                                <a href="javascript:;">
                                    <span class="task">
                                        <span class="desc">{{ $item->teacher->name  . ' : ' .$item->name}} </span>
                                        <span class="percent">{{$item->progress}} %</span>
                                    </span>
                                    <span class="progress">
                                        <span style="width: {{$item->progress}}%;" class="progress-bar progress-bar-success"
                                            aria-valuenow="{{$item->progress}}" aria-valuemin="0" aria-valuemax="100">
                                            <span class="sr-only">{{$item->progress}}% Complete</span>
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                        <div class="slimScrollBar"
                            style="background: rgb(99, 114, 131); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 7px; z-index: 99; right: 1px;">
                        </div>
                        <div class="slimScrollRail"
                            style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(234, 234, 234); opacity: 0.2; z-index: 90; right: 1px;">
                        </div>
                    </div>
                </li>
            </ul>
        </li>

        {{-- end courses --}}
        <li class="dropdown dropdown-extended dropdown-inbox dropdown-dark " id="header_inbox_bar">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                data-close-others="true" aria-expanded="true">
                <i class="icon-envelope-open"></i>
                @if ($Students_Admin_Messages->count() != null || $Teachers_Admin_Messages->count() != 0)
                    <span class="badge badge-default"><?= $Students_Admin_Messages->count() + $Teachers_Admin_Messages->count()?></span>
                @endif
            </a>
            <ul class="dropdown-menu">
                <li class="external">
                    @if ($Students_Admin_Messages->count() != null || $Teachers_Admin_Messages->count() != 0)
                        <h3>لديك
                            <span class="bold"><?= $Students_Admin_Messages->count() + $Teachers_Admin_Messages->count() ?></span> مراسلات جديدة
                        </h3>
                    @endif
                </li>
                <li>
                    <div class="slimScrollDiv"
                        style="position: relative; overflow: hidden; width: auto; height: 275px;">
                        <ul class="dropdown-menu-list scroller" style="height: 275px; overflow: hidden; width: auto;"
                            data-handle-color="#637283" data-initialized="1">
                            @if ($Students_Admin_Messages->count() != null)
                                @foreach ($Students_Admin_Messages as $item)
                                    <li>
                                        <a href="{{route('students.messages')}}">
                                            <span class="photo">
                                                @if ($item->student->image != null)
                                                    <img src="{{ url($item->student->image) }}" />
                                                @else
                                                    <img src="{{ url('assets/oxford/img/students/avatar.png') }}" />
                                                @endif
                                            </span>
                                            <span class="subject">
                                                <span class="from"> {{ $item->student->name . ' : ' . $item->title }}
                                                </span>
                                                <span
                                                    class="time">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                                </span>
                                            </span>
                                            @isset($item->content)
                                                <span class="message"> {{ \Illuminate\Support\Str::limit($item->content, 5) }}</span>
                                            @endisset
                                        </a>
                                    </li>
                                @endforeach
                            @endif
 
                            @if ($Teachers_Admin_Messages->count() != null)
                                @foreach ($Teachers_Admin_Messages as $item)
                                    <li>
                                        <a href="{{route('teachers.messages')}}">
                                            <span class="photo">
                                                @if ($item->teacher->image != null)
                                                    <img src="{{ url($item->teacher->image) }}" />
                                                @else
                                                    <img src="{{ url('assets/oxford/img/students/avatar.png') }}" />
                                                @endif
                                            </span>
                                            <span class="subject">
                                                <span class="from"> {{ $item->teacher->name . ' : ' . $item->title }}
                                                </span>
                                                <span
                                                    class="time">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                                </span>
                                            </span>
                                            @isset($item->content)
                                                <span class="message"> {{ \Illuminate\Support\Str::limit($item->content, 5) }}</span>
                                            @endisset
                                        </a>
                                    </li>
                                @endforeach
                            @endif
 
                        </ul>
                        <div class="slimScrollBar"
                            style="background: rgb(99, 114, 131); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 158.211px;">
                        </div>
                        <div class="slimScrollRail"
                            style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(234, 234, 234); opacity: 0.2; z-index: 90; right: 1px;">
                        </div>
                    </div>
                </li>
            </ul>
        </li>
        {{-- student msg --}}
        <li class="dropdown dropdown-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                data-close-others="true">
                <img alt="" class="img-circle" src="{{ url('assets/oxford/img/logo.png') }}"
                    style="width: 46px;
                height: 46px;" />
                <span class="username username-hide-on-mobile"> {{ Auth::user()->name }} </span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-default">
                <li>
                    <a href="{{ route('dashboard.profile') }}">
                        <i class="icon-user"></i> الملف الشخصي </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.password') }}">
                        <i class="icon-lock"></i> تغيير كلمة المرور </a>
                </li>
                <li class="divider"> </li>
                <li>
                    <a href="{{ route('app.logout') }}">
                        <i class="icon-key"></i> تسجيل الخروج </a>
                </li>
            </ul>
        </li>

    </ul>
</div>
