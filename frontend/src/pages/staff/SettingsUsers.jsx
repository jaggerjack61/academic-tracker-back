import React, { useEffect, useState, useCallback } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import TableStatusRow from '../../components/TableStatusRow';
import { Plus, Edit2, Search, ChevronLeft, ChevronRight } from 'lucide-react';

export default function SettingsUsers() {
  const [users, setUsers] = useState([]);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [pageSize, setPageSize] = useState(20);
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ full_name: '', email: '', role: '', phone_number: '', id_number: '', sex: 'male', dob: '' });
  const [roles, setRoles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const { success, error } = useToast();

  const load = useCallback(() => {
    setLoading(true);
    api.get('/settings/users/', { params: { search, page, page_size: pageSize } })
      .then(r => { setUsers(r.data.results || r.data); setTotal(r.data.total || 0); })
      .catch(() => error('Failed to load users'))
      .finally(() => setLoading(false));
  }, [search, page, pageSize, error]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { setPage(1); }, [search, pageSize]);

  const openCreate = () => {
    api.get('/lookup/roles/').then(r => {
      setRoles(r.data);
      setForm({ full_name: '', email: '', role: '', phone_number: '', id_number: '', sex: 'male', dob: '' });
      setEditing(null);
      setShowModal(true);
    });
  };

  const openEdit = (u) => {
    api.get('/lookup/roles/').then(r => {
      setRoles(r.data);
      setForm({ full_name: `${u.first_name} ${u.last_name}`, email: u.user?.email || '', role: u.role?.id || '', phone_number: u.phone_number || '', id_number: u.id_number || '', sex: u.sex || 'male', dob: u.dob || '' });
      setEditing(u.id);
      setShowModal(true);
    });
  };

  const save = async () => {
    setSaving(true);
    try {
      const roleName = roles.find(r => String(r.id) === String(form.role))?.name || '';
      const payload = {
        name: form.full_name.trim(),
        email: form.email,
        role: roleName,
        phone_number: form.phone_number,
        id_number: form.id_number,
        sex: form.sex,
        dob: form.dob,
      };
      if (editing) {
        await api.put(`/settings/users/${editing}/update/`, payload);
        success('User updated');
      } else {
        await api.post('/settings/users/create/', payload);
        success('User created (password = email)');
      }
      setShowModal(false);
      load();
    } catch (e) {
      error(e.response?.data?.error || e.response?.data?.detail || 'Failed to save user');
    } finally {
      setSaving(false);
    }
  };

  return (
    <>
      <div className="page-header">
        <h1>Users</h1>
        <p>Manage system users</p>
      </div>
      <div className="page-body">
        <div className="toolbar">
          <div className="search-box">
            <Search size={16} />
            <input placeholder="Search users..." value={search} onChange={e => setSearch(e.target.value)} />
          </div>
          <button className="btn btn-primary" onClick={openCreate}><Plus size={16}/> New User</button>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
              {users.map(u => (
                <tr key={u.id}>
                  <td>{u.first_name} {u.last_name}</td>
                  <td>{u.user?.email}</td>
                  <td>{u.role?.name || '—'}</td>
                  <td><span className={`badge ${u.is_active ? 'badge-active' : 'badge-inactive'}`}>{u.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td><button className="btn btn-sm btn-ghost" onClick={() => openEdit(u)}><Edit2 size={14}/></button></td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={users.length > 0} colSpan={5} loadingMessage="Loading users..." emptyMessage="No users found" />
            </tbody>
          </table>
        </div>

        {(() => { const totalPages = Math.ceil(total / pageSize); return totalPages > 1 ? (
          <div className="pagination">
            <button className="btn btn-sm btn-ghost" disabled={page <= 1} onClick={() => setPage(p => p - 1)}><ChevronLeft size={16}/></button>
            <span>Page {page} of {totalPages}</span>
            <button className="btn btn-sm btn-ghost" disabled={page >= totalPages} onClick={() => setPage(p => p + 1)}><ChevronRight size={16}/></button>
            <select value={pageSize} onChange={e => { setPageSize(Number(e.target.value)); setPage(1); }} style={{ marginLeft: 'auto' }}>
              {[10, 20, 50, 100].map(n => <option key={n} value={n}>{n} / page</option>)}
            </select>
          </div>
        ) : null; })()}

        {showModal && (
          <div className="modal-overlay" onClick={() => setShowModal(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>{editing ? 'Edit User' : 'New User'}</h2>
              <div className="form-group">
                <label>Full Name</label>
                <input value={form.full_name} onChange={e => setForm({ ...form, full_name: e.target.value })} placeholder="John Doe" />
              </div>
              <div className="form-group">
                <label>Email</label>
                <input type="email" value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} placeholder="john@example.com" />
              </div>
              <div className="form-group">
                <label>Role</label>
                <select value={form.role} onChange={e => setForm({ ...form, role: e.target.value })}>
                  <option value="">None</option>
                  {roles.map(r => <option key={r.id} value={r.id}>{r.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Phone Number</label>
                <input value={form.phone_number} onChange={e => setForm({ ...form, phone_number: e.target.value })} placeholder="0771234567" />
              </div>
              <div className="form-group">
                <label>ID Number</label>
                <input value={form.id_number} onChange={e => setForm({ ...form, id_number: e.target.value })} placeholder="e.g. 12345678" />
              </div>
              <div style={{ display: 'flex', gap: '1rem' }}>
                <div className="form-group" style={{ flex: 1 }}>
                  <label>Sex</label>
                  <select value={form.sex} onChange={e => setForm({ ...form, sex: e.target.value })}>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
                <div className="form-group" style={{ flex: 1 }}>
                  <label>Date of Birth</label>
                  <input type="date" value={form.dob} onChange={e => setForm({ ...form, dob: e.target.value })} />
                </div>
              </div>
              {!editing && <p style={{ fontSize: '0.75rem', color: 'var(--text-muted)', marginTop: 4 }}>Password will be set to the email address</p>}
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowModal(false)}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={save} loading={saving} loadingText={editing ? 'Saving...' : 'Creating...'} disabled={!form.full_name || !form.email}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
