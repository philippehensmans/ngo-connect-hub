import { Button } from "@/components/ui/button";
import { Form, FormControl, FormField, FormItem, FormLabel } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { useForm } from "react-hook-form";
import { usePayments } from "@/contexts/PaymentsContext";
import { PaymentAmountDateSection } from "./PaymentAmountDateSection";
import { PaymentStatusMethodSection } from "./PaymentStatusMethodSection";

interface PaymentFormProps {
  editingPayment?: any;
  onClose: () => void;
}

export function PaymentForm({ editingPayment, onClose }: PaymentFormProps) {
  const { addPayment, updatePayment } = usePayments();

  const form = useForm({
    defaultValues: editingPayment ? {
      amount: editingPayment.amount,
      date: editingPayment.date,
      status: editingPayment.status,
      method: editingPayment.method,
      reference: editingPayment.reference,
      description: editingPayment.description,
      donorId: editingPayment.donorId
    } : {
      amount: 0,
      date: new Date().toISOString().split('T')[0],
      status: "pending",
      method: "",
      reference: "",
      description: "",
      donorId: undefined
    }
  });

  const onSubmit = (data: any) => {
    if (editingPayment) {
      updatePayment(editingPayment.id, data);
    } else {
      addPayment(data);
    }
    onClose();
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
        <PaymentAmountDateSection control={form.control} />
        <PaymentStatusMethodSection control={form.control} />

        <FormField
          control={form.control}
          name="reference"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Reference Number</FormLabel>
              <FormControl>
                <Input {...field} />
              </FormControl>
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="description"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Description</FormLabel>
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
            {editingPayment ? 'Update' : 'Add'} Payment
          </Button>
        </div>
      </form>
    </Form>
  );
}