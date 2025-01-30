import { Users, Heart, Calendar, DollarSign } from "lucide-react";
import DashboardCard from "@/components/DashboardCard";
import Navbar from "@/components/Navbar";

const Index = () => {
  // This would be replaced with real data in a future iteration
  const mockData = {
    totalContacts: 156,
    activeDonors: 43,
    upcomingEvents: 5,
    monthlyDonations: "$12,450",
  };

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <main className="max-w-7xl mx-auto px-4 pt-24 pb-12">
        <h1 className="text-3xl font-bold mb-8">Dashboard Overview</h1>
        
        <div className="dashboard-grid">
          <DashboardCard
            title="Total Contacts"
            value={mockData.totalContacts}
            icon={<Users className="w-6 h-6" />}
          />
          <DashboardCard
            title="Active Donors"
            value={mockData.activeDonors}
            icon={<Heart className="w-6 h-6" />}
          />
          <DashboardCard
            title="Upcoming Events"
            value={mockData.upcomingEvents}
            icon={<Calendar className="w-6 h-6" />}
          />
          <DashboardCard
            title="Monthly Donations"
            value={mockData.monthlyDonations}
            icon={<DollarSign className="w-6 h-6" />}
          />
        </div>
      </main>
    </div>
  );
};

export default Index;