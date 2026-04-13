"use client";
import React, { createContext, useContext, ReactNode } from "react";
import { Toaster, toast } from "sonner";

type MessageType = "success" | "error";

interface FlashMessageContextType {
  showMessage: (message: string, type?: MessageType) => void;
}

const FlashMessageContext = createContext<FlashMessageContextType | undefined>(
  undefined,
);

export const FlashMessageProvider = ({ children }: { children: ReactNode }) => {
  const showMessage = (msg: string, type: MessageType = "success") => {
    if (type === "error") {
      toast.error(msg, {
        duration: 5000,
      });
      return;
    }

    toast.success(msg, {
      duration: 5000,
      style: {
        backgroundColor: "#000",
        color: "#fff",
        border: "1px solid rgba(255,255,255,0.08)",
      },
    });
  };

  return (
    <FlashMessageContext.Provider value={{ showMessage }}>
      {children}
      <Toaster richColors position="top-right" closeButton />
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
