import React, { useEffect, useState, useCallback } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { Plus, Edit2, Power } from 'lucide-react';

export default function SettingsActivityTypes() {
  const [items, setItems] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ name: '', description: '', type: 'value', image: null, true_value: '', false_value: '' });
  const { success, error } = useToast();

  const load = useCallback(() => {
    api.get('/settings/activity-types/').then(r => setItems(r.data)).catch(() => error('Failed to load'));
  }, [error]);

  useEffect(() => { load(); }, [load]);

  const openCreate = () => { setForm({ name: '', description: '', type: 'value', image: null, true_value: '', false_value: '' }); setEditing(null); setShowModal(true); };
  const openEdit = (at) => { setForm({ name: at.name, description: at.description || '', type: at.type, image: null, true_value: at.true_value || '', false_value: at.false_value || '' }); setEditing(at.id); setShowModal(true); };

  const save = async () => {
    try {
      const fd = new FormData();
      fd.append('name', form.name);
      fd.append('description', form.description);
      fd.append('type', form.type);
      if (form.image) fd.append('image', form.image);
      if (form.type === 'boolean') {
        fd.append('true_value', form.true_value);
        fd.append('false_value', form.false_value);
      }
      if (editing) {
        await api.put(`/settings/activity-types/${editing}/update/`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        success('Updated');
      } else {
        await api.post('/settings/activity-types/create/', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        success('Created');
      }
      setShowModal(false);
      load();
    } catch (e) { error(e.response?.data?.error || e.response?.data?.detail || 'Failed'); }
  };

  const toggle = async (id) => {
    try {
      await api.post(`/settings/activity-types/${id}/toggle/`);
      success('Toggled');
      load();
    } catch { error('Failed'); }
  };

  return (
    <>
      <div className="page-header"><h1>Activity Types</h1><p>Configure activity types</p></div>
      <div className="page-body">
        <div className="toolbar" style={{ justifyContent: 'flex-end' }}>
          <button className="btn btn-primary" onClick={openCreate}><Plus size={16}/> New Type</button>
        </div>
        <div className="table-wrapper">
          <table>
            <thead><tr><th>Name</th><th>Kind</th><th>Labels</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              {items.map(at => (
                <tr key={at.id}>
                  <td style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                    {at.image && <img src={at.image} alt="" style={{ width: 24, height: 24, borderRadius: 4, objectFit: 'cover' }} />}
                    {at.name}
                  </td>
                  <td><span className="badge">{at.type}</span></td>
                  <td>{at.type === 'boolean' ? `${at.true_value || 'Yes'} / ${at.false_value || 'No'}` : '—'}</td>
                  <td><span className={`badge ${at.is_active ? 'badge-active' : 'badge-inactive'}`}>{at.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td>
                    <button className="btn btn-sm btn-ghost" onClick={() => openEdit(at)}><Edit2 size={14}/></button>
                    <button className="btn btn-sm btn-ghost" onClick={() => toggle(at.id)}><Power size={14}/></button>
                  </td>
                </tr>
              ))}
              {items.length === 0 && <tr><td colSpan={5} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No activity types</td></tr>}
            </tbody>
          </table>
        </div>
        {showModal && (
          <div className="modal-overlay" onClick={() => setShowModal(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>{editing ? 'Edit Activity Type' : 'New Activity Type'}</h2>
              <div className="form-group">
                <label>Name</label>
                <input value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} placeholder="e.g. Homework" />
              </div>
              <div className="form-group">
                <label>Description</label>
                <textarea value={form.description} onChange={e => setForm({ ...form, description: e.target.value })} placeholder="Optional description" rows={2} />
              </div>
              <div className="form-group">
                <label>Type</label>
                <select value={form.type} onChange={e => setForm({ ...form, type: e.target.value })} disabled={!!editing}>
                  <option value="value">Value (numeric)</option>
                  <option value="boolean">Boolean (yes/no)</option>
                  <option value="static">Static (info only)</option>
                </select>
              </div>
              {form.type === 'boolean' && (
                <>
                  <div className="form-group">
                    <label>True Label</label>
                    <input value={form.true_value} onChange={e => setForm({ ...form, true_value: e.target.value })} placeholder="e.g. Present" />
                  </div>
                  <div className="form-group">
                    <label>False Label</label>
                    <input value={form.false_value} onChange={e => setForm({ ...form, false_value: e.target.value })} placeholder="e.g. Absent" />
                  </div>
                </>
              )}
              <div className="form-group">
                <label>Image{!editing && ' (required)'}</label>
                <input type="file" accept="image/*" onChange={e => setForm({ ...form, image: e.target.files[0] || null })} />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowModal(false)}>Cancel</button>
                <button className="btn btn-primary" onClick={save} disabled={!form.name.trim() || (!editing && !form.image)}>Save</button>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
