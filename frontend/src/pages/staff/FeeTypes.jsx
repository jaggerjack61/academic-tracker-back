import React, { useEffect, useState, useCallback } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import TableStatusRow from '../../components/TableStatusRow';
import { Plus, Edit2, Power } from 'lucide-react';

export default function FeeTypes() {
  const [items, setItems] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ name: '', description: '' });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const { success, error } = useToast();

  const load = useCallback(() => {
    setLoading(true);
    api.get('/finance/fee-types/')
      .then(r => setItems(r.data))
      .catch(() => error('Failed to load'))
      .finally(() => setLoading(false));
  }, [error]);

  useEffect(() => { load(); }, [load]);

  const openCreate = () => { setForm({ name: '', description: '' }); setEditing(null); setShowModal(true); };
  const openEdit = (t) => { setForm({ name: t.name, description: t.description || '' }); setEditing(t.id); setShowModal(true); };

  const save = async () => {
    setSaving(true);
    try {
      if (editing) {
        await api.put(`/finance/fee-types/${editing}/update/`, form);
        success('Updated');
      } else {
        await api.post('/finance/fee-types/create/', form);
        success('Created');
      }
      setShowModal(false);
      load();
    } catch (e) { error(e.response?.data?.error || 'Failed'); }
    finally { setSaving(false); }
  };

  const toggle = async (id) => {
    try { await api.post(`/finance/fee-types/${id}/toggle/`); success('Toggled'); load(); }
    catch { error('Failed'); }
  };

  return (
    <>
      <div className="page-header"><h1>Fee Types</h1><p>Manage categories of fees</p></div>
      <div className="page-body">
        <div className="toolbar" style={{ justifyContent: 'flex-end' }}>
          <button className="btn btn-primary" onClick={openCreate}><Plus size={16} /> New Fee Type</button>
        </div>
        <div className="table-wrapper">
          <table>
            <thead><tr><th>Name</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              {items.map(t => (
                <tr key={t.id}>
                  <td>{t.name}</td>
                  <td>{t.description || '—'}</td>
                  <td><span className={`badge ${t.is_active ? 'badge-active' : 'badge-inactive'}`}>{t.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td>
                    <button className="btn btn-sm btn-ghost" onClick={() => openEdit(t)}><Edit2 size={14} /></button>
                    <button className="btn btn-sm btn-ghost" onClick={() => toggle(t.id)}><Power size={14} /></button>
                  </td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={items.length > 0} colSpan={4} loadingMessage="Loading fee types..." emptyMessage="No fee types" />
            </tbody>
          </table>
        </div>
        {showModal && (
          <div className="modal-overlay" onClick={() => setShowModal(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>{editing ? 'Edit Fee Type' : 'New Fee Type'}</h2>
              <div className="form-group">
                <label>Name</label>
                <input value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} placeholder="e.g. Tuition" />
              </div>
              <div className="form-group">
                <label>Description</label>
                <textarea value={form.description} onChange={e => setForm({ ...form, description: e.target.value })} rows={2} />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowModal(false)}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={save} loading={saving} loadingText={editing ? 'Updating...' : 'Saving...'} disabled={!form.name.trim()}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
