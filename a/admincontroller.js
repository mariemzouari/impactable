// ADMIN CONTROLLER - Logique métier administrateur
class AdminController {
    constructor(model, view) {
        this.model = model;
        this.view = view;
        this.currentFilters = {
            search: '',
            status: '',
            priorite: '',
            categorie: ''
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadDashboard();
        this.loadReclamations();
    }

    setupEventListeners() {
        // Navigation
        this.view.elements.navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const page = e.currentTarget.dataset.page;
                this.switchPage(page);
            });
        });

        // Recherche et filtres
        if (this.view.elements.searchInput) {
            this.view.elements.searchInput.addEventListener('input', (e) => {
                this.currentFilters.search = e.target.value;
                this.applyFilters();
            });
        }

        if (this.view.elements.filterStatus) {
            this.view.elements.filterStatus.addEventListener('change', (e) => {
                this.currentFilters.status = e.target.value;
                this.applyFilters();
            });
        }

        if (this.view.elements.filterPriorite) {
            this.view.elements.filterPriorite.addEventListener('change', (e) => {
                this.currentFilters.priorite = e.target.value;
                this.applyFilters();
            });
        }

        if (this.view.elements.filterCategorie) {
            this.view.elements.filterCategorie.addEventListener('change', (e) => {
                this.currentFilters.categorie = e.target.value;
                this.applyFilters();
            });
        }

        // Bouton de rafraîchissement
        if (this.view.elements.refreshBtn) {
            this.view.elements.refreshBtn.addEventListener('click', () => {
                this.refreshData();
            });
        }

        // Déconnexion
        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                this.logout();
            });
        }
    }

    // Changer de page
    switchPage(pageName) {
        this.view.switchPage(pageName);

        // Charger les données spécifiques à la page
        switch (pageName) {
            case 'dashboard':
                this.loadDashboard();
                break;
            case 'reclamations':
                this.loadReclamations();
                break;
            case 'statistiques':
                this.loadStatistiques();
                break;
        }
    }

    // Charger le dashboard
    async loadDashboard() {
        const stats = this.model.stats;
        this.view.displayStats(stats);

        const activities = this.model.getRecentActivities();
        this.view.displayRecentActivities(activities);
    }

    // Charger les réclamations
    async loadReclamations() {
        const reclamations = this.model.getAllReclamations();
        this.view.displayReclamations(reclamations);
    }

    // Appliquer les filtres
    async applyFilters() {
        const filteredReclamations = this.model.filterReclamations(this.currentFilters);
        this.view.displayReclamations(filteredReclamations);
    }

    // Charger les statistiques
    async loadStatistiques() {
        console.log('Chargement des statistiques...');
        // À implémenter avec des vraies bibliothèques de graphiques
    }

    // Rafraîchir les données
    async refreshData() {
        this.view.showRefreshAnimation();
        
        // Simuler un délai de chargement
        await this.delay(1000);

        // Recharger selon la page active
        const activePage = document.querySelector('.page.active').id.replace('-page', '');
        this.switchPage(activePage);

        console.log('✅ Données rafraîchies');
    }

    // Voir les détails d'une réclamation
    viewReclamation(id) {
        const reclamation = this.model.getReclamationById(id);
        if (reclamation) {
            this.view.showReclamationDetails(reclamation);
        } else {
            alert('Réclamation introuvable');
        }
    }

    // Modifier une réclamation
    editReclamation(id) {
        const reclamation = this.model.getReclamationById(id);
        if (reclamation) {
            // Ouvrir le modal de détails qui contient les actions
            this.viewReclamation(id);
        } else {
            alert('Réclamation introuvable');
        }
    }

    // Changer le statut d'une réclamation
    async changeStatus(id, newStatus) {
        if (confirm(`Êtes-vous sûr de vouloir changer le statut à "${this.model.getStatusText(newStatus)}" ?`)) {
            const success = this.model.updateStatus(id, newStatus);
            
            if (success) {
                // Fermer le modal
                this.view.closeModal();
                
                // Afficher un message de succès
                alert(`✅ Statut mis à jour avec succès !`);
                
                // Rafraîchir les données
                await this.refreshData();
            } else {
                alert('❌ Erreur lors de la mise à jour du statut');
            }
        }
    }

    // Supprimer une réclamation
    async deleteReclamation(id) {
        if (confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette réclamation ? Cette action est irréversible.')) {
            const success = this.model.deleteReclamation(id);
            
            if (success) {
                alert('✅ Réclamation supprimée avec succès');
                await this.refreshData();
            } else {
                alert('❌ Erreur lors de la suppression');
            }
        }
    }

    // Déconnexion
    logout() {
        if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
            console.log('Déconnexion...');
            // Redirection vers la page de connexion (à implémenter)
            window.location.href = 'index.html';
        }
    }

    // Utilitaire: délai
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Initialiser l'application admin
document.addEventListener('DOMContentLoaded', () => {
    window.adminController = new AdminController(adminModel, adminView);
    
    console.log('🚀 Interface Administrateur ImpactAble initialisée !');
    console.log('📊 Total réclamations:', adminModel.stats.total);
    console.log('⏳ En attente:', adminModel.stats.enAttente);
    console.log('🔄 En cours:', adminModel.stats.enCours);
    console.log('✅ Résolues:', adminModel.stats.resolues);
    console.log('⚠️ Urgentes:', adminModel.stats.urgentes);
});