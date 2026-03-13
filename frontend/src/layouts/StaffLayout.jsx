import React, { useState } from 'react';
import { Outlet, NavLink, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../AuthContext';
import {
  LayoutDashboard, Users, UserCheck, GraduationCap, BookOpen,
  Settings, Lock, LogOut, Menu, School, X, Wallet, DollarSign,
  Receipt, AlertTriangle, CreditCard, History, MessageCircle
} from 'lucide-react';

export default function StaffLayout() {
  const { role, logout, profile } = useAuth();
  const location = useLocation();
  const navigate = useNavigate();
  const [menuOpen, setMenuOpen] = useState(false);
  const isOnboardingModule = location.pathname === '/app/change-password' || location.pathname.startsWith('/app/settings/');
  const isFinanceModule = location.pathname.startsWith('/app/finance');
  const isCollabModule = location.pathname.startsWith('/app/collab');
  const moduleTone = isCollabModule ? 'collab' : isFinanceModule ? 'finance' : isOnboardingModule ? 'onboarding' : 'elearning';
  const moduleLabel = isCollabModule ? 'Collab module' : isFinanceModule ? 'Finance module' : isOnboardingModule ? 'Onboarding module' : 'Elearning module';

  const closeMenu = () => setMenuOpen(false);

  const handleLogout = async () => {
    await logout();
    closeMenu();
    navigate('/login');
  };

  return (
    <div className={`app-layout ${menuOpen ? 'nav-open' : ''}`} data-module={moduleTone}>
      <button className="sidebar-backdrop" type="button" aria-label="Close navigation" onClick={closeMenu} />
      <aside className={`sidebar ${menuOpen ? 'open' : ''}`}>
        <div className="sidebar-header">
          <div className="brand-lockup">
            <div className="brand-mark"><School size={20} /></div>
            <div className="brand-copy">
              <span className="brand-title">Academic Tracker</span>
              <span className="brand-subtitle">{moduleLabel}</span>
            </div>
          </div>
          <button className="icon-button sidebar-close" type="button" aria-label="Close menu" onClick={closeMenu}>
            <X size={18} />
          </button>
        </div>
        <nav className="sidebar-nav">
          <div className="nav-section">
            <div className="nav-section-title">Modules</div>
            <NavLink to="/modules" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
              <School size={18} /> Module Directory
            </NavLink>
          </div>

          {isCollabModule ? (
            <div className="nav-section">
              <div className="nav-section-title">Collab</div>
              <NavLink to="/app/collab" end onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <MessageCircle size={18} /> Messages
              </NavLink>
            </div>
          ) : isFinanceModule ? (
            <div className="nav-section">
              <div className="nav-section-title">Finance</div>
              <NavLink to="/app/finance" end onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <LayoutDashboard size={18} /> Dashboard
              </NavLink>
              <NavLink to="/app/finance/student-fees" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <GraduationCap size={18} /> Student Fees
              </NavLink>
              <NavLink to="/app/finance/payments" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <Receipt size={18} /> Payments
              </NavLink>
              <NavLink to="/app/finance/logs" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <History size={18} /> Logs
              </NavLink>
              <NavLink to="/app/finance/payment-plans" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <CreditCard size={18} /> Payment Plans
              </NavLink>
              <NavLink to="/app/finance/arrears" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <AlertTriangle size={18} /> Arrears
              </NavLink>
              {role === 'admin' && (
                <>
                  <NavLink to="/app/finance/fee-types" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                    <DollarSign size={18} /> Fee Types
                  </NavLink>
                  <NavLink to="/app/finance/fee-structures" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                    <Settings size={18} /> Fee Structures
                  </NavLink>
                </>
              )}
            </div>
          ) : isOnboardingModule ? (
            <div className="nav-section">
              <div className="nav-section-title">Onboarding</div>
              <NavLink to="/app/change-password" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <Lock size={18} /> Change Password
              </NavLink>
              {role === 'admin' && (
                <>
                  <NavLink to="/app/settings/users" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                    <Users size={18} /> Users
                  </NavLink>
                  <NavLink to="/app/settings/grades" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                    <Settings size={18} /> Grades
                  </NavLink>
                  <NavLink to="/app/settings/subjects" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                    <Settings size={18} /> Subjects
                  </NavLink>
                  <NavLink to="/app/settings/terms" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                    <Settings size={18} /> Terms
                  </NavLink>
                  <NavLink to="/app/settings/activity-types" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                    <Settings size={18} /> Activity Types
                  </NavLink>
                </>
              )}
            </div>
          ) : (
            <div className="nav-section">
              <div className="nav-section-title">Elearning</div>
              <NavLink to="/app" end onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <LayoutDashboard size={18} /> Dashboard
              </NavLink>
              <NavLink to="/app/students" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <GraduationCap size={18} /> Students
              </NavLink>
              <NavLink to="/app/parents" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <Users size={18} /> Parents
              </NavLink>
              {role === 'admin' && (
                <NavLink to="/app/teachers" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                  <UserCheck size={18} /> Teachers
                </NavLink>
              )}
              <NavLink to="/app/classes" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <BookOpen size={18} /> Classes
              </NavLink>
            </div>
          )}
        </nav>
        <div className="sidebar-footer">
          <button onClick={handleLogout} className="nav-link nav-button" type="button">
            <LogOut size={18} /> Logout
          </button>
          {profile && (
            <div className="sidebar-user">
              <strong>{profile.first_name} {profile.last_name}</strong>
              <span>{role}</span>
            </div>
          )}
        </div>
      </aside>
      <main className="main-content">
        <div className="mobile-topbar">
          <button className="icon-button" type="button" aria-label="Open menu" onClick={() => setMenuOpen(true)}>
            <Menu size={18} />
          </button>
          <div className="mobile-topbar-copy">
            <strong>Academic Tracker</strong>
            <span>{profile ? `${profile.first_name} ${profile.last_name} · ${moduleLabel}` : moduleLabel}</span>
          </div>
          <div className="brand-mark"><School size={18} /></div>
        </div>
        <Outlet />
      </main>
    </div>
  );
}
