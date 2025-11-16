<?php require_once __DIR__ . '/../templates/header.php'; ?>

<div class="container-fluid">
    <div class="row">

    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de l'Offre</h6>
                    <div>
                        <span class="badge badge-<?= $offre['disability_friendly'] ? 'success' : 'secondary' ?>">
                            <?= $offre['disability_friendly'] ? 'Accessible' : 'Non accessible' ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="text-primary"><?= htmlspecialchars($offre['titre']) ?></h4>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Description:</h6>
                            <p class="text-justify"><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Impact Social:</h6>
                            <p class="text-justify"><?= nl2br(htmlspecialchars($offre['impact_sociale'])) ?></p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Informations Générales:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Type d'offre:</strong> 
                                    <span class="badge badge-info"><?= $offre['type_offre'] ?></span>
                                </li>
                                <li><strong>Mode:</strong> 
                                    <span class="badge badge-secondary"><?= $offre['mode'] ?></span>
                                </li>
                                <li><strong>Horaire:</strong> 
                                    <span class="badge badge-secondary"><?= $offre['horaire'] ?></span>
                                </li>
                                <li><strong>Lieu:</strong> <?= htmlspecialchars($offre['lieu']) ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Dates:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Publication:</strong> <?= date('d/m/Y H:i', strtotime($offre['date_publication'])) ?></li>
                                <li><strong>Expiration:</strong> 
                                    <?php if ($offre['date_expiration']): ?>
                                        <?= date('d/m/Y', strtotime($offre['date_expiration'])) ?>
                                        <?php if (strtotime($offre['date_expiration']) < time()): ?>
                                            <span class="badge badge-danger ml-1">Expiré</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Non définie</span>
                                    <?php endif; ?>
                                </li>
                                <?php if ($offre['date_modification']): ?>
                                    <li><strong>Dernière modification:</strong> <?= date('d/m/Y H:i', strtotime($offre['date_modification'])) ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <?php if (!empty($offre['type_handicap']) && $offre['type_handicap'] !== 'tous'): ?>
                        <hr>
                        <h6 class="font-weight-bold">Types de handicap ciblés:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $types_handicap = explode(',', $offre['type_handicap']);
                            foreach ($types_handicap as $type): 
                            ?>
                                <span class="badge badge-primary"><?= trim($type) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations du créateur -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Créateur de l'Offre</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                    </div>
                    <h5><?= htmlspecialchars($createur['prenom'] . ' ' . $createur['nom']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($createur['email']) ?></p>
                    <p class="small">
                        <strong>Rôle:</strong> 
                        <span class="badge badge-<?= $createur['role'] === 'admin' ? 'danger' : 'info' ?>">
                            <?= $createur['role'] ?>
                        </span>
                    </p>
                    <p class="small text-muted">
                        Inscrit le: <?= date('d/m/Y', strtotime($createur['date_inscription'])) ?>
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow mt-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="index.php?action=admin-modifier-offre&id=<?= $offre['Id_offre'] ?>" 
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Modifier l'Offre
                        </a>
                        <a href="index.php?action=admin-gestion-offres" 
                           class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                        <a href="index.php?action=admin-supprimer-offre&id=<?= $offre['Id_offre'] ?>" 
                           class="btn btn-danger btn-block"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ? Cette action est irréversible.')">
                            <i class="fas fa-trash"></i> Supprimer l'Offre
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
