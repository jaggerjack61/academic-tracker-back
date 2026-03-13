import React, { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import TableStatusRow from '../../components/TableStatusRow';
import { FinanceControl } from '../../components/FinanceControls';
import { DollarSign, Users, AlertTriangle, TrendingUp, Receipt } from 'lucide-react';

export default function FinanceDashboard() {
  const [data, setData] = useState(null);
  const [terms, setTerms] = useState([]);
  const [selectedTerm, setSelectedTerm] = useState('');
  const [loading, setLoading] = useState(true);
  const { error } = useToast();

  useEffect(() => {
    api.get('/lookup/terms/').then(r => setTerms(r.data)).catch(() => {});
  }, []);

  const load = useCallback(() => {
    setLoading(true);
    const params = {};
    if (selectedTerm) params.term = selectedTerm;

    api.get('/finance/dashboard/', { params })
      .then(r => setData(r.data))
      .catch(() => error('Failed to load finance dashboard'))
      .finally(() => setLoading(false));
  }, [error, selectedTerm]);

  useEffect(() => { load(); }, [load]);

  if (!data) return <div className="loading"><div className="spinner" /></div>;

  const collected = parseFloat(data.total_collected || 0);
  const outstanding = parseFloat(data.total_outstanding || 0);
  const recentPayments = loading ? [] : (data.recent_payments || []);

  return (
    <>
      <div className="page-header">
        <h1>Finance</h1>
        <p>{data.term ? data.term.name : 'All terms'}</p>
      </div>
      <div className="page-body">
        <div className="toolbar finance-toolbar">
          <FinanceControl label="Reporting term" hint="Filter the summary cards and recent payments" minWidth="14rem">
            <select value={selectedTerm} onChange={e => setSelectedTerm(e.target.value)}>
              <option value="">All terms</option>
              {terms.map(term => <option key={term.id} value={term.id}>{term.name}</option>)}
            </select>
          </FinanceControl>
        </div>

        <div className="stats-grid">
          <div className="stat-card">
            <div className="stat-icon" style={{ background: '#d8f2df', color: '#1f5d37' }}><DollarSign size={20} /></div>
            <div className="stat-content">
              <span className="stat-value">${collected.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
              <span className="stat-label">Collected</span>
            </div>
          </div>
          <div className="stat-card">
            <div className="stat-icon" style={{ background: '#ffe3c2', color: '#834c00' }}><AlertTriangle size={20} /></div>
            <div className="stat-content">
              <span className="stat-value">${outstanding.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
              <span className="stat-label">Outstanding</span>
            </div>
          </div>
          <div className="stat-card">
            <div className="stat-icon" style={{ background: '#d8f2df', color: '#1f5d37' }}><Users size={20} /></div>
            <div className="stat-content">
              <span className="stat-value">{data.paid_count}</span>
              <span className="stat-label">Fully Paid</span>
            </div>
          </div>
          <div className="stat-card">
            <div className="stat-icon" style={{ background: '#ffe3c2', color: '#834c00' }}><TrendingUp size={20} /></div>
            <div className="stat-content">
              <span className="stat-value">{data.partial_count}</span>
              <span className="stat-label">Partial</span>
            </div>
          </div>
          <div className="stat-card">
            <div className="stat-icon" style={{ background: 'var(--md-sys-color-error-container)', color: 'var(--md-sys-color-on-error-container)' }}><AlertTriangle size={20} /></div>
            <div className="stat-content">
              <span className="stat-value">{data.unpaid_count}</span>
              <span className="stat-label">Unpaid</span>
            </div>
          </div>
        </div>

        <div className="finance-quick-links">
          <Link to="/app/finance/student-fees" className="btn btn-secondary"><Users size={16} /> Student Fees</Link>
          <Link to="/app/finance/payments" className="btn btn-secondary"><Receipt size={16} /> Payments</Link>
          <Link to="/app/finance/arrears" className="btn btn-secondary"><AlertTriangle size={16} /> Arrears Report</Link>
        </div>

        <div className="detail-card">
          <h3>Recent Payments</h3>
          <div className="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Amount</th>
                  <th>Date</th>
                  <th>Method</th>
                  <th>Reference</th>
                </tr>
              </thead>
              <tbody>
                {recentPayments.map(p => (
                  <tr key={p.id}>
                    <td>{p.student_name}</td>
                    <td>${parseFloat(p.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td>{p.payment_date}</td>
                    <td><span className="badge badge-accent">{p.method}</span></td>
                    <td>{p.reference || '—'}</td>
                  </tr>
                ))}
                <TableStatusRow loading={loading} hasRows={recentPayments.length > 0} colSpan={5} loadingMessage="Loading recent payments..." emptyMessage="No payments recorded yet" />
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </>
  );
}
