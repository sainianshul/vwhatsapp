<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Social Monitor | Registration</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AdminLTE 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-box { 
            width: 420px; 
        }
        .card { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
            border: 1px solid rgba(255,255,255,0.2);
            overflow: hidden;
        }
        .card-header { 
            background: transparent; 
            border-bottom: none; 
            text-align: center; 
            padding-top: 40px; 
        }
        h1 b { color: #1e3c72; font-weight: 700; }
        h1 { color: #444; font-weight: 300; letter-spacing: -1px; }
        .login-box-msg { color: #666; font-size: 15px; margin-bottom: 20px; }
        
        .btn-primary { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            border-radius: 12px; 
            padding: 12px; 
            font-weight: 500; 
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.4);
        }
        
        .input-group {
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            border-radius: 12px;
        }
        .form-control { 
            border-radius: 12px 0 0 12px; 
            padding: 14px 20px; 
            border: 1px solid #e1e5eb;
            border-right: none;
            font-size: 15px;
        }
        .input-group-text { 
            border-radius: 0 12px 12px 0; 
            background: #fff;
            border: 1px solid #e1e5eb;
            border-left: none;
            color: #1e3c72;
        }
        .form-control:focus { 
            box-shadow: none; 
            border-color: #1e3c72; 
        }
        .form-control:focus + .input-group-text {
            border-color: #1e3c72; 
        }
        a { color: #1e3c72; text-decoration: none; font-weight: 500; }
        a:hover { color: #2a5298; text-decoration: underline; }
    </style>
</head>
<body class="register-page">
    <div class="register-box">
        <div class="card">
            <div class="card-header">
                <a href="#" class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover">
                    <h1 class="mb-0"><b>Social</b>Monitor</h1>
                </a>
            </div>
            <div class="card-body register-card-body px-5 pb-5">
                <p class="login-box-msg text-center">Create a new account</p>
                
                <form action="{{ route('register.custom') }}" method="post">
                    @csrf
                    
                    <div class="input-group mb-4">
                        <input type="text" name="name" class="form-control" placeholder="Full name" required autofocus>
                        <div class="input-group-text"> <span class="bi bi-person"></span> </div>
                    </div>
                    @if ($errors->has('name'))
                        <span class="text-danger d-block mb-3" style="margin-top: -15px;">{{ $errors->first('name') }}</span>
                    @endif
                    
                    <div class="input-group mb-4">
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        <div class="input-group-text"> <span class="bi bi-envelope"></span> </div>
                    </div>
                    @if ($errors->has('email'))
                        <span class="text-danger d-block mb-3" style="margin-top: -15px;">{{ $errors->first('email') }}</span>
                    @endif
                    
                    <div class="input-group mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-text"> <span class="bi bi-lock-fill"></span> </div>
                    </div>
                    @if ($errors->has('password'))
                        <span class="text-danger d-block mb-3" style="margin-top: -15px;">{{ $errors->first('password') }}</span>
                    @endif
                    
                    <div class="row">
                        <div class="col-12 mb-3 mt-2">
                            <div class="d-grid gap-2"> 
                                <button type="submit" class="btn btn-primary btn-lg">Register</button> 
                            </div>
                        </div>
                    </div>
                </form>
                
                <p class="mb-0 text-center mt-3" style="font-size: 14px;">
                    <span class="text-muted">Already have an account?</span> <a href="{{ route('login') }}">Sign In</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
</body>
</html>
