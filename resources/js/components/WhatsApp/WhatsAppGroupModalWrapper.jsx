import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import WhatsAppGroupModal from "./WhatsAppGroupModal";

export default function WhatsAppGroupModalWrapper() {
    const [open, setOpen] = useState(false);

    useEffect(() => {
        // Check global user object metadata injected in layout
        const user = window.authUser;
        if (user.metadata) {
            if (user.metadata.whatsapp_joined) return;
            if (user.metadata.whatsapp_remind_at) {
                const remindAt = new Date(user.metadata.whatsapp_remind_at);
                if (new Date() < remindAt) return;
            }
        }

        // Delay popup slightly to not disrupt initial rendering load
        const timer = setTimeout(() => {
            setOpen(true);
        }, 1500);

        return () => clearTimeout(timer);
    }, []);

    const submitPreference = (action, duration = null) => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        fetch('/member/whatsapp-preference', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({ action, duration })
        }).catch(err => console.error("Could not save whatsapp preference:", err));
    };

    const handleRemind = (duration) => {
        submitPreference('remind', duration);
        // Modal will close automatically via component implementation wait time
    };

    const handleJoin = () => {
        submitPreference('join');
    };

    const handleClose = () => {
        setOpen(false);
        // If they just close without picking remind later, defer to a week later so it isn't overly annoying
        submitPreference('dismiss');
    };

    return (
        <WhatsAppGroupModal
            open={open}
            onClose={handleClose}
            onRemind={handleRemind}
            onJoin={handleJoin}
            whatsappLink="https://chat.whatsapp.com/KBdrA2wBg3M3K7O6rgNwOj?mode=gi_t"
        />
    );
}

if (document.getElementById("whatsappGroupModalRoot")) {
    const root = ReactDOM.createRoot(document.getElementById("whatsappGroupModalRoot"));
    root.render(
        <React.StrictMode>
            <WhatsAppGroupModalWrapper />
        </React.StrictMode>
    );
}
