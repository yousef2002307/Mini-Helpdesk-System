// ─── Entity Types ───────────────────────────────────────────────────────────

export type TicketStatus = 'open' | 'in_progress' | 'closed';
export type UserRole = 'admin' | 'user';

export interface User {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    created_at: string;
    updated_at: string;
}

export interface Ticket {
    id: number;
    title: string;
    description: string;
    status: TicketStatus;
    created_at: string;
    updated_at: string;
    user?: User;
    replies?: Reply[];
}

export interface Reply {
    id: number;
    body: string;
    ticket_id: number;
    user_id: number;
    created_at: string;
    updated_at: string;
    author?: User;
}

// ─── API Response Envelope ──────────────────────────────────────────────────

export interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface ApiResponse<T> {
    status: number;
    success: boolean;
    message: string;
    data: T;
    pagination?: Pagination;
}

// ─── Auth ────────────────────────────────────────────────────────────────────

export interface LoginPayload {
    email: string;
    password: string;
}

export interface AuthResponse {
    token: string;
    user: User;
}



export interface Props {
    ticketId: number;
}