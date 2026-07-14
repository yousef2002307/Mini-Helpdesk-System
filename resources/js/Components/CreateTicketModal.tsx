import { useState, type FormEvent } from 'react';
import api from '../lib/api';

interface CreateTicketModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
}

export default function CreateTicketModal({ isOpen, onClose, onSuccess }: CreateTicketModalProps) {
    const [title, setTitle]             = useState('');
    const [description, setDescription] = useState('');
    const [error, setError]             = useState<string | null>(null);
    const [submitting, setSubmitting]   = useState(false);

    if (!isOpen) return null;

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        if (!title.trim() || !description.trim()) return;

        setSubmitting(true);
        setError(null);

        try {
            await api.post('/user/tickets', {
                title: title.trim(),
                description: description.trim(),
            });
            // Clear inputs, close modal, and notify parent
            setTitle('');
            setDescription('');
            onSuccess();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to create ticket');
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/10">
            <div className="glass w-full max-w-md rounded-2xl p-6 shadow-2xl fade-up space-y-4">
                <div className="flex items-center justify-between">
                    <h3 className="text-lg font-bold" style={{ color: 'var(--text-primary)' }}>
                        Create New Support Ticket
                    </h3>
                    <button
                        onClick={onClose}
                        className="text-xl hover:opacity-75 transition-opacity"
                        style={{ color: 'var(--text-muted)' }}
                    >
                        &times;
                    </button>
                </div>

                {error && (
                    <div className="rounded-lg px-4 py-2.5 text-sm" style={{
                        background: 'rgba(239,68,68,0.06)',
                        border: '1px solid rgba(239,68,68,0.15)',
                        color: 'var(--danger)',
                    }}>
                        {error}
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="space-y-1.5">
                        <label className="block text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>
                            Title
                        </label>
                        <input
                            className="input"
                            type="text"
                            placeholder="Brief summary of the issue"
                            value={title}
                            onChange={e => setTitle(e.target.value)}
                            required
                            autoFocus
                        />
                    </div>

                    <div className="space-y-1.5">
                        <label className="block text-xs font-semibold uppercase tracking-wider" style={{ color: 'var(--text-muted)' }}>
                            Description
                        </label>
                        <textarea
                            className="input resize-none"
                            rows={4}
                            placeholder="Provide detailed information regarding this request..."
                            value={description}
                            onChange={e => setDescription(e.target.value)}
                            required
                        />
                    </div>

                    <div className="flex justify-end gap-3 pt-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="btn-ghost"
                            disabled={submitting}
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            className="btn-primary"
                            disabled={submitting || !title.trim() || !description.trim()}
                        >
                            {submitting ? <span className="spinner" /> : 'Create Ticket'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
