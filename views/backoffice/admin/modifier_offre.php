<?php require_once __DIR__ . '/../templates/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            
            <h1 class="h3 mb-4">Modifier l'Offre</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulaire de Modification</h6>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $success ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titre">Titre de l'offre *</label>
                                    <input type="text" class="form-control" id="titre" name="titre" 
                                           value="<?= htmlspecialchars($offre['titre']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type_offre">Type d'offre *</label>
                                    <select class="form-control" id="type_offre" name="type_offre" required>
                                        <option value="emploi" <?= $offre['type_offre'] === 'emploi' ? 'selected' : '' ?>>Emploi</option>
                                        <option value="stage" <?= $offre['type_offre'] === 'stage' ? 'selected' : '' ?>>Stage</option>
                                        <option value="alternance" <?= $offre['type_offre'] === 'alternance' ? 'selected' : '' ?>>Alternance</option>
                                        <option value="freelance" <?= $offre['type_offre'] === 'freelance' ? 'selected' : '' ?>>Freelance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description de l'offre *</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($offre['description']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="impact_sociale">Impact social *</label>
                            <textarea class="form-control" id="impact_sociale" name="impact_sociale" rows="3" required><?= htmlspecialchars($offre['impact_sociale']) ?></textarea>
                            <small class="form-text text-muted">Décrivez l'impact social positif de ce poste.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_expiration">Date d'expiration *</label>
                                    <input type="date" class="form-control" id="date_expiration" name="date_expiration" 
                                           value="<?= $offre['date_expiration'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lieu">Lieu *</label>
                                    <input type="text" class="form-control" id="lieu" name="lieu" 
                                           value="<?= htmlspecialchars($offre['lieu']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mode">Mode de travail</label>
                                    <select class="form-control" id="mode" name="mode">
                                        <option value="presentiel" <?= $offre['mode'] === 'presentiel' ? 'selected' : '' ?>>Présentiel</option>
                                        <option value="teletravail" <?= $offre['mode'] === 'teletravail' ? 'selected' : '' ?>>Télétravail</option>
                                        <option value="hybride" <?= $offre['mode'] === 'hybride' ? 'selected' : '' ?>>Hybride</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="horaire">Horaire</label>
                                    <select class="form-control" id="horaire" name="horaire">
                                        <option value="temps_plein" <?= $offre['horaire'] === 'temps_plein' ? 'selected' : '' ?>>Temps plein</option>
                                        <option value="temps_partiel" <?= $offre['horaire'] === 'temps_partiel' ? 'selected' : '' ?>>Temps partiel</option>
                                        <option value="flexible" <?= $offre['horaire'] === 'flexible' ? 'selected' : '' ?>>Flexible</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="disability_friendly" 
                                               name="disability_friendly" value="1" 
                                               <?= $offre['disability_friendly'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="disability_friendly">
                                            Accessible aux personnes handicapées
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Types de handicap ciblés</label>
                            <div class="row">
                                <?php
                                $types_handicap_disponibles = ['moteur', 'visuel', 'auditif', 'mental', 'psychique'];
                                $types_selectionnes = explode(',', $offre['type_handicap']);
                                ?>
                                <?php foreach ($types_handicap_disponibles as $type): ?>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="type_handicap[]" 
                                                   value="<?= $type ?>" 
                                                   <?= in_array($type, $types_selectionnes) ? 'checked' : '' ?>>
                                            <label class="form-check-label"><?= ucfirst($type) ?></label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="col-12 mt-2">
                                    <small class="form-text text-muted">
                                        Laissez vide ou cochez "tous" pour cibler tous les types de handicap.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                            <a href="index.php?action=admin-voir-offre&id=<?= $offre['Id_offre'] ?>" 
                               class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
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
                </div>
            </div>

            <!-- Informations de l'offre -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Techniques</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>ID:</strong> <?= $offre['Id_offre'] ?></li>
                        <li><strong>Créée le:</strong> <?= date('d/m/Y H:i', strtotime($offre['date_publication'])) ?></li>
                        <?php if ($offre['date_modification']): ?>
                            <li><strong>Modifiée le:</strong> <?= date('d/m/Y H:i', strtotime($offre['date_modification'])) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

