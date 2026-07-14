import type { Pagination } from '../types';

interface PaginationBarProps {
    pagination: Pagination;
    onPageChange: (page: number) => void;
}

export default function PaginationBar({ pagination, onPageChange }: PaginationBarProps) {
    const { current_page, last_page, per_page, total } = pagination;
    const start = (current_page - 1) * per_page + 1;
    const end   = Math.min(current_page * per_page, total);

    const windowStart = Math.max(1, current_page - 2);
    const windowEnd   = Math.min(last_page, windowStart + 4);
    const pages = Array.from({ length: windowEnd - windowStart + 1 }, (_, i) => windowStart + i);

    const btnBase = 'flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium transition-colors';
    const btnActive = 'text-white';
    const btnInactive = 'transition-colors';

    return (
        <div className="flex items-center justify-between flex-wrap gap-4 pt-4">
            <p className="text-sm" style={{ color: 'var(--text-muted)' }}>
                Showing <strong style={{ color: 'var(--text-primary)' }}>{start}–{end}</strong> of{' '}
                <strong style={{ color: 'var(--text-primary)' }}>{total}</strong> tickets
            </p>

            <div className="flex items-center gap-1">
                <button
                    onClick={() => onPageChange(1)}
                    disabled={current_page === 1}
                    className={`${btnBase} btn-ghost`}
                    title="First page"
                >
                    «
                </button>
                <button
                    onClick={() => onPageChange(current_page - 1)}
                    disabled={current_page === 1}
                    className={`${btnBase} btn-ghost`}
                >
                    ‹
                </button>
                {windowStart > 1 && (
                    <span className="text-sm px-1" style={{ color: 'var(--text-subtle)' }}>…</span>
                )}
                {pages.map(p => (
                    <button
                        key={p}
                        onClick={() => onPageChange(p)}
                        className={`${btnBase} ${p === current_page ? btnActive : btnInactive}`}
                        style={p === current_page
                            ? { background: 'var(--accent)' }
                            : { color: 'var(--text-muted)' }
                        }
                    >
                        {p}
                    </button>
                ))}
                {windowEnd < last_page && (
                    <span className="text-sm px-1" style={{ color: 'var(--text-subtle)' }}>…</span>
                )}
                <button
                    onClick={() => onPageChange(current_page + 1)}
                    disabled={current_page === last_page}
                    className={`${btnBase} btn-ghost`}
                >
                    ›
                </button>
                <button
                    onClick={() => onPageChange(last_page)}
                    disabled={current_page === last_page}
                    className={`${btnBase} btn-ghost`}
                    title="Last page"
                >
                    »
                </button>
            </div>
        </div>
    );
}
