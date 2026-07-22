<?php
$titre = 'Modifier le client';
$enTete = 'Modifier le client';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="max-w-xl">
    <a href="<?= WEBROOT ?>/clients" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-indigo-600 mb-6 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Retour à la liste
    </a>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <form action="<?= WEBROOT ?>/clients/modifier" method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="id" value="<?= $client['id'] ?>">

            <div class="flex flex-col items-center gap-3 pb-2">
                <div id="apercu-photo" class="w-24 h-24 rounded-full bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden">
                    <?php if (!empty($client['photo'])): ?>
                        <img src="<?= WEBROOT ?>/<?= htmlspecialchars($client['photo']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i data-lucide="user" class="w-8 h-8 text-slate-400"></i>
                    <?php endif; ?>
                </div>
                <label class="cursor-pointer inline-flex items-center gap-2 text-xs font-medium text-indigo-600 hover:text-indigo-700 transition">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                    Changer la photo de profil
                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="hidden"
                           onchange="document.getElementById('apercu-photo').innerHTML = '<img src=\'' + URL.createObjectURL(this.files[0]) + '\' class=\'w-full h-full object-cover\'>'">
                </label>
                <?php if (isset($erreurs['photo'])): ?><p class="text-xs text-red-500"><?= $erreurs['photo'] ?></p><?php endif; ?>
                <p class="text-xs text-slate-400">Laisser vide pour conserver la photo actuelle.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($client['nom']) ?>"
                           class="w-full border <?= isset($erreurs['nom']) ? 'border-red-400' : 'border-slate-200' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php if (isset($erreurs['nom'])): ?><p class="text-xs text-red-500 mt-1"><?= $erreurs['nom'] ?></p><?php endif; ?>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($client['prenom']) ?>"
                           class="w-full border <?= isset($erreurs['prenom']) ? 'border-red-400' : 'border-slate-200' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php if (isset($erreurs['prenom'])): ?><p class="text-xs text-red-500 mt-1"><?= $erreurs['prenom'] ?></p><?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>"
                       class="w-full border <?= isset($erreurs['email']) ? 'border-red-400' : 'border-slate-200' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (isset($erreurs['email'])): ?><p class="text-xs text-red-500 mt-1"><?= $erreurs['email'] ?></p><?php endif; ?>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Téléphone</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($client['telephone']) ?>"
                       class="w-full border <?= isset($erreurs['telephone']) ? 'border-red-400' : 'border-slate-200' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (isset($erreurs['telephone'])): ?><p class="text-xs text-red-500 mt-1"><?= $erreurs['telephone'] ?></p><?php endif; ?>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Nouveau mot de passe</label>
                <input type="password" name="mot_de_passe"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-slate-400 mt-1">Laisser vide pour conserver le mot de passe actuel.</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="<?= WEBROOT ?>/clients" class="px-4 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-100 transition">Annuler</a>
                <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-200 transition">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
