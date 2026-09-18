self.addEventListener('push', function (event) {
    if (!event.data) {
        return;
    }

    const payload = event.data.json();
    const title = payload.title || 'Kingsley Khord';
    const options = {
        body: payload.body || '',
        icon: payload.icon || '/favicon-32x32.png',
        badge: payload.badge || '/favicon-32x32.png',
        data: { url: (payload.data && payload.data.url) || '/' },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
            for (const client of windowClients) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
