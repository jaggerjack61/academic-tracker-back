import React, { useEffect, useState, useCallback } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import TableStatusRow from '../../components/TableStatusRow';
import { Plus, Edit2, Power } from 'lucide-react';

export default function SettingsTerms() {
  const [items, setItems] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ name: '', start: '', end: '' });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const { success, error } = useToast();

  const load = useCallback(() => {
    setLoading(true);
    api.get('/settings/terms/').then(r => setItems(r.data)).catch(() => error('Failed to load')).finally(() => setLoading(false));
  }, [error]);

  useEffect(() => { load(); }, [load]);

  const openCreate = () => { setForm({ name: '', start: '', end: '' }); setEditing(null); setShowModal(true); };
  const openEdit = (t) => { setForm({ name: t.name, start: t.start || '', end: t.end || '' }); setEditing(t.id); setShowModal(true); };

  const save = async () => {
    setSaving(true);
    try {
      const payload = { name: form.name, start: form.start || null, end: form.end || null };
      if (editing) {
        await api.put(`/settings/terms/${editing}/update/`, payload);
        success('Updated');
      } else {
        await api.post('/settings/terms/create/', payload);
        success('Created');
      }
      setShowModal(false);
      load();
    } catch (e) { error(e.response?.data?.error || e.response?.data?.detail || 'Failed'); }
    finally { setSaving(false); }
  };

  const toggle = async (id) => {
    try {
      await api.post(`/settings/terms/${id}/toggle/`);
      success('Toggled');
      load();
    } catch { error('Failed'); }
  };

  return (
    <>
      <div className="page-header"><h1>Terms</h1><p>Manage academic terms</p></div>
      <div className="page-body">
        <div className="toolbar" style={{ justifyContent: 'flex-end' }}>
          <button className="btn btn-primary" onClick={openCreate}><Plus size={16}/> New Term</button>
        </div>
        <div className="table-wrapper">
          <table>
            <thead><tr><th>Name</th><th>Start</th><th>End</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              {items.map(t => (
                <tr key={t.id}>
                  <td>{t.name}</td>
                  <td>{t.start || '—'}</td>
                  <td>{t.end || '—'}</td>
                  <td><span className={`badge ${t.is_active ? 'badge-active' : 'badge-inactive'}`}>{t.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td>
                    <button className="btn btn-sm btn-ghost" onClick={() => openEdit(t)}><Edit2 size={14}/></button>
                    <button className="btn btn-sm btn-ghost" onClick={() => toggle(t.id)}><Power size={14}/></button>
                  </td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={items.length > 0} colSpan={5} loadingMessage="Loading terms..." emptyMessage="No terms" />
            </tbody>
          </table>
        </div>
        {showModal && (
          <div className="modal-overlay" onClick={() => setShowModal(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>{editing ? 'Edit Term' : 'New Term'}</h2>
              <div className="form-group">
                <label>Name</label>
                <input value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} placeholder="e.g. 2024 Term 1" />
              </div>
              <div className="form-group">
                <label>Start Date</label>
                <input type="date" value={form.start} onChange={e => setForm({ ...form, start: e.target.value })} />
              </div>
              <div className="form-group">
                <label>End Date</label>
                <input type="date" value={form.end} onChange={e => setForm({ ...form, end: e.target.value })} />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowModal(false)}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={save} loading={saving} loadingText={editing ? 'Saving...' : 'Creating...'} disabled={!form.name.trim()}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
