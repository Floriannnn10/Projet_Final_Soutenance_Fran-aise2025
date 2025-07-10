import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Search,
    Plus,
    Filter,
    Download,
    Upload,
    Edit,
    Trash2,
    Eye,
    Mail,
    Phone,
    MapPin,
    Calendar,
    GraduationCap,
    Users,
    MoreHorizontal
} from 'lucide-react';

export default function EtudiantsIndex({ auth }) {
    const [etudiants, setEtudiants] = useState([]);
    const [filteredEtudiants, setFilteredEtudiants] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedClasse, setSelectedClasse] = useState('');
    const [loading, setLoading] = useState(true);
    const [showFilters, setShowFilters] = useState(false);

    // Données simulées
    const mockEtudiants = [
        {
            id: 1,
            nom: 'Dubois',
            prenom: 'Lucas',
            email: 'lucas.dubois@student.ifran.fr',
            date_naissance: '2000-05-15',
            classe: { nom: 'BTS SIO 1ère année' },
            telephone: '06 12 34 56 78',
            adresse: '123 Rue de la Paix, Paris',
            taux_presence: 92,
            statut: 'actif'
        },
        {
            id: 2,
            nom: 'Martin',
            prenom: 'Emma',
            email: 'emma.martin@student.ifran.fr',
            date_naissance: '2001-03-22',
            classe: { nom: 'BTS SIO 2ème année' },
            telephone: '06 98 76 54 32',
            adresse: '456 Avenue des Champs, Lyon',
            taux_presence: 88,
            statut: 'actif'
        },
        {
            id: 3,
            nom: 'Bernard',
            prenom: 'Thomas',
            email: 'thomas.bernard@student.ifran.fr',
            date_naissance: '1999-11-08',
            classe: { nom: 'BTS SIO 1ère année' },
            telephone: '06 55 44 33 22',
            adresse: '789 Boulevard Central, Marseille',
            taux_presence: 75,
            statut: 'en_risque'
        }
    ];

    const classes = [
        { id: 1, nom: 'BTS SIO 1ère année' },
        { id: 2, nom: 'BTS SIO 2ème année' },
        { id: 3, nom: 'BTS CG 1ère année' },
        { id: 4, nom: 'BTS CG 2ème année' }
    ];

    useEffect(() => {
        // Simuler le chargement des données
        setTimeout(() => {
            setEtudiants(mockEtudiants);
            setFilteredEtudiants(mockEtudiants);
            setLoading(false);
        }, 1000);
    }, []);

    useEffect(() => {
        let filtered = etudiants;

        if (searchTerm) {
            filtered = filtered.filter(etudiant =>
                etudiant.nom.toLowerCase().includes(searchTerm.toLowerCase()) ||
                etudiant.prenom.toLowerCase().includes(searchTerm.toLowerCase()) ||
                etudiant.email.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }

        if (selectedClasse) {
            filtered = filtered.filter(etudiant =>
                etudiant.classe.nom === selectedClasse
            );
        }

        setFilteredEtudiants(filtered);
    }, [searchTerm, selectedClasse, etudiants]);

    const getStatusColor = (statut) => {
        switch (statut) {
            case 'actif': return 'bg-green-100 text-green-800';
            case 'en_risque': return 'bg-orange-100 text-orange-800';
            case 'inactif': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const getPresenceColor = (taux) => {
        if (taux >= 90) return 'text-green-600';
        if (taux >= 75) return 'text-orange-600';
        return 'text-red-600';
    };

    if (loading) {
        return (
            <AuthenticatedLayout user={auth.user}>
                <Head title="Étudiants" />
                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="animate-pulse">
                            <div className="h-8 bg-gray-200 rounded w-1/4 mb-6"></div>
                            <div className="h-12 bg-gray-200 rounded mb-6"></div>
                            <div className="space-y-4">
                                {[...Array(5)].map((_, i) => (
                                    <div key={i} className="h-16 bg-gray-200 rounded"></div>
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
            <Head title="Étudiants" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Gestion des étudiants</h1>
                            <p className="text-gray-600 mt-2">
                                {filteredEtudiants.length} étudiant{filteredEtudiants.length > 1 ? 's' : ''} trouvé{filteredEtudiants.length > 1 ? 's' : ''}
                            </p>
                        </div>
                        <div className="flex space-x-3 mt-4 sm:mt-0">
                            <button className="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center space-x-2">
                                <Upload className="h-4 w-4" />
                                <span>Importer</span>
                            </button>
                            <button className="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center space-x-2">
                                <Plus className="h-4 w-4" />
                                <span>Nouvel étudiant</span>
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
                                        placeholder="Rechercher un étudiant..."
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            {/* Filters */}
                            <div className="flex space-x-3">
                                <select
                                    value={selectedClasse}
                                    onChange={(e) => setSelectedClasse(e.target.value)}
                                    className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Toutes les classes</option>
                                    {classes.map(classe => (
                                        <option key={classe.id} value={classe.nom}>
                                            {classe.nom}
                                        </option>
                                    ))}
                                </select>

                                <button
                                    onClick={() => setShowFilters(!showFilters)}
                                    className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2"
                                >
                                    <Filter className="h-4 w-4" />
                                    <span>Filtres</span>
                                </button>

                                <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2">
                                    <Download className="h-4 w-4" />
                                    <span>Exporter</span>
                                </button>
                            </div>
                        </div>

                        {/* Advanced Filters */}
                        {showFilters && (
                            <div className="mt-6 pt-6 border-t border-gray-200">
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Statut
                                        </label>
                                        <select className="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Tous les statuts</option>
                                            <option value="actif">Actif</option>
                                            <option value="en_risque">En risque</option>
                                            <option value="inactif">Inactif</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Taux de présence
                                        </label>
                                        <select className="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Tous</option>
                                            <option value="90+">90% et plus</option>
                                            <option value="75-90">75% à 90%</option>
                                            <option value="<75">Moins de 75%</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Année scolaire
                                        </label>
                                        <select className="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Toutes</option>
                                            <option value="2024-2025">2024-2025</option>
                                            <option value="2023-2024">2023-2024</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Students Table */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Étudiant
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Classe
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Contact
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Présence
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {filteredEtudiants.map((etudiant) => (
                                        <tr key={etudiant.id} className="hover:bg-gray-50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <Users className="h-5 w-5 text-blue-600" />
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {etudiant.prenom} {etudiant.nom}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {etudiant.email}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <GraduationCap className="h-4 w-4 text-gray-400 mr-2" />
                                                    <span className="text-sm text-gray-900">{etudiant.classe.nom}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900">
                                                    <div className="flex items-center space-x-1 mb-1">
                                                        <Phone className="h-3 w-3 text-gray-400" />
                                                        <span>{etudiant.telephone}</span>
                                                    </div>
                                                    <div className="flex items-center space-x-1">
                                                        <MapPin className="h-3 w-3 text-gray-400" />
                                                        <span className="text-xs text-gray-500 truncate max-w-xs">
                                                            {etudiant.adresse}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <span className={`text-sm font-medium ${getPresenceColor(etudiant.taux_presence)}`}>
                                                        {etudiant.taux_presence}%
                                                    </span>
                                                    <div className="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                        <div
                                                            className={`h-2 rounded-full ${getPresenceColor(etudiant.taux_presence).replace('text-', 'bg-')}`}
                                                            style={{ width: `${etudiant.taux_presence}%` }}
                                                        ></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(etudiant.statut)}`}>
                                                    {etudiant.statut === 'actif' ? 'Actif' :
                                                     etudiant.statut === 'en_risque' ? 'En risque' : 'Inactif'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div className="flex items-center justify-end space-x-2">
                                                    <button className="text-blue-600 hover:text-blue-900 p-1">
                                                        <Eye className="h-4 w-4" />
                                            </button>
                                                    <button className="text-green-600 hover:text-green-900 p-1">
                                                        <Edit className="h-4 w-4" />
                                                    </button>
                                                    <button className="text-orange-600 hover:text-orange-900 p-1">
                                                        <Mail className="h-4 w-4" />
                                                    </button>
                                                    <button className="text-red-600 hover:text-red-900 p-1">
                                                        <Trash2 className="h-4 w-4" />
                                        </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Pagination */}
                    <div className="mt-8 flex items-center justify-between">
                        <div className="text-sm text-gray-700">
                            Affichage de 1 à {filteredEtudiants.length} sur {etudiants.length} résultats
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
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
