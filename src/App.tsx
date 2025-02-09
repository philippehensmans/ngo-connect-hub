
import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { GDPRProvider } from "./contexts/GDPRContext";
import { GDPRBanner } from "./components/GDPRBanner";
import Index from "./pages/Index";
import Contacts from "./pages/Contacts";
import Donors from "./pages/Donors";
import Payments from "./pages/Payments";
import NotFound from "./pages/NotFound";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <GDPRProvider>
        <Toaster />
        <Sonner />
        <BrowserRouter>
          <Routes>
            <Route path="/" element={<Index />} />
            <Route path="/contacts" element={<Contacts />} />
            <Route path="/donors" element={<Donors />} />
            <Route path="/payments" element={<Payments />} />
            <Route path="*" element={<NotFound />} />
          </Routes>
        </BrowserRouter>
        <GDPRBanner />
      </GDPRProvider>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
