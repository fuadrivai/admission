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

                @if (!auth()->check() || auth()->user()->role != 'user')
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
                @endif
                <li class="sidebar-item has-sub {{ Request::is('schoolvisit*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-building"></i>
                        <span>School Visit</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/schoolvisit" class="submenu-link">List</a></li>
                        @if (!auth()->check() || auth()->user()->role != 'user')
                            <li class="submenu-item"><a href="/schoolvisit/setting"
                                    class="submenu-link {{ Request::is('schoolvisit/setting') ? 'text-red' : '' }}">Settings</a>
                            </li>
                        @endif
                        <li class="submenu-item"><a target="blank" href="/schoolvisit-form"
                                class="submenu-link">Form</a>
                        </li>
                    </ul>
                </li>
                <li
                    class="sidebar-item has-sub {{ Request::is('enrolment*') || Request::is('price*') || Request::is('setting/discount-rule*') || Request::is('setting/statement*') || Request::is('setting/financial-document*') || Request::is('setting/registration-place*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-currency-exchange"></i>
                        <span>Enrolment</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/enrolment" class="submenu-link">List</a></li>
                        @if (!auth()->check() || auth()->user()->role != 'user')
                            {{-- <li class="submenu-item"><a href="{{ route('enrolment.merge') }}"
                                    class="submenu-link {{ Request::is('enrolment/merge') ? 'text-red' : '' }}">Merge Records</a></li> --}}
                            <li
                                class="submenu-item has-sub {{ Request::is('enrolment/setting*') || Request::is('enrolment/target*') || Request::is('setting/discount-rule*') || Request::is('setting/registration-place*') ? 'active' : '' }}">
                                <a href="#" class="submenu-link">Setting</a>
                                <ul class="submenu">
                                    <li class="submenu-item"><a href="/enrolment/setting"
                                            class="submenu-link {{ Request::is('enrolment/setting') ? 'text-red' : '' }}">Settings</a>
                                    </li>
                                    <li class="submenu-item"><a href="{{ route('enrolment.target.index') }}"
                                            class="submenu-link {{ Request::is('enrolment/target*') ? 'text-red' : '' }}">Enrolment
                                            Target</a>
                                    </li>
                                    <li class="submenu-item"><a href="/setting/discount-rule"
                                            class="submenu-link {{ Request::is('setting/discount-rule*') ? 'text-red' : '' }}">Discount
                                            Rules</a>
                                    </li>
                                    <li class="submenu-item"><a href="/setting/registration-place"
                                            class="submenu-link {{ Request::is('setting/registration-place*') ? 'text-red' : '' }}">Registration
                                            Place</a>
                                    </li>
                                </ul>
                            </li>
                            <li
                                class="submenu-item has-sub {{ Request::is('setting/statement*') || Request::is('setting/financial-document*') ? 'active' : '' }}">
                                <a href="#" class="submenu-link">Letter</a>
                                <ul class="submenu">
                                    <li class="submenu-item"><a href="/setting/statement"
                                            class="submenu-link {{ Request::is('setting/statement*') ? 'text-red' : '' }}">Parent
                                            Statement</a>
                                    </li>
                                    <li class="submenu-item"><a href="/setting/financial-document"
                                            class="submenu-link {{ Request::is('setting/financial-document*') ? 'text-red' : '' }}">Financial
                                            Agreement</a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <li
                            class="submenu-item has-sub {{ Request::is('enrolment/form*') || Request::is('enrolment/dp*') ? 'active' : '' }}">
                            <a href="#" class="submenu-link">Form</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a target="blank" href="/enrolment/form/"
                                        class="submenu-link">Enrolment Form</a>
                                </li>
                                @if (!auth()->check() || auth()->user()->role != 'user')
                                    <li class="submenu-item"><a href="{{ route('enrolment.dp.index') }}"
                                            class="submenu-link {{ Request::is('enrolment/dp*') ? 'text-red' : '' }}">Development
                                            Fee</a>
                                    </li>
                                @endif
                                <li class="submenu-item"><a target="blank"
                                        href="{{ route('enrolment.dp-public.index') }}" class="submenu-link">Development
                                        Fee Public</a>
                                </li>
                            </ul>
                        </li>
                        <li class="submenu-item"><a href="{{ route('enrolment.report') }}"
                                class="submenu-link {{ Request::is('enrolment/report') ? 'text-red' : '' }}">Report</a>
                        </li>
                    </ul>
                </li>
                @if (!auth()->check() || auth()->user()->role != 'user')
                    <li class="sidebar-item has-sub {{ Request::is('applicant*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-file-person-fill"></i>
                            <span>Document</span>
                        </a>
                        <ul class="submenu ">
                            <li class="submenu-item"><a href="/applicant" class="submenu-link">List</a></li>
                        </ul>
                    </li>
                @endif
                <li class="sidebar-item has-sub {{ Request::is('observation*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-stack"></i>
                        <span>Observation</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/observation" class="submenu-link">user</a></li>
                        @if (!auth()->check() || auth()->user()->role != 'user')
                            <li class="submenu-item"><a href="/observation/setting"
                                    class="submenu-link {{ Request::is('observation/setting') ? 'text-red' : '' }}">Settings</a>
                            </li>
                        @endif
                        <li class="submenu-item"><a target="blank" href="/observation-form"
                                class="submenu-link">Form</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-title">Merchandise</li>
                <li class="sidebar-item has-sub {{ Request::is('merchandise*') || Request::is('api/docs/merchandise') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-bag-fill"></i>
                        <span>Merchandise</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="{{ route('merchandise.dashboard') }}"
                                class="submenu-link {{ Request::is('merchandise') ? 'text-red' : '' }}">Dashboard</a></li>
                        <li class="submenu-item"><a href="{{ route('api.merchandise-docs') }}"
                                class="submenu-link {{ Request::is('api/docs/merchandise') ? 'text-red' : '' }}">Mobile API Documentation</a></li>
                        <li class="submenu-item has-sub {{ Request::is('merchandise/categories*') || Request::is('merchandise/products*') || Request::is('merchandise/selling-prices*') ? 'active' : '' }}">
                            <a href="#" class="submenu-link">Product Setup</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ route('merchandise.categories') }}"
                                        class="submenu-link {{ Request::is('merchandise/categories*') ? 'text-red' : '' }}">Categories</a></li>
                                <li class="submenu-item"><a href="{{ route('merchandise.products') }}"
                                        class="submenu-link {{ Request::is('merchandise/products*') ? 'text-red' : '' }}">Products</a></li>
                                @if (auth()->user()->role === 'admin')
                                    <li class="submenu-item"><a href="{{ route('merchandise.selling-prices') }}"
                                            class="submenu-link {{ Request::is('merchandise/selling-prices*') ? 'text-red' : '' }}">Selling Prices</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="submenu-item has-sub {{ Request::is('merchandise/stock-in*') || Request::is('merchandise/stock-opname*') || Request::is('merchandise/inventory') ? 'active' : '' }}">
                            <a href="#" class="submenu-link">Inventory</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="{{ route('merchandise.inventory') }}"
                                        class="submenu-link {{ Request::is('merchandise/inventory') ? 'text-red' : '' }}">Stock Overview</a></li>
                                @if (auth()->user()->role === 'admin')
                                    <li class="submenu-item"><a href="{{ route('merchandise.stock-ins') }}"
                                            class="submenu-link {{ Request::is('merchandise/stock-in*') ? 'text-red' : '' }}">Stock In</a></li>
                                    <li class="submenu-item"><a href="{{ route('merchandise.stock-opnames') }}"
                                            class="submenu-link {{ Request::is('merchandise/stock-opname*') ? 'text-red' : '' }}">Stock Opname</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="submenu-item has-sub {{ Request::is('merchandise/store-settings*') || Request::is('merchandise/catalog*') || Request::is('merchandise/email-templates*') ? 'active' : '' }}">
                            <a href="#" class="submenu-link">Online Store</a>
                            <ul class="submenu">
                                @if (auth()->user()->role === 'admin')
                                    <li class="submenu-item"><a href="{{ route('merchandise.store-settings') }}"
                                            class="submenu-link {{ Request::is('merchandise/store-settings*') ? 'text-red' : '' }}">Store Settings</a></li>
                                    <li class="submenu-item"><a href="{{ route('merchandise.catalog') }}"
                                            class="submenu-link {{ Request::is('merchandise/catalog*') ? 'text-red' : '' }}">Catalog</a></li>
                                    <li class="submenu-item"><a href="{{ route('merchandise.email-templates') }}"
                                            class="submenu-link {{ Request::is('merchandise/email-templates*') ? 'text-red' : '' }}">Email Templates</a></li>
                                @endif
                                <li class="submenu-item"><a target="_blank" href="{{ route('merchandise.public-store') }}"
                                        class="submenu-link">View Public Store</a></li>
                            </ul>
                        </li>
                        @if (auth()->user()->role === 'admin')
                            <li class="submenu-item has-sub {{ Request::is('merchandise/promotions*') || Request::is('merchandise/orders*') ? 'active' : '' }}">
                                <a href="#" class="submenu-link">Sales</a>
                                <ul class="submenu">
                                    <li class="submenu-item"><a href="{{ route('merchandise.promotions') }}"
                                            class="submenu-link {{ Request::is('merchandise/promotions*') ? 'text-red' : '' }}">Promotions</a></li>
                                    <li class="submenu-item"><a href="{{ route('merchandise.orders') }}"
                                            class="submenu-link {{ Request::is('merchandise/orders*') ? 'text-red' : '' }}">Order Management</a></li>
                                </ul>
                            </li>
                            <li class="submenu-item"><a href="{{ route('merchandise.reports') }}"
                                    class="submenu-link {{ Request::is('merchandise/reports*') ? 'text-red' : '' }}">Reports &amp; Analytics</a></li>
                        @endif
                    </ul>
                </li>
                <li class="sidebar-title">Transaction</li>
                <li class="sidebar-item has-sub {{ Request::is('uniform*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cart-check-fill"></i>
                        <span>Uniform</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/uniform"
                                class="submenu-link {{ Request::is('uniform') ? 'text-red' : '' }}">List</a></li>
                        <li class="submenu-item"><a href="/uniform/list"
                                class="submenu-link {{ Request::is('uniform/list') ? 'text-red' : '' }}">List
                                public</a></li>
                        @if (!auth()->check() || auth()->user()->role != 'user')
                            <li class="submenu-item"><a href="/uniform/setting"
                                    class="submenu-link {{ Request::is('uniform/setting') ? 'text-red' : '' }}">Settings</a>
                            </li>
                        @endif
                        <li class="submenu-item"><a href="/uniform/form"
                                class="submenu-link {{ Request::is('uniform/form') ? 'text-red' : '' }}">Form</a></li>
                    </ul>
                </li>
                @if (!auth()->check() || auth()->user()->role != 'user')
                    <li
                        class="sidebar-item has-sub {{ Request::is('blast*') || Request::is('email-campaigns*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-envelope"></i>
                            <span>Blast Message</span>
                        </a>
                        <ul class="submenu ">
                            <li class="submenu-item"><a href="/blast/email"
                                    class="submenu-link {{ Request::is('blast/email') ? 'text-red' : '' }}">Email</a>
                            </li>
                            {{-- <li class="submenu-item"><a href="/blast/whatsapp"
                                    class="submenu-link {{ Request::is('blast/whatsapp') ? 'text-red' : '' }}">Whatsapp</a>
                            </li> --}}
                        </ul>
                    </li>
                @endif

                <li class="sidebar-title">Form Builder</li>
                <li class="sidebar-item has-sub {{ Request::is('event*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="fa fa-calendar"></i>
                        <span>Event</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item"><a href="/event" class="submenu-link">List</a></li>
                    </ul>
                </li>
                <li class="sidebar-title">Setting</li>
                <li class="sidebar-item has-sub {{ Request::is('setting*') && !Request::is('setting/statement*') && !Request::is('setting/financial-document*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="fa fa-gear"></i>
                        <span>Setting</span>
                    </a>
                    <ul class="submenu ">
                        @if (!auth()->check() || auth()->user()->role != 'user')
                            <li class="submenu-item"><a href="/setting/role" class="submenu-link">Role</a></li>
                            <li class="submenu-item"><a href="{{ route('setting.google-sheet-master.index') }}"
                                    class="submenu-link {{ Request::is('setting/gSheet-master*') ? 'text-red' : '' }}">gSheet Master</a>
                            </li>
                            <li class="submenu-item"><a href="/setting/form" class="submenu-link">General</a>
                            </li>
                            <li class="submenu-item"><a href="/setting/year" class="submenu-link">Academic
                                    Year</a>
                            </li>
                            <li class="submenu-item"><a href="/setting/password/change"
                                    class="submenu-link">Users</a>
                            </li>
                        @endif
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
