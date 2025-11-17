// ADMIN MODEL - Gestion des données administrateur
class AdminModel {
    constructor() {
        // Données complètes des réclamations avec plus de détails
        this.reclamations = [
            {
                id: 'REC-12A4B5C',
                sujet: 'Problème d\'accessibilité rampe d\'accès',
                description: 'Rampe d\'accès trop raide et dangereuse pour les fauteuils roulants à l\'entrée de la clinique. Pente estimée à plus de 15 degrés, norme non respectée.',
                categorie: 'accessibilite',
                lieu: 'Clinique La Rose, Tunis',
                dateIncident: '2024-11-10',
                dateCreation: '2024-11-12',
                status: 'en_cours',
                priorite: 'haute',
                nom: 'Ahmed Ben Salah',
                prenom: 'Ahmed',
                email: 'ahmed.bs@email.com',
                telephone: '+216 98 123 456',
                typeHandicap: 'physique',
                cin: '08456789',
                adresse: '15 Avenue Habib Bourguiba, Tunis 1000',
                personnesImpliquees: 'Service accueil - Mme Fatima',
                temoins: 'M. Mohamed Karim, patient',
                actionsPrecedentes: 'Email envoyé le 08/11/2024 - Aucune réponse',
                reponseRecue: 'Aucune',
                solutionSouhaitee: 'Mise aux normes de la rampe d\'accès selon réglementation',
                accepteContact: true,
                acceptePublic: false,
                derniereModification: '2024-11-14'
            },
            {
                id: 'REC-78X9Y2Z',
                sujet: 'Refus d\'accès au service',
                description: 'Refus d\'accès au service administratif en raison de mon handicap invisible. Agent irrespectueux et discriminatoire.',
                categorie: 'discrimination',
                lieu: 'Mairie de Sfax',
                dateIncident: '2024-10-28',
                dateCreation: '2024-10-29',
                status: 'en_attente',
                priorite: 'urgente',
                nom: 'Trabelsi',
                prenom: 'Fatima',
                email: 'fatima.t@email.com',
                telephone: '+216 50 987 654',
                typeHandicap: 'invisible',
                cin: '09123456',
                adresse: '23 Rue de la République, Sfax 3000',
                personnesImpliquees: 'Agent administratif - Service état civil',
                temoins: 'Plusieurs personnes dans la file',
                actionsPrecedentes: 'Plainte verbale au responsable',
                reponseRecue: 'Promesse d\'enquête interne',
                solutionSouhaitee: 'Sanctions disciplinaires, formation du personnel',
                accepteContact: true,
                acceptePublic: true,
                derniereModification: '2024-10-29'
            },
            {
                id: 'REC-45K6L7M',
                sujet: 'Transport inaccessible',
                description: 'Bus sans rampe d\'accès, impossible de monter avec mon fauteuil roulant. Chauffeur refusant d\'aider.',
                categorie: 'transport',
                lieu: 'Station Transtu Centre-Ville, Tunis',
                dateIncident: '2024-11-05',
                dateCreation: '2024-11-06',
                status: 'resolu',
                priorite: 'moyenne',
                nom: 'Karray',
                prenom: 'Mohamed',
                email: 'mohamed.k@email.com',
                telephone: '+216 22 456 789',
                typeHandicap: 'physique',
                cin: '07789456',
                adresse: '45 Avenue Mohamed V, Tunis 1002',
                personnesImpliquees: 'Chauffeur bus ligne 12',
                temoins: 'Plusieurs passagers',
                actionsPrecedentes: 'Appel au service client Transtu',
                reponseRecue: 'Excuses et engagement d\'amélioration',
                solutionSouhaitee: 'Mise en service de bus accessibles',
                accepteContact: true,
                acceptePublic: true,
                derniereModification: '2024-11-10'
            },
            {
                id: 'REC-89P3Q4R',
                sujet: 'Discrimination à l\'embauche',
                description: 'Candidature refusée explicitement en raison de mon handicap visuel, malgré mes compétences.',
                categorie: 'emploi',
                lieu: 'Société TechCorp, Ariana',
                dateIncident: '2024-11-01',
                dateCreation: '2024-11-02',
                status: 'en_cours',
                priorite: 'haute',
                nom: 'Mansouri',
                prenom: 'Leila',
                email: 'leila.m@email.com',
                telephone: '+216 28 567 890',
                typeHandicap: 'sensoriel',
                cin: '10234567',
                adresse: '78 Rue des Jasmins, Ariana 2080',
                personnesImpliquees: 'Responsable RH - M. Karim Ben Ali',
                temoins: 'N/A',
                actionsPrecedentes: 'Email de réclamation à la direction',
                reponseRecue: 'Réponse évasive',
                solutionSouhaitee: 'Réexamen de ma candidature, sanctions',
                accepteContact: true,
                acceptePublic: false,
                derniereModification: '2024-11-13'
            },
            {
                id: 'REC-56T7U8V',
                sujet: 'Établissement scolaire non adapté',
                description: 'École sans ascenseur ni toilettes adaptées. Enfant contraint d\'abandonner sa scolarité.',
                categorie: 'education',
                lieu: 'École Primaire Ibn Khaldoun, Sousse',
                dateIncident: '2024-10-15',
                dateCreation: '2024-10-16',
                status: 'en_attente',
                priorite: 'urgente',
                nom: 'Gharbi',
                prenom: 'Sami',
                email: 'sami.g@email.com',
                telephone: '+216 55 234 567',
                typeHandicap: 'physique',
                cin: '08912345',
                adresse: '12 Rue Farhat Hached, Sousse 4000',
                personnesImpliquees: 'Direction de l\'école',
                temoins: 'Autres parents d\'élèves',
                actionsPrecedentes: 'Réunion avec le directeur',
                reponseRecue: 'Manque de budget',
                solutionSouhaitee: 'Travaux d\'accessibilité urgents',
                accepteContact: true,
                acceptePublic: true,
                derniereModification: '2024-10-16'
            },
            {
                id: 'REC-23W4X5Y',
                sujet: 'Refus de soins médicaux',
                description: 'Médecin refusant de me recevoir en consultation en raison de mon handicap mental.',
                categorie: 'sante',
                lieu: 'Cabinet Dr. Mansour, Monastir',
                dateIncident: '2024-11-08',
                dateCreation: '2024-11-09',
                status: 'en_attente',
                priorite: 'haute',
                nom: 'Bouzid',
                prenom: 'Nadia',
                email: 'nadia.b@email.com',
                telephone: '+216 29 876 543',
                typeHandicap: 'mental',
                cin: '09876543',
                adresse: '34 Avenue de l\'Indépendance, Monastir 5000',
                personnesImpliquees: 'Dr. Mansour',
                temoins: 'Secrétaire médicale',
                actionsPrecedentes: 'Plainte à l\'ordre des médecins',
                reponseRecue: 'En cours d\'instruction',
                solutionSouhaitee: 'Sanctions, formation à la diversité',
                accepteContact: true,
                acceptePublic: false,
                derniereModification: '2024-11-09'
            },
            {
                id: 'REC-67Z8A9B',
                sujet: 'Site web inaccessible',
                description: 'Site de l\'administration fiscale non compatible avec les lecteurs d\'écran pour personnes aveugles.',
                categorie: 'administration',
                lieu: 'Site web impots.gov.tn',
                dateIncident: '2024-11-11',
                dateCreation: '2024-11-12',
                status: 'en_cours',
                priorite: 'moyenne',
                nom: 'Jebali',
                prenom: 'Youssef',
                email: 'youssef.j@email.com',
                telephone: '+216 26 345 678',
                typeHandicap: 'sensoriel',
                cin: '11234567',
                adresse: '56 Boulevard 7 Novembre, Tunis 1001',
                personnesImpliquees: 'Service informatique des impôts',
                temoins: 'N/A',
                actionsPrecedentes: 'Email au service technique',
                reponseRecue: 'Prise en compte, mise à jour prévue',
                solutionSouhaitee: 'Mise en conformité WCAG',
                accepteContact: true,
                acceptePublic: true,
                derniereModification: '2024-11-13'
            },
            {
                id: 'REC-90C1D2E',
                sujet: 'Harcèlement moral au travail',
                description: 'Harcèlement répété de la part de mon supérieur en raison de mon handicap psychique.',
                categorie: 'emploi',
                lieu: 'Entreprise XYZ, La Marsa',
                dateIncident: '2024-10-25',
                dateCreation: '2024-10-26',
                status: 'rejete',
                priorite: 'haute',
                nom: 'Sassi',
                prenom: 'Rim',
                email: 'rim.s@email.com',
                telephone: '+216 24 789 012',
                typeHandicap: 'psychique',
                cin: '09345678',
                adresse: '89 Avenue Taieb Mhiri, La Marsa 2070',
                personnesImpliquees: 'Manager - M. Samir Khalil',
                temoins: 'Collègues de bureau',
                actionsPrecedentes: 'Signalement aux RH',
                reponseRecue: 'Classé sans suite - preuves insuffisantes',
                solutionSouhaitee: 'Enquête approfondie, médiation',
                accepteContact: false,
                acceptePublic: false,
                derniereModification: '2024-11-01'
            }
        ];

        // Statistiques
        this.stats = this.calculateStats();
    }

    // Calculer les statistiques
    calculateStats() {
        return {
            enAttente: this.reclamations.filter(r => r.status === 'en_attente').length,
            enCours: this.reclamations.filter(r => r.status === 'en_cours').length,
            resolues: this.reclamations.filter(r => r.status === 'resolu').length,
            urgentes: this.reclamations.filter(r => r.priorite === 'urgente').length,
            total: this.reclamations.length
        };
    }

    // Récupérer toutes les réclamations
    getAllReclamations() {
        return this.reclamations;
    }

    // Récupérer une réclamation par ID
    getReclamationById(id) {
        return this.reclamations.find(r => r.id === id);
    }

    // Filtrer les réclamations
    filterReclamations(filters) {
        let results = [...this.reclamations];

        if (filters.search) {
            const searchLower = filters.search.toLowerCase();
            results = results.filter(r =>
                r.id.toLowerCase().includes(searchLower) ||
                r.nom.toLowerCase().includes(searchLower) ||
                r.sujet.toLowerCase().includes(searchLower) ||
                r.email.toLowerCase().includes(searchLower)
            );
        }

        if (filters.status) {
            results = results.filter(r => r.status === filters.status);
        }

        if (filters.priorite) {
            results = results.filter(r => r.priorite === filters.priorite);
        }

        if (filters.categorie) {
            results = results.filter(r => r.categorie === filters.categorie);
        }

        return results;
    }

    // Mettre à jour le statut d'une réclamation
    updateStatus(id, newStatus) {
        const reclamation = this.getReclamationById(id);
        if (reclamation) {
            reclamation.status = newStatus;
            reclamation.derniereModification = new Date().toISOString().split('T')[0];
            this.stats = this.calculateStats();
            return true;
        }
        return false;
    }

    // Supprimer une réclamation
    deleteReclamation(id) {
        const index = this.reclamations.findIndex(r => r.id === id);
        if (index !== -1) {
            this.reclamations.splice(index, 1);
            this.stats = this.calculateStats();
            return true;
        }
        return false;
    }

    // Mapper le statut en français
    getStatusText(status) {
        const statusMap = {
            'en_attente': 'En attente',
            'en_cours': 'En cours',
            'resolu': 'Résolu',
            'rejete': 'Rejeté'
        };
        return statusMap[status] || status;
    }

    // Mapper la priorité en français
    getPrioriteText(priorite) {
        const prioriteMap = {
            'basse': 'Basse',
            'moyenne': 'Moyenne',
            'haute': 'Haute',
            'urgente': 'Urgente'
        };
        return prioriteMap[priorite] || priorite;
    }

    // Mapper la catégorie en français
    getCategorieText(categorie) {
        const categorieMap = {
            'accessibilite': 'Accessibilité',
            'discrimination': 'Discrimination',
            'service': 'Service',
            'transport': 'Transport',
            'education': 'Éducation',
            'emploi': 'Emploi',
            'sante': 'Santé',
            'administration': 'Administration',
            'autre': 'Autre'
        };
        return categorieMap[categorie] || categorie;
    }

    // Récupérer les activités récentes
    getRecentActivities() {
        return this.reclamations
            .sort((a, b) => new Date(b.derniereModification) - new Date(a.derniereModification))
            .slice(0, 5)
            .map(r => ({
                id: r.id,
                title: `Réclamation ${r.id}`,
                description: `${r.nom} - ${r.sujet}`,
                status: r.status,
                date: r.derniereModification
            }));
    }
}

// Créer une instance globale du modèle admin
const adminModel = new AdminModel();