import { Home, Users, Calendar, DollarSign, Heart } from "lucide-react";
import { Link, useLocation } from "react-router-dom";

const Navbar = () => {
  const location = useLocation();
  
  const links = [
    { to: "/", icon: Home, label: "Dashboard" },
    { to: "/contacts", icon: Users, label: "Contacts" },
    { to: "/donors", icon: Heart, label: "Donors" },
    { to: "/events", icon: Calendar, label: "Events" },
    { to: "/payments", icon: DollarSign, label: "Payments" },
  ];

  return (
    <nav className="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          <div className="flex-shrink-0">
            <h1 className="text-xl font-bold text-primary">NGO Manager</h1>
          </div>
          <div className="hidden md:block">
            <div className="flex items-center space-x-4">
              {links.map((link) => {
                const Icon = link.icon;
                const isActive = location.pathname === link.to;
                return (
                  <Link
                    key={link.to}
                    to={link.to}
                    className={`flex items-center space-x-2 px-3 py-2 rounded-md text-sm font-medium transition-colors ${
                      isActive
                        ? "bg-primary text-white"
                        : "text-gray-600 hover:bg-gray-100"
                    }`}
                  >
                    <Icon className="w-4 h-4" />
                    <span>{link.label}</span>
                  </Link>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;