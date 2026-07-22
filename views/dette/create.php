<?php
$titre = 'Ajouter une dette';
$enTete = 'Ajouter une dette';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="max-w-xl">
    <a href="<?= WEBROOT ?>/dettes" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-indigo-600 mb-6 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Retour à la liste
    </a>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <form action="<?= WEBROOT ?>/dettes/creer" method="POST" class="space-y-5">

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Client</label>
                <select name="utilisateur_id"
                        class="w-full border <?= isset($erreurs['utilisateur_id']) ? 'border-red-400' : 'border-slate-200' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Sélectionner un client --</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (($ancien['utilisateur_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($erreurs['utilisateur_id'])): ?><p class="text-xs text-red-500 mt-1"><?= $erreurs['utilisateur_id'] ?></p><?php endif; ?>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Motif de la dette</label>
                <input type="text" name="dette" value="<?= htmlspecialchars($ancien['dette'] ?? '') ?>"
                       placeholder="Ex : Prêt personnel, facture impayée..."
                       class="w-full border <?= isset($erreurs['dette']) ? 'border-red-400' : 'border-slate-200' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (isset($erreurs['dette'])): ?><p class="text-xs text-red-500 mt-1"><?= $erreurs['dette'] ?></p><?php endif; ?>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Montant (FCFA)</label>
                <input type="number" step="0.01" name="montant" value="<?= htmlspecialchars($ancien['montant'] ?? '') ?>"
                       class="w-full border <?= isset($erreurs['montant']) ? 'border-red-400' : 'border-slate-200' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (isset($erreurs['montant'])): ?><p class="text-xs text-red-500 mt-1"><?= $erreurs['montant'] ?></p><?php endif; ?>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="<?= WEBROOT ?>/dettes" class="px-4 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-100 transition">Annuler</a>
                <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-200 transition">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
