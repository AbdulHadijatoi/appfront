<div class="admin-header">
    <h1>@yield('header', 'Admin Dashboard')</h1>
    <div>
        <a href="{{ route('admin.add.product') }}" class="btn btn-primary">Add New Product</a>
        <a href="{{ route('logout') }}" class="btn btn-secondary">Logout</a>
    </div>
</div>
