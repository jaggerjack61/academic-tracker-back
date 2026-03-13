import React, { useState } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { Lock } from 'lucide-react';

export default function ChangePassword() {
  const [form, setForm] = useState({ old_password: '', new_password: '', confirm: '' });
  const [loading, setLoading] = useState(false);
  const { success, error } = useToast();

  const submit = async (e) => {
    e.preventDefault();
    if (form.new_password !== form.confirm) {
      error('Passwords do not match');
      return;
    }
    setLoading(true);
    try {
      await api.post('/auth/change-password/', { old_password: form.old_password, new_password: form.new_password });
      success('Password changed successfully');
      setForm({ old_password: '', new_password: '', confirm: '' });
    } catch (err) {
      error(err.response?.data?.error || err.response?.data?.detail || 'Failed to change password');
    }
    setLoading(false);
  };

  return (
    <>
      <div className="page-header">
        <h1>Change Password</h1>
      </div>
      <div className="page-body">
        <div className="card form-card">
          <div className="card-body">
            <form onSubmit={submit}>
              <div className="form-group">
                <label htmlFor="current-password">Current Password</label>
                <input type="password" value={form.old_password} onChange={e => setForm({ ...form, old_password: e.target.value })} required />
              </div>
              <div className="form-group">
                <label htmlFor="new-password">New Password</label>
                <input type="password" value={form.new_password} onChange={e => setForm({ ...form, new_password: e.target.value })} required minLength={5} />
              </div>
              <div className="form-group">
                <label htmlFor="confirm-password">Confirm New Password</label>
                <input type="password" value={form.confirm} onChange={e => setForm({ ...form, confirm: e.target.value })} required />
              </div>
              <button className="btn btn-primary btn-full" type="submit" disabled={loading}>
                <Lock size={16} />
                {loading ? 'Changing...' : 'Change Password'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </>
  );
}
