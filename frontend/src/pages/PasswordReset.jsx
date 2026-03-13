import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import { ArrowLeft, School, ShieldCheck } from 'lucide-react';

export default function PasswordReset() {
  const [email, setEmail] = useState('');
  const [sent, setSent] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post('/auth/password-reset/', { email });
    } catch {}
    setSent(true);
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
            <h1>Recover<br />access.</h1>
            <p>The reset flow preserves privacy — we won't disclose whether an email exists in the system.</p>
          </div>
          <div className="auth-brand-chips">
            <span className="land-chip land-chip--onboarding"><ShieldCheck size={13} /> Private by design</span>
          </div>
        </div>

        <div className="auth-form-panel">
          <div className="auth-form-top">
            <h2>Reset password</h2>
            <p>Enter your email to receive a reset link.</p>
          </div>

          {sent ? (
            <div className="auth-form">
              <div className="auth-sent-msg">
                <p>If that email exists, a reset link has been sent.</p>
              </div>
              <Link to="/login" className="land-btn land-btn--outline land-btn--full">
                <ArrowLeft size={16} /> Back to sign in
              </Link>
            </div>
          ) : (
            <>
              <form onSubmit={handleSubmit} className="auth-form">
                <div className="auth-field">
                  <label htmlFor="reset-email">Email</label>
                  <input id="reset-email" type="email" value={email} onChange={e => setEmail(e.target.value)} required autoFocus placeholder="you@school.edu" />
                </div>
                <button type="submit" className="land-btn land-btn--gold land-btn--full">
                  Send reset link
                </button>
              </form>
              <div className="auth-footer">
                <span>Remembered?</span>
                <Link to="/login">Back to sign in</Link>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
