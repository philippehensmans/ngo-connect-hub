
import React, { createContext, useContext, useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";

interface Donor {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  totalDonated: number;
  lastDonation: string;
  preferredCause: string;
  notes: string;
  status: "active" | "inactive" | "potential";
}

interface DonorsContextType {
  donors: Donor[];
  addDonor: (donor: Omit<Donor, "id">) => Promise<void>;
  updateDonor: (id: number, donor: Omit<Donor, "id">) => Promise<void>;
  checkDuplicateDonor: (email: string, excludeId?: number) => Promise<boolean>;
}

const DonorsContext = createContext<DonorsContextType | undefined>(undefined);

export function DonorsProvider({ children }: { children: React.ReactNode }) {
  const [donors, setDonors] = useState<Donor[]>([]);

  const addDonor = async (donor: Omit<Donor, "id">) => {
    const isDuplicate = await checkDuplicateDonor(donor.email);
    if (isDuplicate) {
      throw new Error("A donor with this email already exists");
    }
    
    const { data, error } = await supabase
      .from('donors')
      .insert([{
        first_name: donor.firstName,
        last_name: donor.lastName,
        email: donor.email,
        phone: donor.phone,
        total_donated: donor.totalDonated,
        last_donation: donor.lastDonation,
        preferred_cause: donor.preferredCause,
        notes: donor.notes,
        status: donor.status
      }])
      .select();

    if (error) throw error;
    if (data) {
      setDonors(prev => [...prev, { ...donor, id: data[0].id }]);
    }
  };

  const updateDonor = async (id: number, donor: Omit<Donor, "id">) => {
    const isDuplicate = await checkDuplicateDonor(donor.email, id);
    if (isDuplicate) {
      throw new Error("A donor with this email already exists");
    }

    const { error } = await supabase
      .from('donors')
      .update({
        first_name: donor.firstName,
        last_name: donor.lastName,
        email: donor.email,
        phone: donor.phone,
        total_donated: donor.totalDonated,
        last_donation: donor.lastDonation,
        preferred_cause: donor.preferredCause,
        notes: donor.notes,
        status: donor.status
      })
      .eq('id', id);

    if (error) throw error;
    setDonors(prev => prev.map(d => d.id === id ? { ...donor, id } : d));
  };

  const checkDuplicateDonor = async (email: string, excludeId?: number) => {
    const { data, error } = await supabase
      .from('donors')
      .select('id')
      .eq('email', email)
      .neq('id', excludeId || null)
      .maybeSingle();

    if (error) throw error;
    return data !== null;
  };

  useEffect(() => {
    const fetchDonors = async () => {
      const { data, error } = await supabase
        .from('donors')
        .select('*');
      
      if (error) throw error;
      if (data) {
        setDonors(data.map(d => ({
          id: d.id,
          firstName: d.first_name,
          lastName: d.last_name,
          email: d.email,
          phone: d.phone,
          totalDonated: d.total_donated,
          lastDonation: d.last_donation,
          preferredCause: d.preferred_cause,
          notes: d.notes,
          status: d.status
        })));
      }
    };

    fetchDonors();
  }, []);

  return (
    <DonorsContext.Provider value={{ donors, addDonor, updateDonor, checkDuplicateDonor }}>
      {children}
    </DonorsContext.Provider>
  );
}

export function useDonors() {
  const context = useContext(DonorsContext);
  if (context === undefined) {
    throw new Error("useDonors must be used within a DonorsProvider");
  }
  return context;
}
