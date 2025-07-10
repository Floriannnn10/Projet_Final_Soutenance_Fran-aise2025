import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    Bell, 
    Plus, 
    Search, 
    Filter, 
    Trash2, 
    Edit, 
    Eye,
    CheckCircle,
    AlertTriangle,
    Info,
    Mail,
    Users,
    Calendar,
    Clock,
    MoreHorizontal,
    Send,
    Archive
} from 'lucide-react';

export default function NotificationsIndex({ auth }) {
    const [notifications, setNotifications] = useState([]);
    const [filteredNotifications, setFilteredNotifications] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedType, setSelectedType] = useState('');
    const [selectedStatus, setSelectedStatus] = useState('');
    const [loading, setLoading] = useState(true);
    const [showFilters, setShowFilters] = useState(false);

    // Données simulées
    const mockNotifications = [
        {
            id: 1,
            titre: 'Rappel : Cours de Mathématiques',
            contenu: 'N\'oubliez pas votre cours de mathématiques aujourd\'hui à 14h00.',
            type: 'info',
            statut: 'envoyee',
            destinataires: ['BTS SIO 1ère année'],
            date_creation: '2025-01-09T10:30:00',
            date_envoi: '2025-01-09T10:35:00',
            lu_par: 15,
            total_destinataires: 25,
            priorite: 'normale'
        },
        {
            id: 2,
            titre: 'Alerte : Absences excessives',
            contenu: '3 étudiants ont dépassé le seuil d\'absences autorisées.',
            type: 'alerte',
            statut: 'envoyee',
            destinataires: ['Coordinateurs', 'Parents concernés'],
            date_creation: '2025-01-09T09:15:00',
            date_envoi: '2025-01-09T09:20:00',
            lu_par: 8,
            total_destinataires: 12,
            priorite: 'haute'
        },
        {
            id: 3,
            titre: 'Nouvelle matière ajoutée',
            contenu: 'La matière "Développement Web" a été ajoutée à votre emploi du temps.',
            type: 'info',
            statut: 'brouillon',
            destinataires: ['BTS SIO 2ème année'],
            date_creation: '2025-01-09T08:45:00',
            date_envoi: null,
            lu_par: 0,
            total_destinataires: 22,
            priorite: 'normale'
        },
        {
            id: 4,
            titre: 'Maintenance système',
            contenu: 'Le système sera en maintenance ce soir de 22h à 2h du matin.',
            type: 'maintenance',
            statut: 'programmee',
            destinataires: ['Tous les utilisateurs'],
            date_creation: '2025-01-09T08:00:00',
            date_envoi: null,
            lu_par: 0,
            total_destinataires: 156,
            priorite: 'normale'
        }
    ];

    useEffect(() => {
        // Simuler le chargement des données
        setTimeout(() => {
            setNotifications(mockNotifications);
            setFilteredNotifications(mockNotifications);
            setLoading(false);
        }, 1000);
    }, []);

    useEffect(() => {
        let filtered = notifications;

        if (searchTerm) {
            filtered = filtered.filter(notification =>
                notification.titre.toLowerCase().includes(searchTerm.toLowerCase()) ||
                notification.contenu.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }

        if (selectedType) {
            filtered = filtered.filter(notification => notification.type === selectedType);
        }

        if (selectedStatus) {
            filtered = filtered.filter(notification => notification.statut === selectedStatus);
        }

        setFilteredNotifications(filtered);
    }, [searchTerm, selectedType, selectedStatus, notifications]);

    const getTypeIcon = (type) => {
        switch (type) {
            case 'info': return <Info className="h-5 w-5 text-blue-500" />;
            case 'alerte': return <AlertTriangle className="h-5 w-5 text-orange-500" />;
            case 'maintenance': return <Bell className="h-5 w-5 text-red-500" />;
            default: return <Bell className="h-5 w-5 text-gray-500" />;
        }
    };

    const getTypeColor = (type) => {
        switch (type) {
            case 'info': return 'bg-blue-100 text-blue-800';
            case 'alerte': return 'bg-orange-100 text-orange-800';
            case 'maintenance': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusColor = (statut) => {
        switch (statut) {
            case 'envoyee': return 'bg-green-100 text-green-800';
            case 'brouillon': return 'bg-gray-100 text-gray-800';
            case 'programmee': return 'bg-blue-100 text-blue-800';
            case 'echec': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const getPriorityColor = (priorite) => {
        switch (priorite) {
            case 'haute': return 'text-red-600';
            case 'normale': return 'text-blue-600';
            case 'basse': return 'text-gray-600';
            default: return 'text-gray-600';
        }
    };

    const formatDate = (dateString) => {
        if (!dateString) return 'Non envoyée';
        const date = new Date(dateString);
        return date.toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const getReadPercentage = (lu, total) => {
        if (total === 0) return 0;
        return Math.round((lu / total) * 100);
    };

    if (loading) {
        return (
            <AuthenticatedLayout user={auth.user}>
                <Head title="Notifications" />
                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="animate-pulse">
                            <div className="h-8 bg-gray-200 rounded w-1/4 mb-6"></div>
                            <div className="h-12 bg-gray-200 rounded mb-6"></div>
                            <div className="space-y-4">
                                {[...Array(5)].map((_, i) => (
                                    <div key={i} className="h-32 bg-gray-200 rounded"></div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Notifications" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Gestion des notifications</h1>
                            <p className="text-gray-600 mt-2">
                                {filteredNotifications.length} notification{filteredNotifications.length > 1 ? 's' : ''} trouvée{filteredNotifications.length > 1 ? 's' : ''}
                            </p>
                        </div>
                        <div className="flex space-x-3 mt-4 sm:mt-0">
                            <button className="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center space-x-2">
                                <Plus className="h-4 w-4" />
                                <span>Nouvelle notification</span>
                            </button>
                        </div>
                    </div>

                    {/* Search and Filters */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                        <div className="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-4 lg:space-y-0">
                            {/* Search */}
                            <div className="flex-1">
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                    <input
                                        type="text"
                                        placeholder="Rechercher une notification..."
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            {/* Filters */}
                            <div className="flex space-x-3">
                                <select
                                    value={selectedType}
                                    onChange={(e) => setSelectedType(e.target.value)}
                                    className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Tous les types</option>
                                    <option value="info">Information</option>
                                    <option value="alerte">Alerte</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>

                                <select
                                    value={selectedStatus}
                                    onChange={(e) => setSelectedStatus(e.target.value)}
                                    className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Tous les statuts</option>
                                    <option value="envoyee">Envoyée</option>
                                    <option value="brouillon">Brouillon</option>
                                    <option value="programmee">Programmée</option>
                                    <option value="echec">Échec</option>
                                </select>

                                <button
                                    onClick={() => setShowFilters(!showFilters)}
                                    className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2"
                                >
                                    <Filter className="h-4 w-4" />
                                    <span>Filtres</span>
                                </button>
                            </div>
                        </div>

                        {/* Advanced Filters */}
                        {showFilters && (
                            <div className="mt-6 pt-6 border-t border-gray-200">
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Priorité
                                        </label>
                                        <select className="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Toutes les priorités</option>
                                            <option value="haute">Haute</option>
                                            <option value="normale">Normale</option>
                                            <option value="basse">Basse</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Date de création
                                        </label>
                                        <input
                                            type="date"
                                            className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Destinataires
                                        </label>
                                        <select className="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Tous</option>
                                            <option value="etudiants">Étudiants</option>
                                            <option value="enseignants">Enseignants</option>
                                            <option value="parents">Parents</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Notifications Grid */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {filteredNotifications.map((notification) => (
                            <div key={notification.id} className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                                {/* Header */}
                                <div className="flex items-start justify-between mb-4">
                                    <div className="flex items-center space-x-3">
                                        {getTypeIcon(notification.type)}
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">
                                                {notification.titre}
                                            </h3>
                                            <div className="flex items-center space-x-2 mt-1">
                                                <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getTypeColor(notification.type)}`}>
                                                    {notification.type === 'info' ? 'Information' : 
                                                     notification.type === 'alerte' ? 'Alerte' : 'Maintenance'}
                                                </span>
                                                <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(notification.statut)}`}>
                                                    {notification.statut === 'envoyee' ? 'Envoyée' :
                                                     notification.statut === 'brouillon' ? 'Brouillon' :
                                                     notification.statut === 'programmee' ? 'Programmée' : 'Échec'}
                                                </span>
                                                <span className={`text-xs font-medium ${getPriorityColor(notification.priorite)}`}>
                                                    {notification.priorite === 'haute' ? 'Haute priorité' :
                                                     notification.priorite === 'normale' ? 'Priorité normale' : 'Basse priorité'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <button className="text-gray-400 hover:text-gray-600 p-1">
                                            <MoreHorizontal className="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>

                                {/* Content */}
                                <div className="mb-4">
                                    <p className="text-gray-600 text-sm leading-relaxed">
                                        {notification.contenu}
                                    </p>
                                </div>

                                {/* Destinataires */}
                                <div className="mb-4">
                                    <div className="flex items-center space-x-2 mb-2">
                                        <Users className="h-4 w-4 text-gray-400" />
                                        <span className="text-sm font-medium text-gray-700">Destinataires :</span>
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        {notification.destinataires.map((dest, index) => (
                                            <span key={index} className="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                                {dest}
                                            </span>
                                        ))}
                                    </div>
                                </div>

                                {/* Stats */}
                                <div className="mb-4">
                                    <div className="flex items-center justify-between text-sm text-gray-600 mb-2">
                                        <span>Lecture : {notification.lu_par}/{notification.total_destinataires}</span>
                                        <span>{getReadPercentage(notification.lu_par, notification.total_destinataires)}%</span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            className="bg-blue-500 h-2 rounded-full"
                                            style={{ width: `${getReadPercentage(notification.lu_par, notification.total_destinataires)}%` }}
                                        ></div>
                                    </div>
                                </div>

                                {/* Footer */}
                                <div className="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div className="flex items-center space-x-4 text-sm text-gray-500">
                                        <div className="flex items-center space-x-1">
                                            <Calendar className="h-3 w-3" />
                                            <span>Créée : {formatDate(notification.date_creation)}</span>
                                        </div>
                                        {notification.date_envoi && (
                                            <div className="flex items-center space-x-1">
                                                <Send className="h-3 w-3" />
                                                <span>Envoyée : {formatDate(notification.date_envoi)}</span>
                                            </div>
                                        )}
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <button className="text-blue-600 hover:text-blue-900 p-1">
                                            <Eye className="h-4 w-4" />
                                        </button>
                                        <button className="text-green-600 hover:text-green-900 p-1">
                                            <Edit className="h-4 w-4" />
                                        </button>
                                        <button className="text-orange-600 hover:text-orange-900 p-1">
                                            <Send className="h-4 w-4" />
                                        </button>
                                        <button className="text-red-600 hover:text-red-900 p-1">
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Empty State */}
                    {filteredNotifications.length === 0 && (
                        <div className="text-center py-12">
                            <Bell className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                            <h3 className="text-lg font-medium text-gray-900 mb-2">Aucune notification trouvée</h3>
                            <p className="text-gray-600">Aucune notification ne correspond à vos critères de recherche.</p>
                        </div>
                    )}

                    {/* Pagination */}
                    {filteredNotifications.length > 0 && (
                        <div className="mt-8 flex items-center justify-between">
                            <div className="text-sm text-gray-700">
                                Affichage de 1 à {filteredNotifications.length} sur {notifications.length} résultats
                            </div>
                            <div className="flex space-x-2">
                                <button className="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                                    Précédent
                                </button>
                                <button className="px-3 py-2 bg-blue-500 text-white rounded-lg text-sm">
                                    1
                                </button>
                                <button className="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                                    Suivant
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 