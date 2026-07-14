import type { TicketStatus } from '../types';

const STATUS_LABELS: Record<TicketStatus, string> = {
    open: 'Open',
    in_progress: 'In Progress',
    closed: 'Closed',
};

interface StatusBadgeProps {
    status: TicketStatus;
}

export default function StatusBadge({ status }: StatusBadgeProps) {
    const cls: Record<TicketStatus, string> = {
        open: 'badge badge-open',
        in_progress: 'badge badge-progress',
        closed: 'badge badge-closed',
    };
    return <span className={cls[status]}>{STATUS_LABELS[status]}</span>;
}
