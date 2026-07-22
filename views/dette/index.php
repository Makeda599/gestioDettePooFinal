<?php
$estVueDetail      = $estVueDetail ?? false;
$estClientConnecte = $estClientConnecte ?? false; // Définit si c'est le client qui est connecté
$clientFiltre      = $clientFiltre ?? null;
$dettes            = $dettes ?? [];

$pageCourante = (int) ($pageCourante ?? 1);
$totalPages   = (int) ($totalPages ?? 1);

$titre = $estClientConnecte ? 'Mon Profil & Mes Dettes' : ($estVueDetail ? 'Détails du client' : 'Liste des dettes');
$enTete = $titre;
require dirname(__DIR__) . '/layouts/header.php';
?>

<!-- En-tête de page -->
<div class="flex items-center justify-between mb-6">
    <div>
        <?php if ($estClientConnecte): ?>
            <h1 class="text-xl font-bold text-slate-800">Espace Client</h1>
            <p class="text-sm text-slate-500">Consultez l'historique et le récapitulatif de vos dettes</p>
        <?php elseif ($estVueDetail): ?>
            <a href="<?= WEBROOT ?>/clients" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-indigo-600 mb-2 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour aux clients
            </a>
            <h1 class="text-xl font-bold text-slate-800">Détail des dettes du client</h1>
        <?php else: ?>
            <p class="text-sm text-slate-500">Suivez l'ensemble des dettes enregistrées pour tous les clients</p>
        <?php endif; ?>
    </div>

    <!-- Seul l'Admin peut ajouter une dette -->
    <?php if (!$estClientConnecte): ?>
        <a href="<?= WEBROOT ?>/dettes/creer<?= ($estVueDetail && !empty($clientFiltre)) ? '?client_id=' . $clientFiltre['id'] : '' ?>"
           class="flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-lg shadow-indigo-200 transition">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Ajouter une dette
        </a>
    <?php endif; ?>
</div>

<!-- FICHE PROFIL DU CLIENT (Visible pour le client connecté ou en vue détail admin) -->
<?php if (($estVueDetail || $estClientConnecte) && !empty($clientFiltre)): ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0">
                    <?php if (!empty($clientFiltre['photo'])): ?>
                        <img src="<?= WEBROOT ?>/<?= htmlspecialchars($clientFiltre['photo']) ?>" alt="Photo" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-xl font-bold text-indigo-600">
                            <?= strtoupper(substr($clientFiltre['prenom'] ?? '', 0, 1) . substr($clientFiltre['nom'] ?? '', 0, 1)) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        <?= htmlspecialchars(($clientFiltre['prenom'] ?? '') . ' ' . ($clientFiltre['nom'] ?? '')) ?>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">📧 <?= htmlspecialchars($clientFiltre['email'] ?? 'N/A') ?></p>
                    <p class="text-xs text-slate-500 mt-0.5">📞 <?= htmlspecialchars($clientFiltre['telephone'] ?? 'N/A') ?></p>
                </div>
            </div>

            <?php if (!$estClientConnecte): ?>
                <div>
                    <a href="<?= WEBROOT ?>/dettes" 
                       class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition">
                        Voir toutes les dettes
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php 
            $totalDettes = array_sum(array_column($dettes, 'montant'));
            $restant = array_sum(array_map(fn($d) => ($d['etat'] ?? '') !== 'solde' ? (float) $d['montant'] : 0, $dettes));
        ?>

        <div class="grid grid-cols-2 gap-4 text-center">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="block text-xs font-medium text-slate-400 uppercase">Total Cumulé</span>
                <span class="text-base font-bold text-slate-700"><?= number_format($totalDettes, 0, ',', ' ') ?> FCFA</span>
            </div>
            <div class="bg-amber-50/50 p-3 rounded-xl border border-amber-100">
                <span class="block text-xs font-medium text-amber-600 uppercase">Reste À Payer</span>
                <span class="text-base font-bold text-amber-700"><?= number_format($restant, 0, ',', ' ') ?> FCFA</span>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- TABLEAU DES DETTES -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <?php if (!$estVueDetail && !$estClientConnecte): ?>
                    <th class="px-6 py-3">Client</th>
                <?php endif; ?>
                <th class="px-6 py-3">Motif</th>
                <th class="px-6 py-3">Montant</th>
                <th class="px-6 py-3">État</th>
                <!-- On masque la colonne Actions pour le client -->
                <?php if (!$estClientConnecte): ?>
                    <th class="px-6 py-3 text-right">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($dettes)): ?>
                <tr>
                    <td colspan="<?= $estClientConnecte ? '3' : ($estVueDetail ? '4' : '5') ?>" class="px-6 py-10 text-center text-slate-400">
                        Aucune dette enregistrée.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($dettes as $d): ?>
                <tr class="hover:bg-slate-50/60 transition">
                    <?php if (!$estVueDetail && !$estClientConnecte): ?>
                        <td class="px-6 py-3.5 font-medium text-slate-800">
                            <a href="<?= WEBROOT ?>/dettes?client_id=<?= $d['utilisateur_id'] ?>" class="hover:text-indigo-600 transition">
                                <?= htmlspecialchars(($d['prenom'] ?? '') . ' ' . ($d['nom'] ?? '')) ?>
                            </a>
                        </td>
                    <?php endif; ?>
                    <td class="px-6 py-3.5 text-slate-600"><?= htmlspecialchars($d['dette'] ?? '') ?></td>
                    <td class="px-6 py-3.5 text-slate-700 font-medium"><?= number_format((float) ($d['montant'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                    <td class="px-6 py-3.5">
                        <?php if (($d['etat'] ?? '') === 'solde'): ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Soldée</span>
                        <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Non soldée</span>
                        <?php endif; ?>
                    </td>

                    <!-- Boutons d'action affichés UNIQUEMENT si ce n'est pas un client -->
                    <?php if (!$estClientConnecte): ?>
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <?php if (($d['etat'] ?? '') !== 'solde'): ?>
                                    <a href="<?= WEBROOT ?>/dettes/solder?id=<?= $d['id'] ?>" 
                                       title="Marquer comme soldée"
                                       class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= WEBROOT ?>/dettes/editer?id=<?= $d['id'] ?>" 
                                   title="Modifier"
                                   class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= WEBROOT ?>/dettes/supprimer?id=<?= $d['id'] ?>" 
                                   title="Supprimer"
                                   onclick="return confirm('Voulez-vous vraiment supprimer cette dette ?');"
                                   class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-6 bg-white px-4 py-3 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs text-slate-500">
            Page <span class="font-semibold text-slate-700"><?= $pageCourante ?></span> sur <span class="font-semibold text-slate-700"><?= $totalPages ?></span>
        </p>
        <div class="flex items-center gap-1">
            <?php if ($pageCourante > 1): ?>
                <a href="<?= WEBROOT ?>/dettes?page=<?= $pageCourante - 1 ?>" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                    Précédent
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $pageCourante): ?>
                    <span class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm shadow-indigo-200">
                        <?= $i ?>
                    </span>
                <?php else: ?>
                    <a href="<?= WEBROOT ?>/dettes?page=<?= $i ?>" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pageCourante < $totalPages): ?>
                <a href="<?= WEBROOT ?>/dettes?page=<?= $pageCourante + 1 ?>" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                    Suivant
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>