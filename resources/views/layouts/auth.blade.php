<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Ganesha Sora</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            --primary-color: #3e2f1c;     
            --secondary-color: #fdf6e3;   
            --accent-color:rgb(216, 216, 216);      
            --text-color: #ffffff;        
            --bg-color: #fffaf0;    
        }

        .auth-box {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            background: var(--primary-color);
            border-radius: 8px;
            box-shadow: 0 0 10px var(--primary-color);
        }

        .auth-box h2 {
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="auth-box">
        @yield('content')
    </div>
</body>
</html>
