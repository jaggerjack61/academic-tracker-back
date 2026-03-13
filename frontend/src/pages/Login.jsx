import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../AuthContext';
import LoadingButton from '../components/LoadingButton';
import { ArrowRight, Compass, LibraryBig, MessagesSquare, School, Wallet } from 'lucide-react';

export default function Login() {
  const { login } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      await login(email, password);
    } catch (err) {
      setError(err.response?.data?.error || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="auth-shell">
      <div className="auth-card">
        <div className="auth-brand-panel">
          <div className="land-logo">
            <School size={18} />
            <span>Academic Tracker</span>
          </div>
          <div className="auth-brand-body">
            <h1>Welcome<br />back.</h1>
            <p>Sign in to access your modules and continue where you left off.</p>
          </div>
          <div className="auth-brand-chips">
            <span className="land-chip land-chip--onboarding"><Compass size={13} /> Onboarding</span>
            <span className="land-chip land-chip--elearning"><LibraryBig size={13} /> Elearning</span>
            <span className="land-chip land-chip--finance"><Wallet size={13} /> Finance</span>
            <span className="land-chip land-chip--collab"><MessagesSquare size={13} /> Collab</span>
          </div>
        </div>

        <div className="auth-form-panel">
          <div className="auth-form-top">
            <h2>Sign in</h2>
            <p>Use your school account credentials.</p>
          </div>

          <form onSubmit={handleSubmit} className="auth-form">
            <div className="auth-field">
              <label htmlFor="email">Email</label>
              <input id="email" type="email" value={email} onChange={e => setEmail(e.target.value)} required autoFocus placeholder="you@school.edu" />
            </div>
            <div className="auth-field">
              <label htmlFor="password">Password</label>
              <input id="password" type="password" value={password} onChange={e => setPassword(e.target.value)} required placeholder="••••••••" />
            </div>
            {error && <p className="auth-error" role="alert">{error}</p>}
            <LoadingButton type="submit" className="land-btn land-btn--gold land-btn--full" loading={loading} loadingText="Signing in…">
              Sign in
              <ArrowRight size={16} />
            </LoadingButton>
          </form>

          <div className="auth-footer">
            <span>Need access?</span>
            <Link to="/password-reset">Reset password</Link>
          </div>
        </div>
      </div>
    </div>
  );
}
