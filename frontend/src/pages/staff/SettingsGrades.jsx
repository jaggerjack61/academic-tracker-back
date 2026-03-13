import React, { useEffect, useState, useCallback } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { Plus, Edit2, Power } from 'lucide-react';

export default function SettingsGrades() {
  const [items, setItems] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [name, setName] = useState('');
  const { success, error } = useToast();

  const load = useCallback(() => {
    api.get('/settings/grades/').then(r => setItems(r.data)).catch(() => error('Failed to load'));
  }, [error]);

  useEffect(() => { load(); }, [load]);

  const openCreate = () => { setName(''); setEditing(null); setShowModal(true); };
  const openEdit = (item) => { setName(item.name); setEditing(item.id); setShowModal(true); };

  const save = async () => {
    try {
      if (editing) {
        await api.put(`/settings/grades/${editing}/update/`, { name });
        success('Updated');
      } else {
        await api.post('/settings/grades/create/', { name });
        success('Created');
      }
      setShowModal(false);
      load();
    } catch (e) { error(e.response?.data?.error || e.response?.data?.detail || 'Failed'); }
  };

  const toggle = async (id) => {
    try {
      await api.post(`/settings/grades/${id}/toggle/`);
      success('Toggled');
      load();
    } catch { error('Failed'); }
  };

  return (
    <>
      <div className="page-header"><h1>Grades</h1><p>Manage grade levels</p></div>
      <div className="page-body">
        <div className="toolbar" style={{ justifyContent: 'flex-end' }}>
          <button className="btn btn-primary" onClick={openCreate}><Plus size={16}/> New Grade</button>
        </div>
        <div className="table-wrapper">
          <table>
            <thead><tr><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              {items.map(g => (
                <tr key={g.id}>
                  <td>{g.name}</td>
                  <td><span className={`badge ${g.is_active ? 'badge-active' : 'badge-inactive'}`}>{g.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td>
                    <button className="btn btn-sm btn-ghost" onClick={() => openEdit(g)}><Edit2 size={14}/></button>
                    <button className="btn btn-sm btn-ghost" onClick={() => toggle(g.id)}><Power size={14}/></button>
                  </td>
                </tr>
              ))}
              {items.length === 0 && <tr><td colSpan={3} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No grades</td></tr>}
            </tbody>
          </table>
        </div>
        {showModal && (
          <div className="modal-overlay" onClick={() => setShowModal(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>{editing ? 'Edit Grade' : 'New Grade'}</h2>
              <div className="form-group">
                <label>Name</label>
                <input value={name} onChange={e => setName(e.target.value)} placeholder="e.g. Grade 1" />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowModal(false)}>Cancel</button>
                <button className="btn btn-primary" onClick={save} disabled={!name.trim()}>Save</button>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
