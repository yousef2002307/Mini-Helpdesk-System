import {
    createContext,
    useCallback,
    useContext,
    useEffect,
    useState,
    type ReactNode,
} from 'react';
import { router } from '@inertiajs/react';
import api, { clearToken, getToken, setToken } from '../lib/api';
import type { AuthResponse, LoginPayload, User } from '../types';

// ─── Context shape ────────────────────────────────────────────────────────────

interface AuthContextType {
    user: User | null;
    isLoading: boolean;
    isAuthenticated: boolean;
    isAdmin: boolean;
    login: (payload: LoginPayload) => Promise<void>;
    logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | null>(null);

// ─── Provider ─────────────────────────────────────────────────────────────────

export function AuthProvider({ children }: { children: ReactNode }) {
    const [user, setUser]       = useState<User | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    /** Restore session from stored token on first load */
    useEffect(() => {
        const token = getToken();
        if (!token) { setIsLoading(false); return; }

        api.get<User>('/me')
            .then(res => setUser(res.data))
            .catch(() => clearToken())
            .finally(() => setIsLoading(false));
    }, []);

    const login = useCallback(async (payload: LoginPayload): Promise<void> => {
        const res = await api.post<AuthResponse>('/login', payload);
        setToken(res.data.token);
        setUser(res.data.user);
        router.visit('/');
    }, []);

    const logout = useCallback(async (): Promise<void> => {
        try { await api.post<null>('/logout', {}); } catch { /* ignore */ }
        clearToken();
        setUser(null);
        router.visit('/login');
    }, []);

    return (
        <AuthContext.Provider value={{
            user,
            isLoading,
            isAuthenticated: !!user,
            isAdmin: user?.role === 'admin',
            login,
            logout,
        }}>
            {children}
        </AuthContext.Provider>
    );
}

// ─── Hook ─────────────────────────────────────────────────────────────────────

export function useAuth(): AuthContextType {
    const ctx = useContext(AuthContext);
    if (!ctx) throw new Error('useAuth must be used inside <AuthProvider>');
    return ctx;
}
