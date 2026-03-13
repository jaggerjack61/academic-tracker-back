import React, { useState } from 'react';
import { Outlet, NavLink, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../AuthContext';
import { BookOpenCheck, KeyRound, LayoutDashboard, LogOut, Menu, MessageCircle, School, X } from 'lucide-react';

export default function StudentLayout() {
  const { logout, profile } = useAuth();
  const location = useLocation();
  const navigate = useNavigate();
  const [menuOpen, setMenuOpen] = useState(false);
  const isOnboardingModule = location.pathname === '/student/change-password';
  const isCollabModule = location.pathname.startsWith('/student/collab');
  const moduleTone = isCollabModule ? 'collab' : isOnboardingModule ? 'onboarding' : 'elearning';
  const moduleLabel = isCollabModule ? 'Collab module' : isOnboardingModule ? 'Onboarding module' : 'Elearning module';

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
              <NavLink to="/student/collab" end onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <MessageCircle size={18} /> Messages
              </NavLink>
            </div>
          ) : isOnboardingModule ? (
            <div className="nav-section">
              <div className="nav-section-title">Onboarding</div>
              <NavLink to="/student/change-password" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <KeyRound size={18} /> Change Password
              </NavLink>
            </div>
          ) : (
            <div className="nav-section">
              <div className="nav-section-title">Elearning</div>
              <NavLink to="/student" end onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <LayoutDashboard size={18} /> My Dashboard
              </NavLink>
              <NavLink to="/student/assignments" onClick={closeMenu} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                <BookOpenCheck size={18} /> Assignments
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
              <span>Student</span>
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
