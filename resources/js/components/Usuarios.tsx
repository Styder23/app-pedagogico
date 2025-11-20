import Layout from './Layout';

function Usuarios() {
    return (
        <Layout title="Usuarios">
            <div className="bg-white rounded-xl shadow-lg p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i className="fas fa-users text-blue-500 mr-3"></i>
                    Gestión de Usuarios
                </h2>
                <p className="text-gray-600">Aquí podrás gestionar todos los usuarios del sistema.</p>
            </div>
        </Layout>
    );
}

export default Usuarios;


