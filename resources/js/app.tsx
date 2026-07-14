import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { AuthProvider } from './contexts/AuthContext';
import '../css/app.css';

createInertiaApp({
    title: (title) => (title ? `${title} — Helpdesk` : 'Helpdesk'),

    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob('./Pages/**/*.tsx'),
        ),

    setup({ el, App, props }) {
        createRoot(el).render(
            <AuthProvider>
                <App {...props} />
            </AuthProvider>,
        );
    },


    progress: {
        color: '#6366f1',
    },
});
