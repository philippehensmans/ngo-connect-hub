
import React, { createContext, useContext, useState, useEffect } from "react";
import { useToast } from "@/hooks/use-toast";
import { supabase } from "@/integrations/supabase/client";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";

interface Payment {
  id: string;
  amount: number;
  date: string;
  status: "pending" | "completed" | "failed";
  method: string;
  reference: string | null;
  description: string | null;
  donor_id: string | null;
  created_at: string | null;
  updated_at: string | null;
}

interface PaymentsContextType {
  payments: Payment[];
  isLoading: boolean;
  addPayment: (payment: Omit<Payment, "id" | "created_at" | "updated_at">) => void;
  updatePayment: (id: string, payment: Partial<Omit<Payment, "id" | "created_at" | "updated_at">>) => void;
  deletePayment: (id: string) => void;
}

const PaymentsContext = createContext<PaymentsContextType | undefined>(undefined);

export function PaymentsProvider({ children }: { children: React.ReactNode }) {
  const { toast } = useToast();
  const queryClient = useQueryClient();

  // Fetch payments
  const { data: payments = [], isLoading } = useQuery({
    queryKey: ['payments'],
    queryFn: async () => {
      const { data, error } = await supabase
        .from('payments')
        .select('*')
        .order('date', { ascending: false });
      
      if (error) throw error;
      return data as Payment[];
    }
  });

  // Add payment mutation
  const addPaymentMutation = useMutation({
    mutationFn: async (payment: Omit<Payment, "id" | "created_at" | "updated_at">) => {
      const { data, error } = await supabase
        .from('payments')
        .insert([payment])
        .select()
        .single();
      
      if (error) throw error;
      return data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['payments'] });
      toast({
        title: "Payment Added",
        description: "The payment has been successfully recorded.",
      });
    },
    onError: (error) => {
      toast({
        title: "Error",
        description: "Failed to add payment: " + error.message,
        variant: "destructive",
      });
    }
  });

  // Update payment mutation
  const updatePaymentMutation = useMutation({
    mutationFn: async ({ id, payment }: { id: string, payment: Partial<Omit<Payment, "id" | "created_at" | "updated_at">> }) => {
      const { data, error } = await supabase
        .from('payments')
        .update(payment)
        .eq('id', id)
        .select()
        .single();
      
      if (error) throw error;
      return data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['payments'] });
      toast({
        title: "Payment Updated",
        description: "The payment has been successfully updated.",
      });
    },
    onError: (error) => {
      toast({
        title: "Error",
        description: "Failed to update payment: " + error.message,
        variant: "destructive",
      });
    }
  });

  // Delete payment mutation
  const deletePaymentMutation = useMutation({
    mutationFn: async (id: string) => {
      const { error } = await supabase
        .from('payments')
        .delete()
        .eq('id', id);
      
      if (error) throw error;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['payments'] });
      toast({
        title: "Payment Deleted",
        description: "The payment has been successfully deleted.",
      });
    },
    onError: (error) => {
      toast({
        title: "Error",
        description: "Failed to delete payment: " + error.message,
        variant: "destructive",
      });
    }
  });

  const addPayment = (payment: Omit<Payment, "id" | "created_at" | "updated_at">) => {
    addPaymentMutation.mutate(payment);
  };

  const updatePayment = (id: string, payment: Partial<Omit<Payment, "id" | "created_at" | "updated_at">>) => {
    updatePaymentMutation.mutate({ id, payment });
  };

  const deletePayment = (id: string) => {
    deletePaymentMutation.mutate(id);
  };

  return (
    <PaymentsContext.Provider value={{ payments, isLoading, addPayment, updatePayment, deletePayment }}>
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
