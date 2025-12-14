<?php
// Inclure le contrôleur
include_once __DIR__ . '/../../Model/CalendarController.php';

$calendarController = new CalendarController();

// Récupérer les événements (utiliser getEvents() ou getCalendarEvents() selon ce qui existe)
if (method_exists($calendarController, 'getCalendarEvents')) {
    $events = $calendarController->getCalendarEvents();
} elseif (method_exists($calendarController, 'getEvents')) {
    $events = $calendarController->getEvents();
} else {
    $events = []; // Fallback si aucune méthode
}

// Convertir en format JSON pour FullCalendar
$events_json = json_encode($events);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactAble — Calendrier des Campagnes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        .calendar-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .calendar-actions {
            display: flex;
            gap: 10px;
        }

        .fc-toolbar-title {
            font-size: 1.5em !important;
            color: var(--sage) !important;
        }

        .fc-button-primary {
            background-color: var(--sage) !important;
            border-color: var(--sage) !important;
        }

        .fc-button-primary:hover {
            background-color: #5a7a59 !important;
            border-color: #5a7a59 !important;
        }

        .event-legend {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9em;
        }

        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 3px;
        }

        .event-active {
            background-color: #28a745;
        }

        .event-pending {
            background-color: #ffc107;
        }

        .event-completed {
            background-color: #17a2b8;
        }

        .event-default {
            background-color: #3788d8;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar (identique à index.php) -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <img src="assets/images/logo.png" alt="ImpactAble" class="admin-logo-image">
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">Principal</div>
                    <a href="index.php" class="sidebar-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Gestion de contenu</div>
                    <a href="Ges_utilisateurs.php" class="sidebar-link">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="index.php?action=admin-dashboard" class="sidebar-link">
                        <i class="fas fa-briefcase"></i>
                        <span>Opportunités</span>
                    </a>
                    <a href="evenment_back.php" class="sidebar-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>

                    <div class="sidebar-dropdown">
                        <a href="#" class="sidebar-link dropdown-toggle" aria-expanded="true">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Campagnes</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="sidebar-submenu show">
                            <a href="list-camp.php" class="submenu-link">
                                <i class="fas fa-list"></i>
                                <span>Toutes les campagnes</span>
                            </a>
                            <a href="addCampagne.php" class="submenu-link">
                                <i class="fas fa-plus"></i>
                                <span>Nouvelle campagne</span>
                            </a>
                            <a href="Calendar.php" class="submenu-link active">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Calendrier</span>
                            </a>
                            <a href="stats_dashboard.php" class="submenu-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Statistiques</span>
                            </a>
                            <a href="referral.php" class="submenu-link">
                                <i class="fas fa-user-friends"></i>
                                <span>Parrainage</span>
                            </a>
                        </div>
                    </div>

                    <a href="list-don.php" class="sidebar-link">
                        <i class="fas fa-donate"></i>
                        <span>Dons</span>
                    </a>
                    <a href="#resources" class="sidebar-link">
                        <i class="fas fa-book"></i>
                        <span>Ressources</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Communauté</div>
                    <a href="#forum" class="sidebar-link">
                        <i class="fas fa-comments"></i>
                        <span>Forum</span>
                    </a>
                    <a href="#reclamations" class="sidebar-link">
                        <i class="fas fa-comment-alt"></i>
                        <span>Réclamations</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Paramètres</div>
                    <a href="#settings" class="sidebar-link">
                        <i class="fas fa-cog"></i>
                        <span>Configuration</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="admin-user">
                    <div class="admin-avatar">AD</div>
                    <div class="admin-user-info">
                        <h4>Admin User</h4>
                        <p>Administrateur</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h2>Calendrier des Campagnes</h2>
                    <p class="text-muted">Visualisez les dates importantes des campagnes</p>
                </div>
                <div class="header-actions">
                    <a href="addCampagne.php" class="btn primary">
                        <i class="fas fa-plus-circle"></i>
                        Nouvelle Campagne
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <div class="calendar-container">
                    <div class="calendar-header">
                        <h3><i class="fas fa-calendar-alt"></i> Calendrier des Échéances</h3>
                        <div class="calendar-actions">
                            <button id="today-btn" class="btn secondary">
                                <i class="fas fa-calendar-day"></i> Aujourd'hui
                            </button>
                            <a href="list-camp.php" class="btn">
                                <i class="fas fa-list"></i> Voir Liste
                            </a>
                        </div>
                    </div>

                    <!-- Calendrier -->
                    <div id="calendar"></div>

                    <!-- Légende -->
                    <div class="event-legend">
                        <div class="legend-item">
                            <div class="legend-color event-active"></div>
                            <span>Campagne Active</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-pending"></div>
                            <span>En Attente</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-completed"></div>
                            <span>Terminée</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color event-default"></div>
                            <span>Autre Événement</span>
                        </div>
                    </div>
                </div>

                <!-- Liste des campagnes à venir -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-clock"></i> Échéances à Venir</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; gap: 10px;">
                            <?php
                            // Récupérer les campagnes avec deadlines
                            $upcomingCampaigns = $calendarController->getCampaignsWithDeadlines();

                            if (empty($upcomingCampaigns)): ?>
                                <p class="text-muted">Aucune échéance à venir.</p>
                            <?php else:
                                foreach ($upcomingCampaigns as $campaign):
                                    $daysLeft = floor((strtotime($campaign['date_fin']) - time()) / (60 * 60 * 24));
                                    $badgeClass = $daysLeft <= 7 ? 'badge warning' : 'badge success';
                                    ?>
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($campaign['titre']); ?></strong>
                                            <div style="font-size: 0.9em; color: #666;">
                                                Date limite: <?php echo date('d/m/Y', strtotime($campaign['date_fin'])); ?>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <span class="<?php echo $badgeClass; ?>"
                                                style="padding: 4px 8px; border-radius: 4px;">
                                                <?php echo $daysLeft; ?> jour(s) restant(s)
                                            </span>
                                            <div style="font-size: 0.8em; margin-top: 5px;">
                                                <a href="update-camp.php?id=<?php echo $campaign['Id_campagne']; ?>"
                                                    class="btn small">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialiser le calendrier
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: <?php echo $events_json; ?>,
                eventClick: function (info) {
                    // Quand on clique sur un événement
                    if (info.event.extendedProps.campagne_id) {
                        window.location.href = 'update-camp.php?id=' + info.event.extendedProps.campagne_id;
                    }
                },
                eventContent: function (arg) {
                    // Personnaliser l'affichage des événements
                    return {
                        html: '<div style="padding: 2px; font-size: 0.8em;">' +
                            '<i class="fas fa-hand-holding-heart" style="margin-right: 5px;"></i>' +
                            arg.event.title +
                            '</div>'
                    };
                }
            });

            calendar.render();

            // Bouton "Aujourd'hui"
            document.getElementById('today-btn').addEventListener('click', function () {
                calendar.today();
            });

            // Gestion des sous-menus (identique à index.php)
            const menuItems = document.querySelectorAll('.sidebar-link.has-submenu');
            menuItems.forEach(item => {
                item.addEventListener('click', function (e) {
                    if (e.target.closest('.sidebar-link')) {
                        e.preventDefault();
                        const subMenu = this.nextElementSibling;
                        document.querySelectorAll('.sub-menu').forEach(menu => {
                            if (menu !== subMenu) {
                                menu.classList.remove('active');
                                menu.previousElementSibling.classList.remove('active');
                            }
                        });
                        subMenu.classList.toggle('active');
                        this.classList.toggle('active');
                    }
                });
            });
        });
    </script>
</body>

</html>