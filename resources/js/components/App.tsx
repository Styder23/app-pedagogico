import { ReactNode } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import Login from './Login';
import Dashboard from './Dashboard';
import Usuarios from './Usuarios';
import Instituciones from './Instituciones';
import Inscripciones from './Inscripciones';
import Asistencia from './Asistencia';
import Notas from './Notas';
import Prediccion from './Prediccion';
import Alertas from './Alertas';

function App() {
    const isAuthenticated = (): boolean => {
        return localStorage.getItem('auth_token') !== null;
    };

    const ProtectedRoute = ({ children }: { children: ReactNode }) => {
        return isAuthenticated() ? <>{children}</> : <Navigate to="/login" />;
    };

    return (
        <Router>
            <Routes>
                <Route 
                    path="/login" 
                    element={isAuthenticated() ? <Navigate to="/dashboard" /> : <Login />} 
                />
                <Route 
                    path="/dashboard" 
                    element={
                        <ProtectedRoute>
                            <Dashboard />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/usuarios" 
                    element={
                        <ProtectedRoute>
                            <Usuarios />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/instituciones" 
                    element={
                        <ProtectedRoute>
                            <Instituciones />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/inscripciones" 
                    element={
                        <ProtectedRoute>
                            <Inscripciones />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/asistencia" 
                    element={
                        <ProtectedRoute>
                            <Asistencia />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/notas" 
                    element={
                        <ProtectedRoute>
                            <Notas />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/prediccion" 
                    element={
                        <ProtectedRoute>
                            <Prediccion />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/dashboard/alertas" 
                    element={
                        <ProtectedRoute>
                            <Alertas />
                        </ProtectedRoute>
                    } 
                />
                <Route 
                    path="/" 
                    element={<Navigate to={isAuthenticated() ? "/dashboard" : "/login"} />} 
                />
            </Routes>
        </Router>
    );
}

export default App;


