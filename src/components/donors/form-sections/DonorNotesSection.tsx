
import { FormField, FormItem, FormLabel, FormControl } from "@/components/ui/form";
import { Textarea } from "@/components/ui/textarea";
import { Control } from "react-hook-form";

interface DonorNotesSectionProps {
  control: Control<any>;
}

export function DonorNotesSection({ control }: DonorNotesSectionProps) {
  return (
    <FormField
      control={control}
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
  );
}
