import { useState } from "react";
import { Clock, X } from "lucide-react";
import {
  Dialog,
  DialogContent,
  DialogHeader,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Collapsible, CollapsibleContent } from "@/components/ui/collapsible";

const DURATIONS = [
  { label: "1 hour", value: "1h" },
  { label: "3 hours", value: "3h" },
  { label: "6 hours", value: "6h" },
  { label: "Tomorrow", value: "tomorrow" },
  { label: "In 3 days", value: "3d" },
  { label: "Next week", value: "1w" },
];

const FEATURES = [
  "Exclusive Piano breakdowns",
  "Live lesson schedules",
  "Guided practice sessions",
  "Live chat and support",
  "And many more...",
];

const WhatsAppIcon = ({ size = 20, color = "#fff" }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill={color}>
    <path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.57 1.38 5.04L2 22l5.1-1.35A9.96 9.96 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm4.93 13.53c-.2.55-1.17 1.05-1.6 1.1-.4.05-.77.18-2.62-.55-2.2-.88-3.6-3.1-3.71-3.24-.11-.15-.88-1.17-.88-2.23s.55-1.58.77-1.8c.2-.2.44-.25.59-.25h.44c.14 0 .33-.05.52.4.2.46.66 1.6.72 1.72.06.11.1.24.02.38-.08.14-.12.23-.24.36-.11.13-.24.3-.34.4-.11.11-.23.24-.1.47.13.23.57.93 1.22 1.5.84.75 1.54.98 1.77 1.09.22.1.36.08.49-.05.13-.13.55-.64.7-.86.14-.22.28-.18.47-.11l1.49.7c.22.1.36.15.41.24.06.1.06.55-.14 1.1z" />
  </svg>
);

export default function WhatsAppGroupModal({
  open,
  onClose,
  whatsappLink = "https://chat.whatsapp.com/KBdrA2wBg3M3K7O6rgNwOj",
  onRemind,
  onJoin,
}) {
  const [showRemind, setShowRemind] = useState(false);
  const [selectedDur, setSelectedDur] = useState(null);
  const [confirmed, setConfirmed] = useState(false);

  const handleClose = () => {
    setShowRemind(false);
    setSelectedDur(null);
    setConfirmed(false);
    onClose?.();
  };

  const handleJoin = () => {
    window.open(whatsappLink, "_blank", "noopener,noreferrer");
    onJoin?.();
    handleClose();
  };

  const handleConfirmRemind = () => {
    if (!selectedDur) return;
    setConfirmed(true);
    onRemind?.(selectedDur.value);
    setTimeout(() => handleClose(), 2000);
  };

  return (
    <Dialog open={open} onOpenChange={handleClose}>
      <DialogContent className="tw-p-0 tw-overflow-hidden tw-max-w-sm tw-gap-0">

        {/* Header */}
        <div className="tw-bg-[#0a1628] tw-pt-6 tw-pb-5 tw-px-4 tw-text-center">
          <p className="tw-text-[#f5c518] tw-text-base tw-tracking-widest tw-opacity-50 tw-mb-1">♪ ♫ ♪</p>
          <p className="tw-text-white tw-text-sm tw-font-medium">Join Our Private</p>
          <p className="tw-text-[#25D366] tw-text-lg tw-font-medium">WhatsApp Group!</p>
        </div>

        {/* Body */}
        <div className="tw-p-4 tw-space-y-4">

          {/* Connect row */}
          <div className="tw-flex tw-items-center tw-gap-3 tw-pb-4 tw-border-b tw-border-border">
            <div className="tw-w-11 tw-h-11 tw-rounded-full tw-bg-[#25D366] tw-flex tw-items-center tw-justify-content-center tw-shrink-0">
              <WhatsAppIcon size={22} />
            </div>
            <p className="tw-text-sm tw-font-medium">Connect with Fellow Piano Members!</p>
          </div>

          {/* Features */}
          <div className="tw-space-y-1.5">
            {FEATURES.map((feat) => (
              <div key={feat} className="tw-flex tw-items-center tw-gap-2">
                <span className="tw-text-[#25D366] tw-text-sm">✓</span>
                <span className="tw-text-sm tw-text-muted-foreground">{feat}</span>
              </div>
            ))}
          </div>

          {/* Banner */}
          <div className="tw-bg-[#1b3a24] tw-rounded-md tw-px-3 tw-py-2 tw-text-center">
            <p className="tw-text-[#7ecf8e] tw-text-xs tw-font-medium">
              Don't miss out — join our community today!
            </p>
          </div>

          {/* Join button */}
          <Button
            className="tw-w-full tw-bg-[#25D366] hover:tw-bg-[#128C7E] tw-text-white tw-font-medium"
            onClick={handleJoin}
          >
            <WhatsAppIcon size={18} />
            Join WhatsApp Group
          </Button>

          {/* Remind me later */}
          <Button
            variant="outline"
            className="tw-w-full tw-font-normal tw-text-muted-foreground hover:tw-border-[#25D366] hover:tw-text-[#25D366]"
            onClick={() => setShowRemind((v) => !v)}
          >
            <Clock className="tw-w-4 tw-h-4" />
            Remind me later
          </Button>

          {/* Remind panel */}
          <Collapsible open={showRemind}>
            <CollapsibleContent>
              <div className="tw-border tw-border-[#c8e6c9] tw-rounded-md tw-p-3 tw-bg-muted/40 tw-space-y-3">
                {!confirmed ? (
                  <>
                    <p className="tw-text-xs tw-text-muted-foreground tw-font-medium">
                      When would you like to be reminded?
                    </p>
                    <div className="tw-grid tw-grid-cols-3 tw-gap-1.5">
                      {DURATIONS.map((dur) => (
                        <button
                          key={dur.value}
                          onClick={() => setSelectedDur(dur)}
                          className={`tw-text-xs tw-py-1.5 tw-rounded-md tw-border tw-font-medium tw-transition-all
                            ${selectedDur?.value === dur.value
                              ? "tw-bg-[#25D366] tw-text-white tw-border-[#25D366]"
                              : "tw-border-border tw-text-muted-foreground hover:tw-border-[#25D366] hover:tw-text-[#25D366]"
                            }`}
                        >
                          {dur.label}
                        </button>
                      ))}
                    </div>
                    <Button
                      className="tw-w-full tw-bg-[#25D366] hover:tw-bg-[#128C7E] tw-text-white disabled:tw-bg-[#c8e6c9] disabled:tw-text-white"
                      disabled={!selectedDur}
                      onClick={handleConfirmRemind}
                    >
                      Set reminder
                    </Button>
                  </>
                ) : (
                  <div className="tw-text-center tw-py-1">
                    <p className="tw-text-2xl tw-text-[#25D366]">✓</p>
                    <p className="tw-text-sm tw-text-muted-foreground">
                      Got it! We'll remind you{" "}
                      <strong>{selectedDur?.label?.toLowerCase()}</strong>.
                    </p>
                  </div>
                )}
              </div>
            </CollapsibleContent>
          </Collapsible>

          {/* Skip */}
          <p
            onClick={handleClose}
            className="tw-text-center tw-text-xs tw-text-muted-foreground tw-underline tw-cursor-pointer hover:tw-text-foreground"
          >
            No, thanks. I'll join later.
          </p>
        </div>
      </DialogContent>
    </Dialog>
  );
}