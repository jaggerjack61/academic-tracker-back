import React, { useEffect, useState, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import { FinanceControl } from '../../components/FinanceControls';
import { ArrowLeft, Plus, DollarSign, Receipt, CreditCard, Flag } from 'lucide-react';


const EMPTY_PAYMENT_FORM = {
  term: '',
  fee_type: '',
  amount: '',
  payment_date: '',
  method: 'cash',
  reference: '',
  note: '',
};


const EMPTY_SPECIAL_FORM = {
  term: '',
  name: '',
  amount: '',
  description: '',
};


function getDefaultTermId(terms, selectedTerm) {
  return selectedTerm || String(terms.find(term => term.is_active)?.id || '');
}


function getToday() {
  return new Date().toISOString().split('T')[0];
}

const STATUS_LABELS = {
  'paid': { label: 'Paid', cls: 'badge-active' },
  'partial': { label: 'Partial', cls: 'badge-warning' },
  'unpaid': { label: 'Unpaid', cls: 'badge-inactive' },
  'no-fees': { label: 'No Fees', cls: 'badge-accent' },
};

export default function StudentFeeDetail() {
  const { id } = useParams();
  const [data, setData] = useState(null);
  const [terms, setTerms] = useState([]);
  const [selectedTerm, setSelectedTerm] = useState('');
  const [showPayment, setShowPayment] = useState(false);
  const [showSpecialFee, setShowSpecialFee] = useState(false);
  const [paymentContextLabel, setPaymentContextLabel] = useState('');
  const [paymentSource, setPaymentSource] = useState('general');
  const [feeTypes, setFeeTypes] = useState([]);
  const [paymentForm, setPaymentForm] = useState(EMPTY_PAYMENT_FORM);
  const [specialForm, setSpecialForm] = useState(EMPTY_SPECIAL_FORM);
  const [paymentSaving, setPaymentSaving] = useState(false);
  const [specialSaving, setSpecialSaving] = useState(false);
  const { success, error } = useToast();

  useEffect(() => {
    api.get('/lookup/terms/').then(r => setTerms(r.data));
    api.get('/finance/fee-types/').then(r => setFeeTypes(r.data));
  }, []);

  const load = useCallback(() => {
    const params = selectedTerm ? { term: selectedTerm } : {};
    api.get(`/finance/student-fees/${id}/`, { params })
      .then(r => setData(r.data))
      .catch(() => error('Failed to load student fee details'));
  }, [id, selectedTerm, error]);

  useEffect(() => { load(); }, [load]);

  const closePaymentModal = () => {
    setShowPayment(false);
    setPaymentContextLabel('');
    setPaymentSource('general');
    setPaymentForm({
      ...EMPTY_PAYMENT_FORM,
      term: getDefaultTermId(terms, selectedTerm),
    });
  };

  const openGeneralPaymentModal = () => {
    setPaymentContextLabel('');
    setPaymentSource('general');
    setPaymentForm({
      ...EMPTY_PAYMENT_FORM,
      term: getDefaultTermId(terms, selectedTerm),
      payment_date: getToday(),
    });
    setShowPayment(true);
  };

  const openSpecialFeePaymentModal = (specialFee) => {
    setPaymentContextLabel(`For special fee: ${specialFee.name}`);
    setPaymentSource('special-fee');
    setPaymentForm({
      ...EMPTY_PAYMENT_FORM,
      term: String(specialFee.term_id),
      amount: String(specialFee.amount),
      payment_date: getToday(),
      note: `Payment for special fee: ${specialFee.name}`,
    });
    setShowPayment(true);
  };

  const closeSpecialFeeModal = () => {
    setShowSpecialFee(false);
    setSpecialForm({
      ...EMPTY_SPECIAL_FORM,
      term: getDefaultTermId(terms, selectedTerm),
    });
  };

  const openSpecialFeeModal = () => {
    setSpecialForm({
      ...EMPTY_SPECIAL_FORM,
      term: getDefaultTermId(terms, selectedTerm),
    });
    setShowSpecialFee(true);
  };

  const recordPayment = async () => {
    if (!paymentForm.term || !paymentForm.amount || !paymentForm.payment_date) {
      error('Term, amount and date are required'); return;
    }
    setPaymentSaving(true);
    try {
      await api.post('/finance/payments/create/', {
        student: id,
        term: paymentForm.term,
        fee_type: paymentForm.fee_type || null,
        amount: paymentForm.amount,
        payment_date: paymentForm.payment_date,
        method: paymentForm.method,
        reference: paymentForm.reference,
        note: paymentForm.note,
      });
      success('Payment recorded');
      closePaymentModal();
      load();
    } catch (e) { error(e.response?.data?.error || 'Failed to record payment'); }
    finally { setPaymentSaving(false); }
  };

  const addSpecialFee = async () => {
    if (!specialForm.term || !specialForm.name || !specialForm.amount) {
      error('Term, name and amount are required'); return;
    }
    setSpecialSaving(true);
    try {
      await api.post('/finance/special-fees/create/', {
        student: id,
        term: specialForm.term,
        name: specialForm.name,
        amount: specialForm.amount,
        description: specialForm.description,
      });
      success('Special fee added');
      closeSpecialFeeModal();
      load();
    } catch (e) { error(e.response?.data?.error || 'Failed to add special fee'); }
    finally { setSpecialSaving(false); }
  };

  if (!data) return <div className="loading"><div className="spinner" /></div>;

  const student = data.student;

  return (
    <>
      <div className="page-header">
        <Link to="/app/finance/student-fees" className="btn btn-ghost"><ArrowLeft size={16} /> Back</Link>
        <h1>{student.first_name} {student.last_name}</h1>
        <p>Fee account &middot; {student.id_number}</p>
      </div>
      <div className="page-body">
        <div className="toolbar finance-toolbar">
          <FinanceControl label="View term" minWidth="12rem">
            <select value={selectedTerm} onChange={e => setSelectedTerm(e.target.value)}>
              <option value="">All terms</option>
              {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
            </select>
          </FinanceControl>
          <div className="finance-toolbar-actions">
            <button className="btn btn-primary" onClick={openGeneralPaymentModal}><DollarSign size={16} /> Record Payment</button>
            <button className="btn btn-secondary" onClick={openSpecialFeeModal}><Plus size={16} /> Special Fee</button>
          </div>
        </div>

        {data.terms.map(td => {
          const info = STATUS_LABELS[td.status] || { label: td.status, cls: '' };
          const balance = parseFloat(td.balance);
          return (
            <div className="detail-card" key={td.term.id}>
              <div className="detail-card-header">
                <h3>{td.term.name}</h3>
                <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                  <span className={`badge ${info.cls}`}>{info.label}</span>
                  {(td.status === 'unpaid' || td.status === 'partial') && (
                    <span title="Outstanding balance" style={{ color: 'var(--md-sys-color-error)' }}><Flag size={16} /></span>
                  )}
                </div>
              </div>

              <div className="stats-grid" style={{ marginBottom: '1rem' }}>
                <div className="stat-card">
                  <div className="stat-content">
                    <span className="stat-value">${parseFloat(td.total_owed).toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                    <span className="stat-label">Total Owed</span>
                  </div>
                </div>
                <div className="stat-card">
                  <div className="stat-content">
                    <span className="stat-value">${parseFloat(td.total_paid).toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                    <span className="stat-label">Total Paid</span>
                  </div>
                </div>
                <div className="stat-card">
                  <div className="stat-content">
                    <span className="stat-value" style={balance > 0 ? { color: 'var(--md-sys-color-error)' } : {}}>${balance.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                    <span className="stat-label">Balance</span>
                  </div>
                </div>
              </div>

              {td.fee_structures.length > 0 && (
                <>
                  <h4 style={{ margin: '0.75rem 0 0.5rem', fontSize: '0.88rem', fontWeight: 600 }}>Fee Structure</h4>
                  <div className="table-wrapper">
                    <table>
                      <thead><tr><th>Fee Type</th><th>Grade</th><th>Amount</th></tr></thead>
                      <tbody>
                        {td.fee_structures.map(fs => (
                          <tr key={fs.id}>
                            <td>{fs.fee_type_name}</td>
                            <td>{fs.grade_name}</td>
                            <td>${parseFloat(fs.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </>
              )}

              {td.special_fees.length > 0 && (
                <>
                  <h4 style={{ margin: '0.75rem 0 0.5rem', fontSize: '0.88rem', fontWeight: 600 }}>Special Fees</h4>
                  <div className="table-wrapper">
                    <table>
                      <thead><tr><th>Name</th><th>Amount</th><th>Description</th><th></th></tr></thead>
                      <tbody>
                        {td.special_fees.map(sf => (
                          <tr key={sf.id}>
                            <td>{sf.name}</td>
                            <td>${parseFloat(sf.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                            <td>{sf.description || '—'}</td>
                            <td>
                              <button className="btn btn-sm btn-ghost" onClick={() => openSpecialFeePaymentModal(sf)}>
                                <DollarSign size={14} /> Make Payment
                              </button>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </>
              )}

              {td.payments.length > 0 && (
                <>
                  <h4 style={{ margin: '0.75rem 0 0.5rem', fontSize: '0.88rem', fontWeight: 600 }}>Payments</h4>
                  <div className="table-wrapper">
                    <table>
                      <thead><tr><th>Date</th><th>Amount</th><th>Fee Type</th><th>Method</th><th>Reference</th><th>Recorded By</th></tr></thead>
                      <tbody>
                        {td.payments.map(p => (
                          <tr key={p.id}>
                            <td>{p.payment_date}</td>
                            <td>${parseFloat(p.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                            <td>{p.fee_type_name || '—'}</td>
                            <td><span className="badge badge-accent">{p.method}</span></td>
                            <td>{p.reference || '—'}</td>
                            <td>{p.created_by_name || '—'}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </>
              )}

              {td.payment_plans.length > 0 && (
                <>
                  <h4 style={{ margin: '0.75rem 0 0.5rem', fontSize: '0.88rem', fontWeight: 600 }}>Payment Plans</h4>
                  {td.payment_plans.map(plan => (
                    <div key={plan.id} style={{ marginBottom: '0.75rem' }}>
                      <div style={{ fontSize: '0.84rem', marginBottom: '0.25rem' }}>
                        <strong>${parseFloat(plan.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</strong> in {plan.installments} installments
                        &middot; Paid: ${parseFloat(plan.paid_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                      </div>
                      <div className="table-wrapper">
                        <table>
                          <thead><tr><th>#</th><th>Amount</th><th>Due Date</th><th>Status</th></tr></thead>
                          <tbody>
                            {(plan.plan_installments || []).map(inst => (
                              <tr key={inst.id}>
                                <td>{inst.installment_number}</td>
                                <td>${parseFloat(inst.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                <td>{inst.due_date}</td>
                                <td><span className={`badge ${inst.is_paid ? 'badge-active' : 'badge-inactive'}`}>{inst.is_paid ? 'Paid' : 'Pending'}</span></td>
                              </tr>
                            ))}
                          </tbody>
                        </table>
                      </div>
                    </div>
                  ))}
                </>
              )}
            </div>
          );
        })}

        {data.terms.length === 0 && (
          <div className="detail-card"><p className="empty-cell">No fee data found for this student</p></div>
        )}

        {/* Record Payment Modal */}
        {showPayment && (
          <div className="modal-overlay" onClick={closePaymentModal}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>Record Payment</h2>
              {paymentContextLabel && (
                <p style={{ marginBottom: '1rem', color: 'var(--md-sys-color-on-surface-variant)', fontSize: '0.92rem' }}>
                  {paymentContextLabel}
                </p>
              )}
              <div className="form-group">
                <label>Term</label>
                <select value={paymentForm.term} onChange={e => setPaymentForm({ ...paymentForm, term: e.target.value })} disabled={paymentSource === 'special-fee'}>
                  <option value="">Select term</option>
                  {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                </select>
              </div>
              {paymentSource !== 'special-fee' && (
                <div className="form-group">
                  <label>Fee Type</label>
                  <select value={paymentForm.fee_type} onChange={e => setPaymentForm({ ...paymentForm, fee_type: e.target.value })}>
                    <option value="">Optional fee type</option>
                    {feeTypes.filter(ft => ft.is_active).map(ft => <option key={ft.id} value={ft.id}>{ft.name}</option>)}
                  </select>
                </div>
              )}
              <div className="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" value={paymentForm.amount} onChange={e => setPaymentForm({ ...paymentForm, amount: e.target.value })} placeholder="0.00" />
              </div>
              <div className="form-group">
                <label>Payment Date</label>
                <input type="date" value={paymentForm.payment_date} onChange={e => setPaymentForm({ ...paymentForm, payment_date: e.target.value })} />
              </div>
              <div className="form-group">
                <label>Method</label>
                <select value={paymentForm.method} onChange={e => setPaymentForm({ ...paymentForm, method: e.target.value })}>
                  <option value="cash">Cash</option>
                  <option value="transfer">Bank Transfer</option>
                  <option value="mobile">Mobile Money</option>
                  <option value="cheque">Cheque</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div className="form-group">
                <label>Reference</label>
                <input value={paymentForm.reference} onChange={e => setPaymentForm({ ...paymentForm, reference: e.target.value })} placeholder="Receipt/transaction number" />
              </div>
              <div className="form-group">
                <label>Note</label>
                <textarea value={paymentForm.note} onChange={e => setPaymentForm({ ...paymentForm, note: e.target.value })} rows={2} />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={closePaymentModal}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={recordPayment} loading={paymentSaving} loadingText="Saving..." disabled={!paymentForm.term || !paymentForm.amount || !paymentForm.payment_date}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}

        {/* Special Fee Modal */}
        {showSpecialFee && (
          <div className="modal-overlay" onClick={closeSpecialFeeModal}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>Add Special Fee</h2>
              <div className="form-group">
                <label>Term</label>
                <select value={specialForm.term} onChange={e => setSpecialForm({ ...specialForm, term: e.target.value })}>
                  <option value="">Select term</option>
                  {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Fee Name</label>
                <input value={specialForm.name} onChange={e => setSpecialForm({ ...specialForm, name: e.target.value })} placeholder="e.g. Exam retake fee" />
              </div>
              <div className="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" value={specialForm.amount} onChange={e => setSpecialForm({ ...specialForm, amount: e.target.value })} placeholder="0.00" />
              </div>
              <div className="form-group">
                <label>Description</label>
                <textarea value={specialForm.description} onChange={e => setSpecialForm({ ...specialForm, description: e.target.value })} rows={2} />
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={closeSpecialFeeModal}>Cancel</button>
                <LoadingButton className="btn btn-primary" onClick={addSpecialFee} loading={specialSaving} loadingText="Saving..." disabled={!specialForm.term || !specialForm.name.trim() || !specialForm.amount}>Save</LoadingButton>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
