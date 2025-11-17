// ADMIN VIEW - Gestion de l'affichage administrateur
class AdminView {
    constructor() {
        this.elements = {
            // Navigation
            sidebar: document.getElementById('sidebar'),
            menuToggle: document.getElementById('menuToggle'),
            navItems: document.querySelectorAll('.nav-item'),
            pages: document.querySelectorAll('.page'),
            pageTitle: document.querySelector('.page-title'),
            
            // Dashboard
            activityList: document.getElementById('activityList'),
            refreshBtn: document.getElementById('refreshBtn'),
            
            // Réclamations
            searchInput: document.getElementById('searchReclamations'),
            filterStatus: document.getElementById('filterStatus'),
            filterPriorite: document.getElementById('filterPriorite'),
            filterCategorie: document.getElementById('filterCategorie'),
            tableBody: document.getElementById('reclamationsTableBody'),
            
            // Modal
            modal: document.getElementById('detailsModal'),
            modalBody: document.getElementById('modalBody'),
            closeModal: document.getElementById('closeModal'),
            closeModalBtn: document.getElementById('closeModalBtn')
        };

        this.initEventListeners();
    }

    initEventListeners() {
        // Toggle sidebar mobile
        if (this.elements.menuToggle) {
            this.elements.menuToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Fermer modal
        if (this.elements.closeModal) {
            this.elements.closeModal.addEventListener('click', () => {
                this.closeModal();
            });
        }

        if (this.elements.closeModalBtn) {
            this.elements.closeModalBtn.addEventListener('click', () => {
                this.closeModal();
            });
        }

        // Fermer modal en cliquant à l'extérieur
        if (this.elements.modal) {
            this.elements.modal.addEventListener('click', (e) => {
                if (e.target === this.elements.modal) {
                    this.closeModal();
                }
            });
        }
    }

    // Toggle sidebar
    toggleSidebar() {
        this.elements.sidebar.classList.toggle('active');
    }

    // Changer de page
    switchPage(pageName) {
        // Désactiver tous les items de navigation
        this.elements.navItems.forEach(item => item.classList.remove('active'));
        
        // Activer l'item cliqué
        const activeItem = Array.from(this.elements.navItems).find(
            item => item.dataset.page === pageName
        );
        if (activeItem) {
            activeItem.classList.add('active');
        }

        // Masquer toutes les pages
        this.elements.pages.forEach(page => page.classList.remove('active'));
        
        // Afficher la page correspondante
        const activePage = document.getElementById(`${pageName}-page`);
        if (activePage) {
            activePage.classList.add('active');
        }

        // Mettre à jour le titre
        const titles = {
            'dashboard': 'Tableau de bord',
            'reclamations': 'Gestion des Réclamations',
            'statistiques': 'Statistiques',
            'utilisateurs': 'Gestion des Utilisateurs',
            'parametres': 'Paramètres'
        };
        this.elements.pageTitle.textContent = titles[pageName] || 'Admin Panel';
    }

    // Afficher les stats du dashboard
    displayStats(stats) {
        // Les stats sont déjà affichées dans le HTML statique
        // On peut les mettre à jour dynamiquement ici
        console.log('Stats:', stats);
    }

    // Afficher les activités récentes
    displayRecentActivities(activities) {
        this.elements.activityList.innerHTML = '';

        if (activities.length === 0) {
            this.elements.activityList.innerHTML = '<p style="color: #5E6D38; padding: 20px; text-align: center;">Aucune activité récente</p>';
            return;
        }

        activities.forEach(activity => {
            const item = this.createActivityItem(activity);
            this.elements.activityList.appendChild(item);
        });
    }

    // Créer un item d'activité
    createActivityItem(activity) {
        const div = document.createElement('div');
        div.className = 'activity-item';

        const iconColors = {
            'en_attente': 'background: linear-gradient(135deg, #FFF3CD, #FFE082);',
            'en_cours': 'background: linear-gradient(135deg, #CCE5FF, #90CAF9);',
            'resolu': 'background: linear-gradient(135deg, #D4EDDA, #A5D6A7);',
            'rejete': 'background: linear-gradient(135deg, #F8D7DA, #EF9A9A);'
        };

        const icons = {
            'en_attente': 'fa-clock',
            'en_cours': 'fa-spinner',
            'resolu': 'fa-check-circle',
            'rejete': 'fa-times-circle'
        };

        div.innerHTML = `
            <div class="activity-icon" style="${iconColors[activity.status] || ''}">
                <i class="fas ${icons[activity.status] || 'fa-info'}"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">${activity.title}</div>
                <div class="activity-meta">${activity.description}</div>
            </div>
            <div class="activity-meta">${this.formatDate(activity.date)}</div>
        `;

        return div;
    }

    // Afficher les réclamations dans le tableau
    displayReclamations(reclamations) {
        this.elements.tableBody.innerHTML = '';

        if (reclamations.length === 0) {
            this.elements.tableBody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #5E6D38;">
                        <i class="fas fa-inbox" style="font-size: 3em; display: block; margin-bottom: 15px; color: #A9B57D;"></i>
                        Aucune réclamation trouvée
                    </td>
                </tr>
            `;
            return;
        }

        reclamations.forEach(rec => {
            const row = this.createReclamationRow(rec);
            this.elements.tableBody.appendChild(row);
        });
    }

    // Créer une ligne de réclamation
    createReclamationRow(reclamation) {
        const tr = document.createElement('tr');
        
        tr.innerHTML = `
            <td><strong>${reclamation.id}</strong></td>
            <td>${this.formatDate(reclamation.dateCreation)}</td>
            <td>${reclamation.prenom} ${reclamation.nom}</td>
            <td>${this.truncateText(reclamation.sujet, 40)}</td>
            <td>${adminModel.getCategorieText(reclamation.categorie)}</td>
            <td>
                <span class="priority-badge priority-${reclamation.priorite}">
                    ${adminModel.getPrioriteText(reclamation.priorite)}
                </span>
            </td>
            <td>
                <span class="status-badge-table status-${reclamation.status}">
                    ${adminModel.getStatusText(reclamation.status)}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-view" onclick="adminController.viewReclamation('${reclamation.id}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-edit" onclick="adminController.editReclamation('${reclamation.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-delete" onclick="adminController.deleteReclamation('${reclamation.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

        return tr;
    }

    // Afficher les détails d'une réclamation dans le modal
    showReclamationDetails(reclamation) {
        this.elements.modalBody.innerHTML = `
            <div style="display: grid; gap: 25px;">
                <div style="background: #FFF4F5; padding: 20px; border-radius: 12px; border-left: 4px solid #5E6D38;">
                    <h3 style="color: #4B2E16; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-info-circle"></i> Informations générales
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div>
                            <strong style="color: #5E6D38;">Numéro:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.id}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Date de création:</strong><br>
                            <span style="color: #4B2E16;">${this.formatDate(reclamation.dateCreation)}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Statut:</strong><br>
                            <span class="status-badge-table status-${reclamation.status}">
                                ${adminModel.getStatusText(reclamation.status)}
                            </span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Priorité:</strong><br>
                            <span class="priority-badge priority-${reclamation.priorite}">
                                ${adminModel.getPrioriteText(reclamation.priorite)}
                            </span>
                        </div>
                    </div>
                </div>

                <div style="background: #FFF4F5; padding: 20px; border-radius: 12px; border-left: 4px solid #A9B57D;">
                    <h3 style="color: #4B2E16; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-user"></i> Informations du demandeur
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div>
                            <strong style="color: #5E6D38;">Nom complet:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.prenom} ${reclamation.nom}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Email:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.email}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Téléphone:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.telephone}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">CIN:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.cin || 'Non renseigné'}</span>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <strong style="color: #5E6D38;">Adresse:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.adresse}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Type de handicap:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.typeHandicap}</span>
                        </div>
                    </div>
                </div>

                <div style="background: #FFF4F5; padding: 20px; border-radius: 12px; border-left: 4px solid #B47F47;">
                    <h3 style="color: #4B2E16; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-file-alt"></i> Détails de la réclamation
                    </h3>
                    <div style="display: grid; gap: 15px;">
                        <div>
                            <strong style="color: #5E6D38;">Sujet:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.sujet}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Catégorie:</strong><br>
                            <span style="color: #4B2E16;">${adminModel.getCategorieText(reclamation.categorie)}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Lieu de l'incident:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.lieu}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Date de l'incident:</strong><br>
                            <span style="color: #4B2E16;">${this.formatDate(reclamation.dateIncident)}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Description:</strong><br>
                            <p style="color: #4B2E16; margin-top: 8px; line-height: 1.6; background: white; padding: 15px; border-radius: 8px;">
                                ${reclamation.description}
                            </p>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Personnes impliquées:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.personnesImpliquees || 'Non renseigné'}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Témoins:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.temoins || 'Non renseigné'}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Actions précédentes:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.actionsPrecedentes || 'Aucune'}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Réponse reçue:</strong><br>
                            <span style="color: #4B2E16;">${reclamation.reponseRecue || 'Aucune'}</span>
                        </div>
                        <div>
                            <strong style="color: #5E6D38;">Solution souhaitée:</strong><br>
                            <p style="color: #4B2E16; margin-top: 8px; line-height: 1.6; background: white; padding: 15px; border-radius: 8px;">
                                ${reclamation.solutionSouhaitee}
                            </p>
                        </div>
                    </div>
                </div>

                <div style="background: #FFF4F5; padding: 20px; border-radius: 12px; border-left: 4px solid #5E6D38;">
                    <h3 style="color: #4B2E16; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-cog"></i> Actions administrateur
                    </h3>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button onclick="adminController.changeStatus('${reclamation.id}', 'en_cours')" 
                                style="padding: 10px 20px; background: linear-gradient(135deg, #0277BD, #01579B); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-spinner"></i> Marquer en cours
                        </button>
                        <button onclick="adminController.changeStatus('${reclamation.id}', 'resolu')" 
                                style="padding: 10px 20px; background: linear-gradient(135deg, #2E7D32, #1B5E20); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-check"></i> Marquer résolu
                        </button>
                        <button onclick="adminController.changeStatus('${reclamation.id}', 'rejete')" 
                                style="padding: 10px 20px; background: linear-gradient(135deg, #D32F2F, #B71C1C); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-times"></i> Rejeter
                        </button>
                    </div>
                </div>
            </div>
        `;

        this.openModal();
    }

    // Ouvrir le modal
    openModal() {
        this.elements.modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Fermer le modal
    closeModal() {
        this.elements.modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Animation de rafraîchissement
    showRefreshAnimation() {
        const icon = this.elements.refreshBtn.querySelector('i');
        icon.style.animation = 'spin 1s linear';
        setTimeout(() => {
            icon.style.animation = '';
        }, 1000);
    }

    // Formater une date
    formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('fr-FR', options);
    }

    // Tronquer du texte
    truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
}

// Créer une instance globale de la vue admin
const adminView = new AdminView();