import { router } from '@inertiajs/react';
import StatusBadge from '../Shared/StatusBadge';
import type { Ticket } from '../../types';

interface TicketTableProps {
    tickets: Ticket[];
    loading: boolean;
    isAdmin: boolean;
}

function formatDate(iso: string) {
    return new Date(iso).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}

export default function TicketTable({ tickets, loading, isAdmin }: TicketTableProps) {
    return (
        <div className="glass rounded-2xl overflow-hidden border" style={{ borderColor: 'var(--border)' }}>
            <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                    <thead>
                        <tr style={{ background: 'rgba(0,0,0,0.02)', borderBottom: '1px solid var(--border)' }}>
                            <th className="px-6 py-4 text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>ID</th>
                            <th className="px-6 py-4 text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>Title</th>
                            {isAdmin && (
                                <th className="px-6 py-4 text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>Customer</th>
                            )}
                            <th className="px-6 py-4 text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>Status</th>
                            <th className="px-6 py-4 text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>Created</th>
                            <th className="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-right" style={{ color: 'var(--text-muted)' }}>Action</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y" style={{ divideColor: 'var(--border)' }}>
                        {loading ? (
                            <tr>
                                <td colSpan={isAdmin ? 6 : 5} className="py-20 text-center">
                                    <div className="inline-block spinner" style={{ width: 24, height: 24 }} />
                                </td>
                            </tr>
                        ) : tickets.length === 0 ? (
                            <tr>
                                <td colSpan={isAdmin ? 6 : 5} className="py-16 text-center space-y-2">
                                    <p className="text-3xl">🎫</p>
                                    <p className="font-medium" style={{ color: 'var(--text-primary)' }}>No tickets found</p>
                                </td>
                            </tr>
                        ) : (
                            tickets.map(ticket => (
                                <tr key={ticket.id} className="transition-colors hover:bg-[rgba(0,0,0,0.015)]" style={{ borderColor: 'var(--border)' }}>
                                    <td className="px-6 py-4 text-sm font-mono font-medium" style={{ color: 'var(--text-subtle)' }}>
                                        #{ticket.id}
                                    </td>
                                    <td className="px-6 py-4 text-sm font-semibold max-w-xs truncate" style={{ color: 'var(--text-primary)' }}>
                                        {ticket.title}
                                    </td>
                                    {isAdmin && (
                                        <td className="px-6 py-4 text-sm" style={{ color: 'var(--text-muted)' }}>
                                            {ticket.user?.name ?? 'Unknown'}
                                        </td>
                                    )}
                                    <td className="px-6 py-4 text-sm">
                                        <StatusBadge status={ticket.status} />
                                    </td>
                                    <td className="px-6 py-4 text-sm text-nowrap" style={{ color: 'var(--text-muted)' }}>
                                        {formatDate(ticket.created_at)}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-right">
                                        <button
                                            onClick={() => router.visit(`/tickets/${ticket.id}`)}
                                            className="btn-ghost py-1 px-3 text-xs"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
