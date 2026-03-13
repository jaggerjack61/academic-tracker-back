import React, { useEffect, useState, useCallback } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import { FinanceControl, SearchableSelectField } from '../../components/FinanceControls';
import TableStatusRow from '../../components/TableStatusRow';
import { Search, ChevronLeft, ChevronRight, Plus, Edit2, Trash2 } from 'lucide-react';


const EMPTY_FORM = {
  student: '',
  term: '',
  fee_type: '',
  amount: '',
  payment_date: '',
  method: 'cash',
  reference: '',
  note: '',
};


function formatCurrency(value) {
  return `$${parseFloat(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
}

export default function Payments() {
  const [payments, setPayments] = useState([]);
  const [terms, setTerms] = useState([]);
  const [students, setStudents] = useState([]);
  const [selectedTerm, setSelectedTerm] = useState('');
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState('');
  const [pageSize, setPageSize] = useState(20);
  const [showModal, setShowModal] = useState(false);
  const [feeTypes, setFeeTypes] = useState([]);
  const [form, setForm] = useState(EMPTY_FORM);
  const [editingPayment, setEditingPayment] = useState(null);
  const [saving, setSaving] = useState(false);
  const [loading, setLoading] = useState(true);
  const { success, error } = useToast();

  useEffect(() => {
    api.get('/lookup/terms/').then(r => setTerms(r.data));
    api.get('/lookup/students/').then(r => setStudents(r.data));
    api.get('/finance/fee-types/').then(r => setFeeTypes(r.data));
  }, []);

  const load = useCallback(() => {
    setLoading(true);
    const params = { page, search, page_size: pageSize };
    if (selectedTerm) params.term = selectedTerm;
    api.get('/finance/payments/', { params })
      .then(r => { setPayments(r.data.results); setTotal(r.data.total || 0); })
      .catch(() => error('Failed to load payments'))
      .finally(() => setLoading(false));
  }, [page, search, selectedTerm, pageSize, error]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { setPage(1); }, [search, selectedTerm]);

  const save = async () => {
    setSaving(true);
    try {
      if (editingPayment) {
        await api.put(`/finance/payments/${editingPayment.id}/update/`, form);
        success('Payment updated');
      } else {
        await api.post('/finance/payments/create/', form);
        success('Payment recorded');
      }
      closeModal();
      load();
    } catch (e) {
      error(e.response?.data?.error || (editingPayment ? 'Failed to update payment' : 'Failed to record payment'));
    } finally {
      setSaving(false);
    }
  };

  const openCreate = () => {
    const termId = String(selectedTerm || (terms.find(t => t.is_active)?.id || ''));
    setEditingPayment(null);
    setForm({ ...EMPTY_FORM, term: termId });
    setShowModal(true);
  };

  const openEdit = (payment) => {
    setEditingPayment(payment);
    setForm({
      student: String(payment.student_id || ''),
      term: String(payment.term_id || ''),
      fee_type: String(payment.fee_type_id || ''),
      amount: String(payment.amount || ''),
      payment_date: payment.payment_date || '',
      method: payment.method || 'cash',
      reference: payment.reference || '',
      note: payment.note || '',
    });
    setShowModal(true);
  };

  const closeModal = () => {
    const termId = String(selectedTerm || (terms.find(t => t.is_active)?.id || ''));
    setShowModal(false);
    setEditingPayment(null);
    setForm({ ...EMPTY_FORM, term: termId });
  };

  const removePayment = async (payment) => {
    if (!window.confirm(`Delete payment for ${payment.student_name} on ${payment.payment_date}?`)) return;

    try {
      await api.delete(`/finance/payments/${payment.id}/delete/`);
      success('Payment deleted');
      if (payments.length === 1 && page > 1) {
        setPage(current => current - 1);
      } else {
        load();
      }
    } catch (e) {
      error(e.response?.data?.error || 'Failed to delete payment');
    }
  };

  const totalPages = Math.ceil(total / pageSize);

  return (
    <>
      <div className="page-header">
        <h1>Payments</h1>
        <p>{total} payment records</p>
      </div>
      <div className="page-body">
        <div className="toolbar finance-toolbar">
          <FinanceControl label="Search payments" grow>
            <div className="search-box">
              <Search size={16} />
              <input placeholder="Search by student name, ID, or reference..." value={search} onChange={e => setSearch(e.target.value)} />
            </div>
          </FinanceControl>
          <FinanceControl label="Term" minWidth="12rem">
            <select value={selectedTerm} onChange={e => setSelectedTerm(e.target.value)}>
              <option value="">All terms</option>
              {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
            </select>
          </FinanceControl>
          <div className="finance-toolbar-actions">
            <button className="btn btn-primary" onClick={openCreate}><Plus size={16} /> Record Payment</button>
          </div>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Student</th>
                <th>Amount</th>
                <th>Fee Type</th>
                <th>Date</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Term</th>
                <th>Recorded By</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {payments.map(p => (
                <tr key={p.id}>
                  <td>{p.student_name}</td>
                  <td>{formatCurrency(p.amount)}</td>
                  <td>{p.fee_type_name || '—'}</td>
                  <td>{p.payment_date}</td>
                  <td><span className="badge badge-accent">{p.method}</span></td>
                  <td>{p.reference || '—'}</td>
                  <td>{p.term_name}</td>
                  <td>{p.created_by_name || '—'}</td>
                  <td>
                    <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '0.35rem' }}>
                      <button className="btn btn-sm btn-ghost" onClick={() => openEdit(p)} title="Edit payment">
                        <Edit2 size={14} />
                      </button>
                      <button className="btn btn-sm btn-ghost" onClick={() => removePayment(p)} title="Delete payment">
                        <Trash2 size={14} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={payments.length > 0} colSpan={9} loadingMessage="Loading payments..." emptyMessage="No payments found" />
            </tbody>
          </table>
        </div>

        {totalPages > 1 && (
          <div className="pagination">
            <button className="btn btn-sm btn-ghost" disabled={page <= 1} onClick={() => setPage(p => p - 1)}><ChevronLeft size={16} /></button>
            <span>Page {page} of {totalPages}</span>
            <button className="btn btn-sm btn-ghost" disabled={page >= totalPages} onClick={() => setPage(p => p + 1)}><ChevronRight size={16} /></button>
            <FinanceControl label="Rows" minWidth="8.5rem" className="finance-control--compact finance-control--push">
              <select value={pageSize} onChange={e => { setPageSize(Number(e.target.value)); setPage(1); }}>
                {[10, 20, 50, 100].map(n => <option key={n} value={n}>{n} / page</option>)}
              </select>
            </FinanceControl>
          </div>
        )}

        {showModal && (
          <div className="modal-overlay" onClick={closeModal}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>{editingPayment ? 'Edit Payment' : 'Record Payment'}</h2>
              <SearchableSelectField
                label="Student"
                value={form.student}
                onChange={student => setForm({ ...form, student })}
                options={students.map(student => ({
                  value: String(student.id),
                  label: `${student.first_name} ${student.last_name} (${student.id_number})`,
                  searchText: `${student.first_name} ${student.last_name} ${student.id_number}`,
                }))}
                placeholder="Select student"
                searchPlaceholder="Search by student name or ID..."
              />
              <div className="form-group">
                <label>Term</label>
                <select value={form.term} onChange={e => setForm({ ...form, term: e.target.value })}>
                  <option value="">Select term</option>
                  {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Fee Type</label>
                <select value={form.fee_type} onChange={e => setForm({ ...form, fee_type: e.target.value })}>
                  <option value="">Select fee type</option>
                  {feeTypes.filter(ft => ft.is_active).map(ft => <option key={ft.id} value={ft.id}>{ft.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" value={form.amount} onChange={e => setForm({ ...form, amount: e.target.value })} placeholder="0.00" />
              </div>
              <div className="form-group">
                <label>Payment Date</label>
                <input type="date" value={form.payment_date} onChange={e => setForm({ ...form, payment_date: e.target.value })} />
              </div>
              <div className="form-group">
                <label>Method</label>
                <select value={form.method} onChange={e => setForm({ ...form, method: e.target.value })}>
                  <option value="cash">Cash</option>
                  <option value="transfer">Bank Transfer</option>
                  <option value="mobile">Mobile Money</option>
                  <option value="cheque">Cheque</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div className="form-group">
                <label>Reference</label>
                <input value={form.reference} onChange={e => setForm({ ...form, reference: e.target.value })} placeholder="Receipt/transaction number" />
              </div>
              <div className="form-group">
                <label>Note</label>
                <textarea value={form.note} onChange={e => setForm({ ...form, note: e.target.value })} rows={2} />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={closeModal}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={save} loading={saving} loadingText={editingPayment ? 'Updating...' : 'Saving...'} disabled={!form.student || !form.term || !form.amount || !form.payment_date}>
                  {editingPayment ? 'Update' : 'Save'}
                </LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
