<!DOCTYPE html>
<html lang="vi">

@include('user.layouts.head')

<body id="page-top">
  <div id="wrapper">
    @include('shipper.layouts.sidebar')

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        @include('shipper.layouts.header')
        @yield('main-content')
      </div>
      @include('user.layouts.footer')
    </div>
  </div>
</body>

</html>
