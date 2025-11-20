import Layout from './Layout';

function Dashboard() {
    return (
        <Layout title="Dashboard">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {/* Stats Cards */}
                <div className="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-gray-600 text-sm font-medium">Total Usuarios</p>
                            <p className="text-3xl font-bold text-gray-800 mt-2">1,234</p>
                        </div>
                        <div className="bg-gradient-to-br from-blue-500 to-blue-600 rounded-full p-4">
                            <i className="fas fa-users text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-xl shadow-lg p-6 border-l-4 border-cyan-500 hover:shadow-xl transition-shadow">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-gray-600 text-sm font-medium">Instituciones</p>
                            <p className="text-3xl font-bold text-gray-800 mt-2">45</p>
                        </div>
                        <div className="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-full p-4">
                            <i className="fas fa-building text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-400 hover:shadow-xl transition-shadow">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-gray-600 text-sm font-medium">Inscripciones</p>
                            <p className="text-3xl font-bold text-gray-800 mt-2">892</p>
                        </div>
                        <div className="bg-gradient-to-br from-blue-400 to-blue-500 rounded-full p-4">
                            <i className="fas fa-clipboard-list text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-xl shadow-lg p-6 border-l-4 border-cyan-400 hover:shadow-xl transition-shadow">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-gray-600 text-sm font-medium">Alertas IA</p>
                            <p className="text-3xl font-bold text-gray-800 mt-2">12</p>
                        </div>
                        <div className="bg-gradient-to-br from-cyan-400 to-cyan-500 rounded-full p-4">
                            <i className="fas fa-robot text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {/* Content Sections */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-white rounded-xl shadow-lg p-6">
                    <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i className="fas fa-chart-line text-blue-500 mr-3"></i>
                        Actividad Reciente
                    </h2>
                    <div className="space-y-4">
                        {[1, 2, 3, 4].map((item) => (
                            <div key={item} className="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                <div className="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full p-3">
                                    <i className="fas fa-bell text-white"></i>
                                </div>
                                <div className="flex-1">
                                    <p className="font-medium text-gray-800">Nueva actividad #{item}</p>
                                    <p className="text-sm text-gray-500">Hace {item} horas</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="bg-white rounded-xl shadow-lg p-6">
                    <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i className="fas fa-tasks text-cyan-500 mr-3"></i>
                        Tareas Pendientes
                    </h2>
                    <div className="space-y-4">
                        {[1, 2, 3].map((item) => (
                            <div key={item} className="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                <div className="flex-shrink-0">
                                    <input type="checkbox" className="w-5 h-5 text-blue-500 rounded" />
                                </div>
                                <div className="flex-1">
                                    <p className="font-medium text-gray-800">Tarea pendiente #{item}</p>
                                    <p className="text-sm text-gray-500">Prioridad media</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </Layout>
    );
}

export default Dashboard;


