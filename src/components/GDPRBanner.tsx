
import { useGDPR } from "@/contexts/GDPRContext";
import { Button } from "@/components/ui/button";
import { X } from "lucide-react";

export function GDPRBanner() {
  const { hasConsent, setConsent } = useGDPR();

  if (hasConsent) {
    return null;
  }

  return (
    <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg z-50">
      <div className="container mx-auto flex items-center justify-between gap-4">
        <div className="flex-1">
          <p className="text-sm text-gray-600">
            We use cookies and similar technologies to help personalize content, tailor and measure ads, and provide a better experience. 
            By clicking accept, you agree to this use as described in our Privacy Policy.
          </p>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="outline" onClick={() => setConsent(false)}>
            Decline
          </Button>
          <Button onClick={() => setConsent(true)}>
            Accept
          </Button>
        </div>
      </div>
    </div>
  );
}
