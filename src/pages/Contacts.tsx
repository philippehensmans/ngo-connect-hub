import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Plus } from "lucide-react";
import { useState } from "react";
import Navbar from "@/components/Navbar";
import { ContactsProvider } from "@/contexts/ContactsContext";
import { ContactForm } from "@/components/contacts/ContactForm";
import { ContactList } from "@/components/contacts/ContactList";

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

const Contacts = () => {
  const [editingContact, setEditingContact] = useState<Contact | null>(null);
  const [isDialogOpen, setIsDialogOpen] = useState(false);

  const handleEdit = (contact: Contact) => {
    setEditingContact(contact);
    setIsDialogOpen(true);
  };

  return (
    <ContactsProvider>
      <div className="min-h-screen bg-gray-50">
        <Navbar />
        <main className="container mx-auto px-4 pt-20">
          <div className="flex justify-between items-center mb-6">
            <h1 className="text-3xl font-bold text-gray-900">Contacts</h1>
            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
              <DialogTrigger asChild>
                <Button onClick={() => setEditingContact(null)}>
                  <Plus className="w-4 h-4 mr-2" />
                  Add Contact
                </Button>
              </DialogTrigger>
              <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                  <DialogTitle>{editingContact ? 'Edit Contact' : 'Add New Contact'}</DialogTitle>
                </DialogHeader>
                <ContactForm 
                  editingContact={editingContact} 
                  onClose={() => {
                    setIsDialogOpen(false);
                    setEditingContact(null);
                  }}
                />
              </DialogContent>
            </Dialog>
          </div>

          <Card>
            <CardHeader>
              <CardTitle>Contact List</CardTitle>
            </CardHeader>
            <CardContent className="overflow-x-auto">
              <ContactList onEdit={handleEdit} />
            </CardContent>
          </Card>
        </main>
      </div>
    </ContactsProvider>
  );
};

export default Contacts;