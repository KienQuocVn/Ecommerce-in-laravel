<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('shipper.dashboard')}}">
    <div class="sidebar-brand-text mx-3">Shipper</div>
  </a>

  <hr class="sidebar-divider my-0">

  <li class="nav-item {{request()->routeIs('shipper.dashboard') ? 'active' : ''}}">
    <a class="nav-link" href="{{route('shipper.dashboard')}}">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Bảng điều khiển</span></a>
  </li>

  <hr class="sidebar-divider">

  <div class="sidebar-heading">
    Đơn giao hàng
  </div>

  <li class="nav-item {{request()->routeIs('shipper.deliveries.index') ? 'active' : ''}}">
    <a class="nav-link" href="{{route('shipper.deliveries.index')}}">
      <i class="fas fa-truck"></i>
      <span>Đơn của tôi</span>
    </a>
  </li>

  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
