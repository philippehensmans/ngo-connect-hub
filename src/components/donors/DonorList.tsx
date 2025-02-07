import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { Pencil } from "lucide-react";
import { useDonors } from "@/contexts/DonorsContext";

interface DonorListProps {
  onEdit: (donor: any) => void;
}

export function DonorList({ onEdit }: DonorListProps) {
  const { donors } = useDonors();

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>First Name</TableHead>
          <TableHead>Last Name</TableHead>
          <TableHead>Email</TableHead>
          <TableHead>Phone</TableHead>
          <TableHead>Total Donated</TableHead>
          <TableHead>Last Donation</TableHead>
          <TableHead>Preferred Cause</TableHead>
          <TableHead>Status</TableHead>
          <TableHead>Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {donors.map((donor) => (
          <TableRow key={donor.id}>
            <TableCell>{donor.firstName}</TableCell>
            <TableCell>{donor.lastName}</TableCell>
            <TableCell>{donor.email}</TableCell>
            <TableCell>{donor.phone}</TableCell>
            <TableCell>${donor.totalDonated.toLocaleString()}</TableCell>
            <TableCell>{donor.lastDonation}</TableCell>
            <TableCell>{donor.preferredCause}</TableCell>
            <TableCell>
              <span className={`px-2 py-1 rounded-full text-xs ${
                donor.status === 'active' ? 'bg-green-100 text-green-800' :
                donor.status === 'inactive' ? 'bg-gray-100 text-gray-800' :
                'bg-yellow-100 text-yellow-800'
              }`}>
                {donor.status.charAt(0).toUpperCase() + donor.status.slice(1)}
              </span>
            </TableCell>
            <TableCell>
              <Button variant="ghost" size="icon" onClick={() => onEdit(donor)}>
                <Pencil className="h-4 w-4" />
              </Button>
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}