import { type FormEvent, useEffect, useRef, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
import { useAuth } from '../contexts/AuthContext';
import api from '../lib/api';
import type { Reply, Ticket, TicketStatus } from '../types';
import ReplyBubble from '../Components/Tickets/ReplyBubble';
import { StatusBadge } from '../Functions/Index';
import { Props } from '../types/index';


// ─── Helpers ─────────────────────────────────────────────────────────────────

const STATUS_LABELS: Record<TicketStatus, string> = {
    open: 'Open',
    in_progress: 'In Progress',
    closed: 'Closed',
};




// ─── Page Props ───────────────────────────────────────────────────────────────



// ─── Page ─────────────────────────────────────────────────────────────────────

export default function TicketDetail({ ticketId }: Props) {
    const { isAuthenticated, isLoading: authLoading, isAdmin, user } = useAuth();

    const [ticket, setTicket] = useState<Ticket | null>(null);
    const [replies, setReplies] = useState<Reply[]>([]);
    const [loading, setLoading] = useState(true);
    const [replyBody, setReplyBody] = useState('');
    const [sending, setSending] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [replyErr, setReplyErr] = useState<string | null>(null);

    const threadRef = useRef<HTMLDivElement>(null);

    // Auth guard
    useEffect(() => {
        if (!authLoading && !isAuthenticated) router.visit('/login');
    }, [authLoading, isAuthenticated]);

    // Load ticket + replies
    const loadTicket = () => {
        if (!isAuthenticated) return;
        const base = isAdmin ? `/admin/tickets/${ticketId}` : `/user/tickets/${ticketId}`;
        setLoading(true);
        setError(null);

        api.get<Ticket>(base)
            .then(res => {
                setTicket(res.data);
                setReplies(res.data.replies ?? []);
                console.log("Ticket data : ", res.data);
                console.log("Replies data : ", res.data.replies);
            })
            .catch(err => setError(err instanceof Error ? err.message : 'Failed to load ticket'))
            .finally(() => setLoading(false));
    };

    useEffect(() => { loadTicket(); }, [isAuthenticated, isAdmin, ticketId]);

    // Scroll to bottom on new replies
    useEffect(() => {
        if (threadRef.current) {
            threadRef.current.scrollTop = threadRef.current.scrollHeight;
        }
    }, [replies]);

    // Submit reply
    const handleReply = async (e: FormEvent) => {
        e.preventDefault();
        if (!replyBody.trim()) return;
        setReplyErr(null);
        setSending(true);
        try {
            const base = isAdmin
                ? `/admin/tickets/${ticketId}/replies`
                : `/user/tickets/${ticketId}/replies`;
            await api.post(base, { body: replyBody.trim() });
            setReplyBody('');
            loadTicket();
        } catch (err) {
            setReplyErr(err instanceof Error ? err.message : 'Failed to send reply');
        } finally {
            setSending(false);
        }
    };

    if (authLoading || loading) {
        return (
            <div className="flex h-screen items-center justify-center" style={{ background: 'var(--bg-base)' }}>
                <div className="spinner" style={{ width: 28, height: 28 }} />
            </div>
        );
    }

    if (error || !ticket) {
        return (
            <AppLayout>
                <div className="flex h-full items-center justify-center">
                    <p style={{ color: 'var(--danger)' }}>{error ?? 'Ticket not found'}</p>
                </div>
            </AppLayout>
        );
    }

    return (
        <>
            <Head title={ticket.title} />
            <AppLayout>
                <div className="flex h-full flex-col fade-up">

                    {/* ── Header ── */}
                    <div
                        className="shrink-0 border-b px-8 py-5 space-y-3"
                        style={{ borderColor: 'var(--border)', background: 'var(--bg-surface)' }}
                    >
                        <button
                            onClick={() => router.visit('/')}
                            className="btn-ghost text-xs"
                        >
                            ← Back to Dashboard
                        </button>

                        <div className="flex items-start justify-between gap-4">
                            <div>
                                <p className="text-xs mb-1" style={{ color: 'var(--text-subtle)' }}>
                                    Ticket #{ticket.id}
                                </p>
                                <h1 className="text-xl font-bold leading-snug" style={{ color: 'var(--text-primary)' }}>
                                    {ticket.title}
                                </h1>
                            </div>
                            <StatusBadge status={ticket.status} />
                        </div>

                        {/* Description */}
                        <div
                            className="rounded-xl p-4 text-sm leading-relaxed"
                            style={{
                                background: 'var(--bg-card)',
                                border: '1px solid var(--border)',
                                color: 'var(--text-muted)',
                            }}
                        >
                            {ticket.description}
                        </div>
                    </div>

                    {/* ── Thread ── */}
                    <div
                        ref={threadRef}
                        className="flex-1 overflow-y-auto px-8 py-6 space-y-5"
                    >
                        {replies.length === 0 ? (
                            <div className="flex flex-col items-center justify-center h-full space-y-2 py-12">
                                <span className="text-4xl">💬</span>
                                <p className="text-sm" style={{ color: 'var(--text-subtle)' }}>
                                    No replies yet. Start the conversation!
                                </p>
                            </div>
                        ) : (
                            replies.map(reply => (
                                <ReplyBubble key={reply.id} reply={reply} ownId={user!.id} />
                            ))
                        )}
                    </div>

                    {/* ── Reply Box ── */}
                    {ticket.status !== 'closed' ? (
                        <form
                            onSubmit={handleReply}
                            className="shrink-0 border-t px-8 py-5 space-y-2"
                            style={{ borderColor: 'var(--border)', background: 'var(--bg-surface)' }}
                        >
                            {replyErr && (
                                <p className="text-xs" style={{ color: 'var(--danger)' }}>{replyErr}</p>
                            )}
                            <div className="flex gap-3 items-end">
                                <textarea
                                    className="input flex-1 resize-none"
                                    rows={3}
                                    placeholder="Write a reply…"
                                    value={replyBody}
                                    onChange={e => setReplyBody(e.target.value)}
                                    onKeyDown={e => {
                                        if (e.key === 'Enter' && (e.metaKey || e.ctrlKey)) {
                                            e.preventDefault();
                                            handleReply(e as unknown as FormEvent);
                                        }
                                    }}
                                />
                                <button
                                    type="submit"
                                    className="btn-primary shrink-0"
                                    disabled={sending || !replyBody.trim()}
                                >
                                    {sending ? <span className="spinner" /> : '↑ Send'}
                                </button>
                            </div>
                            <p className="text-xs" style={{ color: 'var(--text-subtle)' }}>
                                ⌘↵ / Ctrl↵ to send
                            </p>
                        </form>
                    ) : (
                        <div
                            className="shrink-0 border-t px-8 py-4 text-center text-sm"
                            style={{ borderColor: 'var(--border)', color: 'var(--text-subtle)' }}
                        >
                            This ticket is closed. No further replies can be added.
                        </div>
                    )}
                </div>
            </AppLayout>
        </>
    );
}
