import React, { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import TableStatusRow from '../../components/TableStatusRow';
import { Search, Plus, Power, ChevronLeft, ChevronRight, Edit2 } from 'lucide-react';

export default function Classes() {
  const [classes, setClasses] = useState([]);
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ name: '', grade: '', subject: '', teacher: '', description: '' });
  const [lookups, setLookups] = useState({ grades: [], subjects: [], teachers: [] });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const { success, error } = useToast();
  const [pageSize, setPageSize] = useState(20);

  const load = useCallback(() => {
    setLoading(true);
    api.get('/classes/', { params: { page, search, page_size: pageSize } })
      .then(r => { setClasses(r.data.results); setTotal(r.data.total || r.data.count || 0); })
      .catch(() => error('Failed to load classes'))
      .finally(() => setLoading(false));
  }, [page, search, pageSize, error]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { setPage(1); }, [search]);

  const openCreate = () => {
    Promise.all([
      api.get('/lookup/grades/'),
      api.get('/lookup/subjects/'),
      api.get('/lookup/teachers/'),
    ]).then(([g, s, t]) => {
      setLookups({ grades: g.data, subjects: s.data, teachers: t.data });
      setForm({ name: '', grade: '', subject: '', teacher: '', description: '' });
      setEditing(null);
      setShowModal(true);
    });
  };

  const openEdit = (cls) => {
    Promise.all([
      api.get('/lookup/grades/'),
      api.get('/lookup/subjects/'),
      api.get('/lookup/teachers/'),
    ]).then(([g, s, t]) => {
      setLookups({ grades: g.data, subjects: s.data, teachers: t.data });
      setForm({ name: cls.name, grade: cls.grade_id, subject: cls.subject_id, teacher: cls.teacher?.id || '', description: cls.description || '' });
      setEditing(cls.id);
      setShowModal(true);
    });
  };

  const save = async () => {
    setSaving(true);
    try {
      const payload = { name: form.name, grade_id: form.grade, subject_id: form.subject, teacher_id: form.teacher || null, description: form.description };
      if (editing) {
        await api.put(`/classes/${editing}/update/`, payload);
        success('Class updated');
      } else {
        await api.post('/classes/create/', payload);
        success('Class created');
      }
      setShowModal(false);
      load();
    } catch (e) {
      error(e.response?.data?.error || e.response?.data?.detail || 'Failed to save class');
    } finally {
      setSaving(false);
    }
  };

  const toggleStatus = async (id) => {
    try {
      await api.post(`/classes/${id}/toggle-status/`);
      success('Status toggled');
      load();
    } catch { error('Failed to toggle'); }
  };

  const totalPages = Math.ceil(total / pageSize);

  return (
    <>
      <div className="page-header">
        <h1>Classes</h1>
        <p>{total} total classes</p>
      </div>
      <div className="page-body">
        <div className="toolbar">
          <div className="search-box">
            <Search size={16} />
            <input placeholder="Search classes..." value={search} onChange={e => setSearch(e.target.value)} />
          </div>
          <button className="btn btn-primary" onClick={openCreate}><Plus size={16}/> New Class</button>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Grade</th>
                <th>Subject</th>
                <th>Teacher</th>
                <th>Students</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {classes.map(c => (
                <tr key={c.id}>
                  <td><Link to={`/app/classes/${c.id}`}>{c.name}</Link></td>
                  <td>{c.grade_name}</td>
                  <td>{c.subject_name}</td>
                  <td>{c.teacher?.full_name || '—'}</td>
                  <td>{c.student_count}</td>
                  <td><span className={`badge ${c.is_active ? 'badge-active' : 'badge-inactive'}`}>{c.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td>
                    <button className="btn btn-sm btn-ghost" onClick={() => openEdit(c)}><Edit2 size={14}/></button>
                    <button className="btn btn-sm btn-ghost" onClick={() => toggleStatus(c.id)}><Power size={14}/></button>
                  </td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={classes.length > 0} colSpan={7} loadingMessage="Loading classes..." emptyMessage="No classes found" />
            </tbody>
          </table>
        </div>

        {totalPages > 1 && (
          <div className="pagination">
            <button className="btn btn-sm btn-ghost" disabled={page <= 1} onClick={() => setPage(p => p - 1)}><ChevronLeft size={16}/></button>
            <span>Page {page} of {totalPages}</span>
            <button className="btn btn-sm btn-ghost" disabled={page >= totalPages} onClick={() => setPage(p => p + 1)}><ChevronRight size={16}/></button>
            <select value={pageSize} onChange={e => { setPageSize(Number(e.target.value)); setPage(1); }} style={{ marginLeft: 'auto' }}>
              {[10, 20, 50, 100].map(n => <option key={n} value={n}>{n} / page</option>)}
            </select>
          </div>
        )}
        {showModal && (
          <div className="modal-overlay" onClick={() => setShowModal(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>{editing ? 'Edit Class' : 'New Class'}</h2>
              <div className="form-group">
                <label>Name</label>
                <input value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} placeholder="e.g. Grade 1 Art" />
              </div>
              <div className="form-group">
                <label>Grade</label>
                <select value={form.grade} onChange={e => setForm({ ...form, grade: e.target.value })}>
                  <option value="">Choose...</option>
                  {lookups.grades.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Subject</label>
                <select value={form.subject} onChange={e => setForm({ ...form, subject: e.target.value })}>
                  <option value="">Choose...</option>
                  {lookups.subjects.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Teacher (optional)</label>
                <select value={form.teacher} onChange={e => setForm({ ...form, teacher: e.target.value })}>
                  <option value="">None</option>
                  {lookups.teachers.map(t => <option key={t.id} value={t.id}>{t.full_name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Description (optional)</label>
                <input value={form.description} onChange={e => setForm({ ...form, description: e.target.value })} placeholder="Optional description" />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowModal(false)}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={save} loading={saving} loadingText={editing ? 'Saving...' : 'Creating...'} disabled={!form.name || !form.grade || !form.subject}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
