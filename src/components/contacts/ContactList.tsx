import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { Pencil } from "lucide-react";
import { useContacts } from "@/contexts/ContactsContext";

interface ContactListProps {
  onEdit: (contact: any) => void;
}

export function ContactList({ onEdit }: ContactListProps) {
  const { contacts } = useContacts();

  return (
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
              <Button variant="ghost" size="icon" onClick={() => onEdit(contact)}>
                <Pencil className="h-4 w-4" />
              </Button>
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}