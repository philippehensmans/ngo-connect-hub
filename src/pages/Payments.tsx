import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Plus } from "lucide-react";
import { useState } from "react";
import Navbar from "@/components/Navbar";
import { PaymentsProvider } from "@/contexts/PaymentsContext";
import { PaymentForm } from "@/components/payments/PaymentForm";
import { PaymentList } from "@/components/payments/PaymentList";

const Payments = () => {
  const [editingPayment, setEditingPayment] = useState(null);
  const [isDialogOpen, setIsDialogOpen] = useState(false);

  const handleEdit = (payment: any) => {
    setEditingPayment(payment);
    setIsDialogOpen(true);
  };

  return (
    <PaymentsProvider>
      <div className="min-h-screen bg-gray-50">
        <Navbar />
        <main className="container mx-auto px-4 pt-20">
          <div className="flex justify-between items-center mb-6">
            <h1 className="text-3xl font-bold text-gray-900">Payments</h1>
            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
              <DialogTrigger asChild>
                <Button onClick={() => setEditingPayment(null)}>
                  <Plus className="w-4 h-4 mr-2" />
                  Add Payment
                </Button>
              </DialogTrigger>
              <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                  <DialogTitle>{editingPayment ? 'Edit Payment' : 'Add New Payment'}</DialogTitle>
                </DialogHeader>
                <PaymentForm 
                  editingPayment={editingPayment} 
                  onClose={() => {
                    setIsDialogOpen(false);
                    setEditingPayment(null);
                  }}
                />
              </DialogContent>
            </Dialog>
          </div>

          <Card>
            <CardHeader>
              <CardTitle>Payment List</CardTitle>
            </CardHeader>
            <CardContent className="overflow-x-auto">
              <PaymentList onEdit={handleEdit} />
            </CardContent>
          </Card>
        </main>
      </div>
    </PaymentsProvider>
  );
};

export default Payments;