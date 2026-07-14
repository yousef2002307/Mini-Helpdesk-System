import { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
import { useAuth } from '../contexts/AuthContext';
import api from '../lib/api';
import type { Ticket, TicketStatus } from '../types';

// ─── Helpers ─────────────────────────────────────────────────────────────────

const STATUS_LABELS: Record<TicketStatus, string> = {
    open: 'Open',
    in_progress: 'In Progress',
    closed: 'Closed',
};

function StatusBadge({ status }: { status: TicketStatus }) {
    const cls: Record<TicketStatus, string> = {
        open: 'badge badge-open',
        in_progress: 'badge badge-progress',
        closed: 'badge badge-closed',
    };
    return <span className={cls[status]}>{STATUS_LABELS[status]}</span>;
}

function formatDate(iso: string) {
    return new Date(iso).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
    });
}

// ─── Stat Card ────────────────────────────────────────────────────────────────

function StatCard({ label, count, color }: { label: string; count: number; color: string }) {
    return (
        <div className="glass rounded-xl px-5 py-5 flex flex-col gap-1 flex-1">
            <span className="text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-subtle)' }}>
                {label}
            </span>
            <span className="text-3xl font-bold" style={{ color }}>
                {count}
            </span>
        </div>
    );
}

// ─── Recent Ticket Card ───────────────────────────────────────────────────────

function RecentTicketRow({ ticket }: { ticket: Ticket }) {
    return (
        <div
            className="glass rounded-xl p-5 flex items-center justify-between gap-4 border"
            style={{ borderColor: 'var(--border)' }}
        >
            <div className="min-w-0 flex-1 space-y-1">
                <div className="flex items-center gap-3">
                    <span className="text-xs font-mono" style={{ color: 'var(--text-subtle)' }}>#{ticket.id}</span>
                    <h3 className="font-semibold text-sm truncate" style={{ color: 'var(--text-primary)' }}>
                        {ticket.title}
                    </h3>
                </div>
                <p className="text-sm line-clamp-1" style={{ color: 'var(--text-muted)' }}>
                    {ticket.description}
                </p>
                {ticket.user && (
                    <p className="text-xs" style={{ color: 'var(--text-subtle)' }}>
                        Submitted by <strong>{ticket.user.name}</strong>
                    </p>
                )}
            </div>

            <div className="flex items-center gap-4 shrink-0">
                <StatusBadge status={ticket.status} />
                <span className="text-xs" style={{ color: 'var(--text-subtle)' }}>{formatDate(ticket.created_at)}</span>
                <button
                    onClick={() => router.visit(`/tickets/${ticket.id}`)}
                    className="btn-ghost py-1 px-3 text-xs"
                >
                    View
                </button>
            </div>
        </div>
    );
}

// ─── Page ─────────────────────────────────────────────────────────────────────

export default function Dashboard() {
    const { isAuthenticated, isLoading: authLoading, isAdmin, user } = useAuth();

    const [recentTickets, setRecentTickets] = useState<Ticket[]>([]);
    const [stats, setStats]                 = useState({ open: 0, in_progress: 0, closed: 0 });
    const [loading, setLoading]             = useState(true);
    const [error, setError]                 = useState<string | null>(null);

    // Auth check
    useEffect(() => {
        if (!authLoading && !isAuthenticated) router.visit('/login');
    }, [authLoading, isAuthenticated]);

    // Fetch stats + recent 3 tickets in parallel
    useEffect(() => {
        if (!isAuthenticated) return;
        setLoading(true);
        setError(null);

        const base = isAdmin ? '/admin/tickets' : '/user/tickets';

        Promise.all([
            api.get<Ticket[]>(`${base}?status=open&per_page=1`),
            api.get<Ticket[]>(`${base}?status=in_progress&per_page=1`),
            api.get<Ticket[]>(`${base}?status=closed&per_page=1`),
            api.get<Ticket[]>(`${base}?per_page=3`),
        ])
            .then(([openRes, progressRes, closedRes, recentRes]) => {
                setStats({
                    open: openRes.pagination?.total ?? 0,
                    in_progress: progressRes.pagination?.total ?? 0,
                    closed: closedRes.pagination?.total ?? 0,
                });
                setRecentTickets(Array.isArray(recentRes.data) ? recentRes.data : []);
            })
            .catch(err => {
                setError(err instanceof Error ? err.message : 'Failed to fetch dashboard data');
            })
            .finally(() => {
                setLoading(false);
            });
    }, [isAuthenticated, isAdmin]);

    if (authLoading) {
        return (
            <div className="flex h-screen items-center justify-center" style={{ background: 'var(--bg-base)' }}>
                <div className="spinner" style={{ width: 28, height: 28 }} />
            </div>
        );
    }

    return (
        <>
            <Head title="Dashboard" />
            <AppLayout>
                <div className="p-8 space-y-8 fade-up">

                    {/* Welcome Header */}
                    <div>
                        <h1 className="text-2xl font-bold" style={{ color: 'var(--text-primary)' }}>
                            Welcome back, {user?.name} 👋
                        </h1>
                        <p className="text-sm mt-1" style={{ color: 'var(--text-muted)' }}>
                            Here is a quick overview of support tickets
                        </p>
                    </div>

                    {/* Stats Section */}
                    {loading ? (
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 animate-pulse">
                            <div className="glass h-24 rounded-xl" />
                            <div className="glass h-24 rounded-xl" />
                            <div className="glass h-24 rounded-xl" />
                        </div>
                    ) : (
                        <div className="flex flex-col md:flex-row gap-4">
                            <StatCard label="Open" count={stats.open} color="var(--accent)" />
                            <StatCard label="In Progress" count={stats.in_progress} color="var(--warning)" />
                            <StatCard label="Closed" count={stats.closed} color="var(--text-subtle)" />
                        </div>
                    )}

                    {/* Recent Tickets Section */}
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <h2 className="text-lg font-bold" style={{ color: 'var(--text-primary)' }}>
                                Recent Tickets
                            </h2>
                            <button
                                onClick={() => router.visit('/tickets')}
                                className="btn-ghost text-xs"
                            >
                                View All Tickets →
                            </button>
                        </div>

                        {error && (
                            <div className="rounded-xl px-4 py-3 text-sm" style={{
                                background: 'rgba(239,68,68,0.06)',
                                border: '1px solid rgba(239,68,68,0.15)',
                                color: 'var(--danger)',
                            }}>
                                {error}
                            </div>
                        )}

                        {loading ? (
                            <div className="space-y-3">
                                <div className="glass h-16 rounded-xl animate-pulse" />
                                <div className="glass h-16 rounded-xl animate-pulse" />
                                <div className="glass h-16 rounded-xl animate-pulse" />
                            </div>
                        ) : recentTickets.length === 0 ? (
                            <div className="glass rounded-xl p-8 text-center space-y-2">
                                <p className="text-3xl">🎫</p>
                                <p className="font-semibold text-sm" style={{ color: 'var(--text-primary)' }}>
                                    No tickets found
                                </p>
                                <p className="text-xs" style={{ color: 'var(--text-subtle)' }}>
                                    Your support dashboard is empty.
                                </p>
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {recentTickets.map(ticket => (
                                    <RecentTicketRow key={ticket.id} ticket={ticket} />
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </AppLayout>
        </>
    );
}
