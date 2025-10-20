import ReactDOM from "react-dom/client";
import React, { useState, useEffect, useRef } from "react";

const PaidCall = () => {
    const calendlyRef = useRef(null);


    useEffect(() => {
        if ( window.Calendly && calendlyRef.current) {
            window.Calendly.initInlineWidget({
                url: "https://calendly.com/kingsleykhord/30min",
                parentElement: calendlyRef.current,
                prefill: {},
                utm: {},
            });
        }
    }, []);

    return (
        <>
            <div
                ref={calendlyRef}
                style={{ minWidth: "320px", height: "500px" }}
            ></div>
        </>
    );
};

export default PaidCall;

if (!document.querySelector("script[src*='calendly.com']")) {
    const script = document.createElement("script");
    script.src = "https://assets.calendly.com/assets/external/widget.js";
    script.async = true;
    document.head.appendChild(script);
}

// Mount to DOM
if (document.getElementById("paid-call")) {
    const Index = ReactDOM.createRoot(document.getElementById("paid-call"));
    Index.render(
        <React.StrictMode>
            <PaidCall />
        </React.StrictMode>
    );
}
