
import type { Ticket } from "../../types";
import { router } from "@inertiajs/react";
import { StatusBadge, formatDate } from "../../Functions/Index";
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
export default RecentTicketRow;