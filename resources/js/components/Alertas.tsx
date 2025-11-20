import Layout from './Layout';

function Alertas() {
    return (
        <Layout title="Alertas Automáticas">
            <div className="bg-white rounded-xl shadow-lg p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i className="fas fa-bell text-blue-500 mr-3"></i>
                    Alertas Automáticas
                </h2>
                <p className="text-gray-600">Sistema de alertas inteligentes basado en IA.</p>
            </div>
        </Layout>
    );
}

export default Alertas;


