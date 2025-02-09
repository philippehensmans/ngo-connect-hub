
import React, { createContext, useContext, useState, useEffect } from 'react';
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/components/ui/use-toast";

interface GDPRContextType {
  hasConsent: boolean;
  setConsent: (value: boolean) => Promise<void>;
}

const GDPRContext = createContext<GDPRContextType | undefined>(undefined);

export function GDPRProvider({ children }: { children: React.ReactNode }) {
  const [hasConsent, setHasConsent] = useState(false);
  const { toast } = useToast();

  useEffect(() => {
    checkExistingConsent();
  }, []);

  const checkExistingConsent = async () => {
    try {
      const { data, error } = await supabase
        .from('gdpr_consents')
        .select('consent_given')
        .order('created_at', { ascending: false })
        .limit(1);

      if (error) throw error;
      if (data && data.length > 0) {
        setHasConsent(data[0].consent_given);
      }
    } catch (error) {
      console.error('Error checking consent:', error);
    }
  };

  const setConsent = async (value: boolean) => {
    try {
      const { error } = await supabase
        .from('gdpr_consents')
        .insert([
          {
            consent_type: 'general',
            consent_given: value,
            user_ip: '', // We'll leave this empty for privacy
          }
        ]);

      if (error) throw error;
      setHasConsent(value);
      toast({
        title: value ? "Consent Given" : "Consent Withdrawn",
        description: value 
          ? "Thank you for accepting our privacy policy." 
          : "Your privacy settings have been updated.",
      });
    } catch (error) {
      console.error('Error setting consent:', error);
      toast({
        title: "Error",
        description: "Failed to update consent settings.",
        variant: "destructive",
      });
    }
  };

  return (
    <GDPRContext.Provider value={{ hasConsent, setConsent }}>
      {children}
    </GDPRContext.Provider>
  );
}

export function useGDPR() {
  const context = useContext(GDPRContext);
  if (context === undefined) {
    throw new Error('useGDPR must be used within a GDPRProvider');
  }
  return context;
}
