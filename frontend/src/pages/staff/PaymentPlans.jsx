import React, { useEffect, useState, useCallback } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import { FinanceControl, SearchableSelectField } from '../../components/FinanceControls';
import { Search, ChevronLeft, ChevronRight, Plus, CreditCard, Check, Clock } from 'lucide-react';


const EMPTY_PLAN_FORM = {
  student: '',
  term: '',
  fee_type: '',
  total_amount: '',
  installments: 2,
  description: '',
  installments_data: [],
};


const EMPTY_INSTALLMENT_PAYMENT_FORM = {
  fee_type: '',
  term: '',
  amount: '',
  payment_date: '',
  method: 'cash',
  reference: '',
  note: '',
};


function todayValue() {
  return new Date().toISOString().split('T')[0];
}

export default function PaymentPlans() {
  const [plans, setPlans] = useState([]);
  const [terms, setTerms] = useState([]);
  const [students, setStudents] = useState([]);
  const [feeTypes, setFeeTypes] = useState([]);
  const [selectedTerm, setSelectedTerm] = useState('');
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState('');
  const [pageSize, setPageSize] = useState(20);
  const [showModal, setShowModal] = useState(false);
  const [showInstallmentModal, setShowInstallmentModal] = useState(false);
  const [selectedInstallment, setSelectedInstallment] = useState(null);
  const [form, setForm] = useState(EMPTY_PLAN_FORM);
  const [installmentForm, setInstallmentForm] = useState(EMPTY_INSTALLMENT_PAYMENT_FORM);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [installmentSaving, setInstallmentSaving] = useState(false);
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
    api.get('/finance/payment-plans/', { params })
      .then(r => { setPlans(r.data.results); setTotal(r.data.total || 0); })
      .catch(() => error('Failed to load'))
      .finally(() => setLoading(false));
  }, [page, search, selectedTerm, pageSize, error]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { setPage(1); }, [search, selectedTerm]);

  const closeInstallmentModal = () => {
    setShowInstallmentModal(false);
    setSelectedInstallment(null);
    setInstallmentForm(EMPTY_INSTALLMENT_PAYMENT_FORM);
  };

  const openInstallmentModal = (plan, installment) => {
    setSelectedInstallment({ plan, installment });
    setInstallmentForm({
      fee_type: String(plan.fee_type_id || ''),
      term: String(plan.term_id || ''),
      amount: String(installment.amount || ''),
      payment_date: todayValue(),
      method: 'cash',
      reference: '',
      note: `Installment ${installment.installment_number} payment${plan.description ? ` · ${plan.description}` : ''}`,
    });
    setShowInstallmentModal(true);
  };

  const markInstallmentUnpaid = async (installment) => {
    if (!window.confirm('Mark this installment as unpaid? The linked payment record will be removed.')) return;

    try {
      await api.post(`/finance/installments/${installment.id}/toggle-paid/`, { mark_unpaid: true });
      success('Installment marked unpaid');
      load();
    } catch (e) {
      error(e.response?.data?.error || 'Failed to update installment');
    }
  };

  const submitInstallmentPayment = async () => {
    if (!selectedInstallment) return;

    setInstallmentSaving(true);
    try {
      await api.post(`/finance/installments/${selectedInstallment.installment.id}/toggle-paid/`, installmentForm);
      success('Installment marked paid');
      closeInstallmentModal();
      load();
    } catch (e) {
      error(e.response?.data?.error || 'Failed to mark installment as paid');
    } finally {
      setInstallmentSaving(false);
    }
  };

  const handleInstallmentAction = (plan, installment) => {
    if (installment.is_paid) {
      markInstallmentUnpaid(installment);
      return;
    }
    openInstallmentModal(plan, installment);
  };

  const openCreate = () => {
    const termId = selectedTerm || (terms.find(t => t.is_active)?.id || '');
    setForm({ ...EMPTY_PLAN_FORM, term: termId, installments_data: [{ amount: '', due_date: '' }, { amount: '', due_date: '' }] });
    setShowModal(true);
  };

  const updateInstCount = (count) => {
    const n = Math.max(1, Math.min(12, count));
    const data = Array.from({ length: n }, (_, i) => form.installments_data[i] || { amount: '', due_date: '' });
    setForm({ ...form, installments: n, installments_data: data });
  };

  const updateInstData = (idx, field, value) => {
    const copy = [...form.installments_data];
    copy[idx] = { ...copy[idx], [field]: value };
    setForm({ ...form, installments_data: copy });
  };

  const save = async () => {
    setSaving(true);
    try {
      await api.post('/finance/payment-plans/create/', form);
      success('Payment plan created');
      setShowModal(false);
      load();
    } catch (e) { error(e.response?.data?.error || 'Failed'); }
    finally { setSaving(false); }
  };

  const totalPages = Math.ceil(total / pageSize);

  return (
    <>
      <div className="page-header"><h1>Payment Plans</h1><p>{total} plans</p></div>
      <div className="page-body">
        <div className="toolbar finance-toolbar">
          <FinanceControl label="Find student" grow>
            <div className="search-box">
              <Search size={16} />
              <input placeholder="Search by student name..." value={search} onChange={e => setSearch(e.target.value)} />
            </div>
          </FinanceControl>
          <FinanceControl label="Term" minWidth="12rem">
            <select value={selectedTerm} onChange={e => setSelectedTerm(e.target.value)}>
              <option value="">All terms</option>
              {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
            </select>
          </FinanceControl>
          <div className="finance-toolbar-actions">
            <button className="btn btn-primary" onClick={openCreate}><Plus size={16} /> New Plan</button>
          </div>
        </div>

        {plans.map(plan => (
          <div className="detail-card" key={plan.id}>
            <div className="detail-card-header">
              <div>
                <h3>{plan.student_name} &middot; {plan.term_name}</h3>
                <p style={{ fontSize: '0.82rem', color: 'var(--md-sys-color-on-surface-variant)', marginTop: '0.2rem' }}>
                  {plan.fee_type_name || 'Fee type not set'}
                </p>
              </div>
              <span className="badge badge-accent">
                ${parseFloat(plan.paid_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })} / ${parseFloat(plan.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}
              </span>
            </div>
            {plan.description && <p style={{ fontSize: '0.84rem', color: 'var(--text-muted)', margin: '0.25rem 0 0.5rem' }}>{plan.description}</p>}
            <div className="table-wrapper">
              <table>
                <thead><tr><th>#</th><th>Amount</th><th>Due Date</th><th>Status</th><th></th></tr></thead>
                <tbody>
                  {(plan.plan_installments || []).map(inst => (
                    <tr key={inst.id}>
                      <td>{inst.installment_number}</td>
                      <td>${parseFloat(inst.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                      <td>{inst.due_date}</td>
                      <td>
                        <span className={`badge ${inst.is_paid ? 'badge-active' : 'badge-warning'}`}>
                          {inst.is_paid ? 'Paid' : 'Pending'}
                        </span>
                      </td>
                      <td>
                        <button className="btn btn-sm btn-ghost" onClick={() => handleInstallmentAction(plan, inst)} title={inst.is_paid ? 'Mark unpaid' : 'Mark paid'}>
                          {inst.is_paid ? <Clock size={14} /> : <Check size={14} />}
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        ))}
        {loading && plans.length === 0 && (
          <div className="detail-card"><div className="loading"><div className="spinner spinner-sm" /></div></div>
        )}

        {!loading && plans.length === 0 && (
          <div className="detail-card"><p className="empty-cell">No payment plans found</p></div>
        )}

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

        {showInstallmentModal && selectedInstallment && (
          <div className="modal-overlay" onClick={closeInstallmentModal}>
            <div className="modal" onClick={e => e.stopPropagation()} style={{ maxWidth: '32rem' }}>
              <h2>Record Installment Payment</h2>
              <div className="form-group">
                <label>Student</label>
                <input value={selectedInstallment.plan.student_name || ''} readOnly />
              </div>
              <div className="form-group">
                <label>Term</label>
                <input value={selectedInstallment.plan.term_name || ''} readOnly />
              </div>
              <div className="form-group">
                <label>Fee Type</label>
                {selectedInstallment.plan.fee_type_id ? (
                  <input value={selectedInstallment.plan.fee_type_name || ''} readOnly />
                ) : (
                  <select value={installmentForm.fee_type} onChange={e => setInstallmentForm({ ...installmentForm, fee_type: e.target.value })}>
                    <option value="">Select fee type</option>
                    {feeTypes.filter(ft => ft.is_active).map(ft => <option key={ft.id} value={ft.id}>{ft.name}</option>)}
                  </select>
                )}
              </div>
              <div className="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" value={installmentForm.amount} readOnly />
              </div>
              <div className="form-group">
                <label>Payment Date</label>
                <input type="date" value={installmentForm.payment_date} onChange={e => setInstallmentForm({ ...installmentForm, payment_date: e.target.value })} />
              </div>
              <div className="form-group">
                <label>Method</label>
                <select value={installmentForm.method} onChange={e => setInstallmentForm({ ...installmentForm, method: e.target.value })}>
                  <option value="cash">Cash</option>
                  <option value="transfer">Bank Transfer</option>
                  <option value="mobile">Mobile Money</option>
                  <option value="cheque">Cheque</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div className="form-group">
                <label>Reference</label>
                <input value={installmentForm.reference} onChange={e => setInstallmentForm({ ...installmentForm, reference: e.target.value })} placeholder="Receipt/transaction number" />
              </div>
              <div className="form-group">
                <label>Note</label>
                <textarea value={installmentForm.note} onChange={e => setInstallmentForm({ ...installmentForm, note: e.target.value })} rows={2} />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={closeInstallmentModal}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={submitInstallmentPayment} loading={installmentSaving} loadingText="Saving..." disabled={!installmentForm.payment_date || !installmentForm.amount}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}

        {showModal && (
          <div className="modal-overlay" onClick={() => setShowModal(false)}>
            <div className="modal" onClick={e => e.stopPropagation()} style={{ maxWidth: '32rem' }}>
              <h2>New Payment Plan</h2>
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
                <label>Total Amount</label>
                <input type="number" step="0.01" value={form.total_amount} onChange={e => setForm({ ...form, total_amount: e.target.value })} placeholder="0.00" />
              </div>
              <div className="form-group">
                <label>Number of Installments</label>
                <input type="number" min={1} max={12} value={form.installments} onChange={e => updateInstCount(Number(e.target.value))} />
              </div>
              <div className="form-group">
                <label>Description</label>
                <textarea value={form.description} onChange={e => setForm({ ...form, description: e.target.value })} rows={2} />
              </div>
              <h4 style={{ margin: '0.75rem 0 0.5rem', fontSize: '0.88rem', fontWeight: 600 }}>Installments</h4>
              {form.installments_data.map((inst, idx) => (
                <div key={idx} style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem', marginBottom: '0.5rem' }}>
                  <div className="form-group" style={{ margin: 0 }}>
                    <label>#{idx + 1} Amount</label>
                    <input type="number" step="0.01" value={inst.amount} onChange={e => updateInstData(idx, 'amount', e.target.value)} placeholder="0.00" />
                  </div>
                  <div className="form-group" style={{ margin: 0 }}>
                    <label>Due Date</label>
                    <input type="date" value={inst.due_date} onChange={e => updateInstData(idx, 'due_date', e.target.value)} />
                  </div>
                </div>
              ))}
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowModal(false)}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={save} loading={saving} loadingText="Saving..." disabled={!form.student || !form.term || !form.total_amount}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
