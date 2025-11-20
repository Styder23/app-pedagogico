import Layout from './Layout';

function Prediccion() {
    return (
        <Layout title="Predicción del Rendimiento">
            <div className="bg-white rounded-xl shadow-lg p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i className="fas fa-chart-line text-blue-500 mr-3"></i>
                    Predicción del Rendimiento
                </h2>
                <p className="text-gray-600">Utiliza inteligencia artificial para predecir el rendimiento académico.</p>
            </div>
        </Layout>
    );
}

export default Prediccion;


