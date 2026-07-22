<?php
$estAdmin = ($_SESSION['utilisateur']['role'] ?? '') === 'admin';
$nomComplet = ($_SESSION['utilisateur']['prenom'] ?? '') . ' ' . ($_SESSION['utilisateur']['nom'] ?? '');

// Détection de la page active via l'URL
$uri = $_SERVER['REQUEST_URI'] ?? '';
$isPageClients = strpos($uri, '/clients') !== false;
$isPageDettes  = strpos($uri, '/dettes') !== false;

// Styles pour les liens du menu
$classActive   = "bg-indigo-600 text-white shadow-lg shadow-indigo-500/20 font-medium";
$classInactive = "text-slate-300 hover:bg-slate-800 hover:text-white font-medium";
?>
<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titre ?? 'GestionDette' ?></title>
    <!-- Tailwind CSS CDN ou Build -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50">

<div class="min-h-full flex flex-col">

    <!-- SIDEBAR (Affichée UNIQUEMENT pour l'Admin) -->
    <?php if ($estAdmin): ?>
        <aside class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-slate-300 flex flex-col justify-between z-30 shadow-xl">
            <div>
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-800">
                    <div class="p-2 bg-indigo-600 rounded-xl text-white">
                        <i data-lucide="wallet" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-white tracking-wide">GestionDette</h1>
                        <span class="text-xs text-slate-400">Panneau admin</span>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-6 px-4 space-y-1">
                    <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Menu</span>
                    
                    <!-- Bouton Liste Client (S'active si l'URL contient /clients) -->
                    <a href="<?= WEBROOT ?>/clients" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition <?= $isPageClients ? $classActive : $classInactive ?>">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        Liste Client
                    </a>
                    
                    <!-- Bouton Liste Dette (S'active si l'URL contient /dettes) -->
                    <a href="<?= WEBROOT ?>/dettes" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition <?= $isPageDettes ? $classActive : $classInactive ?>">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        Liste Dette
                    </a>
                </nav>
            </div>

            <!-- Déconnexion dans Sidebar pour Admin -->
            <div class="p-4 border-t border-slate-800">
                <a href="<?= WEBROOT ?>/deconnexion" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Déconnexion
                </a>
            </div>
        </aside>
    <?php endif; ?>

    <!-- CONTENU PRINCIPAL (Si admin -> décalé à droite avec ml-64, si client -> plein écran) -->
    <div class="flex-1 flex flex-col <?= $estAdmin ? 'ml-64' : 'w-full' ?>">
        
        <!-- HEADER SUPÉRIEUR -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
            <div class="px-8 py-4 flex items-center justify-between">
                <div>
                    <!-- Logo visible pour le client uniquement dans le header -->
                    <?php if (!$estAdmin): ?>
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-indigo-600 rounded-xl text-white">
                                <i data-lucide="wallet" class="w-5 h-5"></i>
                            </div>
                            <span class="font-bold text-slate-800 text-lg">GestionDette</span>
                        </div>
                    <?php else: ?>
                        <h2 class="text-lg font-bold text-slate-800"><?= $enTete ?? '' ?></h2>
                    <?php endif; ?>
                </div>

                <!-- Informations profil & Bouton déconnexion client -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-sm shadow-indigo-200">
                            <?= strtoupper(substr($_SESSION['utilisateur']['prenom'] ?? 'U', 0, 1) . substr($_SESSION['utilisateur']['nom'] ?? '', 0, 1)) ?>
                        </div>
                        <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($nomComplet) ?></span>
                    </div>

                    <!-- Bouton Déconnexion dans la Navbar (Visible UNIQUEMENT pour le client) -->
                    <?php if (!$estAdmin): ?>
                        <a href="<?= WEBROOT ?>/deconnexion" 
                           title="Se déconnecter"
                           class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition border border-rose-100">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            Déconnexion
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- CONTENU DE LA PAGE -->
        <main class="flex-1 p-8">