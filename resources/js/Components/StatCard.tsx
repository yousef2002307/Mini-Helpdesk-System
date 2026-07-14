

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
export default StatCard;