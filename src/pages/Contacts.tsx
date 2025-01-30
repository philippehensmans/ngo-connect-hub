import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Plus, Pencil } from "lucide-react";
import { useForm } from "react-hook-form";
import { useState } from "react";
import Navbar from "@/components/Navbar";
import { useToast } from "@/components/ui/use-toast";

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

const Contacts = () => {
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

  const [editingContact, setEditingContact] = useState<Contact | null>(null);
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const { toast } = useToast();

  const form = useForm<Contact>({
    defaultValues: editingContact || {
      id: 0,
      name: "",
      email: "",
      phone: "",
      address: "",
      zipCode: "",
      city: "",
      state: "",
      country: "",
      organization: "",
      notes: "",
      category: "",
      lastContact: new Date().toISOString().split('T')[0]
    }
  });

  const handleEdit = (contact: Contact) => {
    setEditingContact(contact);
    form.reset(contact);
    setIsDialogOpen(true);
  };

  const checkDuplicateContact = (data: Contact) => {
    return contacts.some(contact => 
      contact.email === data.email && 
      (editingContact ? contact.id !== editingContact.id : true)
    );
  };

  const onSubmit = (data: Contact) => {
    if (checkDuplicateContact(data)) {
      toast({
        variant: "destructive",
        title: "Duplicate Contact",
        description: "A contact with this email already exists.",
      });
      return;
    }

    if (editingContact) {
      setContacts(contacts.map(c => c.id === editingContact.id ? { ...data, id: editingContact.id } : c));
      toast({
        title: "Contact Updated",
        description: "The contact has been successfully updated.",
      });
    } else {
      setContacts([...contacts, { ...data, id: contacts.length + 1 }]);
      toast({
        title: "Contact Added",
        description: "The new contact has been successfully added.",
      });
    }
    setIsDialogOpen(false);
    setEditingContact(null);
    form.reset();
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <main className="container mx-auto px-4 pt-20">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-3xl font-bold text-gray-900">Contacts</h1>
          <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
            <DialogTrigger asChild>
              <Button onClick={() => {
                setEditingContact(null);
                form.reset({
                  id: 0,
                  name: "",
                  email: "",
                  phone: "",
                  address: "",
                  zipCode: "",
                  city: "",
                  state: "",
                  country: "",
                  organization: "",
                  notes: "",
                  category: "",
                  lastContact: new Date().toISOString().split('T')[0]
                });
              }}>
                <Plus className="w-4 h-4 mr-2" />
                Add Contact
              </Button>
            </DialogTrigger>
            <DialogContent className="sm:max-w-[600px]">
              <DialogHeader>
                <DialogTitle>{editingContact ? 'Edit Contact' : 'Add New Contact'}</DialogTitle>
              </DialogHeader>
              <Form {...form}>
                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="name"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Name</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="email"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Email</FormLabel>
                          <FormControl>
                            <Input type="email" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="phone"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Phone</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="address"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Address</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="zipCode"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>ZIP Code</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="city"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>City</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="state"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>State</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="country"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Country</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="organization"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Organization</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="category"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Category</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    <FormField
                      control={form.control}
                      name="lastContact"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Last Contact</FormLabel>
                          <FormControl>
                            <Input type="date" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                  <FormField
                    control={form.control}
                    name="notes"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Notes</FormLabel>
                        <FormControl>
                          <Textarea {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <Button type="submit" className="w-full">
                    {editingContact ? 'Save Changes' : 'Add Contact'}
                  </Button>
                </form>
              </Form>
            </DialogContent>
          </Dialog>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Contact List</CardTitle>
          </CardHeader>
          <CardContent className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Phone</TableHead>
                  <TableHead>Address</TableHead>
                  <TableHead>City</TableHead>
                  <TableHead>State</TableHead>
                  <TableHead>ZIP</TableHead>
                  <TableHead>Country</TableHead>
                  <TableHead>Organization</TableHead>
                  <TableHead>Category</TableHead>
                  <TableHead>Last Contact</TableHead>
                  <TableHead>Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {contacts.map((contact) => (
                  <TableRow key={contact.id}>
                    <TableCell>{contact.name}</TableCell>
                    <TableCell>{contact.email}</TableCell>
                    <TableCell>{contact.phone}</TableCell>
                    <TableCell>{contact.address}</TableCell>
                    <TableCell>{contact.city}</TableCell>
                    <TableCell>{contact.state}</TableCell>
                    <TableCell>{contact.zipCode}</TableCell>
                    <TableCell>{contact.country}</TableCell>
                    <TableCell>{contact.organization}</TableCell>
                    <TableCell>{contact.category}</TableCell>
                    <TableCell>{contact.lastContact}</TableCell>
                    <TableCell>
                      <Button variant="ghost" size="icon" onClick={() => handleEdit(contact)}>
                        <Pencil className="h-4 w-4" />
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </main>
    </div>
  );
};

export default Contacts;
