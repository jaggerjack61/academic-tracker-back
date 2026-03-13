import React, { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import TableStatusRow from '../../components/TableStatusRow';
import { FinanceControl } from '../../components/FinanceControls';
import { Search, ChevronLeft, ChevronRight, Flag, Filter } from 'lucide-react';

const STATUS_LABELS = {
  'paid': { label: 'Paid', cls: 'badge-active' },
  'partial': { label: 'Partial', cls: 'badge-warning' },
  'unpaid': { label: 'Unpaid', cls: 'badge-inactive' },
  'no-fees': { label: 'No Fees', cls: 'badge-accent' },
};

export default function StudentFees() {
  const [students, setStudents] = useState([]);
  const [terms, setTerms] = useState([]);
  const [selectedTerm, setSelectedTerm] = useState('');
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState('');
  const [flag, setFlag] = useState('');
  const [pageSize, setPageSize] = useState(20);
  const [loading, setLoading] = useState(true);
  const { error } = useToast();

  useEffect(() => {
    api.get('/lookup/terms/').then(r => setTerms(r.data));
  }, []);

  const load = useCallback(() => {
    setLoading(true);
    const params = { page, search, flag, page_size: pageSize };
    if (selectedTerm) params.term = selectedTerm;
    api.get('/finance/student-fees/', { params })
      .then(r => { setStudents(r.data.results); setTotal(r.data.total || 0); })
      .catch(() => error('Failed to load student fees'))
      .finally(() => setLoading(false));
  }, [selectedTerm, page, search, flag, pageSize, error]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { setPage(1); }, [search, flag, selectedTerm]);

  const totalPages = Math.ceil(total / pageSize);

  return (
    <>
      <div className="page-header">
        <h1>Student Fees</h1>
        <p>{total} students</p>
      </div>
      <div className="page-body">
        <div className="toolbar finance-toolbar">
          <FinanceControl label="Find student" grow>
            <div className="search-box">
              <Search size={16} />
              <input placeholder="Search by name or ID..." value={search} onChange={e => setSearch(e.target.value)} />
            </div>
          </FinanceControl>
          <FinanceControl label="Term" minWidth="12rem">
            <select value={selectedTerm} onChange={e => setSelectedTerm(e.target.value)}>
              <option value="">All terms</option>
              {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
            </select>
          </FinanceControl>
          <FinanceControl label="Status" minWidth="12rem">
            <select value={flag} onChange={e => setFlag(e.target.value)}>
              <option value="">All statuses</option>
              <option value="paid">Paid</option>
              <option value="partial">Partial</option>
              <option value="unpaid">Unpaid</option>
              <option value="no-fees">No Fees</option>
            </select>
          </FinanceControl>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Student</th>
                <th>ID Number</th>
                <th>Total Owed</th>
                <th>Total Paid</th>
                <th>Balance</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {students.map(s => {
                const info = STATUS_LABELS[s.status] || { label: s.status, cls: '' };
                const balance = parseFloat(s.balance);
                return (
                  <tr key={s.id}>
                    <td><Link to={`/app/finance/student-fees/${s.id}`}>{s.first_name} {s.last_name}</Link></td>
                    <td>{s.id_number}</td>
                    <td>${parseFloat(s.total_owed).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td>${parseFloat(s.total_paid).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td style={balance > 0 ? { color: 'var(--md-sys-color-error)', fontWeight: 600 } : {}}>${balance.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td><span className={`badge ${info.cls}`}>{info.label}</span></td>
                    <td>
                      {(s.status === 'unpaid' || s.status === 'partial') && (
                        <span title="Outstanding balance" style={{ color: 'var(--md-sys-color-error)' }}><Flag size={14} /></span>
                      )}
                    </td>
                  </tr>
                );
              })}
              <TableStatusRow loading={loading} hasRows={students.length > 0} colSpan={7} loadingMessage="Loading student fees..." emptyMessage="No students found" />
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
      </div>
    </>
  );
}
