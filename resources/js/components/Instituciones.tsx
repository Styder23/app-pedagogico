import Layout from './Layout';

function Instituciones() {
    return (
        <Layout title="Instituciones">
            <div className="bg-white rounded-xl shadow-lg p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i className="fas fa-building text-blue-500 mr-3"></i>
                    Gestión de Instituciones
                </h2>
                <p className="text-gray-600">Administra las instituciones educativas del sistema.</p>
            </div>
        </Layout>
    );
}

export default Instituciones;


