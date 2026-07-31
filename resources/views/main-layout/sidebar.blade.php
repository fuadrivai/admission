<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="/"><img style="width:3.5rem !important; height:3.5rem !important;"
                            src="/assets/images/logo.png" alt="Logo" srcset=""> Admission
                    </a>
                </div>

                <div class="sidebar-toggler  x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item {{ Request::is('/') ? 'active' : '' }} ">
                    <a href="/" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li
                    class="sidebar-item has-sub {{ Request::is('level*') || Request::is('division*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="fa fa-graduation-cap"></i>
                        <span>Level</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/level" class="submenu-link">Level</a></li>
                        <li class="submenu-item"><a href="/division" class="submenu-link">Division</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item has-sub {{ Request::is('schoolvisit*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-building"></i>
                        <span>School Visit</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/schoolvisit" class="submenu-link">List</a></li>
                        <li class="submenu-item"><a href="/schoolvisit/setting"
                                class="submenu-link {{ Request::is('schoolvisit/setting') ? 'text-red' : '' }}">Settings</a>
                        </li>
                        <li class="submenu-item"><a target="blank" href="/schoolvisit-form"
                                class="submenu-link">Form</a>
                        </li>
                    </ul>
                </li>
                <li
                    class="sidebar-item has-sub {{ Request::is('enrolment*') || Request::is('price*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-currency-exchange"></i>
                        <span>Enrolment</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/enrolment" class="submenu-link">List</a></li>
                        <li class="submenu-item"><a href="/enrolment/setting"
                                class="submenu-link {{ Request::is('enrolment/setting') ? 'text-red' : '' }}">Settings</a>
                        </li>
                        <li class="submenu-item"><a target="blank" href="/enrolment/form/"
                                class="submenu-link">Enrolment Form</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item has-sub {{ Request::is('applicant*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-person-fill"></i>
                        <span>Document</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/applicant" class="submenu-link">List</a></li>
                    </ul>
                </li>
                <li class="sidebar-item has-sub {{ Request::is('observation*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-stack"></i>
                        <span>Observation</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/observation" class="submenu-link">user</a></li>
                        <li class="submenu-item"><a href="/observation/setting"
                                class="submenu-link {{ Request::is('observation/setting') ? 'text-red' : '' }}">Settings</a>
                        </li>
                        <li class="submenu-item"><a target="blank" href="/observation-form"
                                class="submenu-link">Form</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-title">Transaction</li>
                <li class="sidebar-item has-sub {{ Request::is('uniform*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cart-check-fill"></i>
                        <span>Uniform</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/uniform" class="submenu-link {{ Request::is('uniform') ? 'text-red' : '' }}">List</a></li>
                        {{-- <li class="submenu-item"><a href="/uniform/leaderboard" class="submenu-link {{ Request::is('uniform/leaderboard') ? 'text-red' : '' }}">Leaderboard</a> --}}
                        </li>
                        <li class="submenu-item"><a href="/uniform/setting" class="submenu-link {{ Request::is('uniform/setting') ? 'text-red' : '' }}">Settings</a></li>
                        <li class="submenu-item"><a href="/uniform/form" class="submenu-link {{ Request::is('uniform/form') ? 'text-red' : '' }}">Form</a></li>
                    </ul>
                </li>

                <li class="sidebar-title">Setting</li>
                <li class="sidebar-item has-sub {{ Request::is('setting*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="fa fa-gear"></i>
                        <span>Setting</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/setting/form" class="submenu-link">General</a>
                        <li class="submenu-item"><a href="/setting/year" class="submenu-link">Academic
                                Year</a>
                        </li>
                        <li class="submenu-item"><a href="/setting/password/change" class="submenu-link">Users</a>
                        </li>
                        <li class="submenu-item"><a href="/setting/password/change" class="submenu-link">Change
                                Password</a></li>
                        <li class="submenu-item">
                            <form action="/logout" method="POST">
                                @csrf
                                <button type="submit" style="text-decoration: none"
                                    class="submenu-link btn btn-link align-baseline">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
