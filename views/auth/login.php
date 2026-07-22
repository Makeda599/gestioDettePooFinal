<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — GestionDette</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-950 relative overflow-hidden">

    <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-600/30 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-violet-600/20 rounded-full blur-3xl"></div>

    <div class="relative w-full max-w-sm mx-4">
        <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl shadow-2xl p-8">

            <div class="flex flex-col items-center mb-8">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center mb-4 shadow-lg shadow-indigo-900/40">
                    <i data-lucide="wallet" class="text-white w-7 h-7"></i>
                </div>
                <h1 class="text-xl font-semibold text-white">GestionDette</h1>
                <p class="text-sm text-slate-400 mt-1">Espace administrateur</p>
            </div>

            <?php if (!empty($erreur)): ?>
                <div class="mb-5 flex items-center gap-2 text-sm text-red-300 bg-red-950/60 border border-red-900 px-4 py-3 rounded-lg">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span><?= htmlspecialchars($erreur) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= WEBROOT ?>/connexion" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Adresse email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="email" name="email" required autofocus
                               class="w-full bg-slate-800/60 border border-slate-700 rounded-lg pl-10 pr-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="admin@gmail.sn">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Mot de passe</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="password" name="mot_de_passe" required
                               class="w-full bg-slate-800/60 border border-slate-700 rounded-lg pl-10 pr-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white text-sm font-medium py-2.5 rounded-lg shadow-lg shadow-indigo-950/50 transition">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Se connecter
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-600 mt-6">© <?= date('Y') ?> GestionDette — Tous droits réservés</p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
