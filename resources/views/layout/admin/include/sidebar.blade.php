<div class="page-sidebar">
    <a class="logo-box" href="index.html">
        <span>Space</span>
        <i class="icon-radio_button_unchecked" id="fixed-sidebar-toggle-button"></i>
        <i class="icon-close" id="sidebar-toggle-button-close"></i>
    </a>
    <div class="page-sidebar-inner">
        <div class="page-sidebar-menu">
            <ul class="accordion-menu">
                <li class="{{ classActiveSegment(route('admin.index'), 'active-page') }}">
                    <a href="{{ route('admin.index') }}" class="{{ classActiveSegment(route('admin.index')) }}">
                        <i class="menu-icon icon-home4"></i><span>Bảng quản trị</span>
                    </a>
                </li>
                <li class="{{ classActivePath('backend/contract', 'active-page') }}">
                    <a href="javascript:void(0);">
                        <i class="menu-icon icon-flash_on"></i><span>Hợp đồng</span><i class="accordion-icon fa fa-angle-left"></i>
                    </a>
                    <ul class="sub-menu">
                        <li><a class="{{ classActiveSegment(route('contract.index')) }}" href="{{ route('contract.index') }}">Danh sách hợp đồng</a></li>
                        <li><a class="{{ classActiveSegment(route('contract.create')) }}" href="{{ route('contract.create') }}">Nhập danh sách</a></li>
                    </ul>
                </li>
                <li class="{{ classActivePath('backend/award', 'active-page') }}">
                    <a href="javascript:void(0);">
                        <i class="menu-icon icon-layers"></i><span>Giải thưởng</span><i class="accordion-icon fa fa-angle-left"></i>
                    </a>
                    <ul class="sub-menu">
                        <li><a class="{{ classActiveSegment(route('award.index')) }}" href="{{ route('award.index') }}" class="">Danh sách giải</a></li>
                        <li><a class="{{ classActiveSegment(route('award.create')) }}" href="{{ route('award.create') }}" class="">Thêm giải thưởng</a></li>
                        <li><a class="{{ classActiveSegment(route('award.winners')) }}" href="{{ route('award.winners') }}">Quảy lý người trúng giải</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>