import Layout from './Layout';

function Inscripciones() {
    return (
        <Layout title="Inscripciones">
            <div className="bg-white rounded-xl shadow-lg p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i className="fas fa-clipboard-list text-blue-500 mr-3"></i>
                    Gestión de Inscripciones
                </h2>
                <p className="text-gray-600">Administra las inscripciones de estudiantes.</p>
            </div>
        </Layout>
    );
}

export default Inscripciones;


