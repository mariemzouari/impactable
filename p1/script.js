// ============ MODÈLE ============
class Article {
  constructor(id, title, preview, content, author, date, category, url, imageUrl) {
    this.id = id;
    this.title = title;
    this.preview = preview;
    this.content = content;
    this.author = author;
    this.date = date;
    this.category = category;
    this.url = url;
    this.imageUrl = imageUrl;
  }
}

class HandicapModel {
  constructor(id, icon, titre, categorie, description, imageUrl) {
    this.id = id;
    this.icon = icon;
    this.titre = titre;
    this.categorie = categorie;
    this.description = description;
    this.imageUrl = imageUrl;
  }
}

class AppData {
  static handicaps = [
    new HandicapModel(1, '<i class="fas fa-wheelchair"></i>', 'Handicap physique / moteur', 'physique', 'Difficulté à se déplacer ou à effectuer des mouvements', 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?w=800'),
    new HandicapModel(2, '<i class="fas fa-brain"></i>', 'Handicap mental / intellectuel', 'mental', 'Limitation des capacités intellectuelles et adaptatives', 'https://images.unsplash.com/photo-1559757175-0eb30cd8c063?w=800'),
    new HandicapModel(3, '<i class="fas fa-ear-deaf"></i>', 'Handicap sensoriel', 'sensoriel', 'Déficience visuelle ou auditive', 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?w=800'),
    new HandicapModel(4, '<i class="fas fa-heart-pulse"></i>', 'Handicap psychique', 'psychique', 'Troubles psychiatriques affectant le comportement', 'https://images.unsplash.com/photo-1494707802533-a0b6fa3c4f46?w=800'),
    new HandicapModel(5, '<i class="fas fa-eye-low-vision"></i>', 'Handicap invisible', 'invisible', 'Limitations non visibles mais bien réelles', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800'),
    new HandicapModel(6, '<i class="fas fa-users"></i>', 'Handicap social', 'social', 'Difficultés d\'intégration et d\'interaction sociale', 'https://images.unsplash.com/photo-1529390079861-591de354faf5?w=800'),
  ];

  static defaultArticles = [
    new Article(1, "Vivre avec une paraplégie : mon quotidien en fauteuil", "Depuis mon accident de moto en 2023, je me déplace en fauteuil roulant. La ville n'est pas toujours adaptée... Mais j'ai découvert des solutions : rampes portatives, applications de signalement, et une communauté solidaire.", "", "Jean Dupont", "2025-03-15", "physique", "https://handicap.gouv.fr", "https://images.unsplash.com/photo-1593113598332-cd25a5f4d7d5?w=600"),
    new Article(2, "Sclérose en plaques : gérer les poussées", "Diagnostiquée à 28 ans, la SEP a bouleversé ma vie. Les poussées de fatigue sont imprévisibles, mais j'ai appris à les anticiper : repos, alimentation, suivi régulier.", "", "Sophie Martin", "2025-02-20", "physique", "https://www.ligue-sclerose.fr", "https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=600"),
    new Article(3, "Autisme : inclusion réussie à l’école", "Enfant autiste, j'ai été scolarisé en classe ordinaire grâce à un AESH. L'inclusion, c'est possible quand on adapte le système.", "", "Lucas Bernard", "2025-01-10", "mental", "https://education.gouv.fr", "https://images.unsplash.com/photo-1581093458791-9d6c92b8e802?w=600"),
    new Article(4, "Surdité : la LSF, ma langue maternelle", "Née sourde, la LSF est ma première langue. La surdité n'est pas un manque, c'est une culture riche.", "", "Amina Khalil", "2025-04-05", "sensoriel", "https://www.sante.gouv.fr", "https://images.unsplash.com/photo-1593113646773-028c26a2e45f?w=600"),
    new Article(5, "Cécité : me déplacer seul dans Paris", "Aveugle depuis l'enfance, je me déplace avec ma canne et mon smartphone. Paris progresse, mais il reste du chemin.", "", "Paul Durand", "2025-03-01", "sensoriel", "https://www.paris.fr", "https://images.unsplash.com/photo-1532629345422-49123e8f6f09?w=600"),
    new Article(6, "Bipolarité : vivre avec les cycles", "Mon diagnostic date de 2022. Avec un traitement et un suivi, je gère mieux mes cycles.", "", "Claire Lefèvre", "2025-02-10", "psychique", "https://www.sante-mentale.fr", "https://images.unsplash.com/photo-1559757148-5d1a0e3a8165?w=600"),
    new Article(7, "Fibromyalgie : la douleur invisible", "Atteinte depuis 5 ans, je souffre de douleurs diffuses. La reconnaissance du handicap invisible est essentielle.", "", "Marie Petit", "2025-01-25", "invisible", "https://www.ameli.fr", "https://images.unsplash.com/photo-1559839734-2b4f4d8c6d57?w=600"),
    new Article(8, "Phobie sociale : sortir de ma bulle", "Grâce à une TCC, j'ai appris à affronter mes peurs. Aujourd'hui, j'aide d'autres.", "", "Thomas Roy", "2025-03-20", "social", "https://www.passeportsante.net", "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600"),
  ];

  static getArticles() {
    const saved = localStorage.getItem('impactable_articles');
    const userArticles = saved ? JSON.parse(saved) : [];
    return [...this.defaultArticles, ...userArticles];
  }

  static saveArticle(article) {
    const articles = JSON.parse(localStorage.getItem('impactable_articles') || '[]');
    articles.push(article);
    localStorage.setItem('impactable_articles', JSON.stringify(articles));
  }
}

// ============ VUE ============
class HandicapView {
  constructor() {
    this.mainView = document.getElementById('main-view');
    this.categoryView = document.getElementById('category-view');
    this.modal = document.getElementById('add-article-modal');
    this.loader = document.getElementById('loader');
  }

  showLoader() {
    this.loader.classList.add('active');
    this.mainView.style.display = 'none';
  }

  hideLoader() {
    this.loader.classList.remove('active');
    this.mainView.style.display = 'grid';
  }

  renderGrid(handicaps, onCardClick) {
    this.mainView.innerHTML = '';
    this.hideLoader();
    if (handicaps.length === 0) {
      this.mainView.innerHTML = '<p style="text-align: center; color: #5E6D38; grid-column: 1/-1; font-size: 1.2em;">Aucun résultat trouvé</p>';
      return;
    }
    handicaps.forEach((h, i) => {
      const card = document.createElement('div');
      card.className = 'handicap-card';
      card.style.animationDelay = `${i * 0.1}s`;
      card.innerHTML = `
        <span class="card-badge">Articles</span>
        <div class="handicap-icon">${h.icon}</div>
        <h3>${h.titre}</h3>
        <p>${h.description}</p>
      `;
      card.addEventListener('click', () => onCardClick(h));
      this.mainView.appendChild(card);
    });
  }

  renderCategory(category, articles, onBack, onAddClick) {
    this.mainView.style.display = 'none';
    this.categoryView.classList.add('active');
    this.categoryView.innerHTML = `
      <div class="category-header">
        <button class="back-button">Retour</button>
        <button class="add-article-btn">+ Ajouter un article</button>
      </div>
      <div class="category-title">${category.icon} ${category.titre}</div>
      <p class="category-desc">${category.description}</p>
      <div class="articles-list">
        ${articles.length === 0
          ? `<p class="no-articles">Aucun article pour le moment.<br>Soyez le premier à contribuer !</p>`
          : articles.map(a => `
            <div class="article-card">
              <img src="${a.imageUrl}" alt="${a.title}" class="article-img" onerror="this.src='https://via.placeholder.com/100?text=Image'">
              <div class="article-content">
                <h4>${a.title}</h4>
                <div class="author">Par ${a.author} • ${new Date(a.date).toLocaleDateString('fr')}</div>
                <div class="preview">${a.preview}</div>
                <a href="${a.url}" target="_blank" class="read-more-btn">Voir plus</a>
              </div>
            </div>
          `).join('')}
      </div>
    `;
    this.categoryView.querySelector('.back-button').onclick = onBack;
    this.categoryView.querySelector('.add-article-btn').onclick = () => onAddClick(category.categorie);
  }

  showAddModal(category, onSubmit) {
    document.getElementById('article-category').value = category;
    this.modal.classList.add('active');
    const form = document.getElementById('add-article-form');
    const submitHandler = (e) => {
      e.preventDefault();
      const fullContent = document.getElementById('article-content').value.trim();
      if (!fullContent) return alert("Veuillez écrire un contenu (au moins 4 lignes)");
      const url = prompt("Lien de l'article complet") || "https://example.com";
      const imageUrl = prompt("URL de l'image", "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600") || "https://via.placeholder.com/100";
      const preview = fullContent.length > 200 ? fullContent.substring(0, 200) + '...' : fullContent;
      const article = new Article(
        Date.now(),
        document.getElementById('article-title').value.trim() || "Article sans titre",
        preview,
        fullContent,
        document.getElementById('article-author').value.trim() || "Anonyme",
        new Date().toISOString().split('T')[0],
        category,
        url,
        imageUrl
      );
      onSubmit(article);
      form.reset();
      this.modal.classList.remove('active');
      form.removeEventListener('submit', submitHandler);
    };
    form.addEventListener('submit', submitHandler);
    document.querySelector('.close-modal').onclick = () => {
      this.modal.classList.remove('active');
      form.removeEventListener('submit', submitHandler);
    };
  }

  showGrid() {
    this.categoryView.classList.remove('active');
    this.mainView.style.display = 'grid';
  }
}

// ============ CONTRÔLEUR ============
class HandicapController {
  constructor() {
    this.view = new HandicapView();
    this.handicaps = AppData.handicaps;
    this.articles = AppData.getArticles();
    this.currentFilter = 'all';
    this.searchQuery = '';
    this.currentCategory = null;
    this.init();
  }

  init() {
    this.view.showLoader();
    setTimeout(() => {
      this.showGrid();
      this.setupEventListeners();
    }, 500);
  }

  setupEventListeners() {
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        this.currentFilter = e.target.dataset.filter;
        this.filterHandicaps();
      });
    });
    document.getElementById('search-input').addEventListener('input', (e) => {
      this.searchQuery = e.target.value.toLowerCase();
      this.filterHandicaps();
    });
  }

  filterHandicaps() {
    let filtered = this.handicaps;
    if (this.currentFilter !== 'all') {
      filtered = filtered.filter(h => h.categorie === this.currentFilter);
    }
    if (this.searchQuery) {
      filtered = filtered.filter(h =>
        h.titre.toLowerCase().includes(this.searchQuery) ||
        h.description.toLowerCase().includes(this.searchQuery)
      );
    }
    this.view.renderGrid(filtered, (handicap) => this.showCategory(handicap));
  }

  showGrid() {
    this.filterHandicaps();
  }

  showCategory(handicap) {
    this.currentCategory = handicap;
    const categoryArticles = this.articles.filter(a => a.category === handicap.categorie);
    this.view.renderCategory(
      handicap,
      categoryArticles,
      () => this.view.showGrid(),
      (cat) => this.view.showAddModal(cat, (article) => this.addArticle(article))
    );
  }

  addArticle(article) {
    AppData.saveArticle(article);
    this.articles = AppData.getArticles();
    this.showCategory(this.currentCategory);
  }
}

// ============ AUTO-SCROLL DES IMAGES ============
class ImageAutoScroll {
  constructor() {
    this.scrollContainer = document.querySelector('.images-scroll');
    this.scrollSpeed = 1;
    this.isPaused = false;
    this.rafId = null;
    this.init();
  }

  init() {
    if (!this.scrollContainer) return;
    const images = this.scrollContainer.innerHTML;
    this.scrollContainer.innerHTML += images;
    this.scrollContainer.addEventListener('mouseenter', () => this.pause());
    this.scrollContainer.addEventListener('mouseleave', () => this.resumeAfterDelay());
    this.start();
  }

  start() {
    const animate = () => {
      if (!this.isPaused) {
        this.scrollContainer.scrollLeft += this.scrollSpeed;
        if (this.scrollContainer.scrollLeft >= this.scrollContainer.scrollWidth / 2) {
          this.scrollContainer.scrollLeft = 0;
        }
      }
      this.rafId = requestAnimationFrame(animate);
    };
    this.rafId = requestAnimationFrame(animate);
  }

  pause() { this.isPaused = true; }
  resumeAfterDelay() { setTimeout(() => this.isPaused = false, 2000); }
}

// ============ INITIALISATION ============
document.addEventListener('DOMContentLoaded', () => {
  new HandicapController();
  new ImageAutoScroll();
});