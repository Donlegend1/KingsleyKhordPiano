"use client";
import React, { createContext, useContext, useState, ReactNode } from "react";
import { Alert, Snackbar } from "@mui/material";

type MessageType = "success" | "error";

interface FlashMessageContextType {
  showMessage: (message: string, type?: MessageType) => void;
}

const FlashMessageContext = createContext<FlashMessageContextType | undefined>(
  undefined,
);

export const FlashMessageProvider = ({ children }: { children: ReactNode }) => {
  const [open, setOpen] = useState(false);
  const [message, setMessage] = useState("");
  const [severity, setSeverity] = useState<MessageType>("success");

  const showMessage = (msg: string, type: MessageType = "success") => {
    setMessage(msg);
    setSeverity(type);
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
    setMessage("");
  };

  return (
    <FlashMessageContext.Provider value={{ showMessage }}>
      {children}
      <Snackbar
        open={open}
        autoHideDuration={5000}
        onClose={handleClose}
        anchorOrigin={{ vertical: "top", horizontal: "right" }}
      >
        <Alert
          onClose={handleClose}
          severity={severity}
          variant="filled"
          sx={
            severity === "success"
              ? {
                  backgroundColor: "#000",
                  color: "#fff",
                }
              : {}
          }
        >
          {message}
        </Alert>
      </Snackbar>
    </FlashMessageContext.Provider>
  );
};

export const useFlashMessage = () => {
  const context = useContext(FlashMessageContext);
  if (!context) {
    throw new Error(
      "useFlashMessage must be used within a FlashMessageProvider",
    );
  }
  return context;
};
