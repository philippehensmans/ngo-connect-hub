import React, { createContext, useContext, useState } from "react";
import { useToast } from "@/hooks/use-toast";

interface Payment {
  id: number;
  amount: number;
  date: string;
  status: "pending" | "completed" | "failed";
  method: string;
  reference: string;
  description: string;
  donorId?: number;
}

interface PaymentsContextType {
  payments: Payment[];
  addPayment: (payment: Omit<Payment, "id">) => void;
  updatePayment: (id: number, payment: Omit<Payment, "id">) => void;
  deletePayment: (id: number) => void;
}

const PaymentsContext = createContext<PaymentsContextType | undefined>(undefined);

export function PaymentsProvider({ children }: { children: React.ReactNode }) {
  const [payments, setPayments] = useState<Payment[]>([]);
  const { toast } = useToast();

  const addPayment = (payment: Omit<Payment, "id">) => {
    const newPayment = {
      ...payment,
      id: payments.length + 1,
    };
    setPayments([...payments, newPayment]);
    toast({
      title: "Payment Added",
      description: "The payment has been successfully recorded.",
    });
  };

  const updatePayment = (id: number, payment: Omit<Payment, "id">) => {
    setPayments(payments.map((p) => (p.id === id ? { ...payment, id } : p)));
    toast({
      title: "Payment Updated",
      description: "The payment has been successfully updated.",
    });
  };

  const deletePayment = (id: number) => {
    setPayments(payments.filter((p) => p.id !== id));
    toast({
      title: "Payment Deleted",
      description: "The payment has been successfully deleted.",
    });
  };

  return (
    <PaymentsContext.Provider value={{ payments, addPayment, updatePayment, deletePayment }}>
      {children}
    </PaymentsContext.Provider>
  );
}

export const usePayments = () => {
  const context = useContext(PaymentsContext);
  if (context === undefined) {
    throw new Error("usePayments must be used within a PaymentsProvider");
  }
  return context;
};