import { useState } from "react";
import {
  Dialog,
  DialogContent,
  Box,
  Typography,
  Button,
  IconButton,
  Collapse,
  Grid,
  Chip,
  Fade,
} from "@mui/material";
import { X , CircleCheck, Clock2, CircleCheckBig} from 'lucide-react';

// ── WhatsApp SVG icon ────────────────────────────────────────────────────────
const WhatsAppIcon = ({ size = 28, color = "#fff" }) => (
  <svg width={size} height={size} viewBox="0 0 24 24" fill={color}>
    <path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.57 1.38 5.04L2 22l5.1-1.35A9.96 9.96 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm4.93 13.53c-.2.55-1.17 1.05-1.6 1.1-.4.05-.77.18-2.62-.55-2.2-.88-3.6-3.1-3.71-3.24-.11-.15-.88-1.17-.88-2.23s.55-1.58.77-1.8c.2-.2.44-.25.59-.25h.44c.14 0 .33-.05.52.4.2.46.66 1.6.72 1.72.06.11.1.24.02.38-.08.14-.12.23-.24.36-.11.13-.24.3-.34.4-.11.11-.23.24-.1.47.13.23.57.93 1.22 1.5.84.75 1.54.98 1.77 1.09.22.1.36.08.49-.05.13-.13.55-.64.7-.86.14-.22.28-.18.47-.11l1.49.7c.22.1.36.15.41.24.06.1.06.55-.14 1.1z" />
  </svg>
);

// ── Duration options ─────────────────────────────────────────────────────────
const DURATIONS = [
  { label: "1 hour", value: "1h" },
  { label: "3 hours", value: "3h" },
  { label: "6 hours", value: "6h" },
  { label: "Tomorrow", value: "tomorrow" },
  { label: "In 3 days", value: "3d" },
  { label: "Next week", value: "1w" },
];

// ── Feature list ─────────────────────────────────────────────────────────────
const FEATURES = [
  "Exclusive Piano breakdowns",
  "Live lesson schedules",
  "Guided practice sessions",
  "Live chat and support",
  "And many more..."
];

// ── Styles ───────────────────────────────────────────────────────────────────
const WA_GREEN = "#25D366";
const WA_DARK = "#128C7E";
const HEADER_BG = "#0a1628";

// ── Main component ───────────────────────────────────────────────────────────

/**
 * WhatsAppGroupModal
 *
 * Props:
 *  open          {boolean}   — controls dialog visibility
 *  onClose       {function}  — called when user dismisses the modal
 *  whatsappLink  {string}    — your WhatsApp group invite URL
 *  onRemind      {function}  — called with the selected duration value (string)
 *                              when user confirms a reminder
 *                              e.g. "1h" | "3h" | "6h" | "tomorrow" | "3d" | "1w"
 */
export default function WhatsAppGroupModal({
  open,
  onClose,
  whatsappLink = "https://chat.whatsapp.com/KBdrA2wBg3M3K7O6rgNwOj?mode=gi_t",
  onRemind,
  onJoin,
}) {
  const [showRemind, setShowRemind] = useState(false);
  const [selectedDur, setSelectedDur] = useState(null);
  const [confirmed, setConfirmed] = useState(false);

  // Reset internal state whenever modal reopens
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
    // Auto-close after 2 s so user sees confirmation
    setTimeout(() => handleClose(), 2000);
  };

  return (
    <Dialog
      open={open}
      onClose={handleClose}
      maxWidth="xs"
      fullWidth
      TransitionComponent={Fade}
      transitionDuration={300}
      PaperProps={{
        sx: {
          borderRadius: 4,
          overflow: "hidden",
          boxShadow: "0 24px 64px rgba(0,0,0,0.35)",
        },
      }}
    >
      {/* ── Header ── */}
      <Box
        sx={{
          background: HEADER_BG,
          pt: 3,
          pb: 2.5,
          px: 3,
          textAlign: "center",
          position: "relative",
        }}
      >
        <IconButton
          onClick={handleClose}
          size="small"
          sx={{
            position: "absolute",
            top: 10,
            right: 12,
            color: "rgba(255,255,255,0.5)",
            "&:hover": { color: "#fff", background: "rgba(255,255,255,0.08)" },
          }}
        >
          <X fontSize="small" />
        </IconButton>

        {/* Music notes decoration */}
        <Typography
          sx={{
            fontSize: 20,
            mb: 0.5,
            letterSpacing: 6,
            opacity: 0.5,
            color: "#f5c518",
          }}
        >
          ♪ ♫ ♪
        </Typography>

        <Typography
          variant="h6"
          sx={{ color: "#fff", fontWeight: 600, lineHeight: 1.2, mb: 0.25 }}
        >
          Join Our Private
        </Typography>
        <Typography
          variant="h5"
          sx={{ color: WA_GREEN, fontWeight: 700, lineHeight: 1.2 }}
        >
          WhatsApp Group!
        </Typography>
      </Box>

      {/* ── Body ── */}
      <DialogContent sx={{ p: 2.5 }}>
        {/* Connect row */}
        <Box
          sx={{
            display: "flex",
            alignItems: "center",
            gap: 1.5,
            mb: 2,
            pb: 2,
            borderBottom: "1px solid",
            borderColor: "divider",
          }}
        >
          <Box
            sx={{
              width: 52,
              height: 52,
              borderRadius: "50%",
              background: WA_GREEN,
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              flexShrink: 0,
              boxShadow: `0 4px 14px ${WA_GREEN}55`,
            }}
          >
            <WhatsAppIcon size={28} />
          </Box>
          <Typography sx={{ fontWeight: 600, fontSize: 15 }}>
            Connect with Fellow Piano Members!
          </Typography>
        </Box>

        {/* Feature list */}
        <Box sx={{ mb: 2 }}>
          {FEATURES.map((feat) => (
            <Box
              key={feat}
              sx={{ display: "flex", alignItems: "center", gap: 1, mb: 0.75 }}
            >
              <CircleCheck sx={{ color: WA_GREEN, fontSize: 20 }} />
              <Typography variant="body2" sx={{ color: "text.secondary" }}>
                {feat}
              </Typography>
            </Box>
          ))}
        </Box>

        {/* Banner */}
        <Box
          sx={{
            background: "#1b3a24",
            borderRadius: 2,
            px: 2,
            py: 1,
            mb: 2,
            textAlign: "center",
          }}
        >
          <Typography
            variant="body2"
            sx={{ color: "#7ecf8e", fontWeight: 600, fontSize: 13 }}
          >
            Don't miss out — join our community today!
          </Typography>
        </Box>

        {/* Join button */}
        <Button
          fullWidth
          variant="contained"
          startIcon={<WhatsAppIcon size={20} />}
          onClick={handleJoin}
          sx={{
            background: WA_GREEN,
            color: "#fff",
            fontWeight: 700,
            fontSize: 15,
            borderRadius: 3,
            py: 1.4,
            mb: 1.25,
            textTransform: "none",
            boxShadow: `0 4px 14px ${WA_GREEN}55`,
            "&:hover": { background: WA_DARK, boxShadow: `0 6px 20px ${WA_GREEN}66` },
          }}
        >
          Join WhatsApp Group
        </Button>

        {/* Remind me later button */}
        <Button
          fullWidth
          variant="outlined"
          startIcon={<Clock2 />}
          onClick={() => setShowRemind((v) => !v)}
          sx={{
            borderColor: "divider",
            color: "text.secondary",
            fontWeight: 500,
            fontSize: 14,
            borderRadius: 3,
            py: 1.2,
            mb: 1,
            textTransform: "none",
            "&:hover": { borderColor: WA_GREEN, color: WA_GREEN, background: "#f0fdf4" },
          }}
        >
          Remind me later
        </Button>

        {/* ── Remind-me panel ── */}
        <Collapse in={showRemind} unmountOnExit>
          <Box
            sx={{
              background: (t) =>
                t.palette.mode === "dark" ? "#0d1f12" : "#f6fdf7",
              borderRadius: 3,
              border: "1px solid",
              borderColor: "#c8e6c9",
              p: 2,
              mb: 1,
            }}
          >
            {!confirmed ? (
              <>
                <Typography
                  variant="caption"
                  sx={{ color: "text.secondary", mb: 1.25, display: "block", fontWeight: 500 }}
                >
                  When would you like to be reminded?
                </Typography>

                <Grid container spacing={1} sx={{ mb: 1.5 }}>
                  {DURATIONS.map((dur) => (
                    <Grid item xs={4} key={dur.value}>
                      <Chip
                        label={dur.label}
                        onClick={() => setSelectedDur(dur)}
                        variant={selectedDur?.value === dur.value ? "filled" : "outlined"}
                        sx={{
                          width: "100%",
                          cursor: "pointer",
                          fontSize: 12,
                          fontWeight: 500,
                          borderColor:
                            selectedDur?.value === dur.value ? WA_GREEN : "divider",
                          background:
                            selectedDur?.value === dur.value ? WA_GREEN : "transparent",
                          color:
                            selectedDur?.value === dur.value ? "#fff" : "text.secondary",
                          "&:hover": {
                            background:
                              selectedDur?.value === dur.value ? WA_DARK : "#e8f5e9",
                            borderColor: WA_GREEN,
                            color: selectedDur?.value === dur.value ? "#fff" : WA_GREEN,
                          },
                          transition: "all 0.15s",
                        }}
                      />
                    </Grid>
                  ))}
                </Grid>

                <Button
                  fullWidth
                  variant="contained"
                  disabled={!selectedDur}
                  onClick={handleConfirmRemind}
                  sx={{
                    background: WA_GREEN,
                    color: "#fff",
                    fontWeight: 600,
                    borderRadius: 2,
                    textTransform: "none",
                    fontSize: 14,
                    py: 1,
                    "&:hover": { background: WA_DARK },
                    "&.Mui-disabled": { background: "#c8e6c9", color: "#fff" },
                  }}
                >
                  Set reminder
                </Button>
              </>
            ) : (
              /* Success state */
              <Fade in={confirmed}>
                <Box sx={{ textAlign: "center", py: 0.5 }}>
                  <CircleCheckBig sx={{ color: WA_GREEN, fontSize: 36, mb: 0.5 }} />
                  <Typography variant="body2" sx={{ color: "text.secondary" }}>
                    Got it! We'll remind you{" "}
                    <strong>{selectedDur?.label?.toLowerCase()}</strong>.
                  </Typography>
                </Box>
              </Fade>
            )}
          </Box>
        </Collapse>

        {/* Skip link */}
        <Typography
          variant="caption"
          onClick={handleClose}
          sx={{
            display: "block",
            textAlign: "center",
            color: "text.disabled",
            cursor: "pointer",
            textDecoration: "underline",
            "&:hover": { color: "text.secondary" },
          }}
        >
          No, thanks. I'll join later.
        </Typography>
      </DialogContent>
    </Dialog>
  );
}


// ── Usage example (remove or move to your own file) ──────────────────────────
//
// import { useState } from "react";
// import WhatsAppGroupModal from "./WhatsAppGroupModal";
//
// export default function App() {
//   const [open, setOpen] = useState(true);
//
//   const handleRemind = (duration) => {
//     // duration: "1h" | "3h" | "6h" | "tomorrow" | "3d" | "1w"
//     console.log("Remind in:", duration);
//     // TODO: schedule a notification or persist to your backend
//   };
//
//   return (
//     <>
//       <button onClick={() => setOpen(true)}>Open Modal</button>
//       <WhatsAppGroupModal
//         open={open}
//         onClose={() => setOpen(false)}
//         whatsappLink="https://chat.whatsapp.com/YOUR_REAL_LINK"
//         onRemind={handleRemind}
//       />
//     </>
//   );
// }