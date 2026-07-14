import type { TicketStatus } from '../types';

export function formatDateTime(iso: string) {
    return new Date(iso).toLocaleString('en-US', {
        month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}

export function StatusBadge({ status }: { status: TicketStatus }) {
    const cls: Record<TicketStatus, string> = {
        open: 'badge badge-open',
        in_progress: 'badge badge-progress',
        closed: 'badge badge-closed',
    };
    const STATUS_LABELS: Record<TicketStatus, string> = {
        open: 'Open',
        in_progress: 'In Progress',
        closed: 'Closed',
    };
    return <span className={cls[status]}>{STATUS_LABELS[status]}</span>;
}

export function formatDate(iso: string) {
    return new Date(iso).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
    });
}