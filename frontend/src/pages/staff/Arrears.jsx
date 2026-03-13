import React, { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import TableStatusRow from '../../components/TableStatusRow';
import { FinanceControl } from '../../components/FinanceControls';
import { Search, ChevronLeft, ChevronRight, Flag, AlertTriangle } from 'lucide-react';

export default function Arrears() {
  const [results, setResults] = useState([]);
  const [terms, setTerms] = useState([]);
  const [selectedTerm, setSelectedTerm] = useState('');
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState('');
  const [pageSize, setPageSize] = useState(20);
  const [loading, setLoading] = useState(true);
  const { error } = useToast();

  useEffect(() => {
    api.get('/lookup/terms/').then(r => setTerms(r.data));
  }, []);

  const load = useCallback(() => {
    setLoading(true);
    const params = { page, search, page_size: pageSize };
    if (selectedTerm) params.term = selectedTerm;
    api.get('/finance/arrears/', { params })
      .then(r => { setResults(r.data.results); setTotal(r.data.total || 0); })
      .catch(() => error('Failed to load arrears'))
      .finally(() => setLoading(false));
  }, [page, search, selectedTerm, pageSize, error]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { setPage(1); }, [search, selectedTerm]);

  const totalPages = Math.ceil(total / pageSize);

  return (
    <>
      <div className="page-header">
        <h1>Arrears Report</h1>
        <p>{total} students with outstanding balances</p>
      </div>
      <div className="page-body">
        <div className="toolbar finance-toolbar">
          <FinanceControl label="Find account" grow>
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
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th></th>
                <th>Student</th>
                <th>ID Number</th>
                <th>Total Outstanding</th>
                <th>Terms With Arrears</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {results.map(s => (
                <tr key={s.id}>
                  <td><Flag size={14} style={{ color: 'var(--md-sys-color-error)' }} /></td>
                  <td><Link to={`/app/finance/student-fees/${s.id}`}>{s.first_name} {s.last_name}</Link></td>
                  <td>{s.id_number}</td>
                  <td style={{ color: 'var(--md-sys-color-error)', fontWeight: 600 }}>${parseFloat(s.total_balance).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                  <td>
                    <div style={{ display: 'flex', gap: '0.35rem', flexWrap: 'wrap' }}>
                      {s.terms.map(t => (
                        <span key={t.term_id} className="badge badge-warning" title={`Owed: $${t.owed} | Paid: $${t.paid}`}>
                          {t.term_name}: ${parseFloat(t.balance).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                        </span>
                      ))}
                    </div>
                  </td>
                  <td>
                    <Link to={`/app/finance/student-fees/${s.id}`} className="btn btn-sm btn-ghost">View</Link>
                  </td>
                </tr>
              ))}
              <TableStatusRow loading={loading} hasRows={results.length > 0} colSpan={6} loadingMessage="Loading arrears report..." emptyMessage="No students with arrears found" />
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
