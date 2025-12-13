<div class="content-header">
    <h1>Gestion des Participations</h1>
    <div style="display: flex; gap: 10px;">
        <button class="btn primary" onclick="showFilters()"><i class="fas fa-filter"></i> Filtrer</button>
        <button class="btn secondary" onclick="refreshTable()"><i class="fas fa-sync"></i> Actualiser</button>
    </div>
</div>

<!-- Filters Section -->
<div id="filtersSection" class="content-card" style="display: none; margin-bottom: 20px;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label>Statut</label>
                <select class="select" id="filterStatus">
                    <option value="">Tous</option>
                    <option value="inscrit">Inscrit</option>
                    <option value="confirmé">Confirmé</option>
                    <option value="annulé">Annulé</option>
                </select>
            </div>
            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button class="btn primary" onclick="applyFilters()" style="flex: 1;">Appliquer</button>
                <button class="btn ghost" onclick="resetFilters()" style="flex: 1;">Réinitialiser</button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
    <div class="content-card">
        <div class="card-body" style="text-align: center; padding: 15px;">
            <div style="font-size: 1.8rem; color: #5e6d3b; font-weight: 700;"><?= htmlspecialchars(count($participations ?? [])) ?></div>
            <div style="color: #6b4b44; font-size: 0.9rem; font-weight: 600;">Total Participations</div>
        </div>
    </div>
    <div class="content-card">
        <div class="card-body" style="text-align: center; padding: 15px;">
            <div style="font-size: 1.8rem; color: #27ae60; font-weight: 700;">35</div>
            <div style="color: #6b4b44; font-size: 0.9rem; font-weight: 600;">Confirmées</div>
        </div>
    </div>
    <div class="content-card">
        <div class="card-body" style="text-align: center; padding: 15px;">
            <div style="font-size: 1.8rem; color: #f39c12; font-weight: 700;">5</div>
            <div style="color: #6b4b44; font-size: 0.9rem; font-weight: 600;">En attente</div>
        </div>
    </div>
    <div class="content-card">
        <div class="card-body" style="text-align: center; padding: 15px;">
            <div style="font-size: 1.8rem; color: #c0392b; font-weight: 700;">2</div>
            <div style="color: #6b4b44; font-size: 0.9rem; font-weight: 600;">Annulées</div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="content-card">
    <div class="card-header">
        <h3>Liste complète des participations</h3>
        <span style="color: #6b4b44; font-size: 0.9rem;"><?= count($participations ?? []) ?> participations</span>
    </div>
    <div class="card-body" style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" onchange="selectAllRows()"></th>
                    <th>ID</th>
                    <th>Participant</th>
                    <th>Événement</th>
                    <th>Date Inscription</th>
                    <th>Statut</th>
                    <th>Accompagnants</th>
                    <th>Accessibilité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($participations)): ?>
                    <?php foreach($participations as $index => $p): ?>
                        <tr class="table-row" data-status="<?= htmlspecialchars($p['statut'] ?? 'inscrit') ?>" data-id="<?= htmlspecialchars($p['id']) ?>">
                            <td><input type="checkbox" class="row-checkbox"></td>
                            <td>#<?= str_pad($index + 1, 3, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar"><?= substr($p['nom'] ?? 'U', 0, 1) . substr($p['prenom'] ?? 'S', 0, 1) ?></div>
                                    <div>
                                        <div style="font-weight: 600; color: #4b2e16;"><?= htmlspecialchars($p['nom'] ?? 'Unknown') . ' ' . htmlspecialchars($p['prenom'] ?? '') ?></div>
                                        <div style="font-size: 0.85rem; color: #6b4b44;"><?= htmlspecialchars($p['email'] ?? 'N/A') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($p['titre'] ?? 'Événement') ?></td>
                            <td><?= isset($p['date_inscription']) ? date('d M Y', strtotime($p['date_inscription'])) : 'N/A' ?></td>
                            <td>
                                <span class="status <?= htmlspecialchars($p['statut'] ?? 'inscrit') ?>">
                                    <?php if($p['statut'] === 'confirmé'): ?>
                                        <i class="fas fa-check-circle"></i> Confirmé
                                    <?php elseif($p['statut'] === 'annulé'): ?>
                                        <i class="fas fa-times-circle"></i> Annulé
                                    <?php else: ?>
                                        <i class="fas fa-hourglass-half"></i> En attente
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td><span style="background: rgba(94,109,59,0.1); color: #5e6d3b; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;"><?= htmlspecialchars($p['nombre_accompagnants'] ?? 0) ?></span></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <?php if(!empty($p['besoins_accessibilite'])): ?>
                                        <?php foreach(explode(',', $p['besoins_accessibilite']) as $acc): ?>
                                            <span style="background: rgba(94,109,59,0.1); color: #5e6d3b; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem;"><?= htmlspecialchars(trim($acc)) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span style="background: rgba(94,109,59,0.1); color: #5e6d3b; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem;">Aucune</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn small ghost" title="Voir" onclick="viewParticipation(<?= htmlspecialchars($p['id']) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn small ghost" title="Éditer" onclick="editParticipation(<?= htmlspecialchars($p['id']) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn small ghost" title="Supprimer" onclick="deleteParticipation(<?= htmlspecialchars($p['id']) ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" style="text-align: center; padding: 20px; color: #6b4b44;">Aucune participation trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bulk Actions -->
<div style="display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end;">
    <button class="btn ghost" onclick="bulkAction('delete')">
        <i class="fas fa-trash"></i> Supprimer sélection
    </button>
    <button class="btn primary" onclick="bulkAction('confirm')">
        <i class="fas fa-check"></i> Confirmer sélection
    </button>
</div>

<script>
function showFilters() {
    const section = document.getElementById('filtersSection');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function applyFilters() {
    const status = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('.table-row');
    rows.forEach(row => {
        const rowStatus = row.dataset.status;
        row.style.display = (!status || rowStatus === status) ? 'table-row' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filterStatus').value = '';
    document.querySelectorAll('.table-row').forEach(row => {
        row.style.display = 'table-row';
    });
}

function refreshTable() {
    location.reload();
}

function selectAllRows() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selectAll = document.getElementById('selectAll');
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
    });
}

function bulkAction(action) {
    const selectedCheckboxes = Array.from(document.querySelectorAll('.row-checkbox:checked'));
    const selectedIds = selectedCheckboxes.map(cb => cb.closest('tr').dataset.id);

    if (selectedIds.length === 0) {
        alert('Veuillez sélectionner au moins une participation.');
        return;
    }

    let confirmMessage = `Voulez-vous vraiment ${action === 'delete' ? 'supprimer' : 'confirmer'} les ${selectedIds.length} participation(s) sélectionnée(s) ?`;
    if (!confirm(confirmMessage)) {
        return;
    }

    fetch('../Controller/ParticipationController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=bulk_action&type=${action}&ids[]=${selectedIds.join('&ids[]=')}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${data.count} participation(s) ${action === 'delete' ? 'supprimée(s)' : 'confirmée(s)'} avec succès.`);
            refreshTable();
        } else {
            alert('Erreur: ' + (data.error || 'Une erreur est survenue.'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur de communication est survenue.');
    });
}

function viewParticipation(id) { alert('Voir participation ' + id); }
function editParticipation(id) { alert('Éditer participation ' + id); }
function deleteParticipation(id) { if(confirm('Supprimer cette participation ?')) alert('Suppression de ' + id); }
</script>
