import { Button } from "@/components/ui/button";
import { Form, FormControl, FormField, FormItem, FormLabel } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { useForm } from "react-hook-form";
import { useDonors } from "@/contexts/DonorsContext";
import { useToast } from "@/components/ui/use-toast";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

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
        </div>

        <div className="grid grid-cols-2 gap-4">
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
        </div>

        <div className="grid grid-cols-2 gap-4">
          <FormField
            control={form.control}
            name="totalDonated"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Total Donated ($)</FormLabel>
                <FormControl>
                  <Input type="number" {...field} />
                </FormControl>
              </FormItem>
            )}
          />
          <FormField
            control={form.control}
            name="lastDonation"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Last Donation Date</FormLabel>
                <FormControl>
                  <Input type="date" {...field} />
                </FormControl>
              </FormItem>
            )}
          />
        </div>

        <div className="grid grid-cols-2 gap-4">
          <FormField
            control={form.control}
            name="preferredCause"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Preferred Cause</FormLabel>
                <FormControl>
                  <Input {...field} />
                </FormControl>
              </FormItem>
            )}
          />
          <FormField
            control={form.control}
            name="status"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Status</FormLabel>
                <Select onValueChange={field.onChange} defaultValue={field.value}>
                  <FormControl>
                    <SelectTrigger>
                      <SelectValue placeholder="Select status" />
                    </SelectTrigger>
                  </FormControl>
                  <SelectContent>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">Inactive</SelectItem>
                    <SelectItem value="potential">Potential</SelectItem>
                  </SelectContent>
                </Select>
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