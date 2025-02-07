import React, { createContext, useContext, useState } from "react";

interface Contact {
  id: number;
  name: string;
  email: string;
  phone: string;
  address: string;
  zipCode: string;
  city: string;
  state: string;
  country: string;
  organization: string;
  notes: string;
  category: string;
  lastContact: string;
}

interface ContactsContextType {
  contacts: Contact[];
  addContact: (contact: Omit<Contact, "id">) => void;
  updateContact: (id: number, contact: Omit<Contact, "id">) => void;
  checkDuplicateContact: (email: string, excludeId?: number) => boolean;
}

const ContactsContext = createContext<ContactsContextType | undefined>(undefined);

export function ContactsProvider({ children }: { children: React.ReactNode }) {
  const [contacts, setContacts] = useState<Contact[]>([
    {
      id: 1,
      name: "John Doe",
      email: "john@example.com",
      phone: "+1 234 567 890",
      address: "123 Main St",
      zipCode: "12345",
      city: "Springfield",
      state: "IL",
      country: "United States",
      organization: "Local Charity",
      notes: "Regular donor",
      category: "Donor",
      lastContact: "2024-01-15"
    },
    {
      id: 2,
      name: "Jane Smith",
      email: "jane@example.com",
      phone: "+1 234 567 891",
      address: "456 Oak Avenue",
      zipCode: "67890",
      city: "Rivertown",
      state: "CA",
      country: "United States",
      organization: "Community Center",
      notes: "Volunteer coordinator",
      category: "Partner",
      lastContact: "2024-02-01"
    },
  ]);

  const addContact = (contact: Omit<Contact, "id">) => {
    setContacts(prev => [...prev, { ...contact, id: prev.length + 1 }]);
  };

  const updateContact = (id: number, contact: Omit<Contact, "id">) => {
    setContacts(prev => prev.map(c => c.id === id ? { ...contact, id } : c));
  };

  const checkDuplicateContact = (email: string, excludeId?: number) => {
    return contacts.some(contact => 
      contact.email === email && contact.id !== excludeId
    );
  };

  return (
    <ContactsContext.Provider value={{ contacts, addContact, updateContact, checkDuplicateContact }}>
      {children}
    </ContactsContext.Provider>
  );
}

export function useContacts() {
  const context = useContext(ContactsContext);
  if (context === undefined) {
    throw new Error("useContacts must be used within a ContactsProvider");
  }
  return context;
}