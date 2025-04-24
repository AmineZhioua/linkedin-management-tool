<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - @yield('title')</title>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }
        .sidebar h1 {
            font-size: 24px;
            margin-bottom: 30px;
        }
        .sidebar nav ul li {
            list-style: none;
            margin-bottom: 10px;
        }
        .sidebar nav ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 4px;
        }
        .sidebar nav ul li a:hover {
            background-color: #34495e;
        }
        .sidebar nav ul.sub-menu {
            padding-left: 20px;
        }
        .sidebar nav ul.sub-menu li a {
            color: #bdc3c7;
        }
        .sidebar nav ul li.active > a {
            background-color: #34495e;
            font-weight: bold;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #fff;
        }
        .notification.success {
            background-color: #28a745;
        }
        .notification.error {
            background-color: #dc3545;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }
        .welcome-text {
            color: #555;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .dashboard-buttons {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
            justify-content: center;
        }
        .dashboard-buttons a {
            display: flex;
            align-items: center;
            padding: 20px 40px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.1s ease, background-color 0.3s ease;
        }
        .dashboard-buttons a:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .dashboard-buttons a i {
            margin-right: 15px;
            font-size: 24px;
        }
        .table-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }
        .create-btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .create-btn:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-size: 12px;
            text-transform: uppercase;
            color: #555;
        }
        td {
            font-size: 14px;
            color: #333;
        }
        .action-link {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-link:hover {
            text-decoration: underline;
        }
        .delete-btn {
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .delete-btn:hover {
            text-decoration: underline;
        }
        .form-container {
            max-width: 500px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }
        .error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        .submit-btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .cancel-link {
            color: #555;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
        }
        .cancel-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 8px;
            }
            .form-container {
                padding: 15px;
            }
            .dashboard-buttons a {
                padding: 15px 30px;
                font-size: 18px;
            }
            .dashboard-buttons a i {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li class="{{ Route::is('admin.dashboard') || Route::is('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <ul class="sub-menu">
                        <li class="{{ Route::is('admin.users.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.index') }}">Users</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        @if (session('success'))
            <div class="notification success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="notification error">
                {{ session('error') }}
            </div>
        @endif

        @section('dashboard')
            @if (Route::is('admin.dashboard'))
                <h1>Welcome to the Admin Dashboard</h1>
                <p class="welcome-text">Manage users and other resources from the sidebar.</p>
                <div class="dashboard-buttons">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <!-- Add more sub-items here in the future -->
                </div>
            @endif
        @show

        @section('users-index')
            @if (Route::is('admin.users.index'))
                <div class="table-header">
                    <h1>Users</h1>
                    <a href="{{ route('admin.users.create') }}" class="create-btn">Create User</a>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users ?? [] as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="action-link">Edit</a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @show

        @section('users-create')
            @if (Route::is('admin.users.create'))
                <h1>Create User</h1>
                <div class="form-container">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" required>
                            @error('name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" required>
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" required>
                            @error('password')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <button type="submit" class="submit-btn">Create</button>
                            <a href="{{ route('admin.users.index') }}" class="cancel-link">Cancel</a>
                        </div>
                    </form>
                </div>
            @endif
        @show

        @section('users-edit')
            @if (Route::is('admin.users.edit'))
                <h1>Edit User</h1>
                <div class="form-container">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password (leave blank to keep current)</label>
                            <input type="password" name="password" id="password">
                            @error('password')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation">
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" required>
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <button type="submit" class="submit-btn">Update</button>
                            <a href="{{ route('admin.users.index') }}" class="cancel-link">Cancel</a>
                        </div>
                    </form>
                </div>
            @endif
        @show
    </div>
</body>
</html>