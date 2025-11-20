import Layout from './Layout';

function Asistencia() {
    return (
        <Layout title="Asistencia">
            <div className="bg-white rounded-xl shadow-lg p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i className="fas fa-calendar-check text-blue-500 mr-3"></i>
                    Control de Asistencia
                </h2>
                <p className="text-gray-600">Registra y consulta la asistencia de los estudiantes.</p>
            </div>
        </Layout>
    );
}

export default Asistencia;


