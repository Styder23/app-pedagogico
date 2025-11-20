import { useState, useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';

interface SidebarProps {
    isOpen: boolean;
    setIsOpen: (open: boolean) => void;
    onLogout: () => void;
}

interface MenuItem {
    id: string;
    label: string;
    icon: string;
    path?: string;
    submenu?: SubMenuItem[];
}

interface SubMenuItem {
    id: string;
    label: string;
    icon: string;
    path: string;
}

function Sidebar({ isOpen, setIsOpen, onLogout }: SidebarProps) {
    const navigate = useNavigate();
    const location = useLocation();
    const [openSubmenu, setOpenSubmenu] = useState<string | null>(null);

    const menuItems: MenuItem[] = [
        {
            id: 'dashboard',
            label: 'Dashboard',
            icon: 'fa-home',
            path: '/dashboard',
        },
        {
            id: 'usuarios',
            label: 'Usuarios',
            icon: 'fa-users',
            path: '/dashboard/usuarios',
        },
        {
            id: 'instituciones',
            label: 'Instituciones',
            icon: 'fa-building',
            path: '/dashboard/instituciones',
        },
        {
            id: 'datos-pedagogicos',
            label: 'Datos Pedagógicos',
            icon: 'fa-book',
            submenu: [
                { id: 'inscripciones', label: 'Inscripciones', icon: 'fa-clipboard-list', path: '/dashboard/inscripciones' },
                { id: 'asistencia', label: 'Asistencia', icon: 'fa-calendar-check', path: '/dashboard/asistencia' },
                { id: 'notas', label: 'Notas', icon: 'fa-star', path: '/dashboard/notas' },
            ],
        },
        {
            id: 'inteligencia-artificial',
            label: 'Inteligencia Artificial',
            icon: 'fa-robot',
            submenu: [
                { id: 'prediccion', label: 'Predicción del rendimiento', icon: 'fa-chart-line', path: '/dashboard/prediccion' },
                { id: 'alertas', label: 'Alertas automáticas', icon: 'fa-bell', path: '/dashboard/alertas' },
            ],
        },
    ];

    // Auto-open submenu if any of its items is active
    useEffect(() => {
        menuItems.forEach((item) => {
            if (item.submenu) {
                const hasActiveSubmenu = item.submenu.some((subItem) => location.pathname === subItem.path);
                if (hasActiveSubmenu) {
                    setOpenSubmenu(item.id);
                }
            }
        });
    }, [location.pathname]);

    const handleMenuClick = (item: MenuItem) => {
        if (item.submenu) {
            setOpenSubmenu(openSubmenu === item.id ? null : item.id);
        } else if (item.path) {
            navigate(item.path);
            setIsOpen(false);
        }
    };

    const handleSubmenuClick = (path: string) => {
        navigate(path);
        setIsOpen(false);
    };

    const isActive = (path?: string): boolean => {
        if (!path) return false;
        return location.pathname === path;
    };

    const hasActiveSubmenu = (item: MenuItem): boolean => {
        if (!item.submenu) return false;
        return item.submenu.some((subItem) => location.pathname === subItem.path);
    };

    return (
        <>
            {/* Overlay para móvil */}
            {isOpen && (
                <div
                    className="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
                    onClick={() => setIsOpen(false)}
                ></div>
            )}

            {/* Sidebar */}
            <aside
                className={`fixed top-0 left-0 h-full bg-gradient-to-b from-blue-600 via-blue-500 to-cyan-500 text-white z-50 transition-all duration-300 ease-in-out ${
                    isOpen ? 'w-64' : 'w-20'
                } shadow-2xl`}
            >
                {/* Logo/Header */}
                <div className="flex items-center justify-between p-4 border-b border-blue-400 border-opacity-30">
                    {isOpen && (
                        <div className="flex items-center space-x-2">
                            <div className="bg-white rounded-lg p-2">
                                <i className="fas fa-graduation-cap text-blue-600 text-xl"></i>
                            </div>
                            <span className="font-bold text-lg">App IA</span>
                        </div>
                    )}
                    {!isOpen && (
                        <div className="w-full flex justify-center">
                            <div className="bg-white rounded-lg p-2">
                                <i className="fas fa-graduation-cap text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    )}
                    <button
                        onClick={() => setIsOpen(false)}
                        className={`lg:hidden text-white hover:text-cyan-200 transition-colors ${!isOpen && 'hidden'}`}
                    >
                        <i className="fas fa-times text-xl"></i>
                    </button>
                </div>

                {/* Menu Items */}
                <nav className="mt-4 px-2">
                    {menuItems.map((item) => (
                        <div key={item.id}>
                            <button
                                onClick={() => handleMenuClick(item)}
                                className={`w-full flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 transition-all hover:bg-blue-700 hover:bg-opacity-50 ${
                                    isActive(item.path) || hasActiveSubmenu(item) ? 'bg-blue-700 bg-opacity-70 shadow-lg' : ''
                                } ${!isOpen ? 'justify-center' : ''}`}
                            >
                                <i className={`fas ${item.icon} text-xl ${!isOpen ? 'mx-auto' : ''}`}></i>
                                {isOpen && (
                                    <>
                                        <span className="flex-1 text-left font-medium">{item.label}</span>
                                        {item.submenu && (
                                            <i
                                                className={`fas fa-chevron-${openSubmenu === item.id ? 'down' : 'right'} text-sm transition-transform`}
                                            ></i>
                                        )}
                                    </>
                                )}
                            </button>

                            {/* Submenu */}
                            {item.submenu && isOpen && (openSubmenu === item.id || hasActiveSubmenu(item)) && (
                                <div className="ml-4 mt-2 space-y-1 border-l-2 border-blue-400 border-opacity-30 pl-4">
                                    {item.submenu.map((subItem) => (
                                        <button
                                            key={subItem.id}
                                            onClick={() => handleSubmenuClick(subItem.path)}
                                            className={`w-full flex items-center space-x-3 px-4 py-2 rounded-lg transition-all hover:bg-blue-700 hover:bg-opacity-50 ${
                                                isActive(subItem.path) ? 'bg-blue-700 bg-opacity-70' : ''
                                            }`}
                                        >
                                            <i className={`fas ${subItem.icon} text-sm`}></i>
                                            <span className="text-sm font-medium">{subItem.label}</span>
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                    ))}
                </nav>

                {/* Logout Button */}
                <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-400 border-opacity-30">
                    <button
                        onClick={onLogout}
                        className={`w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition-all ${
                            !isOpen ? 'justify-center' : ''
                        }`}
                    >
                        <i className="fas fa-sign-out-alt text-xl"></i>
                        {isOpen && <span className="font-medium">Cerrar Sesión</span>}
                    </button>
                </div>
            </aside>

            {/* Toggle Button para desktop (cuando está cerrado) */}
            {!isOpen && (
                <button
                    onClick={() => setIsOpen(true)}
                    className="hidden lg:fixed top-4 left-4 z-30 bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-3 rounded-lg shadow-lg hover:shadow-xl transition-all"
                >
                    <i className="fas fa-bars text-xl"></i>
                </button>
            )}
        </>
    );
}

export default Sidebar;


