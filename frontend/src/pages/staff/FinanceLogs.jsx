import React, { useCallback, useEffect, useState } from 'react';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { FinanceControl } from '../../components/FinanceControls';
import TableStatusRow from '../../components/TableStatusRow';
import { Search, ChevronLeft, ChevronRight, History } from 'lucide-react';


const ACTION_STYLES = {
  create: 'badge-active',
  update: 'badge-accent',
  delete: 'badge-warning',
};


function formatCurrency(value) {
  return `$${parseFloat(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
}


function formatTimestamp(value) {
  if (!value) return '—';
  return new Date(value).toLocaleString();
}


export default function FinanceLogs() {
  const [logs, setLogs] = useState([]);
  const [terms, setTerms] = useState([]);
  const [selectedTerm, setSelectedTerm] = useState('');
  const [actionFilter, setActionFilter] = useState('');
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [pageSize, setPageSize] = useState(20);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(true);
  const { error } = useToast();

  useEffect(() => {
    api.get('/lookup/terms/').then(r => setTerms(r.data));
  }, []);

  const load = useCallback(() => {
    setLoading(true);
    const params = { page, page_size: pageSize, search };
    if (selectedTerm) params.term = selectedTerm;
    if (actionFilter) params.action = actionFilter;

    api.get('/finance/payment-logs/', { params })
      .then(r => {
        setLogs(r.data.results || []);
        setTotal(r.data.total || 0);
      })
      .catch(() => error('Failed to load payment logs'))
      .finally(() => setLoading(false));
  }, [actionFilter, error, page, pageSize, search, selectedTerm]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { setPage(1); }, [search, selectedTerm, actionFilter]);

  const totalPages = Math.ceil(total / pageSize);

  return (
    <>
      <div className="page-header">
        <h1>Logs</h1>
        <p>Read-only audit trail for all finance payment changes</p>
      </div>
      <div className="page-body">
        <div className="detail-card" style={{ marginBottom: '1rem' }}>
          <div className="detail-card-header">
            <h3 style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}><History size={18} /> Immutable audit trail</h3>
            <span className="badge badge-accent">{total} entries</span>
          </div>
          <p style={{ marginTop: '0.4rem', color: 'var(--md-sys-color-on-surface-variant)', fontSize: '0.92rem' }}>
            Logs are generated automatically when payments are created, edited, or deleted. They cannot be edited or deleted.
          </p>
        </div>

        <div className="toolbar finance-toolbar">
          <FinanceControl label="Search logs" grow>
            <div className="search-box">
              <Search size={16} />
              <input placeholder="Search by student, reference, fee type, or user..." value={search} onChange={e => setSearch(e.target.value)} />
            </div>
          </FinanceControl>
          <FinanceControl label="Term" minWidth="12rem">
            <select value={selectedTerm} onChange={e => setSelectedTerm(e.target.value)}>
              <option value="">All terms</option>
              {terms.map(term => <option key={term.id} value={term.id}>{term.name}</option>)}
            </select>
          </FinanceControl>
          <FinanceControl label="Action" minWidth="12rem">
            <select value={actionFilter} onChange={e => setActionFilter(e.target.value)}>
              <option value="">All actions</option>
              <option value="create">Created</option>
              <option value="update">Updated</option>
              <option value="delete">Deleted</option>
            </select>
          </FinanceControl>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>When</th>
                <th>Action</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Term</th>
                <th>Reference</th>
                <th>What Changed</th>
                <th>By</th>
              </tr>
            </thead>
            <tbody>
              {logs.map(log => (
                <tr key={log.id}>
                  <td>{formatTimestamp(log.created_at)}</td>
                  <td>
                    <span className={`badge ${ACTION_STYLES[log.action] || 'badge-accent'}`}>
                      {log.action}
                    </span>
                  </td>
                  <td>
                    <div>{log.student_name || '—'}</div>
                    {log.fee_type_name && (
                      <div style={{ fontSize: '0.78rem', color: 'var(--md-sys-color-on-surface-variant)', marginTop: '0.15rem' }}>
                        {log.fee_type_name}
                      </div>
                    )}
                  </td>
                  <td>{formatCurrency(log.amount)}</td>
                  <td>{log.term_name || '—'}</td>
                  <td>{log.reference || '—'}</td>
                  <td>
                    <div>{log.change_summary}</div>
                    {Array.isArray(log.changes) && log.changes.length > 0 && (
                      <div style={{ fontSize: '0.78rem', color: 'var(--md-sys-color-on-surface-variant)', marginTop: '0.15rem' }}>
                        {log.changes.map(change => `${change.label}: ${change.before || '—'} -> ${change.after || '—'}`).join(' | ')}
                      </div>
                    )}
                  </td>
                  <td>{log.actor_name || log.actor_email || '—'}</td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={logs.length > 0} colSpan={8} loadingMessage="Loading payment logs..." emptyMessage="No payment logs found" />
            </tbody>
          </table>
        </div>

        {totalPages > 1 && (
          <div className="pagination">
            <button className="btn btn-sm btn-ghost" disabled={page <= 1} onClick={() => setPage(current => current - 1)}><ChevronLeft size={16} /></button>
            <span>Page {page} of {totalPages}</span>
            <button className="btn btn-sm btn-ghost" disabled={page >= totalPages} onClick={() => setPage(current => current + 1)}><ChevronRight size={16} /></button>
            <FinanceControl label="Rows" minWidth="8.5rem" className="finance-control--compact finance-control--push">
              <select value={pageSize} onChange={e => { setPageSize(Number(e.target.value)); setPage(1); }}>
                {[10, 20, 50, 100].map(size => <option key={size} value={size}>{size} / page</option>)}
              </select>
            </FinanceControl>
          </div>
        )}
      </div>
    </>
  );
}