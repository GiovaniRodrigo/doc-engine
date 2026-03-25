<!DOCTYPE html>
<html>
<head>
    <title>Documentation</title>

    <style>
        body {
            font-family: Arial;
            margin:0;
            display:flex;
        }

        .sidebar {
            width:260px;
            background:#111;
            color:white;
            height:100vh;
            padding:20px;
        }

        .content {
            flex:1;
            padding:40px;
        }

        a {
            color:white;
            text-decoration:none;
            display:block;
            margin-bottom:10px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>Docs</h3>
</div>

<div class="content">
    @yield('content')
</div>

</body>
</html>