import React, { createContext, useContext, useState } from "react";

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
  addDonor: (donor: Omit<Donor, "id">) => void;
  updateDonor: (id: number, donor: Omit<Donor, "id">) => void;
  checkDuplicateDonor: (email: string, excludeId?: number) => boolean;
}

const DonorsContext = createContext<DonorsContextType | undefined>(undefined);

export function DonorsProvider({ children }: { children: React.ReactNode }) {
  const [donors, setDonors] = useState<Donor[]>([
    {
      id: 1,
      firstName: "Alice",
      lastName: "Johnson",
      email: "alice@example.com",
      phone: "+1 234 567 890",
      totalDonated: 5000,
      lastDonation: "2024-02-15",
      preferredCause: "Education",
      notes: "Regular monthly donor",
      status: "active"
    },
    {
      id: 2,
      firstName: "Bob",
      lastName: "Wilson",
      email: "bob@example.com",
      phone: "+1 234 567 891",
      totalDonated: 2500,
      lastDonation: "2024-01-20",
      preferredCause: "Healthcare",
      notes: "Annual donor",
      status: "active"
    },
  ]);

  const addDonor = (donor: Omit<Donor, "id">) => {
    setDonors(prev => [...prev, { ...donor, id: prev.length + 1 }]);
  };

  const updateDonor = (id: number, donor: Omit<Donor, "id">) => {
    setDonors(prev => prev.map(d => d.id === id ? { ...donor, id } : d));
  };

  const checkDuplicateDonor = (email: string, excludeId?: number) => {
    return donors.some(donor => 
      donor.email === email && donor.id !== excludeId
    );
  };

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