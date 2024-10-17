 <!-- BEGIN: Main Menu-->

        <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
            <div class="main-menu-content">
                <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                    <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                        <a href="/dashboard">
                            <i class="fa fa-home"></i>
                            <span class="menu-title" data-i18n="Dashboard Hospital">Dashboard</span>
                        </a>
                    </li>

                    @if(Auth::user()->role_id == 1)
                    <li class="navigation-header">
                        <span data-i18n="Professional">Professional</span>
                        <i class="la la-ellipsis-h" data-toggle="tooltip" data-placement="right" data-original-title="Professional"></i>
                    </li>
                    <li class="nav-item {{ Request::is('distributor*') ? 'active' : '' }}">
                        <a href="{{ route('distributor.index') }}">
                            <i class="fa fa-user"></i>
                            <span class="menu-title" data-i18n="Report">Distributor</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('driver*') ? 'active' : '' }}">
                        <a href="{{ route('driver.index') }}">
                            <i class="fa fa-car"></i>
                            <span class="menu-title" data-i18n="Report">Driver</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('product*') || Request::is('map_product_price*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-box"></i>
                            <span class="menu-title" data-i18n="Appointment">Product</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::is('product*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('product.index') }}">
                                    <i></i><span>Product</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('map_product_price*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('map_product_price.index') }}">
                                    <i></i><span>Product Price</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ Request::is('admin_order*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="menu-title" data-i18n="Appointment">Order</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::is('admin_order*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('admin_order.index') }}">
                                    <i></i><span>Order</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ Request::is('admin_bills*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-file-invoice"></i>
                            <span class="menu-title" data-i18n="Appointment">Bills</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::is('admin_bills*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('admin_bills.index') }}">
                                    <i></i><span>Bills</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item {{ Request::is('admin_driver_task*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-clipboard-list"></i>
                            <span class="menu-title" data-i18n="Appointment">Driver Task</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::is('admin_driver_task/index') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('admin_driver_task.index') }}">
                                    <i></i><span>Task</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('admin_driver_task/add') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('admin_driver_task.add') }}">
                                    <i></i><span>Add Task</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ Request::is('admin_cred*') ? 'active' : '' }}">
                        <a href="{{ route('admin_cred.index') }}">
                            <i class="fa fa-box"></i>
                            <span class="menu-title" data-i18n="Report">CRED</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->role_id == 2)
                    <li class="nav-item {{ Request::is('distributor_product*') || Request::is('cart*') || Request::is('order*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="menu-title" data-i18n="Patients">Product</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::is('distributor_product*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('distributor_product.index') }}">
                                    <i></i><span>Order Products</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('cart*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('cart.index') }}">
                                    <i></i><span>Shopping Cart</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('order*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('order.index') }}">
                                    <i></i><span>Placed Order</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ Request::is('bills*') ? 'active' : '' }}">
                        <a href="{{ route('bills.index') }}">
                            <i class="fa fa-file-invoice"></i>
                            <span class="menu-title" data-i18n="Report">Bills</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('distributor_cred*') ? 'active' : '' }}">
                        <a href="{{ route('distributor_cred.index') }}">
                            <i class="fa fa-box"></i>
                            <span class="menu-title" data-i18n="Report">CRED</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->role_id == 3)
                    <li class="nav-item {{ Request::is('driver_task*') || Request::is('cart*') || Request::is('order*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-clipboard-list"></i>
                            <span class="menu-title" data-i18n="Patients">Task</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::is('driver_task*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('driver_task.index') }}">
                                    <i></i><span>Alloted Task</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ Request::is('driver_cred*')}}">
                        <a href="#">
                            <i class="fa fa-clipboard-list"></i>
                            <span class="menu-title" data-i18n="Patients">Cred</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::is('driver_cred.*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('driver_cred.index') }}">
                                    <i></i><span>Cred</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('driver_cred.*') ? 'active' : '' }}">
                                <a class="menu-item" href="{{ route('driver_cred.add') }}">
                                    <i></i><span>Add Cred</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

            {{-- <li class=" nav-item"><a href="hospital-payment-reports.html"><i class="la la-bar-chart"></i><span class="menu-title" data-i18n="Report">Report</span></a>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-dollar"></i><span class="menu-title" data-i18n="Payments">Payments</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="hospital-payments.html"><i></i><span>Payments</span></a>
                    </li>
                    <li><a class="menu-item" href="hospital-add-payments.html"><i></i><span>Add Payments</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-clipboard"></i><span class="menu-title" data-i18n="Invoice">Invoice</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="invoice-summary.html"><i></i><span data-i18n="Invoice Summary">Invoice Summary</span></a>
                    </li>
                    <li><a class="menu-item" href="invoice-template.html"><i></i><span data-i18n="Invoice Template">Invoice Template</span></a>
                    </li>
                    <li><a class="menu-item" href="invoice-list.html"><i></i><span data-i18n="Invoice List">Invoice List</span></a>
                    </li>
                </ul>
            </li>
            <li class=" navigation-header"><span data-i18n="Apps">Apps</span><i class="la la-ellipsis-h" data-toggle="tooltip" data-placement="right" data-original-title="Apps"></i>
            </li>
            <li class=" nav-item"><a href="full-calender-basic.html"><i class="la la-calendar"></i><span class="menu-title" data-i18n="Calendar">Calendar</span></a>
            </li>
            <li class=" nav-item"><a href="app-email.html"><i class="la la-envelope"></i><span class="menu-title" data-i18n="Inbox">Inbox</span></a>
            </li>
            <li class=" nav-item"><a href="app-chat.html"><i class="la la-comments"></i><span class="menu-title" data-i18n="Chat">Chat</span></a>
            </li>
            <li class=" nav-item"><a href="app-kanban.html"><i class="la la-file-text"></i><span class="menu-title" data-i18n="Kanban">Kanban</span></a>
            </li>
            <li class=" navigation-header"><span data-i18n="User Interface">User Interface</span><i class="la la-ellipsis-h" data-toggle="tooltip" data-placement="right" data-original-title="User Interface"></i>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-server"></i><span class="menu-title" data-i18n="Components">Components</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="component-alerts.html"><i></i><span data-i18n="Alerts">Alerts</span></a>
                    </li>
                    <li><a class="menu-item" href="component-callout.html"><i></i><span data-i18n="Callout">Callout</span></a>
                    </li>
                    <li><a class="menu-item" href="#"><i></i><span data-i18n="Buttons">Buttons</span></a>
                        <ul class="menu-content">
                            <li><a class="menu-item" href="component-buttons-basic.html"><i></i><span data-i18n="Basic Buttons">Basic Buttons</span></a>
                            </li>
                            <li><a class="menu-item" href="component-buttons-extended.html"><i></i><span data-i18n="Extended Buttons">Extended Buttons</span></a>
                            </li>
                        </ul>
                    </li>
                    <li><a class="menu-item" href="component-carousel.html"><i></i><span data-i18n="Carousel">Carousel</span></a>
                    </li>
                    <li><a class="menu-item" href="component-collapse.html"><i></i><span data-i18n="Collapse">Collapse</span></a>
                    </li>
                    <li><a class="menu-item" href="component-dropdowns.html"><i></i><span data-i18n="Dropdowns">Dropdowns</span></a>
                    </li>
                    <li><a class="menu-item" href="component-list-group.html"><i></i><span data-i18n="List Group">List Group</span></a>
                    </li>
                    <li><a class="menu-item" href="component-modals.html"><i></i><span data-i18n="Modals">Modals</span></a>
                    </li>
                    <li><a class="menu-item" href="component-pagination.html"><i></i><span data-i18n="Pagination">Pagination</span></a>
                    </li>
                    <li><a class="menu-item" href="component-navs-component.html"><i></i><span data-i18n="Navs Component">Navs Component</span></a>
                    </li>
                    <li><a class="menu-item" href="component-tabs-component.html"><i></i><span data-i18n="Tabs Component">Tabs Component</span></a>
                    </li>
                    <li><a class="menu-item" href="component-pills-component.html"><i></i><span data-i18n="Pills Component">Pills Component</span></a>
                    </li>
                    <li><a class="menu-item" href="component-tooltips.html"><i></i><span data-i18n="Tooltips">Tooltips</span></a>
                    </li>
                    <li><a class="menu-item" href="component-popovers.html"><i></i><span data-i18n="Popovers">Popovers</span></a>
                    </li>
                    <li><a class="menu-item" href="component-badges.html"><i></i><span data-i18n="Badges">Badges</span></a>
                    </li>
                    <li><a class="menu-item" href="component-pill-badges.html"><i></i><span>Pill Badges</span></a>
                    </li>
                    <li><a class="menu-item" href="component-progress.html"><i></i><span data-i18n="Progress">Progress</span></a>
                    </li>
                    <li><a class="menu-item" href="component-media-objects.html"><i></i><span data-i18n="Media Objects">Media Objects</span></a>
                    </li>
                    <li><a class="menu-item" href="component-scrollable.html"><i></i><span data-i18n="Scrollable">Scrollable</span></a>
                    </li>
                    <li><a class="menu-item" href="component-spinners.html"><i></i><span data-i18n="Spinners">Spinners</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-unlock"></i><span class="menu-title" data-i18n="Authentication">Authentication</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="login-with-bg-image.html" target="_blank"><i></i><span>Login</span></a>
                    </li>
                    <li><a class="menu-item" href="register-with-bg-image.html" target="_blank"><i></i><span>SignIn</span></a>
                    </li>
                    <li><a class="menu-item" href="recover-password.html" target="_blank"><i></i><span>Forgot Password</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-file-text"></i><span class="menu-title" data-i18n="Form Layouts">Form Layouts</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="form-layout-basic.html"><i></i><span data-i18n="Basic Forms">Basic Forms</span></a>
                    </li>
                    <li><a class="menu-item" href="form-layout-horizontal.html"><i></i><span data-i18n="Horizontal Forms">Horizontal Forms</span></a>
                    </li>
                    <li><a class="menu-item" href="form-layout-hidden-labels.html"><i></i><span data-i18n="Hidden Labels">Hidden Labels</span></a>
                    </li>
                    <li><a class="menu-item" href="form-layout-form-actions.html"><i></i><span data-i18n="Form Actions">Form Actions</span></a>
                    </li>
                    <li><a class="menu-item" href="form-layout-row-separator.html"><i></i><span data-i18n="Row Separator">Row Separator</span></a>
                    </li>
                    <li><a class="menu-item" href="form-layout-bordered.html"><i></i><span data-i18n="Bordered">Bordered</span></a>
                    </li>
                    <li><a class="menu-item" href="form-layout-striped-rows.html"><i></i><span data-i18n="Striped Rows">Striped Rows</span></a>
                    </li>
                    <li><a class="menu-item" href="form-layout-striped-labels.html"><i></i><span data-i18n="Striped Labels">Striped Labels</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-paste"></i><span class="menu-title" data-i18n="Form Wizard">Form Wizard</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="form-wizard-circle-style.html"><i></i><span data-i18n="Circle Style">Circle Style</span></a>
                    </li>
                    <li><a class="menu-item" href="form-wizard-notification-style.html"><i></i><span data-i18n="Notification Style">Notification Style</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-table"></i><span class="menu-title" data-i18n="Bootstrap Tables">Bootstrap Tables</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="table-basic.html"><i></i><span data-i18n="Basic Tables">Basic Tables</span></a>
                    </li>
                    <li><a class="menu-item" href="table-border.html"><i></i><span data-i18n="Table Border">Table Border</span></a>
                    </li>
                    <li><a class="menu-item" href="table-sizing.html"><i></i><span data-i18n="Table Sizing">Table Sizing</span></a>
                    </li>
                    <li><a class="menu-item" href="table-styling.html"><i></i><span data-i18n="Table Styling">Table Styling</span></a>
                    </li>
                    <li><a class="menu-item" href="table-components.html"><i></i><span data-i18n="Table Components">Table Components</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item"><a href="#"><i class="la la-area-chart"></i><span class="menu-title" data-i18n="Chartjs">Chartjs</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="chartjs-line-charts.html"><i></i><span data-i18n="Line charts">Line charts</span></a>
                    </li>
                    <li><a class="menu-item" href="chartjs-bar-charts.html"><i></i><span data-i18n="Bar charts">Bar charts</span></a>
                    </li>
                    <li><a class="menu-item" href="chartjs-pie-doughnut-charts.html"><i></i><span data-i18n="Pie &amp; Doughnut charts">Pie &amp; Doughnut charts</span></a>
                    </li>
                    <li><a class="menu-item" href="chartjs-scatter-charts.html"><i></i><span data-i18n="Scatter charts">Scatter charts</span></a>
                    </li>
                    <li><a class="menu-item" href="chartjs-polar-radar-charts.html"><i></i><span data-i18n="Polar &amp; Radar charts">Polar &amp; Radar charts</span></a>
                    </li>
                    <li><a class="menu-item" href="chartjs-advance-charts.html"><i></i><span data-i18n="Advance charts">Advance charts</span></a>
                    </li>
                </ul>
            </li> --}}
        </ul>
    </div>
</div>

<!-- END: Main Menu-->
