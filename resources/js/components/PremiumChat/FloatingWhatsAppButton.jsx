import React from "react";
import { MessageCircle } from "lucide-react";
import ReactDOM from "react-dom/client";

const FloatingWhatsAppButton = ({
    phone = "2347046982990", // international format, no +
    message = "Hello, I’d like to ask about",
}) => {
    const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(
        message
    )}`;

    return (
        <a
            href={whatsappUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-full bg-green-500 px-4 py-3 text-white shadow-lg hover:bg-green-600 transition active:scale-95"
            aria-label="Chat on WhatsApp"
        >
            <MessageCircle size={22} />
            <span className="hidden sm:inline font-medium">
                Chat on WhatsApp
            </span>
        </a>
    );
};

export default FloatingWhatsAppButton;

if (document.getElementById("whats-app")) {
    const Index = ReactDOM.createRoot(document.getElementById("whats-app"));
    Index.render(<FloatingWhatsAppButton />);
}
