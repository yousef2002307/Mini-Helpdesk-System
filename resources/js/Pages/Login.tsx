import { type FormEvent, useState } from 'react';
import { Head } from '@inertiajs/react';
import { useAuth } from '../contexts/AuthContext';

export default function Login() {
    const { login } = useAuth();
    const [email, setEmail]       = useState('');
    const [password, setPassword] = useState('');
    const [error, setError]       = useState<string | null>(null);
    const [loading, setLoading]   = useState(false);

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        setError(null);
        setLoading(true);
        try {
            await login({ email, password });
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Login failed');
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            <Head title="Login" />

            <div
                className="relative flex min-h-screen items-center justify-center overflow-hidden p-4"
                style={{ background: 'var(--bg-base)' }}
            >
                {/* Decorative orbs */}
                <div className="orb w-96 h-96 -top-32 -left-32" style={{ background: '#6366f1' }} />
                <div className="orb w-80 h-80 -bottom-24 -right-20" style={{ background: '#8b5cf6' }} />
                <div className="orb w-64 h-64 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" style={{ background: '#4f46e5', opacity: 0.08 }} />

                {/* Card */}
                <div className="glass fade-up relative z-10 w-full max-w-sm rounded-2xl p-8 shadow-2xl">

                    {/* Logo */}
                    <div className="mb-8 text-center">
                        <div
                            className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl text-2xl shadow-lg"
                            style={{ background: 'linear-gradient(135deg,#6366f1,#8b5cf6)' }}
                        >
                            🎫
                        </div>
                        <h1 className="text-2xl font-bold" style={{ color: 'var(--text-primary)' }}>
                            Welcome back
                        </h1>
                        <p className="mt-1 text-sm" style={{ color: 'var(--text-muted)' }}>
                            Sign in to your Helpdesk account
                        </p>
                    </div>

                    {/* Error */}
                    {error && (
                        <div
                            className="mb-4 rounded-lg px-4 py-3 text-sm"
                            style={{ background: 'rgba(248 113 113 / 0.1)', color: 'var(--danger)', border: '1px solid rgba(248 113 113 / 0.25)' }}
                        >
                            {error}
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-4">
                        {/* Email */}
                        <div className="space-y-1.5">
                            <label className="block text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>
                                Email
                            </label>
                            <input
                                className="input"
                                type="email"
                                placeholder="you@example.com"
                                value={email}
                                onChange={e => setEmail(e.target.value)}
                                required
                                autoFocus
                            />
                        </div>

                        {/* Password */}
                        <div className="space-y-1.5">
                            <label className="block text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>
                                Password
                            </label>
                            <input
                                className="input"
                                type="password"
                                placeholder="••••••••"
                                value={password}
                                onChange={e => setPassword(e.target.value)}
                                required
                            />
                        </div>

                        <button type="submit" className="btn-primary w-full mt-2" disabled={loading}>
                            {loading ? <><span className="spinner" /> Signing in…</> : 'Sign in'}
                        </button>
                    </form>

                    <p className="mt-6 text-center text-xs" style={{ color: 'var(--text-subtle)' }}>
                        Helpdesk Support Portal · Secure Login
                    </p>
                </div>
            </div>
        </>
    );
}
