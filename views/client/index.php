<?php
$titre = 'Liste des clients';
$enTete = 'Liste des clients';
require dirname(__DIR__) . '/layouts/header.php';

// Initialisation pagination & filtre
$pageCourante = (int)($pageCourante ?? 1);
$totalPages   = (int)($totalPages ?? 1);
$etatFiltre   = $etatFiltre ?? '';

// Paramètre d'état pour conserver le filtre dans la pagination
$paramEtat = !empty($etatFiltre) ? '&etat=' . urlencode($etatFiltre) : '';

$badgesEtat = [
    'solvable'     => 'bg-emerald-100 text-emerald-700',
    'non_solvable' => 'bg-red-100 text-red-700',
    'nouveau'      => 'bg-slate-200 text-slate-600',
];
$labelsEtat = [
    'solvable'     => 'Solvable',
    'non_solvable' => 'Non solvable',
    'nouveau'      => 'Nouveau',
];
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-slate-500">Gérez les clients enregistrés dans la plateforme</p>
    </div>
    
    <!-- Filtres & Action -->
    <div class="flex items-center gap-3">
        <form method="GET" action="<?= WEBROOT ?>/clients" class="flex items-center gap-2">
            <select name="etat" onchange="this.form.submit()" class="text-xs bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous les états</option>
                <option value="solvable" <?= $etatFiltre === 'solvable' ? 'selected' : '' ?>>Solvables</option>
                <option value="non_solvable" <?= $etatFiltre === 'non_solvable' ? 'selected' : '' ?>>Non solvables</option>
                <option value="nouveau" <?= $etatFiltre === 'nouveau' ? 'selected' : '' ?>>Nouveaux</option>
            </select>
        </form>

        <a href="<?= WEBROOT ?>/clients/creer"
           class="flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-lg shadow-indigo-200 transition">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Ajouter un client
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <th class="px-6 py-3"></th>
                <th class="px-6 py-3">Nom</th>
                <th class="px-6 py-3">Prénom</th>
                <th class="px-6 py-3">Téléphone</th>
                <th class="px-6 py-3">État</th>
                <th class="px-6 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                        Aucun client enregistré pour le moment.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($clients as $client): ?>
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-3.5">
                        <?php if (!empty($client['photo'])): ?>
                            <img src="<?= WEBROOT ?>/<?= htmlspecialchars($client['photo']) ?>"
                                 class="w-9 h-9 rounded-full object-cover border border-slate-200">
                        <?php else: ?>
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xs font-semibold">
                                <?= strtoupper(substr($client['prenom'], 0, 1) . substr($client['nom'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-3.5 font-medium text-slate-800"><?= htmlspecialchars($client['nom']) ?></td>
                    <td class="px-6 py-3.5 text-slate-600"><?= htmlspecialchars($client['prenom']) ?></td>
                    <td class="px-6 py-3.5 text-slate-500"><?= htmlspecialchars($client['telephone']) ?></td>
                    <td class="px-6 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $badgesEtat[$client['etat']] ?? 'bg-slate-100 text-slate-600' ?>">
                            <?= $labelsEtat[$client['etat']] ?? $client['etat'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="<?= WEBROOT ?>/clients/modifier?id=<?= $client['id'] ?>"
                               title="Modifier"
                               class="p-2 rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <a href="<?= WEBROOT ?>/dettes?client_id=<?= $client['id'] ?>"
                               title="Voir les dettes"
                               class="p-2 rounded-lg text-slate-400 hover:bg-violet-50 hover:text-violet-600 transition">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                            </a>
                            <form action="<?= WEBROOT ?>/clients/supprimer" method="POST"
                                  onsubmit="return confirm('Supprimer ce client et toutes ses dettes ?');">
                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                <button type="submit" title="Supprimer"
                                        class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- BANDEAU DE PAGINATION (Style avec chiffres) -->
<?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-6 bg-white px-4 py-3 rounded-xl border border-slate-200 shadow-sm">
        <!-- Infos texte -->
        <p class="text-xs text-slate-500">
            Page <span class="font-semibold text-slate-700"><?= $pageCourante ?></span> sur <span class="font-semibold text-slate-700"><?= $totalPages ?></span>
        </p>

        <!-- Boutons de navigation -->
        <div class="flex items-center gap-1">
            <!-- Bouton Précédent -->
            <?php if ($pageCourante > 1): ?>
                <a href="<?= WEBROOT ?>/clients?page=<?= $pageCourante - 1 ?><?= $paramEtat ?>" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                    Précédent
                </a>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-300 bg-slate-50 rounded-lg border border-slate-100 cursor-not-allowed">
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                    Précédent
                </span>
            <?php endif; ?>

            <!-- Numéros de page -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $pageCourante): ?>
                    <span class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm shadow-indigo-200">
                        <?= $i ?>
                    </span>
                <?php else: ?>
                    <a href="<?= WEBROOT ?>/clients?page=<?= $i ?><?= $paramEtat ?>" 
                       class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Bouton Suivant -->
            <?php if ($pageCourante < $totalPages): ?>
                <a href="<?= WEBROOT ?>/clients?page=<?= $pageCourante + 1 ?><?= $paramEtat ?>" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                    Suivant
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-300 bg-slate-50 rounded-lg border border-slate-100 cursor-not-allowed">
                    Suivant
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>