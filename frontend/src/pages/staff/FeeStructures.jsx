import React, { useEffect, useState, useCallback } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import { FinanceControl } from '../../components/FinanceControls';
import TableStatusRow from '../../components/TableStatusRow';
import { Plus, Edit2, Power } from 'lucide-react';

export default function FeeStructures() {
  const [items, setItems] = useState([]);
  const [feeTypes, setFeeTypes] = useState([]);
  const [grades, setGrades] = useState([]);
  const [terms, setTerms] = useState([]);
  const [filterTerm, setFilterTerm] = useState('');
  const [filterGrade, setFilterGrade] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState(null);
  const [form, setForm] = useState({ fee_type: '', grades: [], grade: '', term: '', amount: '' });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const { success, error } = useToast();

  useEffect(() => {
    api.get('/finance/fee-types/').then(r => setFeeTypes(r.data.filter(ft => ft.is_active)));
    api.get('/lookup/grades/').then(r => setGrades(r.data));
    api.get('/lookup/terms/').then(r => setTerms(r.data));
  }, []);

  const load = useCallback(() => {
    setLoading(true);
    const params = {};
    if (filterTerm) params.term = filterTerm;
    if (filterGrade) params.grade = filterGrade;
    api.get('/finance/fee-structures/', { params })
      .then(r => setItems(r.data))
      .catch(() => error('Failed to load'))
      .finally(() => setLoading(false));
  }, [filterTerm, filterGrade, error]);

  useEffect(() => { load(); }, [load]);

  const openCreate = () => {
    const termId = filterTerm || (terms.find(t => t.is_active)?.id || '');
    setForm({ fee_type: '', grades: [], grade: '', term: termId, amount: '' });
    setEditing(null);
    setShowModal(true);
  };

  const openEdit = (fs) => {
    setForm({ fee_type: fs.fee_type_id, grades: [], grade: fs.grade_id, term: fs.term_id, amount: fs.amount });
    setEditing(fs.id);
    setShowModal(true);
  };

  const toggleGrade = (gid) => {
    setForm(prev => {
      const set = new Set(prev.grades);
      if (set.has(gid)) set.delete(gid); else set.add(gid);
      return { ...prev, grades: [...set] };
    });
  };

  const save = async () => {
    setSaving(true);
    try {
      if (editing) {
        await api.put(`/finance/fee-structures/${editing}/update/`, { fee_type: form.fee_type, grade: form.grade, term: form.term, amount: form.amount });
        success('Updated');
      } else {
        const res = await api.post('/finance/fee-structures/create/', { fee_type: form.fee_type, grades: form.grades, term: form.term, amount: form.amount });
        success(`Created ${res.data.length} fee structure(s)`);
      }
      setShowModal(false);
      load();
    } catch (e) { error(e.response?.data?.error || 'Failed'); }
    finally { setSaving(false); }
  };

  const toggle = async (id) => {
    try { await api.post(`/finance/fee-structures/${id}/toggle/`); success('Toggled'); load(); }
    catch { error('Failed'); }
  };

  return (
    <>
      <div className="page-header"><h1>Fee Structures</h1><p>Define fees per grade and term</p></div>
      <div className="page-body">
        <div className="toolbar finance-toolbar">
          <FinanceControl label="Term" minWidth="12rem">
            <select value={filterTerm} onChange={e => setFilterTerm(e.target.value)}>
              <option value="">All terms</option>
              {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
            </select>
          </FinanceControl>
          <FinanceControl label="Grade" minWidth="12rem">
            <select value={filterGrade} onChange={e => setFilterGrade(e.target.value)}>
              <option value="">All grades</option>
              {grades.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
            </select>
          </FinanceControl>
          <div className="finance-toolbar-actions">
            <button className="btn btn-primary" onClick={openCreate}><Plus size={16} /> New Structure</button>
          </div>
        </div>
        <div className="table-wrapper">
          <table>
            <thead><tr><th>Fee Type</th><th>Grade</th><th>Term</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              {items.map(fs => (
                <tr key={fs.id}>
                  <td>{fs.fee_type_name}</td>
                  <td>{fs.grade_name}</td>
                  <td>{fs.term_name}</td>
                  <td>${parseFloat(fs.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                  <td><span className={`badge ${fs.is_active ? 'badge-active' : 'badge-inactive'}`}>{fs.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td>
                    <button className="btn btn-sm btn-ghost" onClick={() => openEdit(fs)}><Edit2 size={14} /></button>
                    <button className="btn btn-sm btn-ghost" onClick={() => toggle(fs.id)}><Power size={14} /></button>
                  </td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={items.length > 0} colSpan={6} loadingMessage="Loading fee structures..." emptyMessage="No fee structures" />
            </tbody>
          </table>
        </div>
        {showModal && (
          <div className="modal-overlay" onClick={() => setShowModal(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>{editing ? 'Edit Fee Structure' : 'New Fee Structure'}</h2>
              <div className="form-group">
                <label>Fee Type</label>
                <select value={form.fee_type} onChange={e => setForm({ ...form, fee_type: e.target.value })}>
                  <option value="">Select fee type</option>
                  {feeTypes.map(ft => <option key={ft.id} value={ft.id}>{ft.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Grade{!editing && 's'}</label>
                {editing ? (
                  <select value={form.grade} onChange={e => setForm({ ...form, grade: e.target.value })}>
                    <option value="">Select grade</option>
                    {grades.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
                  </select>
                ) : (
                  <div className="checkbox-group">
                    {grades.map(g => (
                      <label key={g.id} className="checkbox-label">
                        <input type="checkbox" checked={form.grades.includes(g.id)} onChange={() => toggleGrade(g.id)} />
                        {g.name}
                      </label>
                    ))}
                  </div>
                )}
              </div>
              <div className="form-group">
                <label>Term</label>
                <select value={form.term} onChange={e => setForm({ ...form, term: e.target.value })}>
                  <option value="">Select term</option>
                  {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" value={form.amount} onChange={e => setForm({ ...form, amount: e.target.value })} placeholder="0.00" />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowModal(false)}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={save} loading={saving} loadingText={editing ? 'Updating...' : 'Saving...'} disabled={!form.fee_type || (!editing && form.grades.length === 0) || (editing && !form.grade) || !form.term || !form.amount}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
