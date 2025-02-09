
import { FormField, FormItem, FormLabel, FormControl } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Control } from "react-hook-form";

interface DonorDonationSectionProps {
  control: Control<any>;
}

export function DonorDonationSection({ control }: DonorDonationSectionProps) {
  return (
    <div className="grid grid-cols-2 gap-4">
      <FormField
        control={control}
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
        control={control}
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
  );
}
