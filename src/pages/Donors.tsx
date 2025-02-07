import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Plus } from "lucide-react";
import { useState } from "react";
import Navbar from "@/components/Navbar";
import { DonorsProvider } from "@/contexts/DonorsContext";
import { DonorForm } from "@/components/donors/DonorForm";
import { DonorList } from "@/components/donors/DonorList";

const Donors = () => {
  const [editingDonor, setEditingDonor] = useState(null);
  const [isDialogOpen, setIsDialogOpen] = useState(false);

  const handleEdit = (donor: any) => {
    setEditingDonor(donor);
    setIsDialogOpen(true);
  };

  return (
    <DonorsProvider>
      <div className="min-h-screen bg-gray-50">
        <Navbar />
        <main className="container mx-auto px-4 pt-20">
          <div className="flex justify-between items-center mb-6">
            <h1 className="text-3xl font-bold text-gray-900">Donors</h1>
            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
              <DialogTrigger asChild>
                <Button onClick={() => setEditingDonor(null)}>
                  <Plus className="w-4 h-4 mr-2" />
                  Add Donor
                </Button>
              </DialogTrigger>
              <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                  <DialogTitle>{editingDonor ? 'Edit Donor' : 'Add New Donor'}</DialogTitle>
                </DialogHeader>
                <DonorForm 
                  editingDonor={editingDonor} 
                  onClose={() => {
                    setIsDialogOpen(false);
                    setEditingDonor(null);
                  }}
                />
              </DialogContent>
            </Dialog>
          </div>

          <Card>
            <CardHeader>
              <CardTitle>Donor List</CardTitle>
            </CardHeader>
            <CardContent className="overflow-x-auto">
              <DonorList onEdit={handleEdit} />
            </CardContent>
          </Card>
        </main>
      </div>
    </DonorsProvider>
  );
};

export default Donors;