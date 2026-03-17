<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ISI BURGER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1d1d1d 0%, #333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 30px 0;
        }
        .auth-card { border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.4); }
        .auth-header { background: linear-gradient(135deg, #e63946, #c62836); padding: 30px; text-align: center; }
        .btn-register { background: #e63946; border: none; border-radius: 10px; padding: 12px; font-weight: 600; }
        .btn-register:hover { background: #c62836; }
        .form-control:focus { border-color: #e63946; box-shadow: 0 0 0 .2rem rgba(230,57,70,.25); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card bg-white">
                    <div class="auth-header">
                        <h2 class="text-white fw-bold mb-0">🍔 ISI BURGER</h2>
                        <p class="text-white-50 mb-0">Créer un compte client</p>
                    </div>
                    <div class="p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Nom complet *</label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Email *</label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Mot de passe *</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Confirmer *</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ old('phone') }}">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Adresse</label>
                                <textarea name="address" class="form-control"
                                          rows="2">{{ old('address') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-register text-white w-100 fs-6">
                                Créer mon compte
                            </button>
                        </form>
                        <hr>
                        <p class="text-center text-muted mb-0">
                            Déjà un compte ?
                            <a href="{{ route('login') }}" class="text-danger fw-semibold">Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>