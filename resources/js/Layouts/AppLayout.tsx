import { type ReactNode } from 'react';
import { router } from '@inertiajs/react';
import { useAuth } from '../contexts/AuthContext';

const navItems = [
    { label: 'Dashboard', icon: '📊', path: '/' },
    { label: 'All Tickets', icon: '🎫', path: '/tickets' },
];

export default function AppLayout({ children }: { children: ReactNode }) {
    const { user, logout, isAdmin } = useAuth();
    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '/';

    return (
        <div className="flex h-screen overflow-hidden" style={{ background: 'var(--bg-base)' }}>

            {/* ── Sidebar ── */}
            <aside
                className="flex flex-col w-60 shrink-0 border-r"
                style={{ background: 'var(--bg-surface)', borderColor: 'var(--border)' }}
            >
                {/* Brand */}
                <div className="flex items-center gap-3 px-5 py-5 border-b" style={{ borderColor: 'var(--border)' }}>
                    <div
                        className="flex items-center justify-center w-9 h-9 rounded-xl text-lg"
                        style={{ background: 'linear-gradient(135deg,#6366f1,#8b5cf6)' }}
                    >
                        🎫
                    </div>
                    <div>
                        <p className="font-bold text-sm" style={{ color: 'var(--text-primary)' }}>Helpdesk</p>
                        <p className="text-xs" style={{ color: 'var(--text-subtle)' }}>
                            {isAdmin ? 'Admin Panel' : 'Support Portal'}
                        </p>
                    </div>
                </div>

                {/* Nav */}
                <nav className="flex-1 px-3 py-4 space-y-1">
                    {navItems.map(item => {
                        const isActive = currentPath === item.path;
                        return (
                            <button
                                key={item.path}
                                onClick={() => router.visit(item.path)}
                                className="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm font-medium transition-colors text-left"
                                style={isActive ? {
                                    background: 'var(--accent-glow)',
                                    color: 'var(--accent)',
                                    border: '1px solid rgba(79, 70, 229, 0.15)',
                                } : {
                                    background: 'transparent',
                                    color: 'var(--text-muted)',
                                }}
                            >
                                <span>{item.icon}</span>
                                {item.label}
                            </button>
                        );
                    })}
                </nav>


                {/* User */}
                <div className="p-3 border-t" style={{ borderColor: 'var(--border)' }}>
                    <div className="glass rounded-xl p-3 space-y-3">
                        <div className="flex items-center gap-3">
                            <div
                                className="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold shrink-0"
                                style={{ background: 'linear-gradient(135deg,#6366f1,#8b5cf6)', color: '#fff' }}
                            >
                                {user?.name?.charAt(0).toUpperCase()}
                            </div>
                            <div className="min-w-0">
                                <p className="text-sm font-medium truncate" style={{ color: 'var(--text-primary)' }}>
                                    {user?.name}
                                </p>
                                <p className="text-xs truncate" style={{ color: 'var(--text-subtle)' }}>
                                    {user?.email}
                                </p>
                            </div>
                        </div>
                        <button onClick={logout} className="btn-ghost w-full text-xs justify-center">
                            Sign out
                        </button>
                    </div>
                </div>
            </aside>

            {/* ── Main ── */}
            <main className="flex-1 overflow-y-auto">
                {children}
            </main>
        </div>
    );
}
