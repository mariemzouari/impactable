<?php require_once __DIR__ . '/../templates/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Gestion des Offres</h1>
        </div>
    </div>

    <!-- Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Offres</h6>
            <span class="badge badge-primary"><?= count($offres) ?> offre(s)</span>
        </div>
        <div class="card-body">
            <?php if (!empty($offres)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Créateur</th>
                                <th>Date Publication</th>
                                <th>Date Expiration</th>
                                <th>Accessible</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offres as $offre): ?>
                                <tr>
                                    <td><?= $offre['Id_offre'] ?></td>
                                    <td><?= htmlspecialchars($offre['titre']) ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= $offre['type_offre'] ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            $createur = $this->utilisateurManager->getById($offre['Id_utilisateur']);
                                            echo htmlspecialchars($createur['prenom'] . ' ' . $createur['nom']);
                                        ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($offre['date_publication'])) ?></td>
                                    <td>
                                        <?php if ($offre['date_expiration']): ?>
                                            <?= date('d/m/Y', strtotime($offre['date_expiration'])) ?>
                                            <?php if (strtotime($offre['date_expiration']) < time()): ?>
                                                <span class="badge badge-danger ml-1">Expiré</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Non définie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($offre['disability_friendly']): ?>
                                            <span class="badge badge-success">Oui</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Non</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="index.php?action=admin-voir-offre&id=<?= $offre['Id_offre'] ?>" 
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?action=admin-modifier-offre&id=<?= $offre['Id_offre'] ?>" 
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?action=admin-supprimer-offre&id=<?= $offre['Id_offre'] ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')"
                                               title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Aucune offre trouvée.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>