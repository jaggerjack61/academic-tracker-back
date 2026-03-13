import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ArrowRight, LogOut, School } from 'lucide-react';
import { useAuth } from '../AuthContext';
import { getModulesForRole } from '../moduleCatalog';

export default function Modules() {
  const navigate = useNavigate();
  const { logout, profile, role } = useAuth();
  const modules = getModulesForRole(role);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="module-shell">
      <div className="module-atmosphere" aria-hidden="true">
        <span className="module-glow module-glow-left" />
        <span className="module-glow module-glow-right" />
      </div>

      <div className="module-board">
        <header className="module-topbar">
          <div className="brand-lockup">
            <div className="brand-mark"><School size={20} /></div>
            <div className="brand-copy">
              <span className="brand-title">Academic Tracker</span>
              <span className="brand-subtitle">Module directory</span>
            </div>
          </div>

          <div className="module-topbar-actions">
            <div className="module-user-chip">
              <strong>{profile ? `${profile.first_name} ${profile.last_name}` : 'Signed in user'}</strong>
              <span>{role === 'student' ? 'Student access' : 'Staff access'}</span>
            </div>
            <button type="button" className="btn btn-ghost btn-sm" onClick={handleLogout}>
              <LogOut size={16} /> Logout
            </button>
          </div>
        </header>

        <section className="module-grid module-grid-launcher" aria-label="Available modules">
          {modules.map(moduleItem => {
            const Icon = moduleItem.icon;

            return (
              <article key={moduleItem.key} className={`module-card ${moduleItem.toneClass}`}>
                <div className="module-card-header">
                  <div className="module-icon-wrap">
                    <Icon size={18} />
                  </div>
                  <span className="module-card-tag">Module</span>
                </div>

                <div className="module-card-copy">
                  <h2>{moduleItem.title}</h2>
                  <p>{moduleItem.summary}</p>
                </div>

                <div className="module-card-footer">
                  <Link to={moduleItem.destination} className="btn btn-primary btn-sm">
                    {moduleItem.cta} <ArrowRight size={15} />
                  </Link>
                </div>
              </article>
            );
          })}
        </section>
      </div>
    </div>
  );
}