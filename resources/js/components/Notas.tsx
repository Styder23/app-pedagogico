import Layout from './Layout';

function Notas() {
    return (
        <Layout title="Notas">
            <div className="bg-white rounded-xl shadow-lg p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i className="fas fa-star text-blue-500 mr-3"></i>
                    Gestión de Notas
                </h2>
                <p className="text-gray-600">Administra las calificaciones de los estudiantes.</p>
            </div>
        </Layout>
    );
}

export default Notas;


