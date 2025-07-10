import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    GraduationCap,
    Users,
    BookOpen,
    Calendar,
    Plus,
    Search,
    Filter,
    Edit,
    Trash2,
    Eye,
    TrendingUp,
    Clock,
    MapPin,
    User,
    CheckCircle,
    XCircle,
    AlertTriangle,
    MoreHorizontal
} from 'lucide-react';

export default function ClassesIndex({ auth }) {
    const [classes, setClasses] = useState([]);
    const [filteredClasses, setFilteredClasses] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedNiveau, setSelectedNiveau] = useState('');
    const [loading, setLoading] = useState(true);

    // Données simulées
    const mockClasses = [
        {
            id: 1,
            nom: 'BTS SIO 1ère année',
            niveau: 'BTS',
            annee_scolaire: '2024-2025',
            effectif: 25,
            effectif_max: 30,
            taux_presence: 92.5,
            enseignants: [
                { id: 1, nom: 'Dupont', prenom: 'Marie', matiere: 'Mathématiques' },
                { id: 2, nom: 'Martin', prenom: 'Pierre', matiere: 'Informatique' }
            ],
            matieres: [
                { id: 1, nom: 'Mathématiques', heures: 120 },
                { id: 2, nom: 'Informatique', heures: 180 },
                { id: 3, nom: 'Anglais', heures: 60 }
            ],
            statut: 'active',
            date_creation: '2024-09-01',
            responsable: { id: 1, nom: 'Bernard', prenom: 'Sophie' }
        },
        {
            id: 2,
            nom: 'BTS SIO 2ème année',
            niveau: 'BTS',
            annee_scolaire: '2024-2025',
            effectif: 22,
            effectif_max: 28,
            taux_presence: 89.3,
            enseignants: [
                { id: 3, nom: 'Leroy', prenom: 'Jean', matiere: 'Développement Web' },
                { id: 4, nom: 'Moreau', prenom: 'Anne', matiere: 'Base de données' }
            ],
            matieres: [
                { id: 4, nom: 'Développement Web', heures: 200 },
                { id: 5, nom: 'Base de données', heures: 150 },
                { id: 6, nom: 'Réseaux', heures: 120 }
            ],
            statut: 'active',
            date_creation: '2023-09-01',
            responsable: { id: 2, nom: 'Petit', prenom: 'Michel' }
        },
        {
            id: 3,
            nom: 'BTS CG 1ère année',
            niveau: 'BTS',
            annee_scolaire: '2024-2025',
            effectif: 28,
            effectif_max: 30,
            taux_presence: 94.1,
            enseignants: [
                { id: 5, nom: 'Roux', prenom: 'Claire', matiere: 'Comptabilité' },
                { id: 6, nom: 'Blanc', prenom: 'Paul', matiere: 'Gestion' }
            ],
            matieres: [
                { id: 7, nom: 'Comptabilité', heures: 160 },
                { id: 8, nom: 'Gestion', heures: 140 },
                { id: 9, nom: 'Droit', heures: 80 }
            ],
            statut: 'active',
            date_creation: '2024-09-01',
            responsable: { id: 3, nom: 'Garcia', prenom: 'Isabelle' }
        }
    ];

    useEffect(() => {
        // Simuler le chargement des données
        setTimeout(() => {
            setClasses(mockClasses);
            setFilteredClasses(mockClasses);
            setLoading(false);
        }, 1000);
    }, []);

    useEffect(() => {
        let filtered = classes;

        if (searchTerm) {
            filtered = filtered.filter(classe =>
                classe.nom.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }

        if (selectedNiveau) {
            filtered = filtered.filter(classe => classe.niveau === selectedNiveau);
        }

        setFilteredClasses(filtered);
    }, [searchTerm, selectedNiveau, classes]);

    const getStatusColor = (statut) => {
        switch (statut) {
            case 'active': return 'bg-green-100 text-green-800';
            case 'inactive': return 'bg-red-100 text-red-800';
            case 'archived': return 'bg-gray-100 text-gray-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const getPresenceColor = (taux) => {
        if (taux >= 90) return 'text-green-600';
        if (taux >= 75) return 'text-orange-600';
        return 'text-red-600';
    };

    const getOccupancyColor = (effectif, max) => {
        const percentage = (effectif / max) * 100;
        if (percentage >= 90) return 'text-red-600';
        if (percentage >= 75) return 'text-orange-600';
        return 'text-green-600';
    };

    if (loading) {
        return (
            <AuthenticatedLayout user={auth.user}>
                <Head title="Classes" />
                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="animate-pulse">
                            <div className="h-8 bg-gray-200 rounded w-1/4 mb-6"></div>
                            <div className="h-12 bg-gray-200 rounded mb-6"></div>
                            <div className="space-y-4">
                                {[...Array(3)].map((_, i) => (
                                    <div key={i} className="h-48 bg-gray-200 rounded"></div>
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
            <Head title="Classes" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Gestion des classes</h1>
                            <p className="text-gray-600 mt-2">
                                {filteredClasses.length} classe{filteredClasses.length > 1 ? 's' : ''} trouvée{filteredClasses.length > 1 ? 's' : ''}
                            </p>
                        </div>
                        <div className="flex space-x-3 mt-4 sm:mt-0">
                            <button className="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center space-x-2">
                                <Plus className="h-4 w-4" />
                                <span>Nouvelle classe</span>
                            </button>
                        </div>
                    </div>

                    {/* Stats Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-blue-100">
                                    <GraduationCap className="h-6 w-6 text-blue-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Total classes</p>
                                    <p className="text-2xl font-bold text-gray-900">{classes.length}</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-green-100">
                                    <Users className="h-6 w-6 text-green-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Total étudiants</p>
                                    <p className="text-2xl font-bold text-gray-900">
                                        {classes.reduce((sum, classe) => sum + classe.effectif, 0)}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-purple-100">
                                    <BookOpen className="h-6 w-6 text-purple-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Moyenne présence</p>
                                    <p className="text-2xl font-bold text-gray-900">
                                        {Math.round(classes.reduce((sum, classe) => sum + classe.taux_presence, 0) / classes.length)}%
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-orange-100">
                                    <TrendingUp className="h-6 w-6 text-orange-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Taux d'occupation</p>
                                    <p className="text-2xl font-bold text-gray-900">
                                        {Math.round(classes.reduce((sum, classe) => sum + (classe.effectif / classe.effectif_max * 100), 0) / classes.length)}%
                                    </p>
                                </div>
                            </div>
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
                                        placeholder="Rechercher une classe..."
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            {/* Filters */}
                            <div className="flex space-x-3">
                                <select
                                    value={selectedNiveau}
                                    onChange={(e) => setSelectedNiveau(e.target.value)}
                                    className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Tous les niveaux</option>
                                    <option value="BTS">BTS</option>
                                    <option value="Licence">Licence</option>
                                    <option value="Master">Master</option>
                                </select>

                                <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2">
                                    <Filter className="h-4 w-4" />
                                    <span>Filtres</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Classes Grid */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {filteredClasses.map((classe) => (
                            <div key={classe.id} className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                                {/* Header */}
                                <div className="flex items-start justify-between mb-4">
                                    <div className="flex items-center space-x-3">
                                        <div className="p-3 rounded-lg bg-blue-100">
                                            <GraduationCap className="h-6 w-6 text-blue-600" />
                                        </div>
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">{classe.nom}</h3>
                                            <div className="flex items-center space-x-2 mt-1">
                                                <span className="text-sm text-gray-600">{classe.niveau}</span>
                                                <span className="text-gray-300">•</span>
                                                <span className="text-sm text-gray-600">{classe.annee_scolaire}</span>
                                                <span className="text-gray-300">•</span>
                                                <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(classe.statut)}`}>
                                                    {classe.statut === 'active' ? 'Active' :
                                                     classe.statut === 'inactive' ? 'Inactive' : 'Archivée'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <button className="text-blue-600 hover:text-blue-900 p-1">
                                            <Eye className="h-4 w-4" />
                                        </button>
                                        <button className="text-green-600 hover:text-green-900 p-1">
                                            <Edit className="h-4 w-4" />
                                        </button>
                                        <button className="text-red-600 hover:text-red-900 p-1">
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>

                                {/* Stats */}
                                <div className="grid grid-cols-2 gap-4 mb-4">
                                    <div className="bg-gray-50 rounded-lg p-3">
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm text-gray-600">Étudiants</span>
                                            <span className={`text-sm font-semibold ${getOccupancyColor(classe.effectif, classe.effectif_max)}`}>
                                                {classe.effectif}/{classe.effectif_max}
                                            </span>
                                        </div>
                                        <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
                                            <div
                                                className={`h-2 rounded-full ${getOccupancyColor(classe.effectif, classe.effectif_max).replace('text-', 'bg-')}`}
                                                style={{ width: `${(classe.effectif / classe.effectif_max) * 100}%` }}
                                            ></div>
                                        </div>
                                    </div>

                                    <div className="bg-gray-50 rounded-lg p-3">
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm text-gray-600">Présence</span>
                                            <span className={`text-sm font-semibold ${getPresenceColor(classe.taux_presence)}`}>
                                                {classe.taux_presence}%
                                            </span>
                                        </div>
                                        <div className="w-full bg-gray-200 rounded-full h-2 mt-2">
                                            <div
                                                className={`h-2 rounded-full ${getPresenceColor(classe.taux_presence).replace('text-', 'bg-')}`}
                                                style={{ width: `${classe.taux_presence}%` }}
                                            ></div>
                                        </div>
                                    </div>
                                </div>

                                {/* Responsable */}
                                <div className="mb-4">
                                    <div className="flex items-center space-x-2 mb-2">
                                        <User className="h-4 w-4 text-gray-400" />
                                        <span className="text-sm font-medium text-gray-700">Responsable :</span>
                                    </div>
                                    <span className="text-sm text-gray-900">
                                        {classe.responsable.prenom} {classe.responsable.nom}
                                    </span>
                                </div>

                                {/* Enseignants */}
                                <div className="mb-4">
                                    <div className="flex items-center space-x-2 mb-2">
                                        <Users className="h-4 w-4 text-gray-400" />
                                        <span className="text-sm font-medium text-gray-700">Enseignants ({classe.enseignants.length}) :</span>
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        {classe.enseignants.slice(0, 3).map((enseignant, index) => (
                                            <span key={index} className="inline-flex px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                                {enseignant.prenom} {enseignant.nom}
                                            </span>
                                        ))}
                                        {classe.enseignants.length > 3 && (
                                            <span className="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                                +{classe.enseignants.length - 3} autres
                                            </span>
                                        )}
                                    </div>
                                </div>

                                {/* Matières */}
                                <div className="mb-4">
                                    <div className="flex items-center space-x-2 mb-2">
                                        <BookOpen className="h-4 w-4 text-gray-400" />
                                        <span className="text-sm font-medium text-gray-700">Matières ({classe.matieres.length}) :</span>
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        {classe.matieres.slice(0, 3).map((matiere, index) => (
                                            <span key={index} className="inline-flex px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                                {matiere.nom} ({matiere.heures}h)
                                            </span>
                                        ))}
                                        {classe.matieres.length > 3 && (
                                            <span className="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                                +{classe.matieres.length - 3} autres
                                            </span>
                                        )}
                                    </div>
                                </div>

                                {/* Footer */}
                                <div className="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div className="flex items-center space-x-4 text-sm text-gray-500">
                                        <div className="flex items-center space-x-1">
                                            <Calendar className="h-3 w-3" />
                                            <span>Créée le {new Date(classe.date_creation).toLocaleDateString('fr-FR')}</span>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <button className="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            Voir détails
                                        </button>
                                        <button className="text-green-600 hover:text-green-900 text-sm font-medium">
                                            Modifier
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Empty State */}
                    {filteredClasses.length === 0 && (
                        <div className="text-center py-12">
                            <GraduationCap className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                            <h3 className="text-lg font-medium text-gray-900 mb-2">Aucune classe trouvée</h3>
                            <p className="text-gray-600">Aucune classe ne correspond à vos critères de recherche.</p>
                        </div>
                    )}

                    {/* Pagination */}
                    {filteredClasses.length > 0 && (
                        <div className="mt-8 flex items-center justify-between">
                            <div className="text-sm text-gray-700">
                                Affichage de 1 à {filteredClasses.length} sur {classes.length} résultats
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
