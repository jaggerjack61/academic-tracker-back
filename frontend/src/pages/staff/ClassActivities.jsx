import React, { useEffect, useState, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import TableStatusRow from '../../components/TableStatusRow';
import { ArrowLeft, Plus, ClipboardList, FileText, CheckCircle, File } from 'lucide-react';

export default function ClassActivities() {
  const { id } = useParams();
  const [cls, setCls] = useState(null);
  const [activities, setActivities] = useState([]);
  const [showCreate, setShowCreate] = useState(false);
  const [form, setForm] = useState({ name: '', activity_type: '', term: '', total: '', note: '', file: null });
  const [lookups, setLookups] = useState({ activity_types: [], terms: [] });
  const [tab, setTab] = useState('all');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const { success, error } = useToast();

  const load = useCallback(() => {
    setLoading(true);
    Promise.all([
      api.get(`/classes/${id}/`),
      api.get(`/courses/${id}/activities/`),
    ]).then(([classResponse, activitiesResponse]) => {
      setCls(classResponse.data);
      const all = (activitiesResponse.data.all || []).map(a => ({
        ...a,
        type_kind: a.activity_type_data?.type || 'value',
        type_name: a.activity_type_data?.name || '',
      }));
      setActivities(all);
    }).catch(() => error('Failed to load activities'))
      .finally(() => setLoading(false));
  }, [error, id]);

  useEffect(() => { load(); }, [load]);

  const openCreate = () => {
    Promise.all([
      api.get('/lookup/activity-types/'),
      api.get('/lookup/terms/'),
    ]).then(([at, t]) => {
      setLookups({ activity_types: at.data, terms: t.data });
      setForm({ name: '', activity_type: '', total: '', note: '', due_date: '', file: null });
      setShowCreate(true);
    });
  };

  const create = async () => {
    setSaving(true);
    try {
      const fd = new FormData();
      fd.append('course_id', id);
      fd.append('name', form.name);
      fd.append('activity_type_id', form.activity_type);
      if (form.total) fd.append('total', form.total);
      if (form.note) fd.append('note', form.note);
      if (form.due_date) fd.append('due_date', form.due_date);
      if (form.file) fd.append('file', form.file);
      await api.post('/activities/create/', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      success('Activity created');
      setShowCreate(false);
      load();
    } catch (e) {
      error(e.response?.data?.error || e.response?.data?.detail || 'Failed to create activity');
    } finally {
      setSaving(false);
    }
  };

  if (!cls) return <div className="loading"><div className="spinner" /></div>;

  const selectedType = lookups.activity_types.find(at => at.id === parseInt(form.activity_type));
  const filtered = tab === 'all' ? activities : activities.filter(a => a.type_kind === tab);

  const typeIcon = (kind) => {
    if (kind === 'value') return <FileText size={14} />;
    if (kind === 'boolean') return <CheckCircle size={14} />;
    return <File size={14} />;
  };

  const tabs = [
    { key: 'all', label: 'All' },
    { key: 'value', label: 'Value' },
    { key: 'boolean', label: 'Boolean' },
    { key: 'static', label: 'Static' },
  ];

  return (
    <>
      <div className="page-header">
        <Link to={`/app/classes/${id}`} className="btn btn-ghost"><ArrowLeft size={16}/> Back to {cls.name}</Link>
        <h1>Activities</h1>
      </div>
      <div className="page-body">
        <div className="toolbar">
          <div className="tabs" style={{ marginBottom: 0 }}>
            {tabs.map(t => (
              <button key={t.key} className={`tab ${tab === t.key ? 'active' : ''}`} onClick={() => setTab(t.key)}>{t.label}</button>
            ))}
          </div>
          <button className="btn btn-primary" onClick={openCreate}><Plus size={16}/> New Activity</button>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Term</th>
                <th>Total</th>
                <th>File</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map(a => (
                <tr key={a.id}>
                  <td>{a.name}</td>
                  <td><span style={{ display: 'flex', alignItems: 'center', gap: 4 }}>{typeIcon(a.type_kind)} {a.type_name}</span></td>
                  <td>{a.term_name}</td>
                  <td>{a.total || '—'}</td>
                  <td>{a.file ? <a href={a.file} target="_blank" rel="noreferrer" className="link">Download</a> : '—'}</td>
                  <td>
                    {a.type_kind !== 'static' && (
                      <Link to={`/app/classes/${id}/activities/${a.id}/log`} className="btn btn-sm btn-secondary">
                        <ClipboardList size={14}/> Log
                      </Link>
                    )}
                  </td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={filtered.length > 0} colSpan={6} loadingMessage="Loading activities..." emptyMessage="No activities" />
            </tbody>
          </table>
        </div>

        {showCreate && (
          <div className="modal-overlay" onClick={() => setShowCreate(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>New Activity</h2>
              <div className="form-group">
                <label>Name</label>
                <input value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} placeholder="e.g. Week 1 Homework" />
              </div>
              <div className="form-group">
                <label>Activity Type</label>
                <select value={form.activity_type} onChange={e => setForm({ ...form, activity_type: e.target.value })}>
                  <option value="">Choose...</option>
                  {lookups.activity_types.map(at => <option key={at.id} value={at.id}>{at.name} ({at.type})</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Due Date (optional)</label>
                <input type="date" value={form.due_date} onChange={e => setForm({ ...form, due_date: e.target.value })} />
              </div>
              {selectedType?.type === 'value' && (
                <div className="form-group">
                  <label>Total Marks</label>
                  <input type="number" value={form.total} onChange={e => setForm({ ...form, total: e.target.value })} />
                </div>
              )}
              <div className="form-group">
                <label>Note (optional)</label>
                <textarea value={form.note} onChange={e => setForm({ ...form, note: e.target.value })} rows={2} />
              </div>
              <div className="form-group">
                <label>File (optional)</label>
                <input type="file" onChange={e => setForm({ ...form, file: e.target.files[0] || null })} />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowCreate(false)}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={create} loading={saving} loadingText="Creating..." disabled={!form.name || !form.activity_type}>Create</LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
