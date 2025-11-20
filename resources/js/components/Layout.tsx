import { useState, ReactNode } from 'react';
import { useNavigate } from 'react-router-dom';
import Sidebar from './Sidebar';

interface LayoutProps {
    children: ReactNode;
    title?: string;
}

function Layout({ children, title }: LayoutProps) {
    const [sidebarOpen, setSidebarOpen] = useState<boolean>(false);
    const navigate = useNavigate();

    const handleLogout = () => {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_email');
        navigate('/login');
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100">
            <Sidebar 
                isOpen={sidebarOpen} 
                setIsOpen={setSidebarOpen}
                onLogout={handleLogout}
            />
            
            <div className={`transition-all duration-300 ${sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'}`}>
                <header className="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
                    <div className="flex items-center justify-between px-4 py-4">
                        <button
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                            className="lg:hidden text-gray-600 hover:text-blue-600 transition-colors"
                        >
                            <i className="fas fa-bars text-2xl"></i>
                        </button>
                        <h1 className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                            {title || 'Dashboard'}
                        </h1>
                        <div className="flex items-center space-x-4">
                            <div className="hidden md:flex items-center space-x-2 text-gray-600">
                                <i className="fas fa-user-circle text-xl"></i>
                                <span className="text-sm font-medium">
                                    {localStorage.getItem('user_email') || 'Usuario'}
                                </span>
                            </div>
                        </div>
                    </div>
                </header>

                <main className="p-6">
                    {children}
                </main>
            </div>
        </div>
    );
}

export default Layout;


