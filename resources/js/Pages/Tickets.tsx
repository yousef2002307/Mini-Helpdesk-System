import { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
import CreateTicketModal from '../Components/Tickets/CreateTicketModal';
import PaginationBar from '../Components/Shared/PaginationBar';
import TicketTable from '../Components/Tickets/TicketTable';
import { useAuth } from '../contexts/AuthContext';
import api from '../lib/api';
import type { Pagination, Ticket, TicketStatus } from '../types';

// ─── Filters & Selectors ─────────────────────────────────────────────────────

const FILTERS: Array<{ label: string; value: TicketStatus | 'all' }> = [
    { label: 'All',         value: 'all' },
    { label: 'Open',        value: 'open' },
    { label: 'In Progress', value: 'in_progress' },
    { label: 'Closed',      value: 'closed' },
];

const PER_PAGE_OPTIONS = [5, 10, 20, 50];

// ─── Main Tickets Page ───────────────────────────────────────────────────────

export default function Tickets() {
    const { isAuthenticated, isLoading: authLoading, isAdmin } = useAuth();

    const [tickets, setTickets]       = useState<Ticket[]>([]);
    const [loading, setLoading]       = useState(true);
    const [error, setError]           = useState<string | null>(null);
    const [filter, setFilter]         = useState<TicketStatus | 'all'>('all');
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage]       = useState(10);
    const [pagination, setPagination] = useState<Pagination | null>(null);

    // Modal creation display state
    const [isCreateOpen, setIsCreateOpen] = useState(false);

    // Auth check
    useEffect(() => {
        if (!authLoading && !isAuthenticated) router.visit('/login');
    }, [authLoading, isAuthenticated]);

    // Reset pagination when parameters modify
    useEffect(() => { setCurrentPage(1); }, [filter, perPage]);

    const loadTickets = () => {
        if (!isAuthenticated) return;
        setLoading(true);
        setError(null);

        const base   = isAdmin ? '/admin/tickets' : '/user/tickets';
        const params = new URLSearchParams({ page: String(currentPage), per_page: String(perPage) });
        if (filter !== 'all') params.set('status', filter);

        api.get<Ticket[]>(`${base}?${params}`)
            .then(res => {
                setTickets(Array.isArray(res.data) ? res.data : []);
                setPagination(res.pagination ?? null);
            })
            .catch(err => setError(err instanceof Error ? err.message : 'Failed to load tickets'))
            .finally(() => setLoading(false));
    };

    // Data load
    useEffect(() => {
        loadTickets();
    }, [isAuthenticated, isAdmin, filter, currentPage, perPage]);

    if (authLoading) {
        return (
            <div className="flex h-screen items-center justify-center" style={{ background: 'var(--bg-base)' }}>
                <div className="spinner" style={{ width: 28, height: 28 }} />
            </div>
        );
    }

    return (
        <>
            <Head title="All Tickets" />
            <AppLayout>
                <div className="p-8 space-y-6 fade-up">
                    
                    {/* Header */}
                    <div className="flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <h1 className="text-2xl font-bold" style={{ color: 'var(--text-primary)' }}>
                                All Tickets
                            </h1>
                            <p className="text-sm mt-1" style={{ color: 'var(--text-muted)' }}>
                                Manage and track support requests
                            </p>
                        </div>

                        {/* Actions: Create Ticket (User only) & Per Page */}
                        <div className="flex items-center gap-4">
                            {!isAdmin && (
                                <button
                                    onClick={() => setIsCreateOpen(true)}
                                    className="btn-primary"
                                >
                                    + Create Ticket
                                </button>
                            )}

                            <div className="flex items-center gap-2">
                                <label className="text-xs font-medium" style={{ color: 'var(--text-muted)' }}>
                                    Per page
                                </label>
                                <select
                                    value={perPage}
                                    onChange={e => setPerPage(Number(e.target.value))}
                                    className="input"
                                    style={{ width: 'auto', padding: '0.4rem 0.6rem' }}
                                >
                                    {PER_PAGE_OPTIONS.map(n => (
                                        <option key={n} value={n}>{n}</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Filter Tabs */}
                    <div className="flex gap-2 flex-wrap">
                        {FILTERS.map(f => (
                            <button
                                key={f.value}
                                onClick={() => setFilter(f.value)}
                                className="px-4 py-1.5 rounded-full text-sm font-medium transition-colors"
                                style={filter === f.value
                                    ? { background: 'var(--accent)', color: '#fff' }
                                    : { background: 'var(--bg-card)', color: 'var(--text-muted)', border: '1px solid var(--border)' }
                                }
                            >
                                {f.label}
                            </button>
                        ))}
                    </div>

                    {/* Error Banner */}
                    {error && (
                        <div className="rounded-xl px-4 py-3 text-sm" style={{
                            background: 'rgba(239,68,68,0.06)',
                            border: '1px solid rgba(239,68,68,0.15)',
                            color: 'var(--danger)',
                        }}>
                            {error}
                        </div>
                    )}

                    {/* Ticket Table */}
                    <TicketTable tickets={tickets} loading={loading} isAdmin={isAdmin} />

                    {/* Pagination */}
                    {pagination && pagination.last_page > 1 && (
                        <PaginationBar pagination={pagination} onPageChange={setCurrentPage} />
                    )}
                </div>
            </AppLayout>

            {/* ── Create Ticket Modal ── */}
            <CreateTicketModal
                isOpen={isCreateOpen}
                onClose={() => setIsCreateOpen(false)}
                onSuccess={() => {
                    setIsCreateOpen(false);
                    loadTickets();
                }}
            />
        </>
    );
}
