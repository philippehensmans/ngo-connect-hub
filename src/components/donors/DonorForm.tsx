
import { Button } from "@/components/ui/button";
import { Form } from "@/components/ui/form";
import { useForm } from "react-hook-form";
import { useDonors } from "@/contexts/DonorsContext";
import { useToast } from "@/components/ui/use-toast";
import { DonorPersonalSection } from "./form-sections/DonorPersonalSection";
import { DonorContactSection } from "./form-sections/DonorContactSection";
import { DonorDonationSection } from "./form-sections/DonorDonationSection";
import { DonorPreferencesSection } from "./form-sections/DonorPreferencesSection";
import { DonorNotesSection } from "./form-sections/DonorNotesSection";

interface DonorFormProps {
  editingDonor?: any;
  onClose: () => void;
}

export function DonorForm({ editingDonor, onClose }: DonorFormProps) {
  const { addDonor, updateDonor, checkDuplicateDonor } = useDonors();
  const { toast } = useToast();

  const form = useForm({
    defaultValues: editingDonor ? {
      firstName: editingDonor.firstName,
      lastName: editingDonor.lastName,
      email: editingDonor.email,
      phone: editingDonor.phone,
      totalDonated: editingDonor.totalDonated,
      lastDonation: editingDonor.lastDonation,
      preferredCause: editingDonor.preferredCause,
      notes: editingDonor.notes,
      status: editingDonor.status
    } : {
      firstName: "",
      lastName: "",
      email: "",
      phone: "",
      totalDonated: 0,
      lastDonation: new Date().toISOString().split('T')[0],
      preferredCause: "",
      notes: "",
      status: "potential"
    }
  });

  const onSubmit = (data: any) => {
    const isDuplicate = checkDuplicateDonor(data.email, editingDonor?.id);
    
    if (isDuplicate) {
      toast({
        title: "Error",
        description: "A donor with this email already exists.",
        variant: "destructive"
      });
      return;
    }

    if (editingDonor) {
      updateDonor(editingDonor.id, data);
      toast({
        title: "Success",
        description: "Donor updated successfully"
      });
    } else {
      addDonor(data);
      toast({
        title: "Success",
        description: "Donor added successfully"
      });
    }
    onClose();
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
        <DonorPersonalSection control={form.control} />
        <DonorContactSection control={form.control} />
        <DonorDonationSection control={form.control} />
        <DonorPreferencesSection control={form.control} />
        <DonorNotesSection control={form.control} />

        <div className="flex justify-end space-x-2">
          <Button type="button" variant="outline" onClick={onClose}>
            Cancel
          </Button>
          <Button type="submit">
            {editingDonor ? 'Update' : 'Add'} Donor
          </Button>
        </div>
      </form>
    </Form>
  );
}
