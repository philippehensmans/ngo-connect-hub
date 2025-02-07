import { Button } from "@/components/ui/button";
import { Form, FormControl, FormField, FormItem, FormLabel } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { useToast } from "@/hooks/use-toast";
import { useForm } from "react-hook-form";
import { useContacts } from "@/contexts/ContactsContext";

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

interface ContactFormProps {
  editingContact: Contact | null;
  onClose: () => void;
}

export function ContactForm({ editingContact, onClose }: ContactFormProps) {
  const { addContact, updateContact, checkDuplicateContact } = useContacts();
  const { toast } = useToast();

  const form = useForm<Omit<Contact, "id">>({
    defaultValues: editingContact ? {
      firstName: editingContact.firstName,
      lastName: editingContact.lastName,
      email: editingContact.email,
      phone: editingContact.phone,
      address: editingContact.address,
      zipCode: editingContact.zipCode,
      city: editingContact.city,
      state: editingContact.state,
      country: editingContact.country,
      organization: editingContact.organization,
      notes: editingContact.notes,
      category: editingContact.category,
      lastContact: editingContact.lastContact
    } : {
      firstName: "",
      lastName: "",
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

  const onSubmit = (data: Omit<Contact, "id">) => {
    if (checkDuplicateContact(data.email, editingContact?.id)) {
      toast({
        variant: "destructive",
        title: "Duplicate Contact",
        description: "A contact with this email already exists.",
      });
      return;
    }

    if (editingContact) {
      updateContact(editingContact.id, data);
      toast({
        title: "Contact Updated",
        description: "The contact has been successfully updated.",
      });
    } else {
      addContact(data);
      toast({
        title: "Contact Added",
        description: "The new contact has been successfully added.",
      });
    }
    onClose();
    form.reset();
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
        <div className="grid grid-cols-2 gap-4">
          <FormField
            control={form.control}
            name="firstName"
            render={({ field }) => (
              <FormItem>
                <FormLabel>First Name</FormLabel>
                <FormControl>
                  <Input {...field} />
                </FormControl>
              </FormItem>
            )}
          />
          <FormField
            control={form.control}
            name="lastName"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Last Name</FormLabel>
                <FormControl>
                  <Input {...field} />
                </FormControl>
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
            </FormItem>
          )}
        />
        <Button type="submit" className="w-full">
          {editingContact ? 'Save Changes' : 'Add Contact'}
        </Button>
      </form>
    </Form>
  );
}
