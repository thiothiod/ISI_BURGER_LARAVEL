<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ISI BURGER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1d1d1d 0%, #333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-card { border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.4); }
        .auth-header { background: linear-gradient(135deg, #e63946, #c62836); padding: 40px; text-align: center; }
        .btn-login { background: #e63946; border: none; border-radius: 10px; padding: 12px; font-weight: 600; }
        .btn-login:hover { background: #c62836; }
        .form-control:focus { border-color: #e63946; box-shadow: 0 0 0 .2rem rgba(230,57,70,.25); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="auth-card bg-white">
                    <div class="auth-header">
                        <h1 class="text-white fw-bold mb-0" style="font-size:2.5rem">🍔</h1>
                        <h2 class="text-white fw-bold mt-2">ISI BURGER</h2>
                        <p class="text-white-50 mb-0">Gestion des Commandes</p>
                    </div>
                    <div class="p-4">
                        <h4 class="fw-bold mb-4">Connexion</h4>

                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email') }}" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mot de passe</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Se souvenir de moi</label>
                            </div>
                            <button type="submit" class="btn btn-login text-white w-100 fs-6">
                                Se connecter
                            </button>
                        </form>
                        <hr>
                        <p class="text-center text-muted mb-0">
                            Pas de compte ?
                            <a href="{{ route('register') }}" class="text-danger fw-semibold">S'inscrire</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>