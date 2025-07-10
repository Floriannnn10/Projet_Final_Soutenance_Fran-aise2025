import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Calendar,
    Clock,
    Users,
    CheckCircle,
    XCircle,
    AlertTriangle,
    Filter,
    Download,
    Upload,
    Plus,
    Search,
    ChevronLeft,
    ChevronRight,
    MoreHorizontal,
    BookOpen,
    MapPin,
    User,
    GraduationCap,
    Edit
} from 'lucide-react';

export default function PresencesIndex({ auth }) {
    const [selectedDate, setSelectedDate] = useState(new Date());
    const [selectedClasse, setSelectedClasse] = useState('');
    const [selectedMatiere, setSelectedMatiere] = useState('');
    const [presences, setPresences] = useState([]);
    const [classes, setClasses] = useState([]);
    const [matieres, setMatieres] = useState([]);
    const [loading, setLoading] = useState(true);
    const [viewMode, setViewMode] = useState('list'); // 'list' or 'calendar'

    // Données simulées
    const mockClasses = [
        { id: 1, nom: 'BTS SIO 1ère année', effectif: 25 },
        { id: 2, nom: 'BTS SIO 2ème année', effectif: 22 },
        { id: 3, nom: 'BTS CG 1ère année', effectif: 28 }
    ];

    const mockMatieres = [
        { id: 1, nom: 'Mathématiques', enseignant: 'Prof. Dupont' },
        { id: 2, nom: 'Informatique', enseignant: 'Prof. Martin' },
        { id: 3, nom: 'Anglais', enseignant: 'Prof. Smith' }
    ];

    const mockPresences = [
        {
            id: 1,
            etudiant: { id: 1, nom: 'Dubois', prenom: 'Lucas', email: 'lucas.dubois@student.ifran.fr' },
            classe: { id: 1, nom: 'BTS SIO 1ère année' },
            matiere: { id: 1, nom: 'Mathématiques' },
            date: '2025-01-09',
            heure_debut: '14:00',
            heure_fin: '16:00',
            statut: 'present',
            justifie: false,
            commentaire: ''
        },
        {
            id: 2,
            etudiant: { id: 2, nom: 'Martin', prenom: 'Emma', email: 'emma.martin@student.ifran.fr' },
            classe: { id: 1, nom: 'BTS SIO 1ère année' },
            matiere: { id: 1, nom: 'Mathématiques' },
            date: '2025-01-09',
            heure_debut: '14:00',
            heure_fin: '16:00',
            statut: 'absent',
            justifie: true,
            commentaire: 'Certificat médical'
        },
        {
            id: 3,
            etudiant: { id: 3, nom: 'Bernard', prenom: 'Thomas', email: 'thomas.bernard@student.ifran.fr' },
            classe: { id: 1, nom: 'BTS SIO 1ère année' },
            matiere: { id: 1, nom: 'Mathématiques' },
            date: '2025-01-09',
            heure_debut: '14:00',
            heure_fin: '16:00',
            statut: 'retard',
            justifie: false,
            commentaire: 'Retard de 15 minutes'
        }
    ];

    useEffect(() => {
        // Simuler le chargement des données
        setTimeout(() => {
            setClasses(mockClasses);
            setMatieres(mockMatieres);
            setPresences(mockPresences);
            setLoading(false);
        }, 1000);
    }, []);

    const getStatusIcon = (statut) => {
        switch (statut) {
            case 'present': return <CheckCircle className="h-5 w-5 text-green-500" />;
            case 'absent': return <XCircle className="h-5 w-5 text-red-500" />;
            case 'retard': return <AlertTriangle className="h-5 w-5 text-orange-500" />;
            default: return <Clock className="h-5 w-5 text-gray-500" />;
        }
    };

    const getStatusColor = (statut) => {
        switch (statut) {
            case 'present': return 'bg-green-100 text-green-800';
            case 'absent': return 'bg-red-100 text-red-800';
            case 'retard': return 'bg-orange-100 text-orange-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const getStatusText = (statut) => {
        switch (statut) {
            case 'present': return 'Présent';
            case 'absent': return 'Absent';
            case 'retard': return 'Retard';
            default: return 'Non défini';
        }
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    const formatTime = (timeString) => {
        return timeString;
    };

    if (loading) {
        return (
            <AuthenticatedLayout user={auth.user}>
                <Head title="Présences" />
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
            <Head title="Gestion des présences" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Gestion des présences</h1>
                            <p className="text-gray-600 mt-2">
                                Suivi des présences et absences des étudiants
                            </p>
                        </div>
                        <div className="flex space-x-3 mt-4 sm:mt-0">
                            <button className="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center space-x-2">
                                <Upload className="h-4 w-4" />
                                <span>Importer</span>
                            </button>
                            <button className="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors flex items-center space-x-2">
                                <Plus className="h-4 w-4" />
                                <span>Marquer présences</span>
                            </button>
                        </div>
                    </div>

                    {/* Stats Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-green-100">
                                    <CheckCircle className="h-6 w-6 text-green-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Présents aujourd'hui</p>
                                    <p className="text-2xl font-bold text-gray-900">142</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-red-100">
                                    <XCircle className="h-6 w-6 text-red-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Absents aujourd'hui</p>
                                    <p className="text-2xl font-bold text-gray-900">14</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-orange-100">
                                    <AlertTriangle className="h-6 w-6 text-orange-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Retards aujourd'hui</p>
                                    <p className="text-2xl font-bold text-gray-900">8</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center">
                                <div className="p-3 rounded-lg bg-blue-100">
                                    <Calendar className="h-6 w-6 text-blue-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Cours aujourd'hui</p>
                                    <p className="text-2xl font-bold text-gray-900">24</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Filters */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Date
                                </label>
                                <input
                                    type="date"
                                    value={selectedDate.toISOString().split('T')[0]}
                                    onChange={(e) => setSelectedDate(new Date(e.target.value))}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Classe
                                </label>
                                <select
                                    value={selectedClasse}
                                    onChange={(e) => setSelectedClasse(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Toutes les classes</option>
                                    {classes.map(classe => (
                                        <option key={classe.id} value={classe.nom}>
                                            {classe.nom}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Matière
                                </label>
                                <select
                                    value={selectedMatiere}
                                    onChange={(e) => setSelectedMatiere(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Toutes les matières</option>
                                    {matieres.map(matiere => (
                                        <option key={matiere.id} value={matiere.nom}>
                                            {matiere.nom}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="flex items-end">
                                <div className="flex space-x-2">
                                    <button
                                        onClick={() => setViewMode('list')}
                                        className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                                            viewMode === 'list'
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                        }`}
                                    >
                                        Liste
                                    </button>
                                    <button
                                        onClick={() => setViewMode('calendar')}
                                        className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                                            viewMode === 'calendar'
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                        }`}
                                    >
                                        Calendrier
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Presences Table */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div className="p-6 border-b border-gray-200">
                            <h2 className="text-lg font-semibold text-gray-900">
                                Présences du {formatDate(selectedDate.toISOString())}
                            </h2>
                        </div>

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
                                            Matière
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Horaires
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Justification
                                        </th>
                                        <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {presences.map((presence) => (
                                        <tr key={presence.id} className="hover:bg-gray-50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <User className="h-5 w-5 text-blue-600" />
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {presence.etudiant.prenom} {presence.etudiant.nom}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            {presence.etudiant.email}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <GraduationCap className="h-4 w-4 text-gray-400 mr-2" />
                                                    <span className="text-sm text-gray-900">{presence.classe.nom}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <BookOpen className="h-4 w-4 text-gray-400 mr-2" />
                                                    <span className="text-sm text-gray-900">{presence.matiere.nom}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900">
                                                    <div className="flex items-center space-x-1">
                                                        <Clock className="h-3 w-3 text-gray-400" />
                                                        <span>{formatTime(presence.heure_debut)} - {formatTime(presence.heure_fin)}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center space-x-2">
                                                    {getStatusIcon(presence.statut)}
                                                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(presence.statut)}`}>
                                                        {getStatusText(presence.statut)}
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {presence.justifie ? (
                                                    <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        Justifiée
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Non justifiée
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div className="flex items-center justify-end space-x-2">
                                                    <button className="text-blue-600 hover:text-blue-900 p-1">
                                                        <Edit className="h-4 w-4" />
                                                    </button>
                                                    <button className="text-green-600 hover:text-green-900 p-1">
                                                        <CheckCircle className="h-4 w-4" />
                                                    </button>
                                                    <button className="text-orange-600 hover:text-orange-900 p-1">
                                                        <AlertTriangle className="h-4 w-4" />
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
                            Affichage de 1 à {presences.length} sur {presences.length} résultats
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
