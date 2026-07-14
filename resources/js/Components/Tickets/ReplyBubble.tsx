
import type { Reply } from '../../types';
import { formatDateTime } from '../../Functions/Index';
function ReplyBubble({ reply, ownId }: { reply: Reply; ownId: number }) {
    const isOwn = reply.user_id === ownId;
    const isAdmR = reply.author?.role === 'admin';

    return (
        <div className={`flex gap-3 ${isOwn ? 'flex-row-reverse' : ''}`}>
            {/* Avatar */}
            <div
                className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                style={{
                    background: isAdmR
                        ? 'linear-gradient(135deg,#6366f1,#8b5cf6)'
                        : 'rgba(255 255 255 / 0.1)',
                    color: isAdmR ? '#fff' : 'var(--text-muted)',
                }}
            >
                {reply?.author?.name?.charAt(0).toUpperCase() ?? '?'}
            </div>

            {/* Bubble */}
            <div className={`max-w-[72%] space-y-1 ${isOwn ? 'items-end' : 'items-start'} flex flex-col`}>
                <div className="flex items-center gap-2 text-xs" style={{ color: 'var(--text-subtle)' }}>
                    <span className="font-medium" style={{ color: 'var(--text-muted)' }}>
                        {reply?.author?.name ?? 'Unknown'}
                    </span>
                    {isAdmR && (
                        <span className="badge badge-open" style={{ fontSize: '0.62rem', padding: '0.1rem 0.4rem' }}>
                            Admin
                        </span>
                    )}
                    <span>{formatDateTime(reply.created_at)}</span>
                </div>
                <div
                    className="rounded-2xl px-4 py-2.5 text-sm leading-relaxed"
                    style={isOwn
                        ? { background: 'linear-gradient(135deg,#6366f1,#8b5cf6)', color: '#fff', borderBottomRightRadius: 4 }
                        : { background: 'var(--bg-card)', border: '1px solid var(--border)', color: 'var(--text-primary)', borderBottomLeftRadius: 4 }
                    }
                >
                    {reply.body}
                </div>
            </div>
        </div>
    );
}
export default ReplyBubble;