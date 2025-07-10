import axios from 'axios';

// Configuration de base
const api = axios.create({
    baseURL: '/api', 
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

// Intercepteur pour ajouter le token d'authentification
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Intercepteur pour gérer les erreurs
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Redirection vers la page de connexion
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

// Service pour les étudiants
export const etudiantService = {
    getAll: (params = {}) => api.get('/etudiants', { params }),
    getById: (id) => api.get(`/etudiants/${id}`),
    create: (data) => api.post('/etudiants', data),
    update: (id, data) => api.put(`/etudiants/${id}`, data),
    delete: (id) => api.delete(`/etudiants/${id}`),
    search: (query) => api.get('/etudiants/search', { params: { q: query } }),
    statistiques: (id) => api.get(`/etudiants/${id}/statistiques`),
    parClasse: (classeId) => api.get(`/etudiants/classe/${classeId}`),
};

// Service pour les présences
export const presenceService = {
    getAll: (params = {}) => api.get('/presences', { params }),
    getById: (id) => api.get(`/presences/${id}`),
    create: (data) => api.post('/presences', data),
    update: (id, data) => api.put(`/presences/${id}`, data),
    delete: (id) => api.delete(`/presences/${id}`),
    parEtudiant: (etudiantId) => api.get(`/presences/etudiant/${etudiantId}`),
    parClasse: (classeId) => api.get(`/presences/classe/${classeId}`),
    parMatiere: (matiereId) => api.get(`/presences/matiere/${matiereId}`),
    parDate: (date) => api.get(`/presences/date/${date}`),
};

// Service pour les classes
export const classeService = {
    getAll: () => api.get('/classes'),
    getById: (id) => api.get(`/classes/${id}`),
    create: (data) => api.post('/classes', data),
    update: (id, data) => api.put(`/classes/${id}`, data),
    delete: (id) => api.delete(`/classes/${id}`),
    avecEtudiants: (id) => api.get(`/classes/${id}/etudiants`),
    avecEnseignants: (id) => api.get(`/classes/${id}/enseignants`),
};

// Service pour les matières
export const matiereService = {
    getAll: () => api.get('/matieres'),
    getById: (id) => api.get(`/matieres/${id}`),
    create: (data) => api.post('/matieres', data),
    update: (id, data) => api.put(`/matieres/${id}`, data),
    delete: (id) => api.delete(`/matieres/${id}`),
    parClasse: (classeId) => api.get(`/matieres/classe/${classeId}`),
};

// Service pour les notifications
export const notificationService = {
    getAll: (params = {}) => api.get('/notifications', { params }),
    getById: (id) => api.get(`/notifications/${id}`),
    marquerCommeLue: (id) => api.put(`/notifications/${id}/lue`),
    marquerCommeNonLue: (id) => api.put(`/notifications/${id}/non-lue`),
    supprimer: (id) => api.delete(`/notifications/${id}`),
    nonLues: () => api.get('/notifications/non-lues'),
};

// Service pour les justifications d'absence
export const justificationService = {
    getAll: (params = {}) => api.get('/justifications', { params }),
    getById: (id) => api.get(`/justifications/${id}`),
    create: (data) => api.post('/justifications', data),
    update: (id, data) => api.put(`/justifications/${id}`, data),
    delete: (id) => api.delete(`/justifications/${id}`),
    approuver: (id) => api.put(`/justifications/${id}/approuver`),
    rejeter: (id) => api.put(`/justifications/${id}/rejeter`),
    parEtudiant: (etudiantId) => api.get(`/justifications/etudiant/${etudiantId}`),
};

// Service pour les alertes droppé
export const alerteService = {
    getEtudiantsDroppes: () => api.get('/alertes/etudiants/droppes'),
    declencherDetection: () => api.post('/alertes/detection'),
    envoyerNotifications: (etudiantId, type) =>
        api.post('/alertes/notifications', { etudiant_id: etudiantId, type }),
};

// Service d'authentification
export const authService = {
    login: (credentials) => api.post('/login', credentials),
    logout: () => api.post('/logout'),
    user: () => api.get('/user'),
    register: (data) => api.post('/register', data),
    forgotPassword: (email) => api.post('/forgot-password', { email }),
    resetPassword: (data) => api.post('/reset-password', data),
};

// Service pour les statistiques
export const statistiqueService = {
    globales: () => api.get('/statistiques/globales'),
    parClasse: (classeId) => api.get(`/statistiques/classe/${classeId}`),
    parMatiere: (matiereId) => api.get(`/statistiques/matiere/${matiereId}`),
    parPeriode: (debut, fin) => api.get('/statistiques/periode', { params: { debut, fin } }),
};

export default api;
