
import React, { createContext, useContext, useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";

interface Contact {
  id: number;
  firstName: string;
  lastName: string;
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
  addContact: (contact: Omit<Contact, "id">) => Promise<void>;
  updateContact: (id: number, contact: Omit<Contact, "id">) => Promise<void>;
  checkDuplicateContact: (email: string, excludeId?: number) => Promise<boolean>;
}

const ContactsContext = createContext<ContactsContextType | undefined>(undefined);

export function ContactsProvider({ children }: { children: React.ReactNode }) {
  const [contacts, setContacts] = useState<Contact[]>([]);

  const addContact = async (contact: Omit<Contact, "id">) => {
    const isDuplicate = await checkDuplicateContact(contact.email);
    if (isDuplicate) {
      throw new Error("A contact with this email already exists");
    }

    const { data, error } = await supabase
      .from('contacts')
      .insert([{
        first_name: contact.firstName,
        last_name: contact.lastName,
        email: contact.email,
        phone: contact.phone,
        address: contact.address,
        zip_code: contact.zipCode,
        city: contact.city,
        state: contact.state,
        country: contact.country,
        organization: contact.organization,
        notes: contact.notes,
        category: contact.category,
        last_contact: contact.lastContact
      }])
      .select();

    if (error) throw error;
    if (data) {
      setContacts(prev => [...prev, { ...contact, id: data[0].id }]);
    }
  };

  const updateContact = async (id: number, contact: Omit<Contact, "id">) => {
    const isDuplicate = await checkDuplicateContact(contact.email, id);
    if (isDuplicate) {
      throw new Error("A contact with this email already exists");
    }

    const { error } = await supabase
      .from('contacts')
      .update({
        first_name: contact.firstName,
        last_name: contact.lastName,
        email: contact.email,
        phone: contact.phone,
        address: contact.address,
        zip_code: contact.zipCode,
        city: contact.city,
        state: contact.state,
        country: contact.country,
        organization: contact.organization,
        notes: contact.notes,
        category: contact.category,
        last_contact: contact.lastContact
      })
      .eq('id', id);

    if (error) throw error;
    setContacts(prev => prev.map(c => c.id === id ? { ...contact, id } : c));
  };

  const checkDuplicateContact = async (email: string, excludeId?: number) => {
    const { data, error } = await supabase
      .from('contacts')
      .select('id')
      .eq('email', email)
      .neq('id', excludeId || null)
      .maybeSingle();

    if (error) throw error;
    return data !== null;
  };

  useEffect(() => {
    const fetchContacts = async () => {
      const { data, error } = await supabase
        .from('contacts')
        .select('*');
      
      if (error) throw error;
      if (data) {
        setContacts(data.map(c => ({
          id: c.id,
          firstName: c.first_name,
          lastName: c.last_name,
          email: c.email,
          phone: c.phone,
          address: c.address,
          zipCode: c.zip_code,
          city: c.city,
          state: c.state,
          country: c.country,
          organization: c.organization,
          notes: c.notes,
          category: c.category,
          lastContact: c.last_contact
        })));
      }
    };

    fetchContacts();
  }, []);

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
