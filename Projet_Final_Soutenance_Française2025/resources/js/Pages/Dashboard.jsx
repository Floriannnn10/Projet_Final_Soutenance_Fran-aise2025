import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {
    Users,
    BookOpen,
    Calendar,
    Bell,
    TrendingUp,
    AlertTriangle,
    GraduationCap,
    Clock,
    CheckCircle,
    XCircle
} from 'lucide-react';

export default function Dashboard({ auth }) {
    const [stats, setStats] = useState({
        totalEtudiants: 0,
        totalClasses: 0,
        totalMatieres: 0,
        presencesAujourdhui: 0,
        absencesAujourdhui: 0,
        notificationsNonLues: 0,
        etudiantsDroppes: 0
    });

    const [recentActivity, setRecentActivity] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        // Simuler le chargement des données
        setTimeout(() => {
            setStats({
                totalEtudiants: 156,
                totalClasses: 7,
                totalMatieres: 8,
                presencesAujourdhui: 142,
                absencesAujourdhui: 14,
                notificationsNonLues: 5,
                etudiantsDroppes: 3
            });
            setRecentActivity([
                { id: 1, type: 'presence', message: 'Lucas Dubois présent en Mathématiques', time: '2 min ago' },
                { id: 2, type: 'absence', message: 'Marie Martin absente en Anglais', time: '5 min ago' },
                { id: 3, type: 'notification', message: 'Nouvelle notification pour la classe BTS SIO', time: '10 min ago' },
                { id: 4, type: 'alert', message: 'Alerte : 3 étudiants en risque de décrochage', time: '15 min ago' }
            ]);
            setLoading(false);
        }, 1000);
    }, []);

    const StatCard = ({ title, value, icon: Icon, color, trend }) => (
        <div className={`bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow`}>
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm font-medium text-gray-600">{title}</p>
                    <p className="text-2xl font-bold text-gray-900 mt-1">{value}</p>
                    {trend && (
                        <p className={`text-sm mt-1 ${trend > 0 ? 'text-green-600' : 'text-red-600'}`}>
                            {trend > 0 ? '+' : ''}{trend}% vs hier
                        </p>
                    )}
                </div>
                <div className={`p-3 rounded-lg ${color}`}>
                    <Icon className="h-6 w-6 text-white" />
                </div>
            </div>
        </div>
    );

    const ActivityItem = ({ activity }) => {
        const getIcon = (type) => {
            switch (type) {
                case 'presence': return <CheckCircle className="h-4 w-4 text-green-500" />;
                case 'absence': return <XCircle className="h-4 w-4 text-red-500" />;
                case 'notification': return <Bell className="h-4 w-4 text-blue-500" />;
                case 'alert': return <AlertTriangle className="h-4 w-4 text-orange-500" />;
                default: return <Clock className="h-4 w-4 text-gray-500" />;
            }
        };

        return (
            <div className="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                {getIcon(activity.type)}
                <div className="flex-1">
                    <p className="text-sm text-gray-900">{activity.message}</p>
                    <p className="text-xs text-gray-500">{activity.time}</p>
                </div>
            </div>
        );
    };

    if (loading) {
        return (
            <AuthenticatedLayout user={auth.user}>
                <Head title="Dashboard" />
                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="animate-pulse">
                            <div className="h-8 bg-gray-200 rounded w-1/4 mb-6"></div>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                                {[...Array(4)].map((_, i) => (
                                    <div key={i} className="h-32 bg-gray-200 rounded-xl"></div>
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
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">Tableau de bord</h1>
                        <p className="text-gray-600 mt-2">Bienvenue sur votre espace de gestion IFRAN</p>
                    </div>

                    {/* Stats Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <StatCard
                            title="Étudiants"
                            value={stats.totalEtudiants}
                            icon={Users}
                            color="bg-blue-500"
                            trend={2.5}
                        />
                        <StatCard
                            title="Classes"
                            value={stats.totalClasses}
                            icon={GraduationCap}
                            color="bg-green-500"
                        />
                        <StatCard
                            title="Matières"
                            value={stats.totalMatieres}
                            icon={BookOpen}
                            color="bg-purple-500"
                        />
                        <StatCard
                            title="Présences aujourd'hui"
                            value={stats.presencesAujourdhui}
                            icon={CheckCircle}
                            color="bg-emerald-500"
                            trend={5.2}
                        />
                    </div>

                    {/* Alert Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div className="bg-orange-50 border border-orange-200 rounded-xl p-6">
                            <div className="flex items-center space-x-3">
                                <AlertTriangle className="h-6 w-6 text-orange-500" />
                                <div>
                                    <h3 className="font-semibold text-orange-900">Étudiants en risque</h3>
                                    <p className="text-2xl font-bold text-orange-900">{stats.etudiantsDroppes}</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-red-50 border border-red-200 rounded-xl p-6">
                            <div className="flex items-center space-x-3">
                                <XCircle className="h-6 w-6 text-red-500" />
                                <div>
                                    <h3 className="font-semibold text-red-900">Absences aujourd'hui</h3>
                                    <p className="text-2xl font-bold text-red-900">{stats.absencesAujourdhui}</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-blue-50 border border-blue-200 rounded-xl p-6">
                            <div className="flex items-center space-x-3">
                                <Bell className="h-6 w-6 text-blue-500" />
                                <div>
                                    <h3 className="font-semibold text-blue-900">Notifications non lues</h3>
                                    <p className="text-2xl font-bold text-blue-900">{stats.notificationsNonLues}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Main Content Grid */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Recent Activity */}
                        <div className="lg:col-span-2">
                            <div className="bg-white rounded-xl shadow-sm border border-gray-200">
                                <div className="p-6 border-b border-gray-200">
                                    <h2 className="text-lg font-semibold text-gray-900">Activité récente</h2>
                                </div>
                                <div className="p-6">
                                    <div className="space-y-4">
                                        {recentActivity.map((activity) => (
                                            <ActivityItem key={activity.id} activity={activity} />
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Quick Actions */}
                        <div className="space-y-6">
                            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                                <div className="space-y-3">
                                    <button className="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors flex items-center justify-center space-x-2">
                                        <Users className="h-4 w-4" />
                                        <span>Gérer les étudiants</span>
                                    </button>
                                    <button className="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors flex items-center justify-center space-x-2">
                                        <Calendar className="h-4 w-4" />
                                        <span>Marquer les présences</span>
                                    </button>
                                    <button className="w-full bg-purple-500 text-white py-2 px-4 rounded-lg hover:bg-purple-600 transition-colors flex items-center justify-center space-x-2">
                                        <Bell className="h-4 w-4" />
                                        <span>Envoyer notification</span>
                                    </button>
                                    <button className="w-full bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors flex items-center justify-center space-x-2">
                                        <AlertTriangle className="h-4 w-4" />
                                        <span>Vérifier alertes</span>
                                    </button>
                                </div>
                            </div>

                            {/* Quick Stats */}
                            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">Statistiques rapides</h3>
                                <div className="space-y-4">
                                    <div className="flex justify-between items-center">
                                        <span className="text-sm text-gray-600">Taux de présence</span>
                                        <span className="text-sm font-semibold text-green-600">91.2%</span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-2">
                                        <div className="bg-green-500 h-2 rounded-full" style={{ width: '91.2%' }}></div>
                                    </div>

                                    <div className="flex justify-between items-center">
                                        <span className="text-sm text-gray-600">Classes actives</span>
                                        <span className="text-sm font-semibold text-blue-600">7/7</span>
                                    </div>

                                    <div className="flex justify-between items-center">
                                        <span className="text-sm text-gray-600">Cours aujourd'hui</span>
                                        <span className="text-sm font-semibold text-purple-600">24</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
